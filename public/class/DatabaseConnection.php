<?php
require_once __DIR__ . '/Exceptions.php';

class DatabaseConnection {
    private $connection;

    public function __construct($host, $user, $pass, $db) {
        try {
            $this->connection = new mysqli($host, $user, $pass, $db);
            
            if ($this->connection->connect_error) {
                throw new DatabaseException("MySQL connection failed: " . $this->connection->connect_error);
            }

            if (!$this->connection->set_charset("utf8mb4")) {
                throw new DatabaseException("Error setting charset: " . $this->connection->error);
            }
        } catch (DatabaseException $e) {
            error_log("MySQL connection error: " . $e->getMessage());
            throw $e;
        }
    }

    public function getConnection() {
        return $this->connection;
    }

    public function close() {
        if ($this->connection) {
            $this->connection->close();
            $this->connection = null;
        }
    }
}