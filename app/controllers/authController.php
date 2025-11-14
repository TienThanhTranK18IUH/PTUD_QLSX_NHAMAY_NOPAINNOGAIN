<?php
class AuthController {

    public function login() {
        session_start();

        $error = '';

        // Mảng tạm chứa user (username => password, role)
        $users = [
            'admin' => ['password' => '123456', 'role' => 'Quản trị'],
            'nv1' => ['password' => '123456', 'role' => 'Nhân viên'],
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            if (isset($users[$username]) && $users[$username]['password'] === $password) {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $users[$username]['role'];
                header("Location: index.php?controller=dashboard");
                exit;
            } else {
                $error = "Tên đăng nhập hoặc mật khẩu không đúng!";
            }
        }

        include __DIR__ . '/../views/auth/login.php';
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: index.php?controller=auth&action=login");
        exit;
    }
}
