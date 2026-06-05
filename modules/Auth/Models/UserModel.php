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

    public function getUserByEmailAndPassword($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM user_account WHERE email_address = ? AND user_password = ? LIMIT 1");
        $stmt->execute([$email, $password]);
        return $stmt->fetch();
    }

    public function createUser($data) {
        $stmt = $this->db->prepare("INSERT INTO user_account (user_id, first_name, middle_name, last_name, search_name, contact_number, email_address, user_password, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
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
    }

    public function updateUser($id, $data) {
        $stmt = $this->db->prepare("UPDATE user_account SET first_name = ?, middle_name = ?, last_name = ?, search_name = ?, contact_number = ?, email_address = ?, profile_picture = ?, last_update = ? WHERE user_id = ?");
        return $stmt->execute([
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
    }

    public function searchUsers($name) {
        $stmt = $this->db->prepare("SELECT user_id, first_name, last_name, profile_picture FROM user_account WHERE first_name = ? OR last_name = ? OR search_name = ?");
        $stmt->execute([$name, $name, $name]);
        return $stmt->fetchAll();
    }
}
