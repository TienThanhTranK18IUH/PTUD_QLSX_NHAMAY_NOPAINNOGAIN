<?php
require_once dirname(__FILE__) . '/../models/nhanVien.php';

class AuthController {

    public function login() {
        if (session_id() === '') session_start();

        $error = '';

        $nvModel = new NhanVien();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';

            if ($username === '' || $password === '') {
                $error = 'Vui lòng nhập tên đăng nhập và mật khẩu.';
            } else {
                $user = $nvModel->findByUsername($username);
                if ($user) {
                    $dbPass = isset($user['matKhau']) ? $user['matKhau'] : '';

                    $ok = false;
                    // Nếu password lưu theo bcrypt
                    if (!empty($dbPass) && (strpos($dbPass, '$2y$') === 0 || strpos($dbPass, '$2a$') === 0)) {
                        if (password_verify($password, $dbPass)) $ok = true;
                    } else {
                        // So sánh thẳng (legacy plaintext)
                        if ($password === $dbPass) $ok = true;
                    }

                    if ($ok) {
                        // Lưu thông tin người dùng vào session
                        $_SESSION['user'] = array(
                            'maNguoiDung' => isset($user['maNguoiDung']) ? $user['maNguoiDung'] : '',
                            'tenDangNhap' => isset($user['tenDangNhap']) ? $user['tenDangNhap'] : $username,
                            'hoTen' => isset($user['hoTen']) ? $user['hoTen'] : '',
                            'vaiTro' => isset($user['vaiTro']) ? $user['vaiTro'] : '',
                            'tenXuong' => isset($user['tenXuong']) ? $user['tenXuong'] : '',
                            'maBoPhan' => isset($user['maBoPhan']) ? $user['maBoPhan'] : '',
                            'email' => isset($user['email']) ? $user['email'] : '',
                            'trangThai' => isset($user['trangThai']) ? $user['trangThai'] : ''
                        );

                        header("Location: index.php?controller=dashboard");
                        exit;
                    } else {
                        $error = 'Tên đăng nhập hoặc mật khẩu không đúng.';
                    }
                } else {
                    $error = 'Tên đăng nhập không tồn tại.';
                }
            }
        }

        include dirname(__FILE__) . '/../views/auth/login.php';
    }

    public function logout() {
        if (session_id() === '') session_start();
        unset($_SESSION['user']);
        session_destroy();
        header("Location: index.php?controller=auth&action=login");
        exit;
    }
}
