<div class="content">
    <h2>➕ Thêm người dùng mới</h2>

    <?php
    if (isset($_GET['ok']) && $_GET['ok'] == 1) {
        echo '<p style="color:green;font-weight:bold;">✅ Thêm người dùng thành công!</p>';
    } elseif (isset($_GET['error'])) {
        echo '<p style="color:red;font-weight:bold;">❌ Lỗi khi lưu dữ liệu!</p>';
    }
    ?>

    <form method="POST" action="index.php?controller=nhanvien&action=add"
          style="max-width:650px; background:#fafafa; padding:20px; border-radius:8px; box-shadow:0 0 6px rgba(0,0,0,0.1);">

        <label><b>Tên đăng nhập:</b></label><br>
        <input type="text" name="tenDangNhap" placeholder="Tên đăng nhập" required style="width:100%;"><br><br>

        <label><b>Mật khẩu:</b></label><br>
        <input type="password" name="matKhau" placeholder="Mật khẩu" required style="width:100%;"><br><br>

        <label><b>Họ tên:</b></label><br>
        <input type="text" name="hoTen" placeholder="Nhập họ tên" required style="width:100%;"><br><br>

        <label><b>Giới tính:</b></label><br>
        <select name="gioiTinh" style="width:100%;" required>
            <option value="">-- Chọn giới tính --</option>
            <option value="Nam">Nam</option>
            <option value="Nữ">Nữ</option>
        </select><br><br>

        <label><b>Ngày sinh:</b></label><br>
        <input type="date" name="ngaySinh" style="width:100%;"><br><br>

        <label><b>Địa chỉ:</b></label><br>
        <input type="text" name="diaChi" placeholder="Nhập địa chỉ" style="width:100%;"><br><br>

        <label><b>Email:</b></label><br>
        <input type="email" name="email" placeholder="example@gmail.com" style="width:100%;"><br><br>

        <label><b>Số điện thoại:</b></label><br>
        <input type="text" name="soDienThoai" placeholder="VD: 0912345678" style="width:100%;"><br><br>

        <label><b>Bộ phận:</b></label><br>
        <select name="maBoPhan" style="width:100%;" required>
            <option value="">-- Chọn bộ phận --</option>
            <option value="BP001">Cắt May</option>
            <option value="BP002">Hoàn Thiện</option>
            <option value="BP003">Kho</option>
            <option value="BP004">Kiểm Định</option>
            <option value="BP005">Kỹ Thuật</option>
        </select><br><br>

        <label><b>Vai trò:</b></label><br>
        <select name="vaiTro" style="width:100%;" required>
            <option value="">-- Chọn vai trò --</option>
            <option value="QuanLy">Quản lý</option>
            <option value="XuongTruong">Xưởng trưởng</option>
            <option value="NhanVienKho">Nhân viên kho</option>
            <option value="QC">QC</option>
            <option value="KyThuat">Kỹ thuật</option>
            <option value="CongNhan">Công nhân</option>
        </select><br><br>

    
        <label><b>Trạng thái:</b></label><br>
        <select name="trangThai" style="width:100%;">
            <option value="HoatDong" selected>Hoạt động</option>
            <option value="Ngung">Ngừng</option>
        </select><br><br>

        <button type="submit"
                style="background:#27ae60;color:white;padding:8px 14px;border:none;border-radius:5px;cursor:pointer;">
            💾 Lưu người dùng
        </button>

        <a href="index.php?controller=nhanvien&action=index" 
           style="margin-left:10px;text-decoration:none;background:#ccc;padding:8px 14px;border-radius:5px;color:black;">
           ⬅ Quay lại
        </a>
    </form>
</div>
