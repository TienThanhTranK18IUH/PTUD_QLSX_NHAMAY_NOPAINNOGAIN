<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>
    <link rel="stylesheet" href="public/css/auth.css">
</head>
<body>
<div class="login-container">
    <form method="POST" class="login-form">
        <h2>🔒 Đăng nhập</h2>
        <?php if(!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <label>Tên đăng nhập</label>
        <input type="text" name="username" required>
        <label>Mật khẩu</label>
        <input type="password" name="password" required>
        <button type="submit">Đăng nhập</button>
    </form>
</div>
</body>
</html>
