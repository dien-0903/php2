<?php

class AdminContactController extends AdminController {

    public function index() {
        $model = $this->model('Contact');
        
        $q = $_GET['q'] ?? '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        
        $result = $model->list($page, 5, $q);

        $this->view('admin.contact.index', [
            'title'         => 'Quản lý Liên hệ',
            'contacts'      => $result['data'],
            'totalPages'    => $result['totalPages'],
            'currentPage'   => $page,
            'q'             => $q,
            'totalAll'      => $result['totalAll'],
            'totalFiltered' => $result['totalFiltered']
        ]);
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            if ($id) {
                $this->model('Contact')->delete($id);
                $_SESSION['success'] = "Đã xóa liên hệ thành công.";
            }

            $q = $_GET['q'] ?? '';
            $page = $_GET['page'] ?? 1;
            $this->redirect("admincontact/index?q=$q&page=$page");
        }
    }
}