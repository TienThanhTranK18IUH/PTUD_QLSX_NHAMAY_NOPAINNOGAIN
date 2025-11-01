<div class="content">
    <h2>➕ Thêm nhân viên mới</h2>

    <form method="POST" action="index.php?controller=nhanvien&action=add">
        <table cellpadding="8" cellspacing="0">
            <tr>
                <td><b>Họ tên:</b></td>
                <td><input type="text" name="tenNhanVien" placeholder="Nhập họ tên nhân viên" required></td>
            </tr>

            <tr>
                <td><b>Giới tính:</b></td>
                <td>
                    <select name="gioiTinh" required>
                        <option value="">-- Chọn giới tính --</option>
                        <option value="Nam">Nam</option>
                        <option value="Nữ">Nữ</option>
                    </select>
                </td>
            </tr>

            <tr>
                <td><b>Ngày sinh:</b></td>
                <td><input type="date" name="ngaySinh"></td>
            </tr>

            <tr>
                <td><b>Địa chỉ:</b></td>
                <td><input type="text" name="diaChi" placeholder="Nhập địa chỉ..."></td>
            </tr>

            <tr>
                <td><b>Số điện thoại:</b></td>
                <td><input type="text" name="soDienThoai" placeholder="VD: 0987654321"></td>
            </tr>

            <tr>
                <td><b>Email:</b></td>
                <td><input type="email" name="email" placeholder="example@gmail.com"></td>
            </tr>

            <tr>
                <td><b>Chức vụ:</b></td>
                <td>
                    <select name="chucVu">
                        <option value="">-- Chọn chức vụ --</option>
                        <option value="Quản lý">Quản lý</option>
                        <option value="Xưởng trưởng">Xưởng trưởng</option>
                        <option value="Nhân viên kho">Nhân viên kho</option>
                        <option value="QC">QC</option>
                        <option value="Kỹ thuật">Kỹ thuật</option>
                        <option value="Công nhân">Công nhân</option>
                    </select>
                </td>
            </tr>

            <tr>
                <td><b>Lương (VNĐ):</b></td>
                <td><input type="number" name="luong" min="0" placeholder="Nhập lương cơ bản"></td>
            </tr>

            <tr>
                <td><b>Trạng thái:</b></td>
                <td>
                    <select name="trangThai">
                        <option value="HoatDong" selected>Đang làm</option>
                        <option value="Ngung">Đã nghỉ</option>
                    </select>
                </td>
            </tr>
        </table>

        <br>
        <input type="submit" value="💾 Lưu nhân viên" 
               style="background:#27ae60; color:white; padding:6px 12px; border:none; border-radius:4px; cursor:pointer;">
        <a href="index.php?controller=nhanvien&action=index" 
           style="margin-left:10px; text-decoration:none; color:#555;">⬅ Quay lại</a>
    </form>
</div>
