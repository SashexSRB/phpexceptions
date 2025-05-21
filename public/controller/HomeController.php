<?php
class HomeController {
    public function home() {
        $data = [
            'title' => 'PayBro - Home',
            'content' => 'Welcome to the PayBro Payment System!'
        ];
        require 'view/layout.php';
    }

    public function about() {
        $data = [
            'title' => 'PayBro - About',
            'content' => 'Learn more about PayBro, your trusted payment platform.'
        ];
        require 'view/layout.php';
    }

    public function contact() {
        $data = [
            'title' => 'PayBro - Contact',
            'content' => 'Contact us for support or inquiries.'
        ];
        require 'view/layout.php';
    }
}