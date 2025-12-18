<h2 style="text-align:center;border-bottom:2px solid #007bff;padding-bottom:10px;">
📝 DANH SÁCH PHIẾU XUẤT KHO
</h2>

<?php if (isset($_GET['ok'])): ?>
<div class="alert success">✅ Lập phiếu thành công!</div>
<?php endif; ?>

<table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse:collapse; text-align:center;">
    <tr style="background:#007bff; color:#fff;">
        <th>Mã phiếu</th>
        <th>Ngày lập</th>
        <th>Người lập</th>
        <th>Thành phẩm</th>
        <th>Số lượng xuất</th>
    </tr>
    <?php if (!empty($dsPhieu)): ?>
        <?php foreach ($dsPhieu as $p): ?>
        <tr>
            <td><?php echo $p['maPhieu']; ?></td>
            <td><?php echo date('d/m/Y', strtotime($p['ngayXuat'])); ?></td>
            <td><?php echo $p['maNguoiLap']; ?></td>
            <td><?php echo $p['tenTP']; ?></td>
            <td><?php echo $p['soLuong']; ?></td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="5">Chưa có phiếu nào</td></tr>
    <?php endif; ?>
</table>

<br>
<a href="index.php?controller=phieuNhapXuat&action=taophieu" style="padding:10px 20px;background:#198754;color:#fff;border-radius:5px;text-decoration:none;">➕ Thêm phiếu xuất kho</a>

<style>
.alert.success{background:#d1e7dd;color:#0f5132;padding:10px;border-radius:5px;margin-bottom:10px;text-align:center;font-weight:bold}
table tr:nth-child(even){background:#f2f2f2}
table th, table td{padding:8px}
</style>
