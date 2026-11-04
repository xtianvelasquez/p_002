<?php
namespace App\Core;

use PDO;

class BaseModel {
    /**
     * @var PDO
     */
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }
}
