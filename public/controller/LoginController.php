<?php
require_once 'class/DatabaseConnection.php';
require_once 'model/User.php';

class LoginController {
    public function login() {
        session_start();
        if(isset($_SESSION['account_loggedin'])) {
            header('Location: home.php');
            exit;
        }
        $config = require 'config/database.php';
        $dbConnection = new DatabaseConnection(
            $config['host'],
            $config['user'],
            $config['pass'],
            $config['db']
        );
        $conn = $dbConnection->getConnection();

        $data = [
            'title' => 'Home',
            'content' => 'Welcome to PayBro',
            'users' => [],
            'message' => '',
            'error' => ''
        ];

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
                $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);
                $password = trim(strip_tags($password));

                if ($email && $password) {
                    $user = User::getUser($conn, $email, $password);
                    if ($user) {
                        $data['message'] = "User found!";
                    } else {
                        $data['error'] = "Invalid email or password";
                    }
                } else {
                    $data['error'] = "Invalid input data";
                }
            }
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
            $data['error'] = "Login failed. Please try again.";
        }
        require "view/layoutOut.php";
        $dbConnection->close();
    }
}