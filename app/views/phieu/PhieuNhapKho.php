<h2 style="
    text-align: center; 
    font-weight: bold; 
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
    border-bottom: 2px solid #007bff; 
    padding-bottom: 10px; 
    margin-bottom: 20px;
">
    📝 LẬP PHIẾU NHẬP KHO THÀNH PHẨM
</h2>

<?php
// Thông báo
if (isset($_GET['ok']) && $_GET['ok'] == 1) {
    echo '<p class="alert success">✅ Phiếu nhập kho đã được lưu thành công!</p>';
} elseif (isset($_GET['error'])) {
    $msg = '';
    if ($_GET['error'] == 2) $msg = '❌ Lỗi khi lưu dữ liệu vào cơ sở dữ liệu.';
    if ($_GET['error'] == 3) $msg = '❌ Thành phẩm này đã lập phiếu trước đó.';
    if ($msg) echo '<p class="alert error">'.$msg.'</p>';
}
?>

<form method="post" action="index.php?controller=phieu&action=pnk_luuPhieu" class="phieu-form">

    <div class="row">
        <div class="col">
            <label>Mã phiếu:</label>
            <input type="text" name="maPhieu" value="<?php echo htmlspecialchars($maPhieu); ?>" readonly>
        </div>
        <div class="col">
            <label>Ngày nhập:</label>
            <input type="text" value="<?php echo date('d-m-Y'); ?>" readonly>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <label>Kho:</label>
            <select name="maKho" required>
                <option value="">-- Chọn kho --</option>
                <?php
                if (!empty($dsKho)) {
                    foreach ($dsKho as $k) {
                        $selected = ($k['maKho'] == 'K002') ? 'selected' : '';
                        echo '<option value="'.$k['maKho'].'" '.$selected.'>'.$k['tenKho'].'</option>';
                    }
                }
                ?>
            </select>
        </div>

        <div class="col">
            <label>Thành phẩm:</label>
            <select name="maTP" id="maTP" onchange="layThongTinTP()" required>
                <option value="">-- Chọn thành phẩm --</option>
                <?php
                if (!empty($dsThanhPham)) {
                    foreach ($dsThanhPham as $tp) {
                        echo '<option value="'.$tp['maTP'].'"
                            data-ten="'.$tp['tenTP'].'"
                            data-soluong="'.$tp['soLuong'].'">
                            '.$tp['tenTP'].'
                        </option>';
                    }
                }
                ?>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <label>Tên thành phẩm:</label>
            <input type="text" id="tenTP_display" readonly>
        </div>
        <div class="col">
            <label>Số lượng đạt chuẩn:</label>
            <input type="text" id="soLuong_display" readonly>
        </div>
    </div>

    <input type="hidden" name="tenTP" id="tenTP">
    <input type="hidden" name="soLuong" id="soLuong">

    <div class="row">
        <div class="col">
            <label>Người lập:</label>
            <input type="text" value="<?php echo $nguoiLap; ?>" readonly>
            <input type="hidden" name="maNguoiLap" value="<?php echo $maNguoiLap; ?>">
        </div>
        <div class="col">
            <label>Trạng thái:</label>
            <select name="trangThai">
                <option value="Đã nhập">Đã nhập</option>
            </select>
        </div>
    </div>

    <div class="form-actions">
        <a href="index.php?controller=phieu&action=pnk_index">⬅ Quay lại</a>
        <button type="submit">✅ Lưu phiếu</button>
    </div>
</form>

<script>
function layThongTinTP() {
    var sel = document.getElementById('maTP');
    var opt = sel.options[sel.selectedIndex];

    if (opt && opt.value !== '') {
        document.getElementById('tenTP').value = opt.getAttribute('data-ten');
        document.getElementById('soLuong').value = opt.getAttribute('data-soluong');
        document.getElementById('tenTP_display').value = opt.getAttribute('data-ten');
        document.getElementById('soLuong_display').value = opt.getAttribute('data-soluong');
    } else {
        document.getElementById('tenTP').value = '';
        document.getElementById('soLuong').value = '';
        document.getElementById('tenTP_display').value = '';
        document.getElementById('soLuong_display').value = '';
    }
}
</script>

<style>
.phieu-form {
    max-width: 650px;
    margin: 20px auto;
    padding: 20px;
    background: #fafafa;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.phieu-form .row {
    display: flex;
    gap: 12px;
}

.phieu-form .col {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.phieu-form label {
    font-weight: 600;
    margin-bottom: 4px;
}

.phieu-form input[type=text],
.phieu-form select {
    padding: 8px 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 14px;
    width: 100%;
    box-sizing: border-box;
}

.phieu-form input[readonly] {
    background: #eee;
    color: #555;
}

.form-actions {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-top: 15px;
}

.form-actions button {
    background: #198754;
    color: white;
    border: none;
    padding: 10px 18px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    flex: 1;
}

.form-actions button:hover {
    background: #157347;
}

.form-actions a {
    background: #6c757d;
    color: white;
    text-decoration: none;
    padding: 10px 18px;
    border-radius: 6px;
    text-align: center;
    font-weight: 600;
    flex: 1;
}

.form-actions a:hover {
    background: #565e64;
}

/* Alert */
.alert {
    padding: 10px 12px;
    border-radius: 6px;
    font-weight: 600;
    text-align: center;
}

.alert.success {
    background: #d1e7dd;
    color: #0f5132;
}

.alert.error {
    background: #f8d7da;
    color: #842029;
}

/* Responsive */
@media (max-width: 650px) {
    .phieu-form .row {
        flex-direction: column;
    }
}
/* FIX KHÔNG CLICK ĐƯỢC SELECT */
.phieu-form select {
    position: relative;
    z-index: 999;
}
.phieu-form .row,
.phieu-form .col {
    position: relative;
    z-index: 1;
}

</style>
