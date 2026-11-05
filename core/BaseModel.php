<?php
namespace App\Core;

use PDO;

class BaseModel {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }
}
