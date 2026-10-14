<?php
/**
 * Modelo de Agenda de Reuniones
 * Administra eventos con integración a Google Calendar y notificaciones móviles
 */

namespace App\Models;

use PDO;

class MeetingModel extends Model {
    /**
     * Listar todas las reuniones programadas
     */
    public function getAll(?string $fromDate = null): array {
        $sql = "
            SELECT m.*,
                   r.company_name, r.contact_person, r.email AS realtor_email, r.phone AS realtor_phone,
                   e.full_name AS employee_name, u.email AS employee_email, e.phone AS employee_phone,
                   roles.name AS employee_role
            FROM meetings m
            LEFT JOIN realtors r ON m.realtor_id = r.id
            LEFT JOIN employees e ON m.employee_id = e.id
            LEFT JOIN users u ON e.user_id = u.id
            LEFT JOIN roles ON u.role_id = roles.id
        ";
        $params = [];

        if ($fromDate !== null) {
            $sql .= " WHERE m.meeting_date >= ?";
            $params[] = $fromDate;
        }

        $sql .= " ORDER BY m.meeting_date ASC, m.meeting_time ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Registrar una nueva reunión en la agenda
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO meetings (title, realtor_id, employee_id, meeting_date, meeting_time, duration_minutes, meeting_link, description, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'PROGRAMADA')
        ");
        $stmt->execute([
            trim($data['title']),
            !empty($data['realtor_id']) ? (int)$data['realtor_id'] : null,
            !empty($data['employee_id']) ? (int)$data['employee_id'] : null,
            $data['meeting_date'],
            $data['meeting_time'],
            (int)($data['duration_minutes'] ?? 30),
            !empty($data['meeting_link']) ? trim($data['meeting_link']) : null,
            !empty($data['description']) ? trim($data['description']) : null
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Eliminar reunión
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM meetings WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Construir enlace oficial para agregar a Google Calendar con 1 clic
     * Asigna a los asistentes (trabajador y cliente) mediante el parámetro oficial 'add'
     * para que Google Calendar envíe la invitación y notificación al teléfono móvil
     */
    public static function buildGoogleCalendarUrl(array $meeting): string {
        $startTimestamp = strtotime("{$meeting['meeting_date']} {$meeting['meeting_time']}");
        $duration = (int)($meeting['duration_minutes'] ?? 30);
        $endTimestamp = $startTimestamp + ($duration * 60);
        $startDateStr = date('Ymd\THis', $startTimestamp);
        $endDateStr = date('Ymd\THis', $endTimestamp);

        $title = $meeting['title'];
        if (!empty($meeting['company_name'])) {
            $title .= ' - ' . $meeting['company_name'];
        }

        $details = $meeting['description'] ?? '';
        $details .= "\n\nAgendado desde Nexus Operations Suite (Miami Luxury Media).";
        if (!empty($meeting['employee_name']) && !empty($meeting['employee_email'])) {
            $details .= "\nTrabajador Asignado: {$meeting['employee_name']} <{$meeting['employee_email']}>";
        }
        if (!empty($meeting['company_name'])) {
            $details .= "\nCliente: {$meeting['company_name']}";
            if (!empty($meeting['realtor_email'])) {
                $details .= " <{$meeting['realtor_email']}>";
            }
        }

        // Correos electrónicos a los que les llegará la invitación y alerta al móvil
        $attendees = [];
        if (!empty($meeting['employee_email'])) {
            $attendees[] = trim($meeting['employee_email']);
        }
        if (!empty($meeting['realtor_email'])) {
            $attendees[] = trim($meeting['realtor_email']);
        }

        $params = [
            'action' => 'TEMPLATE',
            'text' => $title,
            'dates' => "{$startDateStr}/{$endDateStr}",
            'details' => $details,
            'location' => !empty($meeting['company_name']) ? "Reunión con {$meeting['company_name']} - Miami, FL" : "Miami Luxury Media Ops",
            'sprop' => 'website:miami-agency-ops'
        ];

        // Parámetro 'add' de Google Calendar para invitar directamente al trabajador y al cliente
        if (!empty($attendees)) {
            $params['add'] = implode(',', array_unique($attendees));
        }

        return 'https://calendar.google.com/calendar/render?' . http_build_query($params);
    }

    /**
     * Generar archivo estándar .ics para descargar e importar a cualquier calendario móvil
     */
    public static function generateIcs(array $meeting): string {
        $startTimestamp = strtotime("{$meeting['meeting_date']} {$meeting['meeting_time']}");
        $duration = (int)($meeting['duration_minutes'] ?? 30);
        $endTimestamp = $startTimestamp + ($duration * 60);

        $dtStart = gmdate('Ymd\THis\Z', $startTimestamp);
        $dtEnd = gmdate('Ymd\THis\Z', $endTimestamp);
        $dtStamp = gmdate('Ymd\THis\Z');
        $uid = 'meeting-' . $meeting['id'] . '-' . time() . '@miamiagency.com';

        $title = addcslashes($meeting['title'], ",;\\");
        $desc = addcslashes($meeting['description'] ?? '', ",;\\");
        $location = addcslashes(!empty($meeting['company_name']) ? "Reunión con {$meeting['company_name']}" : "Miami Luxury Media", ",;\\");

        $attendeesIcs = "";
        if (!empty($meeting['employee_email'])) {
            $empName = !empty($meeting['employee_name']) ? addcslashes($meeting['employee_name'], ",;\\") : 'Trabajador Asignado';
            $attendeesIcs .= "ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;RSVP=TRUE;CN={$empName}:mailto:{$meeting['employee_email']}\r\n";
        }
        if (!empty($meeting['realtor_email'])) {
            $realtorName = !empty($meeting['contact_person']) ? addcslashes($meeting['contact_person'], ",;\\") : 'Cliente';
            $attendeesIcs .= "ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;RSVP=TRUE;CN={$realtorName}:mailto:{$meeting['realtor_email']}\r\n";
        }

        return "BEGIN:VCALENDAR\r\n" .
               "VERSION:2.0\r\n" .
               "PRODID:-//Nexus Operations Suite//Miami Agency//ES\r\n" .
               "CALSCALE:GREGORIAN\r\n" .
               "METHOD:PUBLISH\r\n" .
               "BEGIN:VEVENT\r\n" .
               "UID:{$uid}\r\n" .
               "DTSTAMP:{$dtStamp}\r\n" .
               "DTSTART:{$dtStart}\r\n" .
               "DTEND:{$dtEnd}\r\n" .
               "SUMMARY:{$title}\r\n" .
               "DESCRIPTION:{$desc}\r\n" .
               "LOCATION:{$location}\r\n" .
               $attendeesIcs .
               "STATUS:CONFIRMED\r\n" .
               "BEGIN:VALARM\r\n" .
               "TRIGGER:-PT15M\r\n" .
               "ACTION:DISPLAY\r\n" .
               "DESCRIPTION:Recordatorio de Reunión Miami Agency\r\n" .
               "END:VALARM\r\n" .
               "END:VEVENT\r\n" .
               "END:VCALENDAR\r\n";
    }
}
