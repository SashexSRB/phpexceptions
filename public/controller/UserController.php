<?php
require_once 'class/DatabaseConnection.php';
require_once 'model/User.php';

class UserController {
    public function users() {
        $config = require 'config/database.php';
        $dbConnection = new DatabaseConnection(
            $config['host'],
            $config['user'],
            $config['pass'],
            $config['db']
        );
        $conn = $dbConnection->getConnection();

        $data = [
            'title' => 'PayBro - Users',
            'users' => [],
            'message' => '',
            'error' => ''
        ];

        try {
            $data['users'] = User::getAllUsers($conn);
        } catch (Exception $e) {
            error_log("MySQL Error: " . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
            $data['error'] = "An error occurred. Please try again later.";
        }

        require 'view/layout.php';

        $dbConnection->close();
    }
}