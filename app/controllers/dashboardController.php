<?php
require_once dirname(__FILE__) . '/../helpers/auth.php';

class DashboardController {
    public function index() {
        // Yêu cầu đăng nhập
        requireLogin();

        include 'app/views/layouts/dashboard.php';
    }
}
?>
