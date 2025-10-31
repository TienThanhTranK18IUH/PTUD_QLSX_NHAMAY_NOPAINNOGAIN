<?php
// ================================
// index.php — Front Controller
// ================================

// Bắt đầu session
session_start();

// Gọi file cấu hình
require_once 'config/config.php';
require_once 'config/routes.php';
require_once 'app/models/database.php';  // <-- thêm dòng này nếu chưa có

// Load layout
include 'app/views/layouts/header.php';
include 'app/views/layouts/sidebar.php';

// Tạo kết nối DB (ví dụ dùng mysqli)
$db = new Database();   // $db bây giờ là đối tượng Database, không phải mysqli

// ================================
// Điều hướng controller/action
// ================================
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'dashboard';
$action     = isset($_GET['action']) ? $_GET['action'] : 'index';

// Xác định đường dẫn controller
$controllerFile = "app/controllers/{$controller}Controller.php";

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controllerClass = ucfirst($controller) . 'Controller';
    $controllerObj = new $controllerClass($db);

    // Kiểm tra action tồn tại
    if (method_exists($controllerObj, $action)) {
        $controllerObj->$action();
    } else {
        echo "<div class='content'><h3>❌ Action không tồn tại!</h3></div>";
    }
} else {
    echo "<div class='content'><h3>❌ Controller không tồn tại!</h3></div>";
}


// Footer
include 'app/views/layouts/footer.php';
?>
