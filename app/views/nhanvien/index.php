<div class="content">
    <h2>👥 Danh sách nhân viên</h2>
    <a href="index.php?controller=nhanvien&action=add" style="background:#27ae60; color:white; padding:5px 10px; border-radius:5px;">➕ Thêm mới</a>
    <br><br>

    <table border="1" cellpadding="8" cellspacing="0" width="100%">
        <thead style="background-color:#f0f0f0;">
            <tr>
                <th>Mã NV</th>
                <th>Tên đăng nhập</th>
                <th>Họ tên</th>
                <th>Vai trò</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($nhanviens)) {
                foreach ($nhanviens as $nv) {
                    echo "<tr>";
                    echo "<td>" . $nv['maNguoiDung'] . "</td>";
                    echo "<td>" . $nv['tenDangNhap'] . "</td>";
                    echo "<td>" . $nv['hoTen'] . "</td>";
                    echo "<td>" . $nv['vaiTro'] . "</td>";
                    echo "<td>" . $nv['trangThai'] . "</td>";
                    echo "<td>
                            <a href='index.php?controller=nhanvien&action=edit&id=" . $nv['maNguoiDung'] . "'>✏️ Sửa</a> |
                            <a href='index.php?controller=nhanvien&action=delete&id=" . $nv['maNguoiDung'] . "' onclick=\"return confirm('Xóa nhân viên này?');\">🗑️ Xóa</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Không có dữ liệu nhân viên</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>