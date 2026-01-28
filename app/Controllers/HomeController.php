<?php

class HomeController extends Controller
{
    public function index()
    {
        $productModel = $this->model('Product');

        $result = $productModel->list(1, 8, '');
        
        $newProducts = $result['data'] ?? [];

        $this->view('user.home.index', [
            'title'       => 'Trang chủ - TechStore',
            'newProducts' => $newProducts, 
            'user'        => $_SESSION['user'] ?? null,
            'cart'        => $_SESSION['cart'] ?? []
        ]);
    }
}