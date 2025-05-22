<?php 

class HomeController {
    public function home() {
        session_start();
        if(isset($_SESSION['account_loggedin'])) {
            $url = "https://phpexceptions.ddev.site/login";
            header('Location: '.$url);
            exit;
        }
        require 'view/layout.php';
    }
}