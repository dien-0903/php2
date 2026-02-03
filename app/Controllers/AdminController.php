<?php

class AdminController extends Controller {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // 1. Middleware: Kiểm tra quyền Admin
        // Tất cả Controller kế thừa từ đây (AdminProduct, AdminUser...) đều sẽ tự động được bảo vệ
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            session_write_close();
            $this->redirect('adminauth/login');
            exit(); 
        }
    }

    // 2. Action mặc định: Dashboard (Trang chủ Admin)
    public function index() {
        // Bạn cần tạo file view: views/admin/dashboard/index.php
        $this->view('admin.dashboard.index', [
            'title' => 'Tổng quan hệ thống'
        ]);
    }

    // 3. Helper: Hàm Upload file dùng chung (Protected để các class con dùng được)
    // Thay vì viết hàm uploadFile ở từng controller con, bạn có thể gọi: $this->uploadFile($file, 'products');
    protected function uploadFile($file, $folder = 'others') {
        // Định nghĩa đường dẫn upload
        // Lưu ý: BASE_PATH cần được định nghĩa ở index.php hoặc config
        $targetDir = BASE_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR;
        
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            // Tạo tên file ngẫu nhiên: time_uniqid.ext để tránh trùng
            $fileName = time() . "_" . uniqid() . "." . $ext;
            
            if (move_uploaded_file($file['tmp_name'], $targetDir . $fileName)) {
                return $fileName;
            }
        }
        return null;
    }

    // 4. Helper: Xử lý lỗi và lưu lại input cũ (Old Input)
    protected function setFlashError($message, $oldData = []) {
        $_SESSION['error'] = $message;
        if (!empty($oldData)) {
            $_SESSION['old'] = $oldData;
        }
    }
}