<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            height: 100vh;

            /* Ảnh nền + lớp mờ */
            background: 
                linear-gradient(rgba(255,255,255,0.6), rgba(255,255,255,0.6)),
                url('/PTUD_QLSX_NHAMAY_NOPAINNOGAIN/public/img/background.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /* Hộp chào mừng */
        .welcome-box {
            text-align: center;
            margin-bottom: 20px;
            padding: 20px 30px;
            max-width: 600px;
        }

        .welcome-box h3 {
            margin: 5px 0;
            font-size: 26px;
            color: #2b4eff;
        }

        .welcome-box p {
            margin: 3px 0;
            font-size: 16px;
            font-weight: 500;
        }

        /* Form login */
        .login-container {
            width: 100%;
            max-width: 450px;
        }

        .login-form {
            background: #ffffff;
            padding: 30px 35px;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }

        .login-form h2 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 26px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        label {
            font-size: 16px;
            font-weight: bold;
            margin-top: 15px;
            display: block;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 14px;
            margin-top: 7px;
            border: 1px solid #ccc;
            border-radius: 10px;
            outline: none;
            font-size: 16px;
            transition: 0.3s;

            /* KEY FIX – không bị lệch trái/phải */
            box-sizing: border-box;
        }

        input:focus {
            border-color: #2b4eff;
            box-shadow: 0 0 5px #a8c1ff;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #2b4eff;
            border: none;
            color: #fff;
            font-size: 18px;
            border-radius: 10px;
            margin-top: 20px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #1e36c0;
        }

        .error {
            background: #ffdedf;
            color: #d60000;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
            text-align: center;
            font-weight: bold;
        }

        /* Icon khóa */
        .login-title-icon {
            font-size: 23px;
        }
    </style>
</head>
<body>

<div class="welcome-box">
    <h3>Xin chào!</h3>
    <p>Bạn đang truy cập hệ thống quản lý sản xuất nhà máy</p>
    <p><b>Công ty NO PAIN NO GAIN</b></p>
</div>

<div class="login-container">
    <form method="POST" class="login-form">
        <h2><span class="login-title-icon">🔒</span> Đăng nhập</h2>

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
