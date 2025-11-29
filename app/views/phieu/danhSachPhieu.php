<?php
// Thông báo khi lưu thành công hoặc lỗi
if (isset($_GET['ok']) && $_GET['ok'] == 1) {
    echo '<script>alert("✅ Lập phiếu nhập kho thành phẩm thành công!");</script>';
}

if (isset($_GET['error'])) {
    if ($_GET['error'] == 1) {
        echo '<p style="color: red;">❌ Dữ liệu không hợp lệ. Vui lòng kiểm tra lại.</p>';
    } elseif ($_GET['error'] == 2) {
        echo '<p style="color: red;">❌ Có lỗi khi lưu vào cơ sở dữ liệu.</p>';
    }
}
?>
<h2>📦 DANH SÁCH PHIẾU NHẬP KHO THÀNH PHẨM</h2>

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
<a href="index.php?controller=phieu&action=pnk_taoPhieu" style="display:inline-block; background:#4CAF50; color:white; padding:8px 15px;
          border-radius:5px; text-decoration:none; font-weight:bold;">
    ➕ Thêm phiếu nhập kho mới
</a>
<style>
/* Container để bảng có scroll ngang khi nhỏ màn hình */
.table-container {
    overflow-x: auto;
    margin-top: 20px;
}

/* Bảng */
table {
    width: 100%;
    border-collapse: collapse;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Header */
table th {
    background: #f2f2f2;
    padding: 10px;
    text-align: center;
    font-weight: 600;
    border: 1px solid #ccc;
}

/* Các ô dữ liệu */
table td {
    padding: 10px;
    text-align: center;
    border: 1px solid #ccc;
    font-size: 14px;
}

/* Nền xen kẽ */
table tr:nth-child(even) td {
    background: #fafafa;
}

/* Hover hiệu ứng */
table tr:hover td {
    background: #e6f2ff;
}

/* Thông báo success / error */
p.alert-success {
    color: green;
    font-weight: bold;
}

p.alert-error {
    color: red;
    font-weight: bold;
}

/* Nút thêm phiếu */
a.btn-add {
    display: inline-block;
    background: #4CAF50;
    color: white;
    text-decoration: none;
    padding: 8px 15px;
    border-radius: 5px;
    font-weight: bold;
    transition: 0.3s;
}

a.btn-add:hover {
    background: #3e8e41;
}

/* Responsive */
@media(max-width: 700px) {

    table th,
    table td {
        font-size: 13px;
        padding: 8px;
    }
}
</style>