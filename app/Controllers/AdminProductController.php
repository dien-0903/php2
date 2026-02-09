<?php

class AdminProductController extends AdminController {
    
    public function index() {
        $productModel = $this->model('Product');
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = $_GET['search'] ?? '';
        $limit = 8; 

        $result = $productModel->list($page, $limit, $search);
        
        // Nạp Gallery cho từng sản phẩm
        if (!empty($result['data'])) {
            foreach ($result['data'] as &$p) {
                $p['gallery'] = $this->model('Model')->query(
                    "SELECT id, image FROM product_images WHERE product_id = ?", 
                    [$p['id']]
                )->fetchAll();
            }
        }
        
        $this->view('admin.product.index', [
            'title'          => 'Quản lý Sản phẩm',
            'products'       => $result['data'],
            'totalPages'     => $result['totalPages'],
            'currentPage'    => $page,
            'search'         => $search,
            'all_categories' => $this->model('Category')->getAll(), 
            'all_brands'     => $this->model('Brand')->getAll()
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $imageName = 'default.jpg';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageName = $this->uploadFile($_FILES['image'], 'products');
            }

            try {
                $productId = $this->model('Product')->create([
                    'name'        => $_POST['name'],
                    'price'       => (float)$_POST['price'],
                    'stock'       => (int)$_POST['stock'],
                    'description' => $_POST['description'],
                    'image'       => $imageName,
                    'category_id' => $_POST['category_id'],
                    'brand_id'    => $_POST['brand_id']
                ]);

                if (isset($_FILES['gallery']) && !empty($_FILES['gallery']['name'][0])) {
                    $this->uploadGallery($productId, $_FILES['gallery']);
                }

                $_SESSION['success'] = "Thêm sản phẩm thành công!";
                $this->redirect('adminproduct/index');
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                $this->redirect('adminproduct/index');
            }
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = $this->model('Product');
            $current = $model->show($id);

            $imageName = $current['image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageName = $this->uploadFile($_FILES['image'], 'products');
            }

            $model->update($id, [
                'name'        => $_POST['name'],
                'price'       => (float)$_POST['price'],
                'stock'       => (int)$_POST['stock'],
                'description' => $_POST['description'],
                'image'       => $imageName,
                'category_id' => $_POST['category_id'],
                'brand_id'    => $_POST['brand_id']
            ]);

            if (isset($_FILES['gallery']) && !empty($_FILES['gallery']['name'][0])) {
                $this->uploadGallery($id, $_FILES['gallery']);
            }

            $_SESSION['success'] = "Cập nhật thành công!";
            $this->redirect('adminproduct/index');
        }
    }

    /**
     * FIX LỖI REDIRECT: Xử lý xóa ảnh gallery hỗ trợ AJAX
     */
    public function deleteGalleryImage($id) {
        $model = $this->model('Model');
        $img = $model->query("SELECT image FROM product_images WHERE id = ?", [$id])->fetch();
        
        if ($img) {
            // 1. Xóa file vật lý
            $filePath = BASE_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR . $img['image'];
            if (file_exists($filePath)) @unlink($filePath);
            
            // 2. Xóa trong DB
            $model->query("DELETE FROM product_images WHERE id = ?", [$id]);

            // 3. Nếu là yêu cầu AJAX thì trả về JSON, không redirect
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Đã xóa ảnh']);
                exit;
            }
            
            $_SESSION['success'] = "Đã xóa ảnh khỏi thư viện!";
        }
        
        // Nếu không phải AJAX, quay lại trang trước đó
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . '/adminproduct/index'));
        exit;
    }

    private function uploadGallery($productId, $files) {
        foreach ($files['name'] as $key => $val) {
            if ($files['error'][$key] === UPLOAD_ERR_OK) {
                $fileData = ['name' => $files['name'][$key], 'tmp_name' => $files['tmp_name'][$key], 'error' => 0];
                $name = $this->uploadFile($fileData, 'products');
                if ($name) $this->model('Model')->query("INSERT INTO product_images (product_id, image) VALUES (?, ?)", [$productId, $name]);
            }
        }
    }

    public function destroy($id) {
        $this->model('Product')->delete($id);
        $_SESSION['success'] = "Đã xóa sản phẩm!";
        $this->redirect('adminproduct/index');
    }
}