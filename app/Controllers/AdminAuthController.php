<?php

class AdminAuthController extends Controller {

    public function login() {
        $this->view('admin.auth.login', ['title' => 'Đăng nhập Quản trị']);
    }

    public function postLogin() {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng nhập Email và Mật khẩu Admin!']);
                return;
            }

            try {
                $userModel = $this->model('User');
                $user = $userModel->findByEmail($email);

                if ($user && ($user['role'] ?? '') === 'admin') {
                    if (password_verify($password, $user['password']) || $password === $user['password']) {
                        
                        $_SESSION['user'] = [
                            'id'       => $user['id'],
                            'fullname' => $user['fullname'],
                            'email'    => $user['email'],
                            'role'     => 'admin'
                        ];
                        
                        session_write_close();
                        
                        echo json_encode([
                            'success' => true, 
                            'message' => 'Xác thực thành công! Đang vào hệ thống...',
                            'redirect' => BASE_URL . '/adminproduct/index'
                        ]);
                        return;
                    }
                }
                echo json_encode(['success' => false, 'message' => 'Tài khoản Admin hoặc mật khẩu không đúng!']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
            }
        }
    }

    public function forgot() {
        $this->view('admin.auth.forgot', ['title' => 'Khôi phục quyền Admin']);
    }

    public function postForgot() {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            if (empty($email) || empty($password) || empty($confirm)) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin!']);
                return;
            }

            if ($password !== $confirm) {
                echo json_encode(['success' => false, 'message' => 'Xác nhận mật khẩu mới không khớp!']);
                return;
            }

            try {
                $userModel = $this->model('User');
                $user = $userModel->findByEmail($email);

                if (!$user || $user['role'] !== 'admin') {
                    echo json_encode(['success' => false, 'message' => 'Email này không có quyền Quản trị viên!']);
                    return;
                }

                $userModel->updatePassword($email, $password);
                session_write_close();

                echo json_encode([
                    'success' => true, 
                    'message' => 'Đã khôi phục mật khẩu Admin! Hãy đăng nhập lại.',
                    'redirect' => BASE_URL . '/adminauth/login'
                ]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
            }
        }
    }
    
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION['user']);
        session_destroy();
        header("Location: " . BASE_URL . "/adminauth/login");
        exit();
    }
}