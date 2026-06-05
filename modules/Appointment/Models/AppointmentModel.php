<?php
namespace App\Modules\Appointment\Models;

use App\Core\BaseModel;
use PDO;

class AppointmentModel extends BaseModel {
    public function getReceivedAppointments($userId) {
        $stmt = $this->db->prepare("SELECT * FROM appointment_details WHERE receiver_id = ? ORDER BY appointment_date ASC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getSentAppointments($userId) {
        $stmt = $this->db->prepare("SELECT * FROM appointment_details WHERE sender_id = ? ORDER BY appointment_date ASC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getAppointmentById($id) {
        $stmt = $this->db->prepare("SELECT * FROM appointment_details WHERE appointment_id = ? LIMIT 1");
        $stmt->execute([$id]);
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

    public function updateAppointmentStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE appointment_details SET appointment_status = ? WHERE appointment_id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function deleteAppointment($id) {
        $stmt = $this->db->prepare("DELETE FROM appointment_details WHERE appointment_id = ?");
        return $stmt->execute([$id]);
    }

    public function getUserDetails($userId) {
        $stmt = $this->db->prepare("SELECT first_name, last_name, contact_number, email_address, profile_picture FROM user_account WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
}
