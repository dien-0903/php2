<?php

use Jenssegers\Blade\Blade;

class Controller
{
    public function view($path, $data = [])
    {
        $viewPath = VIEW_PATH;
        $cachePath = STORAGE_PATH . DIRECTORY_SEPARATOR . 'cache';

        if (!file_exists($cachePath)) {
            mkdir($cachePath, 0777, true);
        }

        try {
            $blade = new Blade($viewPath, $cachePath);

            echo $blade->make($path, $data)->render();
        } catch (Exception $e) {
            die("<div style='padding:20px; border:1px solid red; font-family:sans-serif;'>
                    <h3 style='color:red;'>Lỗi Blade Engine:</h3>
                    <p>" . $e->getMessage() . "</p>
                    <p><b>File:</b> " . $e->getFile() . " (Dòng: " . $e->getLine() . ")</p>
                 </div>");
        }
    }

    public function model($name)
    {
        $class = ucfirst($name);
        if (!class_exists($class)) {
            $modelFile = APP_PATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . $class . '.php';
            if (file_exists($modelFile)) {
                require_once $modelFile;
            } else {
                throw new Exception("Lỗi: Lớp Model '$class' không tồn tại.");
            }
        }
        return new $class();
    }

    public function redirect($path)
    {
        $path = ltrim($path, '/');
        $target = BASE_URL . '/' . $path;
        header("Location: $target");
        exit();
    }

    public function notfound($message = "Trang không tồn tại"): void
    {
        http_response_code(404);
        echo "<div style='text-align:center; margin-top:50px; font-family: sans-serif;'>";
        echo "<h1 style='color: #cbd5e1; font-size: 100px; margin:0;'>404</h1>";
        echo "<h2>Không tìm thấy trang</h2>";
        echo "<p>" . htmlspecialchars($message) . "</p>";
        
        // --- DÒNG ĐÃ SỬA: Thêm dấu / trước product ---
        echo "<a href='/PHP2/product/index'>Quay lại trang chủ</a>";
        // ---------------------------------------------
        
        echo "</div>";
        exit();
    }
}