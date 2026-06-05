<?php
namespace App\Core;

class BaseModel {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }
}
