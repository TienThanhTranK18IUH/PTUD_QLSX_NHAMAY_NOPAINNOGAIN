<?php
if(session_id() == '') {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🏭 Quản lý sản xuất nhà máy</title>
    <!-- Liên kết file CSS header -->
    <link rel="stylesheet" href="public/css/header.css">
</head>
<body>
<header>
    <div class="header-container">
        <h1>🏭 HỆ THỐNG QUẢN LÝ SẢN XUẤT NHÀ MÁY</h1>
        <div class="user-info">
            <span>
                Xin chào, 
                <?php 
                    $username = $_SESSION['username'];
                    $role = $_SESSION['role'];
                    echo htmlspecialchars($username) . " (" . htmlspecialchars($role) . ")";
                ?>
            </span>
            <a href="index.php?controller=auth&action=logout" class="logout-btn">Đăng xuất</a>
        </div>
    </div>
</header>
<hr class="header-divider">
