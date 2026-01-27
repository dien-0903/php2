<?php
/**
 * FRONT CONTROLLER - Điểm đón mọi yêu cầu của ứng dụng
 * Vị trí file: /PHP2/index.php
 */

// 1. Nhúng file khởi tạo hệ thống (Bootstrap)
// File này sẽ định nghĩa các đường dẫn (VIEW_PATH, APP_PATH...) và đăng ký tự động nạp lớp (Autoload)
require_once __DIR__ . '/app/core/bootstrap.php';

// 2. Khởi tạo bộ định tuyến (Router)
// Đối tượng này chịu trách nhiệm phân tích URL để biết cần gọi Controller nào
$router = new Router();

// 3. Lấy URI hiện tại từ biến môi trường của Server
// Ví dụ: Nếu bạn truy cập http://localhost/PHP2/brand, $uri sẽ là "/PHP2/brand"
$uri = $_SERVER['REQUEST_URI'];

/**
 * 4. Điều hướng yêu cầu (Dispatch)
 * * Luồng xử lý của Router:
 * - Loại bỏ phần đường dẫn gốc (/PHP2)
 * - Tách phần còn lại (/brand) để tìm Controller (BrandController)
 * - Mặc định gọi hàm index() nếu không có hành động cụ thể nào được nêu
 */
$router->dispatch($uri);