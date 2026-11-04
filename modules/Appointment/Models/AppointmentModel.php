<?php
namespace App\Modules\Appointment\Models;

use App\Core\BaseModel;
use PDO;

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

    public function getAppointmentById($id) {
        $stmt = $this->db->prepare("SELECT * FROM appointment_details WHERE appointment_id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
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
        $stmt = $this->db->prepare("INSERT INTO appointment_details(appointment_id, receiver_id, sender_id, appointment_date, appointment_time, event_name, location, appointment_status) VALUES(?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['appointment_id'],
            $data['receiver_id'],
            $data['sender_id'],
            $data['appointment_date'],
            $data['appointment_time'],
            $data['event_name'],
            $data['location'],
            $data['appointment_status']
        ]);
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

    public function hasConflict($receiverId, $date, $time) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total 
            FROM appointment_details 
            WHERE receiver_id = ? AND appointment_date = ? AND appointment_time = ? AND appointment_status IN ('approved', 'pending')
        ");
        $stmt->execute([$receiverId, $date, $time]);
        $row = $stmt->fetch();
        return ($row && (int)$row['total'] > 0);
    }

    public function getUserDetails($userId) {
        $stmt = $this->db->prepare("SELECT first_name, last_name, contact_number, email_address, profile_picture FROM user_account WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
}
