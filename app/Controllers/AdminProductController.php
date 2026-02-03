<?php

class AdminProductController extends AdminController {
    
    public function index() {
        $productModel = $this->model('Product');
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = $_GET['search'] ?? '';
        $limit = 8; 

        $result = $productModel->list($page, $limit, $search);
        
        $categories = $this->model('Category')->getAll();
        $brands = $this->model('Brand')->getAll();
        
        $this->view('admin.product.index', [
            'title'          => 'Hệ thống Quản lý Sản phẩm',
            'products'       => $result['data'],
            'totalPages'     => $result['totalPages'],
            'currentPage'    => $page,
            'search'         => $search,
            'all_categories' => $categories, 
            'all_brands'     => $brands
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $name = trim($_POST['name'] ?? '');
            $price = (float)($_POST['price'] ?? 0);
            $category_id = $_POST['category_id'] ?: null;
            $brand_id = $_POST['brand_id'] ?: null;

            // --- Lấy thêm dữ liệu mới ---
            $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
            $description = trim($_POST['description'] ?? '');

            if (empty($name) || $price < 0) { 
                $_SESSION['error'] = "Tên sản phẩm không được để trống và giá phải lớn hơn hoặc bằng 0!";
                $this->handleError('add', $_POST);
                return;
            }

            if ($price > 999999999) {
                $_SESSION['error'] = "Giá tiền quá lớn! Vui lòng nhập giá nhỏ hơn 1 tỷ VNĐ.";
                $this->handleError('add', $_POST);
                return;
            }

            $imageName = 'default.jpg';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                // GỌI HÀM TỪ ADMIN CONTROLLER (Class Cha)
                // Tham số thứ 2 là tên thư mục con trong uploads
                $uploaded = $this->uploadFile($_FILES['image'], 'products');
                if ($uploaded) $imageName = $uploaded;
            }

            try {
                // Tạo sản phẩm và lấy ID vừa tạo
                $productId = $this->model('Product')->create([
                    'name'        => $name,
                    'price'       => $price,
                    'stock'       => $stock,
                    'description' => $description,
                    'image'       => $imageName,
                    'category_id' => $category_id,
                    'brand_id'    => $brand_id
                ]);

                // --- Xử lý upload NHIỀU HÌNH ẢNH (Gallery) ---
                if (isset($_FILES['gallery']) && !empty($_FILES['gallery']['name'][0])) {
                    $this->uploadGallery($productId, $_FILES['gallery']);
                }

                $_SESSION['success'] = "Sản phẩm '$name' đã được niêm yết thành công!";
                session_write_close();
                $this->redirect('adminproduct/index');
            } catch (PDOException $e) {
                if ($e->getCode() == '22003') {
                    $_SESSION['error'] = "Lỗi: Số tiền nhập vào vượt quá giới hạn cho phép của hệ thống!";
                } else {
                    $_SESSION['error'] = "Lỗi cơ sở dữ liệu: " . $e->getMessage();
                }
                $this->handleError('add', $_POST);
            }
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $model = $this->model('Product');
            $currentProduct = $model->show($id);
            
            if (!$currentProduct) {
                $_SESSION['error'] = "Không tìm thấy sản phẩm!";
                $this->redirect('adminproduct/index');
                return;
            }

            $name = trim($_POST['name'] ?? '');
            $price = (float)($_POST['price'] ?? 0);
            
            // --- Lấy thêm dữ liệu mới ---
            $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
            $description = trim($_POST['description'] ?? '');

            if (empty($name) || $price < 0) {
                $_SESSION['error'] = "Dữ liệu cập nhật không hợp lệ (Tên trống hoặc giá < 0)!";
                $this->handleError('edit', $_POST);
                return;
            }

            if ($price > 999999999) {
                $_SESSION['error'] = "Giá tiền quá lớn! Giới hạn cho phép là dưới 1 tỷ VNĐ.";
                $this->handleError('edit', $_POST);
                return;
            }

            $imageName = $currentProduct['image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                // GỌI HÀM TỪ ADMIN CONTROLLER (Class Cha)
                $uploaded = $this->uploadFile($_FILES['image'], 'products');
                if ($uploaded) $imageName = $uploaded;
            }

            try {
                $model->update($id, [
                    'name'        => $name,
                    'price'       => $price,
                    'stock'       => $stock,
                    'description' => $description,
                    'image'       => $imageName,
                    'category_id' => $_POST['category_id'] ?: null,
                    'brand_id'    => $_POST['brand_id'] ?: null
                ]);

                // --- Xử lý upload thêm ảnh vào Gallery (nếu có) ---
                if (isset($_FILES['gallery']) && !empty($_FILES['gallery']['name'][0])) {
                    $this->uploadGallery($id, $_FILES['gallery']);
                }

                $_SESSION['success'] = "Cập nhật sản phẩm thành công!";
                session_write_close();
                $this->redirect('adminproduct/index');
            } catch (PDOException $e) {
                if ($e->getCode() == '22003') {
                    $_SESSION['error'] = "Lỗi: Giá tiền quá lớn không thể lưu vào hệ thống!";
                } else {
                    $_SESSION['error'] = "Lỗi phát sinh: " . $e->getMessage();
                }
                $this->handleError('edit', $_POST);
            }
        }
    }

    // --- HELPER: XỬ LÝ UPLOAD NHIỀU ẢNH (GALLERY) ---
    private function uploadGallery($productId, $files) {
        foreach ($files['name'] as $key => $name) {
            if ($files['error'][$key] === UPLOAD_ERR_OK) {
                $fileData = [
                    'name'     => $files['name'][$key],
                    'tmp_name' => $files['tmp_name'][$key],
                    'size'     => $files['size'][$key],
                    'error'    => $files['error'][$key]
                ];
                // Sử dụng hàm uploadFile từ cha để đồng bộ logic
                $fileName = $this->uploadFile($fileData, 'products');
                if ($fileName) {
                    // Lưu đường dẫn ảnh vào bảng gallery
                    $this->model('Model')->query("INSERT INTO product_images (product_id, image) VALUES (?, ?)", [$productId, $fileName]);
                }
            }
        }
    }

    private function handleError($type, $postData) {
        $_SESSION['error_type'] = $type; 
        $_SESSION['old'] = $postData;    
        session_write_close();
        $this->redirect('adminproduct/index');
    }

    public function destroy($id) {
        $this->model('Product')->delete($id);
        $_SESSION['success'] = "Sản phẩm đã được xóa khỏi hệ thống!";
        session_write_close();
        $this->redirect('adminproduct/index');
    }

    // ĐÃ XÓA hàm uploadFile() private tại đây để dùng hàm protected của cha
    public function deleteGalleryImage($id) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $model = $this->model('Model');
        // 1. Tìm thông tin ảnh trong database
        $img = $model->query("SELECT image FROM product_images WHERE id = ?", [$id])->fetch();
        
        if ($img) {
            // 2. Xóa file vật lý trong thư mục uploads
            // Lưu ý: DIRECTORY_SEPARATOR giúp chạy đúng trên cả Windows/Linux
            $filePath = BASE_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR . $img['image'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            
            // 3. Xóa bản ghi trong database
            $model->query("DELETE FROM product_images WHERE id = ?", [$id]);
            $_SESSION['success'] = "Đã xóa ảnh khỏi thư viện thành công!";
        } else {
            $_SESSION['error'] = "Không tìm thấy dữ liệu ảnh để xóa!";
        }

        // Quay lại trang trước đó (Trang danh sách sản phẩm)
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . '/adminproduct/index'));
        exit;
    }
}