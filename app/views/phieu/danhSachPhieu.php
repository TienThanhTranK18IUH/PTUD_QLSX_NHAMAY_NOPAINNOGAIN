<h2>📦 Danh sách Phiếu Nhập Kho Thành Phẩm</h2>

<?php
// Thông báo khi lưu thành công hoặc lỗi
if (isset($_GET['ok']) && $_GET['ok'] == 1) {
    echo '<p style="color: green; font-weight: bold;">✅ Lưu phiếu nhập kho thành công!</p>';
}
if (isset($_GET['error'])) {
    if ($_GET['error'] == 1) {
        echo '<p style="color: red;">❌ Dữ liệu không hợp lệ. Vui lòng kiểm tra lại.</p>';
    } elseif ($_GET['error'] == 2) {
        echo '<p style="color: red;">❌ Có lỗi khi lưu vào cơ sở dữ liệu.</p>';
    }
}
?>

<?php if (!empty($dsPhieu)): ?>
<table border="1" cellpadding="6" cellspacing="0" style="border-collapse: collapse; width: 100%;">
    <tr style="background: #f2f2f2; text-align: center;">
        <th>Mã phiếu</th>
        <th>Kho</th>
        <th>Ngày nhập</th>
        <th>Người lập</th>
        <th>Trạng thái</th>
        <th>Mã TP</th>
        <th>Tên thành phẩm</th>
        <th>Số lượng</th>
    </tr>
    <?php foreach ($dsPhieu as $p): ?>
    <tr style="text-align: center;">
        <td><?php echo htmlspecialchars($p['maPhieu']); ?></td>
        <td><?php echo htmlspecialchars(isset($p['tenKho']) ? $p['tenKho'] : $p['maKho']); ?></td>
        <td><?php echo htmlspecialchars($p['ngayNhap']); ?></td>
        <td><?php echo htmlspecialchars(isset($p['nguoiLap']) ? $p['nguoiLap'] : $p['maNguoiLap']); ?></td>
        <td><?php echo htmlspecialchars($p['trangThai']); ?></td>
        <td><?php echo htmlspecialchars($p['maTP']); ?></td>
        <td><?php echo htmlspecialchars($p['tenTP']); ?></td>
        <td><?php echo htmlspecialchars($p['soLuong']); ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
<p>⚠️ Chưa có phiếu nhập kho thành phẩm nào.</p>
<?php endif; ?>

<br>
<a href="index.php?controller=phieu&action=pnk_taoPhieu"
   style="display:inline-block; background:#4CAF50; color:white; padding:8px 15px;
          border-radius:5px; text-decoration:none; font-weight:bold;">
   ➕ Thêm phiếu nhập kho mới
</a>
