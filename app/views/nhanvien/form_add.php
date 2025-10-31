<div class="content">
    <h2>➕ Thêm nhân viên mới</h2>
    <form method="POST" action="index.php?controller=nhanvien&action=add">
        <table>
            <tr><td>Tên đăng nhập:</td><td><input type="text" name="tenDangNhap" required></td></tr>
            <tr><td>Mật khẩu:</td><td><input type="password" name="matKhau" required></td></tr>
            <tr><td>Họ tên:</td><td><input type="text" name="hoTen" required></td></tr>
            <tr>
                <td>Vai trò:</td>
                <td>
                    <select name="vaiTro">
                        <option value="QuanLy">Quản lý</option>
                        <option value="XuongTruong">Xưởng trưởng</option>
                        <option value="NhanVienKho">Nhân viên kho</option>
                        <option value="QC">QC</option>
                        <option value="KyThuat">Kỹ thuật</option>
                        <option value="CongNhan">Công nhân</option>
                    </select>
                </td>
            </tr>
        </table>
        <br>
        <input type="submit" value="Lưu" style="background:#27ae60; color:white; padding:5px 10px;">
        <a href="index.php?controller=nhanvien&action=index">⬅ Quay lại</a>
    </form>
</div>