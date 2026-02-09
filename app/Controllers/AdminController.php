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

    public function index() {
        $this->view('admin.dashboard.index', [
            'title' => 'Tổng quan hệ thống'
        ]);
    }

    protected function uploadFile($file, $folder = 'others') {
        $targetDir = BASE_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR;
        
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $fileName = time() . "_" . uniqid() . "." . $ext;
            
            if (move_uploaded_file($file['tmp_name'], $targetDir . $fileName)) {
                return $fileName;
            }
        }
        return null;
    }

    protected function setFlashError($message, $oldData = []) {
        $_SESSION['error'] = $message;
        if (!empty($oldData)) {
            $_SESSION['old'] = $oldData;
        }
    }
}