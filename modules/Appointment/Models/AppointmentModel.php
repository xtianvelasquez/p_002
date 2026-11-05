<?php
namespace App\Modules\Appointment\Models;

use App\Core\BaseModel;

/**
 * @property PDO $db
 */
class AppointmentModel extends BaseModel {
    public function getReceivedAppointments($userId) {
        $stmt = $this->db->prepare("
            SELECT a.*, 
                   u.first_name, u.middle_name, u.last_name, u.search_name, u.contact_number, u.email_address, u.profile_picture
            FROM appointment_details a
            JOIN user_account u ON a.sender_id = u.user_id
            WHERE a.receiver_id = ?
            ORDER BY a.appointment_date ASC, a.appointment_time ASC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getSentAppointments($userId) {
        $stmt = $this->db->prepare("
            SELECT a.*, 
                   u.first_name, u.middle_name, u.last_name, u.search_name, u.contact_number, u.email_address, u.profile_picture
            FROM appointment_details a
            JOIN user_account u ON a.receiver_id = u.user_id
            WHERE a.sender_id = ?
            ORDER BY a.appointment_date ASC, a.appointment_time ASC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getAppointmentForUser($id, $userId) {
        $stmt = $this->db->prepare("
            SELECT a.*, 
                   s.first_name AS sender_first_name, s.last_name AS sender_last_name, s.contact_number AS sender_contact, s.email_address AS sender_email,
                   r.first_name AS receiver_first_name, r.last_name AS receiver_last_name, r.contact_number AS receiver_contact, r.email_address AS receiver_email
            FROM appointment_details a
            LEFT JOIN user_account s ON a.sender_id = s.user_id
            LEFT JOIN user_account r ON a.receiver_id = r.user_id
            WHERE a.appointment_id = ? AND (a.sender_id = ? OR a.receiver_id = ?)
            LIMIT 1
        ");
        $stmt->execute([$id, $userId, $userId]);
        return $stmt->fetch();
    }

    public function createAppointment($data) {
        $this->db->beginTransaction();
        try {
            $lock = $this->db->prepare("SELECT user_id FROM user_account WHERE user_id = ? FOR UPDATE");
            $lock->execute([$data['receiver_id']]);
            if (!$lock->fetch() || $this->hasConflict(
                $data['receiver_id'],
                $data['appointment_date'],
                $data['appointment_time'],
                $data['event_type'] ?? 'consultation'
            )) {
                $this->db->rollBack();
                return false;
            }

            $stmt = $this->db->prepare("
            INSERT INTO appointment_details(
                appointment_id, receiver_id, sender_id, appointment_date, appointment_time, 
                event_name, location, event_type, meeting_mode, agenda, appointment_status
            ) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
            $inserted = $stmt->execute([
            $data['appointment_id'],
            $data['receiver_id'],
            $data['sender_id'],
            $data['appointment_date'],
            $data['appointment_time'],
            $data['event_name'],
            $data['location'],
            $data['event_type'] ?? 'consultation',
            $data['meeting_mode'] ?? 'google_meet',
            $data['agenda'] ?? null,
            $data['appointment_status']
            ]);
            $this->db->commit();
            return $inserted;
        } catch (\Throwable $exception) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $exception;
        }
    }

    public function updateAppointmentStatusForUser($id, $status, $userId) {
        $stmt = $this->db->prepare("
            UPDATE appointment_details 
            SET appointment_status = ? 
            WHERE appointment_id = ? AND (receiver_id = ? OR (sender_id = ? AND ? = 'cancel'))
        ");
        $stmt->execute([$status, $id, $userId, $userId, $status]);
        return $stmt->rowCount() > 0;
    }

    public function deleteReceivedAppointment($id, $userId) {
        $stmt = $this->db->prepare("DELETE FROM appointment_details WHERE appointment_id = ? AND receiver_id = ?");
        $stmt->execute([$id, $userId]);
        return $stmt->rowCount() > 0;
    }

    public function deleteSentAppointment($id, $userId) {
        $stmt = $this->db->prepare("DELETE FROM appointment_details WHERE appointment_id = ? AND sender_id = ?");
        $stmt->execute([$id, $userId]);
        return $stmt->rowCount() > 0;
    }

    public function hasConflict($receiverId, $date, $time, $eventType = 'consultation') {
        $duration = self::durationForEventType($eventType);
        $stmt = $this->db->prepare("
            SELECT 1
            FROM appointment_details
            WHERE receiver_id = ?
              AND appointment_date = ?
              AND appointment_status IN ('approved', 'pending')
              AND appointment_time::time < (CAST(? AS time) + (? * INTERVAL '1 minute'))
              AND (appointment_time::time +
                   (CASE event_type WHEN 'quick_sync' THEN 15 WHEN 'deep_dive' THEN 60 ELSE 30 END * INTERVAL '1 minute')) > CAST(? AS time)
            LIMIT 1
        ");
        $stmt->execute([$receiverId, $date, $time, $duration, $time]);
        return (bool)$stmt->fetchColumn();
    }

    public function getBookedSlots($receiverId, $date) {
        $stmt = $this->db->prepare("
            SELECT appointment_time, event_type
            FROM appointment_details 
            WHERE receiver_id = ? AND appointment_date = ? AND appointment_status IN ('approved', 'pending')
        ");
        $stmt->execute([$receiverId, $date]);
        return $stmt->fetchAll() ?: [];
    }

    public function generateAvailableSlots($receiverId, $date, $customDuration = null) {
        $parsedDate = \DateTime::createFromFormat('!Y-m-d', (string)$date);
        if (!$parsedDate || $parsedDate->format('Y-m-d') !== $date) return [];

        $user = $this->getUserDetails($receiverId);
        if (!$user) {
            return [];
        }

        $startTimeStr = !empty($user['work_start_time']) ? $user['work_start_time'] : '09:00';
        $endTimeStr = !empty($user['work_end_time']) ? $user['work_end_time'] : '17:00';
        $intervalMinutes = in_array((int)$customDuration, [15, 30, 60], true)
            ? (int)$customDuration
            : (in_array((int)($user['slot_duration_minutes'] ?? 30), [15, 30, 45, 60], true)
                ? (int)$user['slot_duration_minutes']
                : 30);

        $bookedSlots = $this->getBookedSlots($receiverId, $date);

        $startTimestamp = strtotime($date . ' ' . $startTimeStr);
        $endTimestamp = strtotime($date . ' ' . $endTimeStr);
        $currentTimestamp = time();

        if ($startTimestamp === false || $endTimestamp === false || $endTimestamp <= $startTimestamp) return [];

        $slots = [];
        $step = $intervalMinutes * 60;

        for ($t = $startTimestamp; $t + ($intervalMinutes * 60) <= $endTimestamp; $t += $step) {
            $timeFormatted = date('H:i', $t);
            $timeDisplay = date('g:i A', $t);
            $slotEnd = $t + ($intervalMinutes * 60);
            $isBooked = false;
            foreach ($bookedSlots as $booked) {
                $bookedStart = strtotime($date . ' ' . $booked['appointment_time']);
                if ($bookedStart === false) continue;
                $bookedEnd = $bookedStart + (self::durationForEventType($booked['event_type'] ?? 'consultation') * 60);
                if ($t < $bookedEnd && $slotEnd > $bookedStart) {
                    $isBooked = true;
                    break;
                }
            }
            $isPast = ($t < $currentTimestamp);

            $slots[] = [
                'time' => $timeFormatted,
                'display' => $timeDisplay,
                'available' => (!$isBooked && !$isPast),
                'reason' => $isBooked ? 'Booked' : ($isPast ? 'Past' : 'Available')
            ];
        }

        return $slots;
    }

    private static function durationForEventType($eventType) {
        return match ($eventType) {
            'quick_sync' => 15,
            'deep_dive' => 60,
            default => 30,
        };
    }

    public function getUserDetails($userId) {
        $stmt = $this->db->prepare("
            SELECT u.user_id, u.first_name, u.middle_name, u.last_name, u.search_name, u.contact_number, u.email_address, u.profile_picture,
                   COALESCE(a.work_start_time, '09:00') AS work_start_time,
                   COALESCE(a.work_end_time, '17:00') AS work_end_time,
                   COALESCE(a.slot_duration_minutes, 30) AS slot_duration_minutes,
                   p.headline, p.bio, p.job_title, p.company, p.location_city, p.skills, p.consultation_topics,
                   p.linkedin_url, p.github_url, p.website_url
            FROM user_account u
            LEFT JOIN user_profile p ON u.user_id = p.user_id
            LEFT JOIN user_availability a ON u.user_id = a.user_id
            WHERE u.user_id = ? 
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
}
