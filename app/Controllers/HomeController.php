<?php

class HomeController
{
    public function index()
    {
        View::render('user.home.index', [
            'title' => 'Trang chủ',
            'user'  => $_SESSION['user'] ?? null,
            'cart'  => $_SESSION['cart'] ?? []
        ]);
    }
}