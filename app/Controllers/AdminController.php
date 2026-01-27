<?php

class AdminController extends Controller {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            $_SESSION['error'] = "Vui lòng đăng nhập tài khoản Quản trị viên!";
            session_write_close();
            $this->redirect('adminauth/login');
        }
    }
}