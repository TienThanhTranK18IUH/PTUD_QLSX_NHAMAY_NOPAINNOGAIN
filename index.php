<?php
// ================================
// index.php — Front Controller (PHP 5.x)
// ================================

// 🟢 BẬT OUTPUT BUFFERING TRƯỚC TIÊN
if (!ob_get_level()) {
    ob_start();
}

// 🟢 Bắt đầu session
if (session_id() === '') {
    session_start();
}

// 🟢 Gọi file cấu hình
require_once 'config/config.php';
require_once 'config/routes.php';
require_once 'app/models/database.php';

// 🟢 Lấy controller/action (PHP 5.x không dùng ??)
$controllerName = isset($_GET['controller']) ? $_GET['controller'] : 'dashboard';
$action         = isset($_GET['action']) ? $_GET['action'] : 'index';

// ================================
// XỬ LÝ POST TRƯỚC – KHÔNG LOAD LAYOUT
// ================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) ) {

    $controllerFile = "app/controllers/{$controllerName}Controller.php";

    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        $controllerClass = ucfirst($controllerName) . 'Controller';

        if (class_exists($controllerClass)) {

            $db = new Database();
            $controllerObj = new $controllerClass($db);

            if (method_exists($controllerObj, $action)) {
                // POST TRẢ RA DIRECT (REDIRECT, JSON, EXIT...)
                $controllerObj->$action();
                exit; // bắt buộc để redirect không lỗi header
            }
        }
    }
}

// ================================
// LOAD LAYOUT (chỉ khi GET hoặc POST không exit)
// ================================
include 'app/views/layouts/header.php';
include 'app/views/layouts/sidebar.php';

// 🟢 Kết nối Database
$db = new Database();

// ================================
// ĐIỀU HƯỚNG CONTROLLER GET
// ================================
$controllerFile = "app/controllers/{$controllerName}Controller.php";

if (file_exists($controllerFile)) {

    require_once $controllerFile;
    $controllerClass = ucfirst($controllerName) . 'Controller';

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

// FOOTER
include 'app/views/layouts/footer.php';

// 🟢 Kết thúc buffer, xuất HTML
if (ob_get_level()) {
    ob_end_flush();
}
?>
