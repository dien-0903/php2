<?php

class AdminSizeController extends AdminController {
    
    public function index() {
        $sizeModel = $this->model('Size');
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = $_GET['search'] ?? '';
        $limit = 10;

        $result = $sizeModel->paginate('sizes', $page, $limit, $search, [], 'name');

        $this->view('admin.size.index', [
            'title'       => 'Quản lý Kích thước',
            'sizes'       => $result['data'],
            'totalPages'  => $result['totalPages'],
            'currentPage' => $page,
            'search'      => $search
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $model = $this->model('Size');

            if (empty($name)) {
                $_SESSION['error'] = "Vui lòng nhập tên kích thước!";
                $this->redirect('adminsize/index');
                return;
            }

            if ($model->exists($name)) {
                $_SESSION['error'] = "Kích thước này đã tồn tại trong hệ thống!";
                $this->redirect('adminsize/index');
                return;
            }

            $model->create(['name' => $name]);
            $_SESSION['success'] = "Đã thêm kích thước mới thành công!";
            session_write_close();
            $this->redirect('adminsize/index');
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');

            $this->model('Size')->query(
                "UPDATE sizes SET name = ?, updated_at = NOW() WHERE id = ?",
                [$name, $id]
            );

            $_SESSION['success'] = "Cập nhật kích thước thành công!";
            session_write_close();
            $this->redirect('adminsize/index');
        }
    }
    public function destroy($id) {
        $this->model('Size')->delete($id);
        $_SESSION['success'] = "Đã gỡ bỏ kích thước thành công!";
        session_write_close();
        $this->redirect('adminsize/index');
    }
}