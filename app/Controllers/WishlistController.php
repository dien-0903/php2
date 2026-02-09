<?php

class WishlistController extends Controller {

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $wishlist = $_SESSION['wishlist'] ?? [];
        $products = [];

        if (!empty($wishlist)) {
            $productModel = $this->model('Product');
            if (method_exists($productModel, 'getByIds')) {
                $products = $productModel->getByIds($wishlist);
            }
        }

        $this->view('user.wishlist.index', [
            'title'        => 'Sản phẩm yêu thích',
            'products'     => $products,
            'wishlist_ids' => $wishlist
        ]);
    }

    public function add($id) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['wishlist'])) {
            $_SESSION['wishlist'] = [];
        }

        $message = "";
        $status = true;

        if (!in_array($id, $_SESSION['wishlist'])) {
            $_SESSION['wishlist'][] = $id;
            $message = "Đã thêm vào danh sách yêu thích!";
            $_SESSION['success'] = $message;
        } else {
            $message = "Sản phẩm này đã có trong danh sách yêu thích!";
            $status = false; 
            $_SESSION['error'] = $message;
        }

        if ($this->isAjax()) {
            echo json_encode(['success' => $status, 'message' => $message, 'action' => 'add']);
            exit;
        }

        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . '/product/index'));
    }

    public function remove($id) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $message = "Sản phẩm không có trong danh sách!";
        $success = false;

        if (isset($_SESSION['wishlist'])) {
            if (($key = array_search($id, $_SESSION['wishlist'])) !== false) {
                unset($_SESSION['wishlist'][$key]);
                $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
                
                $message = "Đã xóa khỏi danh sách yêu thích!";
                $success = true;
                $_SESSION['success'] = $message;
            }
        }

        if ($this->isAjax()) {
            echo json_encode(['success' => $success, 'message' => $message, 'action' => 'remove']);
            exit;
        }

        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . '/product/index'));
    }

    private function isAjax() {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || isset($_GET['ajax']);
    }
}