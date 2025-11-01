<div class="content">
    <h2>✏️ Chỉnh sửa nhân viên</h2>

    <form method="POST" action="">
        <table cellpadding="8" cellspacing="0">
            <tr>
                <td><b>Mã nhân viên:</b></td>
                <td><?php echo htmlspecialchars($nhanvien['maNhanVien']); ?></td>
            </tr>

            <tr>
                <td><b>Họ tên:</b></td>
                <td><input type="text" name="tenNhanVien" value="<?php echo htmlspecialchars($nhanvien['tenNhanVien']); ?>" required></td>
            </tr>

            <tr>
                <td><b>Giới tính:</b></td>
                <td>
                    <select name="gioiTinh" required>
                        <option value="Nam" <?php if ($nhanvien['gioiTinh'] == 'Nam') echo 'selected'; ?>>Nam</option>
                        <option value="Nữ" <?php if ($nhanvien['gioiTinh'] == 'Nữ') echo 'selected'; ?>>Nữ</option>
                    </select>
                </td>
            </tr>

            <tr>
                <td><b>Ngày sinh:</b></td>
                <td><input type="date" name="ngaySinh" value="<?php echo htmlspecialchars($nhanvien['ngaySinh']); ?>"></td>
            </tr>

            <tr>
                <td><b>Địa chỉ:</b></td>
                <td><input type="text" name="diaChi" value="<?php echo htmlspecialchars($nhanvien['diaChi']); ?>"></td>
            </tr>

            <tr>
                <td><b>Số điện thoại:</b></td>
                <td><input type="text" name="soDienThoai" value="<?php echo htmlspecialchars($nhanvien['soDienThoai']); ?>"></td>
            </tr>

            <tr>
                <td><b>Email:</b></td>
                <td><input type="email" name="email" value="<?php echo htmlspecialchars($nhanvien['email']); ?>"></td>
            </tr>

            <tr>
                <td><b>Chức vụ:</b></td>
                <td><input type="text" name="chucVu" value="<?php echo htmlspecialchars($nhanvien['chucVu']); ?>"></td>
            </tr>

            <tr>
                <td><b>Trạng thái:</b></td>
                <td>
                    <select name="trangThai">
                        <option value="HoatDong" <?php if ($nhanvien['trangThai'] == 'Đang làm') echo 'selected'; ?>>Đang làm</option>
                        <option value="Ngung" <?php if ($nhanvien['trangThai'] == 'Đã nghỉ') echo 'selected'; ?>>Đã nghỉ</option>
                    </select>
                </td>
            </tr>
        </table>

        <br>
        <input type="submit" value="💾 Lưu thay đổi" 
               style="background:#27ae60; color:white; padding:6px 12px; border:none; border-radius:4px;">
        <a href="index.php?controller=nhanvien&action=index" 
           style="margin-left:10px; text-decoration:none; color:#555;">⬅ Quay lại</a>
    </form>
</div>
