<?php

class CompareController extends Controller {

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $compareIds = $_SESSION['compare'] ?? [];
        $products = [];

        if (!empty($compareIds)) {
            $productModel = $this->model('Product');
            if (method_exists($productModel, 'getByIds')) {
                $products = $productModel->getByIds($compareIds);
            }
        }

        $this->view('user.compare.index', [
            'title'    => 'So sánh sản phẩm',
            'products' => $products
        ]);
    }

    public function add($id) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['compare'])) {
            $_SESSION['compare'] = [];
        }

        if (count($_SESSION['compare']) >= 3) {
            $_SESSION['error'] = "Chỉ được so sánh tối đa 3 sản phẩm!";
        } elseif (!in_array($id, $_SESSION['compare'])) {
            $_SESSION['compare'][] = $id;
            $_SESSION['success'] = "Đã thêm vào bảng so sánh!";
        } else {
            $_SESSION['error'] = "Sản phẩm đã tồn tại trong bảng so sánh!";
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => true, 'count' => count($_SESSION['compare'])]);
            exit;
       }

        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . '/product/index'));
    }

    public function remove($id) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (isset($_SESSION['compare'])) {
            if (($key = array_search($id, $_SESSION['compare'])) !== false) {
                unset($_SESSION['compare'][$key]);
                $_SESSION['success'] = "Đã xóa khỏi bảng so sánh!";
            }
        }

        header("Location: " . BASE_URL . '/compare/index');
    }
}