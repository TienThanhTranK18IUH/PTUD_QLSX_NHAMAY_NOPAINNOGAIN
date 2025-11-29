<style>
/* Khối form */
.phieu-form {
    max-width: 750px;
    margin: 0 auto;
    padding: 22px;
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    font-family: "Segoe UI", sans-serif;
    display: flex;
    flex-direction: column;
    gap: 18px;
}

/* Hàng – cột */
.phieu-form .row {
    display: flex;
    gap: 16px;
}

.phieu-form .col {
    flex: 1;
    display: flex;
    flex-direction: column;
}

/* Label */
.phieu-form label {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
}

/* Input + Select */
.phieu-form input[type=text],
.phieu-form select {
    padding: 10px 12px;
    font-size: 14px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    background: #f9fafb;
    transition: 0.2s ease;
}

.phieu-form input[type=text]:focus,
.phieu-form select:focus {
    border-color: #2563eb;
    background: #fff;
    outline: none;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.25);
}

.phieu-form input[readonly] {
    background: #f3f4f6;
    color: #6b7280;
}

/* Nút hành động */
.form-actions {
    margin-top: 10px;
    display: flex;
    gap: 12px;
}

.form-actions button {
    flex: 1;
    background: #16a34a;
    border: none;
    padding: 12px;
    border-radius: 8px;
    color: white;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s;
}

.form-actions button:hover {
    background: #0f8a3b;
}

.form-actions a {
    flex: 1;
    text-align: center;
    background: #6b7280;
    color: white;
    padding: 12px;
    border-radius: 8px;
    font-weight: bold;
    text-decoration: none;
    transition: 0.2s;
}

.form-actions a:hover {
    background: #565e64;
}

/* Alert */
.alert {
    max-width: 750px;
    margin: 0 auto 15px auto;
    padding: 12px;
    border-radius: 8px;
    font-weight: 600;
    text-align: center;
}

.alert.success {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #86efac;
}

.alert.error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}

/* Responsive */
@media (max-width: 700px) {
    .phieu-form .row {
        flex-direction: column;
    }
}
</style>
<?php
// Hiển thị thông báo lỗi / thành công
$tenSP = isset($_GET['tenSP']) ? $_GET['tenSP'] : '';
$maDonHang = isset($_GET['maDonHang']) ? $_GET['maDonHang'] : '';

if (isset($_GET['error'])) {
    switch($_GET['error']) {
        case 1: echo '<p class="alert error">❌ Đơn hàng không hợp lệ.</p>'; break;
        case 2: echo '<p class="alert error">❌ Thành phẩm đã xuất kho!</p>'; break;
        case 3: echo '<p class="alert error">❌ Thành phẩm không tồn tại.</p>'; break;
        case 4: echo '<p class="alert error">❌ Số lượng '.$tenSP.' không đủ để xuất kho!</p>'; break;
        case 5: echo '<p class="alert error">❌ Đơn hàng '.$maDonHang.' đã xuất kho thành công. Vui lòng kiểm tra lại!</p>'; break;
    }
}

if (isset($_GET['ok']) && $_GET['ok']==1) {
    echo '<p class="alert success">✅ Lập phiếu xuất kho thành công!</p>';
}

// Lấy danh sách phiếu xuất kho hiện tại
$pxObj = new PhieuXuatKhoTP();
$dsPhieu = $pxObj->getAll();
?>

<h2
    style="text-align:center; font-weight:bold; border-bottom:2px solid #007bff; padding-bottom:10px; margin-bottom:20px;">
    📝 LẬP PHIẾU XUẤT KHO THÀNH PHẨM
</h2>

<form method="post" action="index.php?controller=phieuNhapXuat&action=luuphieu" class="phieu-form">
    <div class="row">
        <div class="col">
            <label>Mã phiếu:</label>
            <input type="text" name="maPhieu" value="<?php echo $maPhieu; ?>" readonly>
        </div>
        <div class="col">
            <label>Ngày lập phiếu:</label>
            <input type="text" name="ngayXuat" value="<?php echo date('d-m-Y'); ?>" readonly>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <label>Mã kho:</label>
            <input type="text" name="maKho" value="K002 - Kho Thành Phẩm" readonly>
        </div>
        <div class="col">
            <label>Người lập:</label>
            <input type="text" name="maNguoiLap" value="<?php echo $maNguoiLap; ?>" readonly>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <label>Chọn mã đơn hàng:</label>
            <select name="maDonHang" id="maDonHang" onchange="layThongTinDonHang()" required>
                <?php if (!empty($dsDonHang)) : ?>
                <option value="">-- Chọn đơn hàng --</option>
                <?php foreach ($dsDonHang as $dh): ?>
                <option value="<?= $dh['maDonHang'] ?>" data-maTP="<?= $dh['maTP'] ?>" data-tenTP="<?= $dh['tenTP'] ?>"
                    data-soluong="<?= $dh['soLuongDH'] ?>">
                    <?= $dh['maDonHang'] ?> - <?= $dh['tenTP'] ?>
                </option>
                <?php endforeach; ?>
                <?php else: ?>
                <option value="" disabled>⚠️ Không có mã đơn hàng phù hợp để lập phiếu</option>
                <?php endif; ?>
            </select>

        </div>
        <div class="col">
            <label>Mã Thành phẩm:</label>
            <input type="text" id="maTP" name="maTP" readonly>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <label>Tên Thành phẩm:</label>
            <input type="text" id="tenTP" name="tenTP" readonly>
        </div>
        <div class="col">
            <label>Số lượng xuất kho:</label>
            <input type="text" id="soLuong" name="soLuong" readonly>
        </div>
    </div>

    <div class="form-actions">
        <a href="index.php?controller=phieuNhapXuat&action=xuatkhotp">⬅ Quay lại</a>
        <button type="submit">✅ Xác nhận</button>
    </div>
</form>

<script type="text/javascript">
function layThongTinDonHang() {
    var select = document.getElementById('maDonHang');
    var opt = select.options[select.selectedIndex];
    if (opt && opt.value != '') {
        document.getElementById('maTP').value = opt.getAttribute('data-maTP');
        document.getElementById('tenTP').value = opt.getAttribute('data-tenTP');
        document.getElementById('soLuong').value = opt.getAttribute('data-soluong');
    } else {
        document.getElementById('maTP').value = '';
        document.getElementById('tenTP').value = '';
        document.getElementById('soLuong').value = '';
    }
}
</script>