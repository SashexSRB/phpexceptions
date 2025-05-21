<?php
require_once 'class/DatabaseConnection.php';
require_once 'model/User.php';

class RegisterController {
    public function register() {
        $config = require 'config/database.php';
        $dbConnection = new DatabaseConnection(
            $config['host'],
            $config['user'],
            $config['pass'],
            $config['db']
        );
        $conn = $dbConnection->getConnection();

        $data = [
            'title' => 'PayBro - Register',
            'content' => 'Register a new PayBro Account.',
            'users' => [],
            'message' => '',
            'error' => ''
        ];

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = filter_input(INPUT_POST, 'username', FILTER_UNSAFE_RAW);
                $username = trim(strip_tags($username));
                $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
                $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);
                $password = trim(strip_tags($password));
                $hashedPass = hash('sha256', $password);
                $money = filter_input(INPUT_POST, 'money', FILTER_VALIDATE_INT);

                if ($username && $email && $money !== false) {
                    $user = new User($username, $email, $money, $hashedPass);
                    $user->saveToDatabase($conn);
                    $data['message'] = "User created successfully!";
                } else {
                    $data['error'] = "Invalid input data";
                }
            }

        } catch (Exception $e) {
            error_log("MySQL Error: " . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
            $data['error'] = "An error occurred. Please try again later.";
        }

        require 'view/layout.php';

        $dbConnection->close();
    }
}