<?php 
require_once 'class/DatabaseConnection.php';
require_once 'model/User.php';
require_once 'model/TransactionHistory.php';
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
            'transactions' => [],
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
                $action = $_POST['action'] ?? '';
                switch ($action) {
                    case 'deposit':
                        $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
                        if (isset($amount)) {
                            $uID = $_SESSION['account_id'];
                            User::deposit($conn, $amount, $uID);
                        } else {
                            $data['error'] = "Something went wrong.";
                        }
                    break;
                    case 'transfer':
                        $username = filter_input(INPUT_POST, 'username');
                        $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
                        if (isset($amount) && isset($username)) {
                            $sourceID = $_SESSION['account_id'];
                            $sourceUname = $_SESSION['account_name'];
                            User::transfer($conn, $amount, $sourceID, $username);
                            $tsHist = new TransactionHistory($sourceUname, $username, $amount, null, $sourceID);
                            $tsHist->addTransaction($conn);
                            return $tsHist;
                        } else {
                            $data['error'] = "Something went wrong.";
                        }
                    break;
                }
            
            }
            $uid = $_SESSION['account_id'];
            $data['transactions'] = TransactionHistory::getTransactions($conn, $uid);
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
            $data['error'] = "Deposit Failed. Please try again.";
        }

        require 'view/layout.php';
    }
}