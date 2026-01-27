<?php

class AdminController extends Controller {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            
            session_write_close();
            
            $this->redirect('adminauth/login');
            
            exit(); 
        }
    }
}