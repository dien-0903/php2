<?php

class HomeController extends Controller
{
    public function index()
    {
        $productModel = $this->model('Product');
        $couponModel = $this->model('Coupon'); 

        if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $search = $_GET['search'] ?? '';
            
            $couponData = $couponModel->getAvailableList($page, 6, $search);
            
            header('Content-Type: application/json');
            echo json_encode($couponData);
            exit;
        }

        $productData = $productModel->list(1, 8, '');
        $newProducts = $productData['data'] ?? [];

        $couponData = $couponModel->getAvailableList(1, 6, '');

        $this->view('user.home.index', [
            'title'       => 'Trang chủ - MD',
            'newProducts' => $newProducts,
            'user'        => $_SESSION['user'] ?? null,
            'cart'        => $_SESSION['cart'] ?? [],
            
            'coupons'     => $couponData['data'],
            'totalPages'  => $couponData['totalPages'],
            'currentPage' => $couponData['currentPage'],
            'search'      => ''
        ]);
    }
}