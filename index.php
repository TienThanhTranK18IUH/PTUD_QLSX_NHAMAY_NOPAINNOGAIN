<?php declare(strict_types=1); 
// ================================
// index.php — Front Controller
// ================================

// START OUTPUT BUFFERING FIRST - BEFORE SESSION AND INCLUDES
if (!ob_get_level()) {
    ob_start();
}

// Bắt đầu session
session_start();

// Gọi file cấu hình
require_once 'config/config.php';
require_once 'config/routes.php';
require_once 'app/models/database.php';

// Check if it's a POST request to a handler that needs to send headers
$controllerName = isset($_GET['controller']) ? $_GET['controller'] : 'dashboard';
$action         = isset($_GET['action']) ? $_GET['action'] : 'index';

// For POST requests, load controller and execute BEFORE loading layouts
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $controllerName === 'phieuNhapXuat') {
    $controllerFile = "app/controllers/{$controllerName}Controller.php";
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        $controllerClass = ucfirst($controllerName) . 'Controller';
        if (class_exists($controllerClass)) {
            $db = new Database();
            var_dump($db->conn);
            exit;
            
            $controllerObj = new $controllerClass($db);
            if (method_exists($controllerObj, $action)) {
                $controllerObj->$action();  // This will exit() if successful
            }
        }
    }
}

// Load layout (only for non-POST or non-redirected requests)
include 'app/views/layouts/header.php';
include 'app/views/layouts/sidebar.php';

// Tạo kết nối DB
$db = new Database();

// ================================
// Điều hướng controller/action
// ================================

// Xác định đường dẫn controller
$controllerFile = "app/controllers/{$controllerName}Controller.php";

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controllerClass = ucfirst($controllerName) . 'Controller';

    // ✅ KIỂM TRA CLASS CÓ TỒN TẠI KHÔNG
    if (class_exists($controllerClass)) {
        $controllerObj = new $controllerClass($db);

        if (method_exists($controllerObj, $action)) {
            $controllerObj->$action();
            
        } else {
            echo "<div class='content'><h3>❌ Action không tồn tại!</h3></div>";
        }
    } else {
        echo "<div class='content'><h3>❌ Class controller không tồn tại!</h3></div>";
    }
} else {
    echo "<div class='content'><h3>❌ Controller file không tồn tại!</h3></div>";
}

// Footer
include 'app/views/layouts/footer.php';

// FLUSH OUTPUT BUFFER AT THE END
if (ob_get_level()) {
    ob_end_flush();
}
?>