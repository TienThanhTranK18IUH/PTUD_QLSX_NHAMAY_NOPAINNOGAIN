<h2>Danh sách Phiếu Xuất Kho Thành Phẩm</h2>

<?php
if(isset($_GET['ok']) && $_GET['ok']==1) {
    echo '<p style="color:green;font-weight:bold;">✅ Lập phiếu xuất kho thành công!</p>';
}

if(empty($data)) {
    echo "<p>Chưa có phiếu xuất kho nào.</p>";
} else {
?>
<table border="1" cellpadding="6" cellspacing="0">
    <tr style="background:#f0f0f0;">
        <th>Mã Phiếu</th>
        <th>Mã Kho</th>
        <th>Ngày Xuất</th>
        <th>Người Lập</th>
        <th>Mã Đơn Hàng</th>
        <th>Mã Thành Phẩm</th>
        <th>Số Lượng</th>
    </tr>
    <?php foreach($data as $row) { ?>
    <tr>
        <td><?php echo $row['maPhieu'];?></td>
        <td><?php echo $row['maKho'];?></td>
        <td><?php echo $row['ngayXuat'];?></td>
        <td><?php echo $row['maNguoiLap'];?></td>
        <td><?php echo $row['maDonHang'];?></td>
        <td><?php echo $row['maTP'];?></td>
        <td><?php echo $row['soLuong'];?></td>
    </tr>
    <?php } ?>
</table>
<?php } ?>
<p><a href="index.php?controller=phieuNhapXuat&action=taophieu">➕ Thêm phiếu xuất kho mới</a></p>