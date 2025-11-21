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
            <?php if (isset($_SESSION['user']) && !empty($_SESSION['user'])): 
                $u = $_SESSION['user'];
                $displayName = isset($u['tenDangNhap']) ? $u['tenDangNhap'] : (isset($u['hoTen']) ? $u['hoTen'] : '');
                $displayRole = isset($u['vaiTro']) ? $u['vaiTro'] : '';
            ?>
                <span>Xin chào, <?php echo htmlspecialchars($displayName) . ' (' . htmlspecialchars($displayRole) . ')'; ?></span>
                <a href="index.php?controller=auth&action=logout" class="logout-btn">Đăng xuất</a>
            <?php else: ?>
                <a href="index.php?controller=auth&action=login" class="logout-btn">Đăng nhập</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<hr class="header-divider">
