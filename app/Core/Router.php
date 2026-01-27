<?php

class Router
{
    public function dispatch(string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '';
        
        $scriptName = $_SERVER['SCRIPT_NAME']; 
        $scriptDir = str_replace('\\', '/', dirname($scriptName));
        
        $baseDir = rtrim(str_replace('/public', '', $scriptDir), '/');
        
        if ($baseDir !== '' && strpos($path, $baseDir) === 0) {
            $path = substr($path, strlen($baseDir));
        }

        $path = str_replace('/index.php', '', $path);

        $path = trim($path, '/');
        
        if ($path === '') {
            $segments = ['home', 'index'];
        } else {
            $segments = explode('/', $path);
        }
        
        $controllerPart = $segments[0] ?? 'home';
        $controllerName = ucfirst($controllerPart) . 'Controller';
        $action = $segments[1] ?? 'index';
        $params = array_slice($segments, 2);

        if (!class_exists($controllerName)) {
            $this->show404("Hệ thống không tìm thấy bộ xử lý: <b>$controllerName</b>.<br>Đường dẫn Route thực tế: <code>$path</code>", $controllerName);
            return;
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $action)) {
            $this->show404("Hành động <b>$action</b> không tồn tại trong <b>$controllerName</b>.", $controllerName);
            return;
        }

        call_user_func_array([$controller, $action], $params);
    }

    private function show404($message, $controllerName = ''): void
    {
        http_response_code(404);
        echo "<div style='text-align:center; padding:100px 20px; font-family: sans-serif; background:#f8fafc; min-height:100vh;'>";
        echo "<h1 style='color: #cbd5e1; font-size: 100px; margin:0; line-height:1;'>404</h1>";
        echo "<h2 style='color: #1e293b; margin-top:20px; text-transform: uppercase;'>Lỗi điều hướng</h2>";
        echo "<div style='color: #64748b; max-width:600px; margin: 20px auto; padding: 25px; background: #fff; border-radius: 20px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); text-align:left;'>";
        echo $message;
        echo "<hr style='border:0; border-top:1px solid #f1f5f9; margin:20px 0;'>";
        echo "<small><b>Gợi ý:</b> Hãy kiểm tra xem file Controller có đặt tên đúng là <code>" . ($controllerName ?: 'TênController') . ".php</code> (viết hoa chữ đầu) trong thư mục <code>app/controllers/</code> hay không.</small>";
        echo "</div>";
        echo "<a href='".BASE_URL."' style='display:inline-block; background:#2563eb; color:#fff; padding:12px 35px; border-radius:50px; text-decoration:none; font-weight:bold; shadow: 0 10px 15px -3px rgba(37,99,235,0.3);'>Quay về trang chủ</a>";
        echo "</div>";
    }
}