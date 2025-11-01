<div class="content">
    <h2>👥 Danh sách nhân viên</h2>
    <a href="index.php?controller=nhanvien&action=add"
       style="background:#27ae60; color:white; padding:6px 12px; border-radius:5px; text-decoration:none;">
       ➕ Thêm mới
    </a>
    <br><br>

    <table border="1" cellpadding="8" cellspacing="0" width="100%" style="border-collapse: collapse;">
        <thead style="background-color:#f0f0f0; text-align:center;">
            <tr>
                <th>Mã NV</th>
                <th>Họ tên</th>
                <th>Giới tính</th>
                <th>Ngày sinh</th>
                <th>Địa chỉ</th>
                <th>Điện thoại</th>
                <th>Email</th>
                <th>Chức vụ</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($nhanviens)) {
                foreach ($nhanviens as $nv) {
                    echo "<tr>";
                    echo "<td align='center'>{$nv['maNhanVien']}</td>";
                    echo "<td>{$nv['tenNhanVien']}</td>";
                    echo "<td align='center'>{$nv['gioiTinh']}</td>";
                    echo "<td align='center'>{$nv['ngaySinh']}</td>";
                    echo "<td>{$nv['diaChi']}</td>";
                    echo "<td align='center'>{$nv['soDienThoai']}</td>";
                    echo "<td>{$nv['email']}</td>";
                    echo "<td align='center'>{$nv['chucVu']}</td>";
                    echo "<td align='center'>{$nv['trangThai']}</td>";
                    echo "<td align='center'>
                            <a href='index.php?controller=nhanvien&action=edit&id={$nv['maNhanVien']}' style='color:#2980b9;'>✏️ Sửa</a> |
                            <a href='index.php?controller=nhanvien&action=delete&id={$nv['maNhanVien']}' onclick=\"return confirm('Xóa nhân viên này?');\" style='color:#c0392b;'>🗑️ Xóa</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10' align='center'>Không có dữ liệu nhân viên</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
