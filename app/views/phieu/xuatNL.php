<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Lập phiếu xuất kho nguyên liệu</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body { font-family: Arial, sans-serif; background: #ecf0f1; margin: 0; padding: 0; }
.content-wrapper { max-width: 900px; margin: 30px auto; background: #f4f6f9; border-radius: 10px; padding: 25px; }
.page-header h2 { font-weight: 700; color: #34495e; margin-bottom: 5px; }
.subtitle { color: #7f8c8d; margin-bottom: 20px; }
.card { background: #fff; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); padding: 25px; }
.form-group { margin-bottom: 18px; display: flex; flex-direction: column; }
.form-group label { font-weight: bold; margin-bottom: 5px; }
.form-control { width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
.btn { padding: 10px 20px; border-radius: 6px; border: none; font-size: 14px; cursor: pointer; margin: 5px; }
.btn-primary { background: #e74c3c; color: #fff; }
.btn-primary:hover { background: #c0392b; }
.btn-secondary { background: #95a5a6; color: #fff; }
.btn-secondary:hover { background: #7f8c8d; }
.form-actions { text-align: center; margin-top: 20px; }
</style>
</head>
<body>
<div class="content-wrapper">
    <div class="page-header">
        <h2><i class="fa fa-dolly"></i> LẬP PHIẾU XUẤT KHO NGUYÊN LIỆU</h2>
        <p class="subtitle">Xuất nguyên liệu cho sản xuất.</p>
        <hr>
    </div>

    <div class="card">
        <form method="post" action="index.php?controller=phieuxuatNL&action=xuatPhieu">
            <div class="form-group">
                <label><strong>Mã kho</strong></label>
                <input type="hidden" name="maKho" value="K001">
                <input type="text" class="form-control" value="K001 - Kho Nguyên Liệu" readonly>
            </div>

            <div class="form-group">
                <label><strong>Ngày xuất</strong></label>
                <input type="date" name="ngayXuat" class="form-control" value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="form-group">
                <label><strong>Mã nhân viên lập phiếu</strong></label>
                <input type="hidden" name="maNguoiLap" value="ND004">
                <input type="text" class="form-control" value="ND004 - Phạm Quốc Kho" readonly>
            </div>

            <div class="form-group">
            <label><strong>Mã phiếu yêu cầu nguyên liệu</strong></label>
            <select name="maPhieuYC" id="maPhieuYC" class="form-control" required onchange="capNhatSoLuongYC()">
                <option value="">-- Chọn phiếu yêu cầu --</option>
                <?php
                if (!empty($ds_phieuyc)) {
                    foreach ($ds_phieuyc as $yc) { ?>
                        <option 
                            value="<?php echo htmlspecialchars($yc['maPhieu']); ?>" 
                            data-sol="<?php echo htmlspecialchars($yc['soLuong']); ?>" 
                            data-nl="<?php echo htmlspecialchars($yc['maNguyenLieu']); ?>">
                            <?php echo htmlspecialchars('Phiếu '.$yc['maPhieu'].' - Nguyên liệu '.$yc['maNguyenLieu'].' (Y/C '.$yc['soLuong'].')'); ?>
                        </option>
                <?php } } ?>
            </select>
        </div>



            <div class="form-group">
                <label><strong>Mã nguyên liệu</strong></label>
                <input type="hidden" id="maNguyenLieu" name="maNguyenLieu">
                <input type="text" id="tenNguyenLieu" class="form-control" readonly>
            </div>

            <div class="form-group">
                <label><strong>Số lượng yêu cầu</strong></label>
                <input type="number" id="soLuongNLYC" name="soLuongNLYC" class="form-control" min="0">
            </div>

            <div class="form-group">
                <label><strong>Số lượng tồn kho</strong></label>
                <input type="number" id="soLuongTonKho" name="soLuongTonKho" class="form-control" readonly>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Lưu phiếu xuất</button>
                <button type="reset" class="btn btn-secondary"><i class="fa fa-times"></i> Hủy</button>
            </div>
        </form>
    </div>
</div>

<script>
function capNhatThongTin() {
    var sel = document.getElementById('maNguyenLieu');
    var opt = sel.options[sel.selectedIndex];
    if (!opt) return;
    document.getElementById('soLuongTonKho').value = opt.getAttribute('data-ton') || 0;
}

function capNhatSoLuongYC() {
    var sel = document.getElementById('maPhieuYC');
    if (!sel || sel.selectedIndex < 0) return;

    var opt = sel.options[sel.selectedIndex];
    var sol = opt.getAttribute('data-sol') ? opt.getAttribute('data-sol') : 0;
    var maNL = opt.getAttribute('data-nl') ? opt.getAttribute('data-nl') : '';

    document.getElementById('soLuongNLYC').value = sol;
    document.getElementById('maNguyenLieu').value = maNL;

    // Dữ liệu nguyên liệu từ PHP
    var ds_nguyenlieu = <?php 
        if (isset($ds_nguyenlieu)) {
            echo json_encode($ds_nguyenlieu);
        } else {
            echo '[]';
        }
    ?>;

    var tenNL = '';
    var ton = 0;
    for (var i = 0; i < ds_nguyenlieu.length; i++) {
        var nl = ds_nguyenlieu[i];
        if (nl.maNguyenLieu == maNL) {
            tenNL = nl.maNguyenLieu + ' - ' + nl.tenNguyenLieu;
            ton = nl.soLuongTon;
            break;
        }
    }

    document.getElementById('tenNguyenLieu').value = tenNL;
    document.getElementById('soLuongTonKho').value = ton;
}
</script>

</body>
</html>
