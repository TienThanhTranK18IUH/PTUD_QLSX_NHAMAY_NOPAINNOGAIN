<div class="content">
    <h2>📋 Danh sách Kế hoạch sản xuất</h2>
    <table border="1" cellpadding="8" cellspacing="0" width="100%">
        <thead style="background-color:#f0f0f0;">
            <tr>
                <th>Mã KH</th>
                <th>Xưởng</th>
                <th>Sản phẩm</th>
                <th>Đơn hàng</th>
                <th>Ngày bắt đầu</th>
                <th>Ngày kết thúc</th>
                <th>Tổng SL</th>
                <th>Trạng thái</th>
                <th>Mã NL</th>
                <th>Tên nguyên liệu</th> <!-- 🆕 Cột mới -->
                <th>SL Nguyên liệu</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if (!empty($kehoachs)) {
            foreach ($kehoachs as $kh) {
                echo '<tr>';
                echo '<td>' . $kh['maKeHoach'] . '</td>';
                echo '<td>' . $kh['tenXuong'] . '</td>';
                echo '<td>' . $kh['tenSP'] . '</td>';
                echo '<td>' . $kh['maDonHang'] . '</td>';
                echo '<td>' . $kh['ngayBatDau'] . '</td>';
                echo '<td>' . $kh['ngayKetThuc'] . '</td>';
                echo '<td align="center">' . $kh['tongSoLuong'] . '</td>';
                echo '<td>' . $kh['trangThai'] . '</td>';
                echo '<td>' . $kh['maNguyenLieu'] . '</td>';
                echo '<td>' . $kh['tenNguyenLieu'] . '</td>'; // 🆕 Cột mới
                echo '<td align="center">' . $kh['soLuongNguyenLieu'] . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="11">Không có dữ liệu kế hoạch</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>
