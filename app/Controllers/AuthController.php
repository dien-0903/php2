<?php

class AuthController extends Controller {
    
    public function login() {
        $this->view('user.auth.login', ['title' => 'Đăng nhập hệ thống']);
    }

    public function postLogin() {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ email và mật khẩu!']);
                return;
            }

            try {
                $userModel = $this->model('User');
                $user = $userModel->findByEmail($email);

                if ($user) {
                    $isPasswordCorrect = false;
                    if (password_verify($password, $user['password'])) {
                        $isPasswordCorrect = true;
                    } elseif ($password === $user['password']) {
                        $isPasswordCorrect = true;
                    }

                    if ($isPasswordCorrect) {
                        $userRole = strtolower(trim($user['role'] ?? 'user'));

                        $_SESSION['user'] = [
                            'id'       => $user['id'],
                            'fullname' => $user['fullname'],
                            'email'    => $user['email'],
                            'role'     => $userRole
                        ];
                        
                        $_SESSION['success'] = "Đăng nhập thành công! Chào mừng " . $user['fullname'];
                        
                        session_write_close();
                        
                        $target = ($userRole === 'admin') ? 'adminproduct/index' : 'product/index';
                        
                        echo json_encode([
                            'success' => true, 
                            'message' => 'Đang chuyển hướng...', 
                            'redirect' => BASE_URL . '/' . $target
                        ]);
                        return;
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Mật khẩu cung cấp không chính xác!']);
                        return;
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Tài khoản không tồn tại trên hệ thống!']);
                    return;
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
                return;
            }
        }
    }

    public function register() {
        $this->view('user.auth.register', ['title' => 'Đăng ký tài khoản']);
    }

    public function postRegister() {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = trim($_POST['fullname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (empty($fullname) || empty($email) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin!']);
                return;
            }

            if ($password !== $confirm_password) {
                echo json_encode(['success' => false, 'message' => 'Mật khẩu xác nhận không khớp!']);
                return;
            }

            try {
                $userModel = $this->model('User');
                if ($userModel->exists($email)) {
                    echo json_encode(['success' => false, 'message' => 'Email này đã được sử dụng!']);
                    return;
                }

                $userModel->create([
                    'fullname' => $fullname,
                    'email'    => $email,
                    'password' => $password,
                    'role'     => 'user'
                ]);

                $_SESSION['success'] = "Đăng ký thành công! Hãy đăng nhập ngay.";
                session_write_close();

                echo json_encode([
                    'success' => true, 
                    'message' => 'Đăng ký thành công!',
                    'redirect' => BASE_URL . '/auth/login'
                ]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
            }
        }
    }

    public function forgot() {
        $this->view('user.auth.forgot', ['title' => 'Khôi phục mật khẩu']);
    }

    public function postForgot() {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (empty($email) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin!']);
                return;
            }

            $userModel = $this->model('User');
            if (!$userModel->exists($email)) {
                echo json_encode(['success' => false, 'message' => 'Email không tồn tại!']);
                return;
            }

            try {
                $userModel->updatePassword($email, $password);
                $_SESSION['success'] = "Đổi mật khẩu thành công! Vui lòng đăng nhập lại.";
                session_write_close();

                echo json_encode([
                    'success' => true, 
                    'message' => 'Thành công!',
                    'redirect' => BASE_URL . '/auth/login'
                ]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật mật khẩu!']);
            }
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION = [];
        session_destroy();
        $this->redirect('product/index');
    }
}