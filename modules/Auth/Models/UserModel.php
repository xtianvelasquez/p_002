<?php
namespace App\Modules\Auth\Models;

use App\Core\BaseModel;
use PDO;

class UserModel extends BaseModel {
    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM user_account WHERE user_id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getUserByEmailOrPhone($email, $phone) {
        $stmt = $this->db->prepare("SELECT email_address, contact_number FROM user_account WHERE contact_number = ? OR email_address = ? LIMIT 1");
        $stmt->execute([$phone, $email]);
        return $stmt->fetch();
    }

    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM user_account WHERE email_address = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function getUserByEmailAndPassword($email, $password) {
        $user = $this->getUserByEmail($email);
        if ($user) {
            if (password_verify($password, $user['user_password'])) {
                return $user;
            }
            if ($user['user_password'] === $password) {
                // Legacy plain text match - rehash
                $this->updatePassword($user['user_id'], password_hash($password, PASSWORD_DEFAULT));
                return $user;
            }
        }
        return false;
    }

    public function updatePassword($id, $hashedPassword) {
        $stmt = $this->db->prepare("UPDATE user_account SET user_password = ? WHERE user_id = ?");
        return $stmt->execute([$hashedPassword, $id]);
    }

    public function createUser($data) {
        $stmt = $this->db->prepare("
            INSERT INTO user_account (
                user_id, first_name, middle_name, last_name, search_name, 
                contact_number, email_address, user_password, profile_picture,
                work_start_time, work_end_time, slot_duration_minutes
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['user_id'],
            $data['first_name'],
            $data['middle_name'],
            $data['last_name'],
            $data['search_name'],
            $data['contact_number'],
            $data['email_address'],
            $data['user_password'],
            $data['profile_picture'],
            $data['work_start_time'] ?? '09:00',
            $data['work_end_time'] ?? '17:00',
            $data['slot_duration_minutes'] ?? 30
        ]);
    }

    public function updateUser($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE user_account 
            SET first_name = ?, middle_name = ?, last_name = ?, search_name = ?, 
                contact_number = ?, email_address = ?, profile_picture = ?, last_update = ?,
                work_start_time = ?, work_end_time = ?, slot_duration_minutes = ?,
                headline = ?, bio = ?, job_title = ?, company = ?, location_city = ?,
                skills = ?, consultation_topics = ?, linkedin_url = ?, github_url = ?, website_url = ?
            WHERE user_id = ?
        ");
        return $stmt->execute([
            $data['first_name'],
            $data['middle_name'],
            $data['last_name'],
            $data['search_name'],
            $data['contact_number'],
            $data['email_address'],
            $data['profile_picture'],
            $data['last_update'],
            $data['work_start_time'] ?? '09:00',
            $data['work_end_time'] ?? '17:00',
            $data['slot_duration_minutes'] ?? 30,
            $data['headline'] ?? null,
            $data['bio'] ?? null,
            $data['job_title'] ?? null,
            $data['company'] ?? null,
            $data['location_city'] ?? null,
            $data['skills'] ?? null,
            $data['consultation_topics'] ?? null,
            $data['linkedin_url'] ?? null,
            $data['github_url'] ?? null,
            $data['website_url'] ?? null,
            $id
        ]);
    }

    public function searchUsers($name) {
        $searchTerm = '%' . trim($name) . '%';
        $stmt = $this->db->prepare("
            SELECT user_id, first_name, middle_name, last_name, profile_picture,
                   headline, job_title, company, location_city, skills, consultation_topics
            FROM user_account 
            WHERE first_name ILIKE ? 
               OR last_name ILIKE ? 
               OR search_name ILIKE ?
               OR headline ILIKE ?
               OR company ILIKE ?
               OR job_title ILIKE ?
               OR skills ILIKE ?
            ORDER BY first_name ASC
        ");
        $stmt->execute([
            $searchTerm, $searchTerm, $searchTerm, 
            $searchTerm, $searchTerm, $searchTerm, $searchTerm
        ]);
        return $stmt->fetchAll();
    }
}
