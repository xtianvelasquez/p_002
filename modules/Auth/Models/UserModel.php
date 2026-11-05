<?php
namespace App\Modules\Auth\Models;

use App\Core\BaseModel;

class UserModel extends BaseModel {
    public function getUserById($id) {
        $stmt = $this->db->prepare("
            SELECT u.user_id, u.first_name, u.middle_name, u.last_name, u.search_name,
                   u.contact_number, u.email_address, u.profile_picture, u.google_id, u.last_update,
                   p.headline, p.bio, p.job_title, p.company, p.location_city, p.skills, p.consultation_topics,
                   p.linkedin_url, p.github_url, p.website_url,
                   COALESCE(a.work_start_time, '09:00') AS work_start_time,
                   COALESCE(a.work_end_time, '17:00') AS work_end_time,
                   COALESCE(a.slot_duration_minutes, 30) AS slot_duration_minutes
            FROM user_account u
            LEFT JOIN user_profile p ON u.user_id = p.user_id
            LEFT JOIN user_availability a ON u.user_id = a.user_id
            WHERE u.user_id = ? 
            LIMIT 1
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getUserByEmailOrPhone($email, $phone) {
        $stmt = $this->db->prepare("SELECT email_address, contact_number FROM user_account WHERE contact_number = ? OR LOWER(email_address) = LOWER(?) LIMIT 1");
        $stmt->execute([$phone, $email]);
        return $stmt->fetch();
    }

    public function getOtherUserByEmailOrPhone($email, $phone, $userId) {
        $stmt = $this->db->prepare("SELECT user_id FROM user_account WHERE (contact_number = ? OR LOWER(email_address) = LOWER(?)) AND user_id <> ? LIMIT 1");
        $stmt->execute([$phone, $email, $userId]);
        return $stmt->fetch();
    }

    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("
            SELECT u.user_id, u.first_name, u.middle_name, u.last_name, u.search_name,
                   u.contact_number, u.email_address, u.user_password, u.profile_picture, u.google_id, u.last_update,
                   p.headline, p.bio, p.job_title, p.company, p.location_city, p.skills, p.consultation_topics,
                   p.linkedin_url, p.github_url, p.website_url,
                   COALESCE(a.work_start_time, '09:00') AS work_start_time,
                   COALESCE(a.work_end_time, '17:00') AS work_end_time,
                   COALESCE(a.slot_duration_minutes, 30) AS slot_duration_minutes
            FROM user_account u
            LEFT JOIN user_profile p ON u.user_id = p.user_id
            LEFT JOIN user_availability a ON u.user_id = a.user_id
            WHERE LOWER(u.email_address) = LOWER(?)
            LIMIT 1
        ");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function getUserByGoogleId($googleId) {
        $stmt = $this->db->prepare("
            SELECT u.user_id, u.first_name, u.middle_name, u.last_name, u.search_name,
                   u.contact_number, u.email_address, u.profile_picture, u.google_id, u.last_update,
                   p.headline, p.bio, p.job_title, p.company, p.location_city, p.skills, p.consultation_topics,
                   p.linkedin_url, p.github_url, p.website_url,
                   COALESCE(a.work_start_time, '09:00') AS work_start_time,
                   COALESCE(a.work_end_time, '17:00') AS work_end_time,
                   COALESCE(a.slot_duration_minutes, 30) AS slot_duration_minutes
            FROM user_account u
            LEFT JOIN user_profile p ON u.user_id = p.user_id
            LEFT JOIN user_availability a ON u.user_id = a.user_id
            WHERE u.google_id = ? 
            LIMIT 1
        ");
        $stmt->execute([$googleId]);
        return $stmt->fetch();
    }

    public function linkGoogleId($userId, $googleId) {
        $stmt = $this->db->prepare("UPDATE user_account SET google_id = ? WHERE user_id = ?");
        return $stmt->execute([$googleId, $userId]);
    }

    public function createGoogleUser($data) {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("
                INSERT INTO user_account (
                    user_id, first_name, middle_name, last_name, search_name, 
                    contact_number, email_address, user_password, profile_picture, google_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['user_id'],
                $data['first_name'],
                $data['middle_name'] ?? null,
                $data['last_name'],
                $data['search_name'],
                $data['contact_number'] ?? '0000000000',
                $data['email_address'],
                $data['user_password'],
                $data['profile_picture'] ?? 'images/logo.png',
                $data['google_id']
            ]);

            $stmtAvail = $this->db->prepare("
                INSERT INTO user_availability (user_id, work_start_time, work_end_time, slot_duration_minutes)
                VALUES (?, ?, ?, ?)
            ");
            $stmtAvail->execute([
                $data['user_id'],
                $data['work_start_time'] ?? '09:00',
                $data['work_end_time'] ?? '17:00',
                $data['slot_duration_minutes'] ?? 30
            ]);

            $stmtProf = $this->db->prepare("
                INSERT INTO user_profile (user_id)
                VALUES (?)
            ");
            $stmtProf->execute([$data['user_id']]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    public function getUserByEmailAndPassword($email, $password) {
        $user = $this->getUserByEmail($email);
        if ($user) {
            if (password_verify($password, $user['user_password'])) {
                return $user;
            }
        }
        return false;
    }

    public function createUser($data) {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("
                INSERT INTO user_account (
                    user_id, first_name, middle_name, last_name, search_name, 
                    contact_number, email_address, user_password, profile_picture
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['user_id'],
                $data['first_name'],
                $data['middle_name'],
                $data['last_name'],
                $data['search_name'],
                $data['contact_number'],
                $data['email_address'],
                $data['user_password'],
                $data['profile_picture']
            ]);

            $stmtAvail = $this->db->prepare("
                INSERT INTO user_availability (user_id, work_start_time, work_end_time, slot_duration_minutes)
                VALUES (?, ?, ?, ?)
            ");
            $stmtAvail->execute([
                $data['user_id'],
                $data['work_start_time'] ?? '09:00',
                $data['work_end_time'] ?? '17:00',
                $data['slot_duration_minutes'] ?? 30
            ]);

            $stmtProf = $this->db->prepare("
                INSERT INTO user_profile (user_id)
                VALUES (?)
            ");
            $stmtProf->execute([$data['user_id']]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    public function updateUser($id, $data) {
        $this->db->beginTransaction();
        try {
            $stmtAccount = $this->db->prepare("
                UPDATE user_account 
                SET first_name = ?, middle_name = ?, last_name = ?, search_name = ?, 
                    contact_number = ?, email_address = ?, profile_picture = ?, last_update = ?
                WHERE user_id = ?
            ");
            $stmtAccount->execute([
                $data['first_name'],
                $data['middle_name'],
                $data['last_name'],
                $data['search_name'],
                $data['contact_number'],
                $data['email_address'],
                $data['profile_picture'],
                $data['last_update'],
                $id
            ]);

            $stmtAvail = $this->db->prepare("
                INSERT INTO user_availability (user_id, work_start_time, work_end_time, slot_duration_minutes)
                VALUES (?, ?, ?, ?)
                ON CONFLICT (user_id) DO UPDATE SET
                    work_start_time = EXCLUDED.work_start_time,
                    work_end_time = EXCLUDED.work_end_time,
                    slot_duration_minutes = EXCLUDED.slot_duration_minutes
            ");
            $stmtAvail->execute([
                $id,
                $data['work_start_time'] ?? '09:00',
                $data['work_end_time'] ?? '17:00',
                $data['slot_duration_minutes'] ?? 30
            ]);

            $stmtProfile = $this->db->prepare("
                INSERT INTO user_profile (
                    user_id, headline, bio, job_title, company, location_city,
                    skills, consultation_topics, linkedin_url, github_url, website_url
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON CONFLICT (user_id) DO UPDATE SET
                    headline = EXCLUDED.headline,
                    bio = EXCLUDED.bio,
                    job_title = EXCLUDED.job_title,
                    company = EXCLUDED.company,
                    location_city = EXCLUDED.location_city,
                    skills = EXCLUDED.skills,
                    consultation_topics = EXCLUDED.consultation_topics,
                    linkedin_url = EXCLUDED.linkedin_url,
                    github_url = EXCLUDED.github_url,
                    website_url = EXCLUDED.website_url
            ");
            $stmtProfile->execute([
                $id,
                $data['headline'] ?? null,
                $data['bio'] ?? null,
                $data['job_title'] ?? null,
                $data['company'] ?? null,
                $data['location_city'] ?? null,
                $data['skills'] ?? null,
                $data['consultation_topics'] ?? null,
                $data['linkedin_url'] ?? null,
                $data['github_url'] ?? null,
                $data['website_url'] ?? null
            ]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    public function searchUsers($name) {
        $searchTerm = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], trim($name)) . '%';
        $stmt = $this->db->prepare("
            SELECT u.user_id, u.first_name, u.middle_name, u.last_name, u.profile_picture,
                   p.headline, p.job_title, p.company, p.location_city, p.skills, p.consultation_topics
            FROM user_account u
            LEFT JOIN user_profile p ON u.user_id = p.user_id
            WHERE u.first_name ILIKE ? 
               OR u.last_name ILIKE ? 
               OR u.search_name ILIKE ?
               OR p.headline ILIKE ?
               OR p.company ILIKE ?
               OR p.job_title ILIKE ?
               OR p.skills ILIKE ?
            ORDER BY u.first_name ASC
            LIMIT 60
        ");
        $stmt->execute([
            $searchTerm, $searchTerm, $searchTerm, 
            $searchTerm, $searchTerm, $searchTerm, $searchTerm
        ]);
        return $stmt->fetchAll();
    }
}
