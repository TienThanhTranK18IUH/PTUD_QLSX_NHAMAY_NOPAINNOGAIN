<?php declare(strict_types=1); ?>
<div class="content">
    <h2>✏️ Chỉnh sửa nhân viên</h2>

    <form method="POST" action="">
        <table cellpadding="8" cellspacing="0">
            <tr>
                <td><b>Mã nhân viên:</b></td>
                <td><?php echo htmlspecialchars($nhanvien['maNguoiDung']); ?></td>
            </tr>

            <tr>
                <td><b>Tên đăng nhập:</b></td>
                <td><input type="text" name="tenDangNhap" value="<?php echo htmlspecialchars($nhanvien['tenDangNhap']); ?>" required></td>
            </tr>

            <tr>
                <td><b>Họ tên:</b></td>
                <td><input type="text" name="hoTen" value="<?php echo htmlspecialchars($nhanvien['hoTen']); ?>" required></td>
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
                <td><b>Vai trò (chức vụ):</b></td>
                <td>
                    <select name="vaiTro" required>
                        <?php
                        $roles = array('QuanLy' => 'Quản lý', 'XuongTruong' => 'Xưởng trưởng', 'NhanVienKho' => 'Nhân viên kho', 'QC' => 'QC', 'KyThuat' => 'Kỹ thuật', 'CongNhan' => 'Công nhân');
                        foreach ($roles as $key => $label) {
                            $selected = ($nhanvien['vaiTro'] == $key) ? 'selected' : '';
                            echo '<option value="' . $key . '" ' . $selected . '>' . $label . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td><b>Bộ phận:</b></td>
                <td>
                    <select name="maBoPhan" id="maBoPhan" style="width:100%;" required onchange="updateXuong()">
                        <option value="">-- Chọn bộ phận --</option>
                        <?php
                        // Mảng bộ phận và tên xưởng tương ứng
                        $boPhanXuong = array(
                            'BP001' => 'Cắt da giày',
                            'BP002' => 'May da giày',
                            'BP003' => 'Dán da giày',
                            'BP004' => 'Đóng đế giày',
                            'BP005' => 'Hoàn thiện',
                            'BP006' => 'Kho'
                        );
                        foreach ($boPhanXuong as $maBP => $tenXuong) {
                            $selected = ($nhanvien['maBoPhan'] == $maBP) ? 'selected' : '';
                            echo '<option value="' . $maBP . '" ' . $selected . '>' . $tenXuong . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td><b>Trạng thái:</b></td>
                <td>
                    <select name="trangThai">
                        <option value="HoatDong" <?php if ($nhanvien['trangThai'] == 'HoatDong') echo 'selected'; ?>>Hoạt động</option>
                        <option value="Ngung" <?php if ($nhanvien['trangThai'] == 'Ngung') echo 'selected'; ?>>Ngừng</option>
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

<script>
// Tự động cập nhật tên xưởng khi chọn bộ phận
var boPhanXuong = <?php echo json_encode($boPhanXuong); ?>;
function updateXuong() {
    var bp = document.getElementById('maBoPhan').value;
    document.getElementById('tenXuong').value = boPhanXuong[bp] ? boPhanXuong[bp] : '';
}
</script>
