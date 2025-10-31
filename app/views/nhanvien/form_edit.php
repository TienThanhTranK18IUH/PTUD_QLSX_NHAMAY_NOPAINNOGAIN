<div class="content">
    <h2>✏️ Chỉnh sửa nhân viên</h2>
    <form method="POST" action="">
        <table>
            <tr><td>Mã nhân viên:</td><td><?php echo $nhanvien['maNguoiDung']; ?></td></tr>
            <tr><td>Tên đăng nhập:</td><td><?php echo $nhanvien['tenDangNhap']; ?></td></tr>
            <tr><td>Họ tên:</td><td><input type="text" name="hoTen" value="<?php echo $nhanvien['hoTen']; ?>" required></td></tr>
            <tr><td>Vai trò:</td>
                <td>
                    <select name="vaiTro">
                        <?php
                        $roles = array('QuanLy','XuongTruong','NhanVienKho','QC','KyThuat','CongNhan');
                        foreach ($roles as $r) {
                            $sel = ($r == $nhanvien['vaiTro']) ? 'selected' : '';
                            echo "<option value='$r' $sel>$r</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr><td>Trạng thái:</td>
                <td>
                    <select name="trangThai">
                        <option value="HoatDong" <?php if($nhanvien['trangThai']=='HoatDong') echo 'selected'; ?>>Hoạt động</option>
                        <option value="Ngung" <?php if($nhanvien['trangThai']=='Ngung') echo 'selected'; ?>>Ngừng</option>
                    </select>
                </td>
            </tr>
        </table>
        <br>
        <input type="submit" value="💾 Lưu thay đổi" style="background:#27ae60; color:white; padding:5px 10px;">
        <a href="index.php?controller=nhanvien&action=index">⬅ Quay lại</a>
    </form>
</div>