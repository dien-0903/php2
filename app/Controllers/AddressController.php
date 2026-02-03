<?php

class AddressController extends Controller {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Middleware: Yêu cầu người dùng đăng nhập mới được thao tác
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = "Vui lòng đăng nhập để quản lý địa chỉ của bạn!";
            $this->redirect('auth/login');
            exit;
        }
    }

    /**
     * Hiển thị danh sách địa chỉ của chính người dùng
     */
    public function index() {
        $userId = $_SESSION['user']['id'];
        $userModel = $this->model('User');
        $addressModel = $this->model('Address');

        // Nạp dữ liệu user tươi mới nhất từ DB (để lấy tên hiển thị badge)
        $userData = $userModel->show($userId);
        // Lấy danh sách địa chỉ chưa bị xóa của user
        $addresses = $addressModel->getByUser($userId);

        $this->view('user.account.address', [
            'title'     => 'Sổ địa chỉ của tôi',
            'user'      => $userData,
            'addresses' => $addresses
        ]);
    }

    /**
     * Xử lý lưu địa chỉ mới từ giao diện người dùng
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];
            $recipientName = trim($_POST['recipient_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $addressFull = trim($_POST['address_full'] ?? ''); // Chuỗi gộp từ JavaScript phía giao diện
            $isDefault = isset($_POST['is_default']) ? 1 : 0;

            if (empty($recipientName) || empty($phone) || empty($addressFull)) {
                $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin nhận hàng!";
                $this->redirect('address/index');
                return;
            }

            try {
                $this->model('Address')->create([
                    'user_id'        => $userId,
                    'recipient_name' => $recipientName,
                    'phone'          => $phone,
                    'address'        => $addressFull,
                    'is_default'     => $isDefault
                ]);

                $_SESSION['success'] = "Đã thêm địa chỉ giao hàng mới!";
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi hệ thống khi lưu địa chỉ!";
            }

            $this->redirect('address/index');
        }
    }

    /**
     * Cập nhật địa chỉ hiện có
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];
            $addressModel = $this->model('Address');

            // Bảo mật: Kiểm tra xem địa chỉ này có thuộc về user đang đăng nhập không
            $check = $addressModel->query("SELECT user_id FROM addresses WHERE id = ? AND deleted_at IS NULL", [$id])->fetch();
            
            if (!$check || $check['user_id'] != $userId) {
                $_SESSION['error'] = "Hành động không hợp lệ!";
                $this->redirect('address/index');
                return;
            }

            $recipientName = trim($_POST['recipient_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $addressFull = trim($_POST['address_full'] ?? '');
            $isDefault = isset($_POST['is_default']) ? 1 : 0;

            try {
                $addressModel->update($id, [
                    'recipient_name' => $recipientName,
                    'phone'          => $phone,
                    'address'        => $addressFull,
                    'is_default'     => $isDefault
                ]);

                $_SESSION['success'] = "Đã cập nhật thông tin địa chỉ!";
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi cập nhật dữ liệu!";
            }

            $this->redirect('address/index');
        }
    }

    /**
     * Thiết lập địa chỉ mặc định nhanh
     */
    public function setDefault($id) {
        $userId = $_SESSION['user']['id'];
        $addressModel = $this->model('Address');

        // Kiểm tra quyền sở hữu trước khi thực hiện
        $check = $addressModel->query("SELECT user_id FROM addresses WHERE id = ?", [$id])->fetch();
        if ($check && $check['user_id'] == $userId) {
            $addressModel->setDefault($id, $userId);
            $_SESSION['success'] = "Đã đặt địa chỉ mặc định mới!";
        }

        $this->redirect('address/index');
    }

    /**
     * Xử lý xóa địa chỉ (Soft delete)
     */
    public function destroy($id) {
        $userId = $_SESSION['user']['id'];
        $addressModel = $this->model('Address');

        $check = $addressModel->query("SELECT user_id FROM addresses WHERE id = ?", [$id])->fetch();
        if ($check && $check['user_id'] == $userId) {
            $addressModel->delete($id);
            $_SESSION['success'] = "Đã xóa địa chỉ thành công!";
        } else {
            $_SESSION['error'] = "Bạn không có quyền xóa địa chỉ này!";
        }

        $this->redirect('address/index');
    }
}