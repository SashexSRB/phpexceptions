<?php 
require_once 'class/DatabaseConnection.php';
require_once 'model/User.php';
class HomeController {
    public function home() {
        session_start();
        if (!isset($_SESSION['accountLoggedIn'])) {
            header('Location: /login');
            exit;
        }

        $data = [
            'title' => 'PayBro - Home',
            'content' => 'Welcome to PayBro',
            'users' => [],
            'message' => $_SESSION['message'] ?? '',
            'error' => ''
        ];


        $config = require 'config/database.php';
        $dbConnection = new DatabaseConnection(
            $config['host'],
            $config['user'],
            $config['pass'],
            $config['db']
        );
        $conn = $dbConnection->getConnection();

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
                if (isset($amount)) {
                    $uID = $_SESSION['account_id'];
                    User::deposit($conn, $amount, $uID);
                } else {
                    $data['error'] = "Something went wrong.";
                }
            }
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
            $data['error'] = "Deposit Failed. Please try again.";
        }

        require 'view/layout.php';
    }
}