<?php

class ContactController extends Controller {

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $this->view('user.contact.index', [
            'title' => 'Liên hệ với chúng tôi'
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $data = [
                'full_name' => trim($_POST['full_name'] ?? ''),
                'email'     => trim($_POST['email'] ?? ''),
                'phone'     => trim($_POST['phone'] ?? ''),
                'subject'   => trim($_POST['subject'] ?? ''),
                'message'   => trim($_POST['message'] ?? '')
            ];

            $errors = [];
            if (empty($data['full_name'])) $errors['full_name'] = "Vui lòng nhập họ tên.";
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = "Email không hợp lệ.";
            if (empty($data['phone'])) $errors['phone'] = "Vui lòng nhập số điện thoại.";
            if (empty($data['subject'])) $errors['subject'] = "Vui lòng nhập tiêu đề.";
            if (empty($data['message'])) $errors['message'] = "Vui lòng nhập nội dung tin nhắn.";

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $data;
                $this->redirect('contact/index');
                return;
            }

            try {
                $this->model('Contact')->create($data);
                $_SESSION['success'] = "Gửi liên hệ thành công!";
                $this->redirect('contact/index');
            } catch (Exception $e) {
                $_SESSION['error'] = "Có lỗi xảy ra, vui lòng thử lại sau.";
                $this->redirect('contact/index');
            }
        }
    }
}