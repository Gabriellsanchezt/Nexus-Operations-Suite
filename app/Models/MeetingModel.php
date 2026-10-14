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
            SELECT m.*, r.company_name, r.contact_person, r.phone AS realtor_phone
            FROM meetings m
            LEFT JOIN realtors r ON m.realtor_id = r.id
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
            INSERT INTO meetings (title, realtor_id, meeting_date, meeting_time, duration_minutes, meeting_link, description, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'PROGRAMADA')
        ");
        $stmt->execute([
            trim($data['title']),
            !empty($data['realtor_id']) ? (int)$data['realtor_id'] : null,
            $data['meeting_date'],
            $data['meeting_time'],
            (int)($data['duration_minutes'] ?? 30),
            trim($data['meeting_link'] ?? '') ?: null,
            trim($data['description'] ?? '') ?: null
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
     * Activa notificaciones y alertas automáticas en el teléfono y correo del usuario
     */
    public static function buildGoogleCalendarUrl(array $meeting): string {
        $date = str_replace('-', '', $meeting['meeting_date']);
        $timeParts = explode(':', $meeting['meeting_time']);
        $startHour = str_pad($timeParts[0] ?? '09', 2, '0', STR_PAD_LEFT);
        $startMin = str_pad($timeParts[1] ?? '00', 2, '0', STR_PAD_LEFT);
        $startSec = '00';

        $duration = (int)($meeting['duration_minutes'] ?? 30);
        $startTimestamp = strtotime("{$meeting['meeting_date']} {$meeting['meeting_time']}");
        $endTimestamp = $startTimestamp + ($duration * 60);
        $endDateStr = date('Ymd\THis', $endTimestamp);
        $startDateStr = date('Ymd\THis', $startTimestamp);

        $title = $meeting['title'];
        if (!empty($meeting['company_name'])) {
            $title .= ' - ' . $meeting['company_name'];
        }

        $details = $meeting['description'] ?? '';
        if (!empty($meeting['meeting_link'])) {
            $details .= "\n\nEnlace de Reunión (Meet/Zoom): " . $meeting['meeting_link'];
        }
        $details .= "\n\nAgendado desde Nexus Operations Suite (Miami Luxury Media).";

        $params = [
            'action' => 'TEMPLATE',
            'text' => $title,
            'dates' => "{$startDateStr}/{$endDateStr}",
            'details' => $details,
            'location' => $meeting['meeting_link'] ?? 'En línea / Miami',
            'sprop' => 'website:miami-agency-ops'
        ];

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
        $location = addcslashes($meeting['meeting_link'] ?? 'En línea', ",;\\");

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
