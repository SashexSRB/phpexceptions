<?php 

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

        require 'view/layout.php';
    }
}