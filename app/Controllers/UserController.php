<?php

class UserController extends Controller {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Middleware: Yêu cầu đăng nhập để truy cập các tính năng cá nhân
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = "Vui lòng đăng nhập để thực hiện chức năng này!";
            $this->redirect('auth/login');
            exit;
        }
    }

    /**
     * Hiển thị trang Hồ sơ cá nhân
     */
    public function profile() {
        $userModel = $this->model('User');
        $userId = $_SESSION['user']['id'];
        
        // Lấy dữ liệu mới nhất từ Database (để hiện SĐT và Ảnh đại diện vừa cập nhật)
        $userData = $userModel->show($userId);

        if (!$userData) {
            session_destroy();
            $this->redirect('auth/login');
            return;
        }

        $this->view('user.account.profile', [
            'title' => 'Hồ sơ của ' . ($userData['fullname'] ?? 'thành viên'),
            'user'  => $userData
        ]);
    }

    /**
     * Hiển thị Sổ địa chỉ của người dùng
     */
    public function address() {
        $userModel = $this->model('User');
        $userId = $_SESSION['user']['id'];
        
        // Lấy dữ liệu user mới nhất
        $userData = $userModel->show($userId);
        $addressModel = $this->model('Address');
        
        // Lấy danh sách địa chỉ của người dùng
        $addresses = $addressModel->getByUser($userId);

        $this->view('user.account.address', [
            'title'     => 'Sổ địa chỉ của ' . ($userData['fullname'] ?? 'tôi'),
            'user'      => $userData,
            'addresses' => $addresses
        ]);
    }

    /**
     * Xử lý cập nhật thông tin cá nhân và tải lên Ảnh đại diện
     */
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_SESSION['user']['id'];
            $fullname = trim($_POST['fullname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');

            // 1. Validation cơ bản
            if (empty($fullname) || empty($email)) {
                $_SESSION['error'] = "Họ tên và Email không được để trống!";
                $this->redirect('user/profile');
                return;
            }

            try {
                $userModel = $this->model('User');
                $currentUser = $userModel->show($id);

                // 2. Kiểm tra trùng Email với tài khoản khác
                if ($userModel->exists($email, $id)) {
                    $_SESSION['error'] = "Địa chỉ Email này đã được sử dụng bởi người khác!";
                    $this->redirect('user/profile');
                    return;
                }

                

                // 4. Cập nhật vào Cơ sở dữ liệu thông qua Model (Hàm update trong Canvas)
                $userModel->update($id, [
                    'fullname' => $fullname,
                    'email'    => $email,
                    'phone'    => $phone,
                    'role'     => $currentUser['role'] // Giữ nguyên role
                ]);

                // 5. Cập nhật lại thông tin trong Session để Header hiển thị đúng ngay lập tức
                $_SESSION['user']['fullname'] = $fullname;
                $_SESSION['user']['email']    = $email;

                $_SESSION['success'] = "Cập nhật thông tin thành công!";
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
            }

            $this->redirect('user/profile');
        }
    }

    /**
     * Xử lý thay đổi mật khẩu (Bảo mật)
     */
    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_SESSION['user']['id'];
            $oldPass = $_POST['old_password'] ?? '';
            $newPass = $_POST['new_password'] ?? '';
            $confirmPass = $_POST['confirm_password'] ?? '';

            if (strlen($newPass) < 6) {
                $_SESSION['error'] = "Mật khẩu mới phải có ít nhất 6 ký tự!";
                $this->redirect('user/profile');
                return;
            }

            if ($newPass !== $confirmPass) {
                $_SESSION['error'] = "Xác nhận mật khẩu mới không trùng khớp!";
                $this->redirect('user/profile');
                return;
            }

            try {
                $userModel = $this->model('User');
                $user = $userModel->show($id);

                // Kiểm tra mật khẩu cũ bằng hàm băm
                if (!password_verify($oldPass, $user['password'])) {
                    $_SESSION['error'] = "Mật khẩu hiện tại không chính xác!";
                    $this->redirect('user/profile');
                    return;
                }

                // Cập nhật mật khẩu mới (Model sẽ tự động mã hóa lại)
                $userModel->updatePassword($user['email'], $newPass);
                
                $_SESSION['success'] = "Thay đổi mật khẩu thành công!";
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi khi đổi mật khẩu: " . $e->getMessage();
            }

            $this->redirect('user/profile');
        }
    }
}