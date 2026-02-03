<?php

class AdminAddressController extends AdminController {
    
    /**
     * Hiển thị danh sách địa chỉ của một người dùng (Trang Index)
     * URL ví dụ: adminaddress/index/1
     */
    public function index($userId = null) {
        $userModel = $this->model('User');
        
        if (!$userId) {
            $_SESSION['error'] = 'Không tìm thấy ID người dùng để xem địa chỉ!';
            $this->redirect('adminuser/index');
            return;
        }

        // Lấy thông tin khách hàng để hiển thị tiêu đề
        $user = $userModel->show($userId);
        if (!$user) {
            $_SESSION['error'] = 'Người dùng không tồn tại!';
            $this->redirect('adminuser/index');
            return;
        }

        $addressModel = $this->model('Address');
        // Lấy danh sách địa chỉ của user này
        $addresses = $addressModel->getByUser($userId);

        $this->view('admin.address.index', [
            'title'     => 'Quản lý địa chỉ: ' . $user['fullname'],
            'user'      => $user,
            'addresses' => $addresses
        ]);
    }

    /**
     * Xử lý thêm địa chỉ mới (Hành động từ Modal Thêm)
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $userId = $_POST['user_id'];
            $recipientName = trim($_POST['recipient_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? ''); // Chuỗi gộp từ JavaScript
            $isDefault = isset($_POST['is_default']) ? 1 : 0;

            if (empty($recipientName) || empty($phone) || empty($address)) {
                $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin người nhận và địa chỉ!";
                $this->redirect('adminaddress/index/' . $userId);
                return;
            }

            try {
                $this->model('Address')->create([
                    'user_id'        => $userId,
                    'recipient_name' => $recipientName,
                    'phone'          => $phone,
                    'address'        => $address,
                    'is_default'     => $isDefault,
                    'status'         => 1 // Mặc định địa chỉ mới là hoạt động
                ]);

                $_SESSION['success'] = "Đã thêm địa chỉ mới thành công!";
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
            }

            $this->redirect('adminaddress/index/' . $userId);
        }
    }

    /**
     * Xử lý cập nhật thông tin địa chỉ (Hành động từ Modal Sửa)
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();
            
            $userId = $_POST['user_id'];
            $recipientName = trim($_POST['recipient_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $isDefault = isset($_POST['is_default']) ? 1 : 0;

            if (empty($recipientName) || empty($phone) || empty($address)) {
                $_SESSION['error'] = "Thông tin cập nhật không được để trống!";
                $this->redirect('adminaddress/index/' . $userId);
                return;
            }

            try {
                $this->model('Address')->update($id, [
                    'recipient_name' => $recipientName,
                    'phone'          => $phone,
                    'address'        => $address,
                    'is_default'     => $isDefault
                ]);

                $_SESSION['success'] = "Cập nhật thông tin địa chỉ thành công!";
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi khi cập nhật dữ liệu!";
            }

            $this->redirect('adminaddress/index/' . $userId);
        }
    }

    /**
     * Đặt một địa chỉ làm mặc định (Nút bấm nhanh)
     */
    public function set_default($id, $userId) {
        try {
            $this->model('Address')->setDefault($id, $userId);
            $_SESSION['success'] = "Đã thiết lập địa chỉ mặc định mới!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi khi đặt mặc định!";
        }
        
        $this->redirect('adminaddress/index/' . $userId);
    }

    /**
     * Bật/Tắt trạng thái hoạt động (Khóa/Mở địa chỉ)
     */
    public function toggle_status($id, $userId) {
        try {
            $this->model('Address')->toggleStatus($id);
            $_SESSION['success'] = "Đã cập nhật trạng thái hoạt động!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Không thể thay đổi trạng thái!";
        }

        $this->redirect('adminaddress/index/' . $userId);
    }

    /**
     * Xóa địa chỉ vĩnh viễn (hoặc Soft Delete tùy Model)
     */
    public function destroy($id, $userId) {
        try {
            $this->model('Address')->delete($id);
            $_SESSION['success'] = "Đã xóa địa chỉ khỏi hệ thống!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi khi xóa địa chỉ!";
        }
        
        $this->redirect('adminaddress/index/' . $userId);
    }

  
}