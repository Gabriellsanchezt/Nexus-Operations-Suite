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
     * Construir enlace oficial para agregar recordatorio a Google Calendar con 1 clic
     * Se sincroniza directamente como alarma y recordatorio personal en el teléfono móvil y correo
     * del usuario administrador/gestor, cargando todos los datos de contacto del trabajador asignado
     * y del cliente para que pueda llamarles o escribirles oportunamente para recordarles la cita.
     */
    public static function buildGoogleCalendarUrl(array $meeting): string {
        $startTimestamp = strtotime("{$meeting['meeting_date']} {$meeting['meeting_time']}");
        $duration = (int)($meeting['duration_minutes'] ?? 30);
        $endTimestamp = $startTimestamp + ($duration * 60);
        $startDateStr = date('Ymd\THis', $startTimestamp);
        $endDateStr = date('Ymd\THis', $endTimestamp);

        $workerName = !empty($meeting['employee_name']) ? $meeting['employee_name'] : 'Equipo';
        $clientName = !empty($meeting['company_name']) ? $meeting['company_name'] : (!empty($meeting['contact_person']) ? $meeting['contact_person'] : 'Cliente');

        // Título explícito de recordatorio para que suene y resalte en el teléfono móvil del gestor
        $title = "Recordatorio: {$workerName} con {$clientName} - {$meeting['title']}";

        // Detalles con información completa de contacto para avisarles de inmediato
        $details = "RECORDATORIO DE REUNIÓN - MIAMI LUXURY MEDIA\n";
        $details .= "============================================\n";
        $details .= "Asunto: " . ($meeting['title'] ?? 'Reunión Operativa') . "\n";
        $details .= "Fecha y Hora: " . ($meeting['meeting_date'] ?? '') . " a las " . date('h:i A', $startTimestamp) . "\n\n";

        $details .= "TRABAJADOR ASIGNADO A RECORDAR:\n";
        $details .= "• Nombre: " . ($meeting['employee_name'] ?? 'Colaborador') . "\n";
        if (!empty($meeting['employee_email'])) {
            $details .= "• Correo: " . $meeting['employee_email'] . "\n";
        }
        if (!empty($meeting['employee_phone'])) {
            $details .= "• Teléfono Móvil: " . $meeting['employee_phone'] . "\n";
        }

        $details .= "\nDATOS DEL CLIENTE (REALTOR):\n";
        $details .= "• Inmobiliaria: " . ($meeting['company_name'] ?? 'Reunión Interna') . "\n";
        if (!empty($meeting['contact_person'])) {
            $details .= "• Contacto: " . $meeting['contact_person'] . "\n";
        }
        if (!empty($meeting['realtor_phone'])) {
            $details .= "• Teléfono Móvil: " . $meeting['realtor_phone'] . "\n";
        }
        if (!empty($meeting['realtor_email'])) {
            $details .= "• Correo: " . $meeting['realtor_email'] . "\n";
        }

        if (!empty($meeting['description'])) {
            $details .= "\nTEMAS / DESCRIPCIÓN DE LA REUNIÓN:\n" . $meeting['description'] . "\n";
        }

        $details .= "\n(Alerta programada para llamar y avisar al trabajador y cliente desde Nexus Operations Suite).";

        $params = [
            'action' => 'TEMPLATE',
            'text' => $title,
            'dates' => "{$startDateStr}/{$endDateStr}",
            'details' => $details,
            'location' => !empty($meeting['company_name']) ? "Miami, FL - {$meeting['company_name']}" : "Miami Luxury Media Ops",
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
        $uid = 'reminder-meeting-' . $meeting['id'] . '-' . time() . '@miamiagency.com';

        $workerName = !empty($meeting['employee_name']) ? $meeting['employee_name'] : 'Colaborador';
        $clientName = !empty($meeting['company_name']) ? $meeting['company_name'] : 'Cliente';

        $title = addcslashes("Recordatorio: {$workerName} con {$clientName} - {$meeting['title']}", ",;\\");

        $descRaw = "Recordatorio para avisar a {$workerName} sobre reunión con {$clientName}.\n";
        if (!empty($meeting['employee_phone'])) {
            $descRaw .= "Teléfono Trabajador: {$meeting['employee_phone']}\n";
        }
        if (!empty($meeting['employee_email'])) {
            $descRaw .= "Correo Trabajador: {$meeting['employee_email']}\n";
        }
        if (!empty($meeting['realtor_phone'])) {
            $descRaw .= "Teléfono Cliente: {$meeting['realtor_phone']}\n";
        }
        if (!empty($meeting['description'])) {
            $descRaw .= "Temas: {$meeting['description']}\n";
        }
        $desc = addcslashes($descRaw, ",;\\");
        $location = addcslashes(!empty($meeting['company_name']) ? "Reunión con {$meeting['company_name']}" : "Miami Luxury Media", ",;\\");

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
               "TRIGGER:-PT30M\r\n" .
               "ACTION:DISPLAY\r\n" .
               "DESCRIPTION:Recordatorio: Avisar a {$workerName} sobre reunión con {$clientName}\r\n" .
               "END:VALARM\r\n" .
               "BEGIN:VALARM\r\n" .
               "TRIGGER:-PT15M\r\n" .
               "ACTION:DISPLAY\r\n" .
               "DESCRIPTION:Alerta 15 min: Recordar reunión a {$workerName}\r\n" .
               "END:VALARM\r\n" .
               "END:VEVENT\r\n" .
               "END:VCALENDAR\r\n";
    }
}
