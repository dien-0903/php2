<?php

class AdminUserController extends AdminController {
    
    public function index() {
        $userModel = $this->model('User');
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = $_GET['search'] ?? '';
        $limit = 10; 

        $result = $userModel->list($page, $limit, $search);

        $this->view('admin.user.index', [
            'title'       => 'Quản lý Thành viên',
            'users'       => $result['data'],
            'totalPages'  => $result['totalPages'],
            'currentPage' => $page,
            'search'      => $search
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $fullname = trim($_POST['fullname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'user';
            
            $userModel = $this->model('User');

            if (empty($fullname) || empty($email) || empty($password)) {
                $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin bắt buộc!";
                $this->handleError('add', $_POST);
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Định dạng email không hợp lệ!";
                $this->handleError('add', $_POST);
                return;
            }

            if ($userModel->exists($email)) {
                $_SESSION['error'] = "Địa chỉ email '$email' đã được sử dụng!";
                $this->handleError('add', $_POST);
                return;
            }

            try {
                $userModel->create([
                    'fullname' => $fullname,
                    'email'    => $email,
                    'password' => $password,
                    'role'     => $role
                ]);

                $_SESSION['success'] = "Đã thêm thành viên '$fullname' thành công!";
                session_write_close();
                $this->redirect('adminuser/index');
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
                $this->handleError('add', $_POST);
            }
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $userModel = $this->model('User');
            $currentUser = $userModel->show($id);
            
            if (!$currentUser) {
                $_SESSION['error'] = "Thành viên không tồn tại!";
                $this->redirect('adminuser/index');
                return;
            }

            $fullname = trim($_POST['fullname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = $_POST['role'] ?? 'user';

            if (empty($fullname) || empty($email)) {
                $_SESSION['error'] = "Họ tên và Email không được để trống!";
                $this->handleError('edit', $_POST);
                return;
            }

            if ($userModel->exists($email, $id)) {
                $_SESSION['error'] = "Email này đã thuộc về người dùng khác!";
                $this->handleError('edit', $_POST);
                return;
            }

            try {
                $userModel->update($id, [
                    'fullname' => $fullname,
                    'email'    => $email,
                    'role'     => $role
                ]);

                $_SESSION['success'] = "Đã cập nhật thông tin thành công!";
                session_write_close();
                $this->redirect('adminuser/index');
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi khi lưu dữ liệu!";
                $this->handleError('edit', $_POST);
            }
        }
    }

    // --- MỚI: CHỨC NĂNG ĐỔI MẬT KHẨU CHO USER ---
    public function updatePassword($id) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $userModel = $this->model('User');
        $user = $userModel->show($id);

        if (!$user) {
            $_SESSION['error'] = "User không tồn tại!";
            $this->redirect('adminuser/index');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (strlen($newPassword) < 6) {
                $_SESSION['error'] = "Mật khẩu phải có ít nhất 6 ký tự!";
                // Lưu ý: Cần xử lý redirect lại trang edit password nếu có view riêng
                // Hoặc flash session và quay lại trang index
                session_write_close();
                $this->redirect('adminuser/index'); 
                return;
            }

            if ($newPassword !== $confirmPassword) {
                $_SESSION['error'] = "Xác nhận mật khẩu không khớp!";
                session_write_close();
                $this->redirect('adminuser/index');
                return;
            }

            try {
                // Hàm updatePassword đã có sẵn trong Model User (được cung cấp ở context)
                $userModel->updatePassword($user['email'], $newPassword);
                $_SESSION['success'] = "Đã đổi mật khẩu cho user {$user['fullname']} thành công!";
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
            }

            session_write_close();
            $this->redirect('adminuser/index');
        } else {
            // Nếu là GET request -> Hiển thị form đổi pass (nếu bạn muốn làm trang riêng)
            // Hiện tại tôi redirect về index để xử lý qua Modal hoặc Form chung
            $this->redirect('adminuser/index'); 
        }
    }

    public function destroy($id) {
        $userModel = $this->model('User');
        $user = $userModel->show($id);

        if ($user && ($user['role'] ?? '') === 'admin') {
            // Ngăn chặn xóa chính mình nếu là admin đang đăng nhập
            if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $id) {
                $_SESSION['error'] = "Bạn không thể xóa tài khoản của chính mình!";
                session_write_close();
                $this->redirect('adminuser/index');
                return;
            }
        }

        $userModel->delete($id);
        $_SESSION['success'] = "Đã gỡ bỏ thành viên khỏi hệ thống!";
        session_write_close();
        $this->redirect('adminuser/index');
    }

    private function handleError($type, $postData) {
        $_SESSION['error_type'] = $type; 
        $_SESSION['old'] = $postData;    
        session_write_close();
        $this->redirect('adminuser/index');
    }
}