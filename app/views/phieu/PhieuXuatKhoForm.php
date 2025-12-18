<?php
/*
Biến controller truyền sang:
$maPhieu
$ngayXuat
$nguoiLap
$maNguoiLap
$dsTP  // danh sách TP đạt
*/
?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Arial;
    background: #f4f6f9;
    margin: 0;
    padding: 0;
}

.phieu-box {
    max-width: 750px;
    margin: 30px auto;
    background: #fff;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 8px 20px rgba(0,0,0,.15);
}

.phieu-box h2 {
    text-align: center;
    border-bottom: 2px solid #0d6efd;
    padding-bottom: 12px;
    margin-bottom: 25px;
}

.row {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
}

.col {
    flex: 1;
    display: flex;
    flex-direction: column;
}

label {
    font-weight: 600;
    margin-bottom: 6px;
}

input, select {
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
}

input[readonly] {
    background: #eee;
}

.actions {
    display: flex;
    gap: 12px;
    margin-top: 25px;
}

button {
    flex: 1;
    background: #198754;
    color: #fff;
    border: none;
    padding: 12px;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    transition: background .3s;
}

button:hover {
    background: #157347;
}

.back {
    flex: 1;
    background: #6c757d;
    color: #fff;
    text-align: center;
    padding: 12px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    transition: background .3s;
}

.back:hover {
    background: #565e64;
}

/* POPUP */
.popup {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 25px;
    border-radius: 8px;
    color: #fff;
    font-weight: bold;
    animation: fadein .5s;
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(0,0,0,.25);
}

.success { background: #198754; }
.error { background: #dc3545; }

@keyframes fadein {
    from {opacity:0; transform: translateY(-10px);}
    to {opacity:1;}
}

@media(max-width: 768px) {
    .row { flex-direction: column; }
}
</style>

<?php if (isset($_GET['ok'])): ?>
<div class="popup success">✅ Lập phiếu xuất kho thành công</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<div class="popup error">❌ Số lượng xuất vượt quá số lượng tồn</div>
<?php endif; ?>

<div class="phieu-box">
<h2>📦 LẬP PHIẾU XUẤT KHO THÀNH PHẨM</h2>
<form method="post" action="index.php?controller=phieuNhapXuat&action=luuphieu">

<div class="row">
    <div class="col">
        <label>Mã phiếu</label>
        <input type="text" value="<?php echo $maPhieu; ?>" readonly>
        <input type="hidden" name="maPhieu" value="<?php echo $maPhieu; ?>">
    </div>
    <div class="col">
        <label>Ngày lập</label>
        <input type="text" value="<?php echo $ngayXuat; ?>" readonly>
    </div>
</div>

<div class="row">
    <div class="col">
        <label>Người lập</label>
        <input type="text" value="<?php echo $nguoiLap; ?>" readonly>
        <input type="hidden" name="maNguoiLap" value="<?php echo $maNguoiLap; ?>">
    </div>
</div>

<hr>

<div class="row">
    <div class="col">
        <label>Chọn mã thành phẩm</label>
        <select name="maTP" id="maTP" onchange="fillTP()" required>
            <option value="">-- Chọn thành phẩm --</option>
            <?php foreach ($dsTP as $tp): ?>
            <option value="<?php echo $tp['maTP']; ?>"
                data-ten="<?php echo $tp['tenTP']; ?>"
                data-kehoach="<?php echo $tp['maKeHoach']; ?>"
                data-xuong="<?php echo $tp['maXuong']; ?>"
                data-soluong="<?php echo $tp['soLuong']; ?>">
                <?php echo $tp['maTP']; ?> - <?php echo $tp['tenTP']; ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="row">
    <div class="col">
        <label>Tên thành phẩm</label>
        <input type="text" id="tenTP" readonly>
    </div>
    <div class="col">
        <label>Mã kế hoạch</label>
        <input type="text" id="maKeHoach" readonly>
    </div>
</div>

<div class="row">
    <div class="col">
        <label>Mã xưởng</label>
        <input type="text" id="maXuong" readonly>
    </div>
    <div class="col">
        <label>Số lượng tồn</label>
        <input type="number" id="soLuongTon" readonly>
        <input type="hidden" name="soLuongTon" id="soLuongTonHidden">
    </div>
</div>

<div class="row">
    <div class="col">
        <label>Số lượng xuất kho</label>
        <input type="number" name="soLuongXuat" required min="1">
    </div>
</div>

<div class="actions">
    <a class="back" href="index.php?controller=phieuNhapXuat&action=xuatkhotp">⬅ Quay lại danh sách</a>
    <button type="submit">✅ Xác nhận</button>
</div>

</form>
</div>

<script>
function fillTP() {
    var sel = document.getElementById('maTP');
    var opt = sel.options[sel.selectedIndex];

    if (opt.value !== '') {
        document.getElementById('tenTP').value = opt.getAttribute('data-ten');
        document.getElementById('maKeHoach').value = opt.getAttribute('data-kehoach');
        document.getElementById('maXuong').value = opt.getAttribute('data-xuong');
        document.getElementById('soLuongTon').value = opt.getAttribute('data-soluong');
        document.getElementById('soLuongTonHidden').value = opt.getAttribute('data-soluong');
    } else {
        document.getElementById('tenTP').value = '';
        document.getElementById('maKeHoach').value = '';
        document.getElementById('maXuong').value = '';
        document.getElementById('soLuongTon').value = '';
        document.getElementById('soLuongTonHidden').value = '';
    }
}
</script>
