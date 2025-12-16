<?php declare(strict_types=1); 
// Ensure we have a $user array available in the view. Prefer a passed-in $user,
// otherwise try helper `getCurrentUser()` or fallback to session data.
if (!isset($user)) {
    if (function_exists('getCurrentUser')) {
        $tmpUser = getCurrentUser();
        $user = $tmpUser ? $tmpUser : array('maNguoiDung' => '', 'hoTen' => '');
    } else {
        if (session_id() === '') @session_start();
        $user = isset($_SESSION['user']) ? $_SESSION['user'] : array('maNguoiDung' => '', 'hoTen' => '');
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Lập phiếu nhập kho</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: #ecf0f1; margin: 0; padding: 0; }
.content-wrapper { max-width: 900px; margin: 30px auto; background: #f4f6f9; border-radius: 10px; padding: 25px; }
.page-header h2 { font-weight: 700; color: #34495e; margin-bottom: 5px; }
.subtitle { color: #7f8c8d; margin-bottom: 20px; }
.card { background: #fff; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); padding: 25px; }
.form-group { margin-bottom: 18px; display: flex; flex-direction: column; }
.form-group label { font-weight: bold; margin-bottom: 5px; }
.form-control { width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
.btn { padding: 10px 20px; border-radius: 6px; border: none; font-size: 14px; cursor: pointer; margin: 5px; }
.btn-primary { background: #3498db; color: #fff; }
.btn-primary:hover { background: #2980b9; }
.btn-secondary { background: #95a5a6; color: #fff; }
.btn-secondary:hover { background: #7f8c8d; }
.form-actions { text-align: center; margin-top: 20px; }
.btn.btn-primary {
    background-color: #28a745 !important; /* xanh lá */
    border-color: #28a745 !important;
}

.btn.btn-secondary {
    background-color: #dc3545 !important; /* đỏ */
    border-color: #dc3545 !important;
    color: #fff !important;
}
</style>
</head>
<body>
<div class="content-wrapper">
    <div class="page-header">
        <h2><i class="fa fa-box"></i> LẬP PHIẾU NHẬP KHO NGUYÊN LIỆU</h2>
        <p class="subtitle">Nhập nguyên liệu theo kế hoạch sản xuất và tình trạng kho hiện tại.</p>
        <hr>
    </div>
    <div class="card">
        <form method="post" action="/PTUD_QLSX_NHAMAY_NOPAINNOGAIN/index.php?controller=phieunhapNL&action=nhapPhieu">
            <div class="form-group">
                <label><strong>Mã kho</strong></label>
                <input type="hidden" name="maKho" value="K001">
                <input type="text" class="form-control" value="K001 - Kho Nguyên Liệu" readonly>
            </div>
            <div class="form-group">
                <label><strong>Ngày nhập</strong></label>
                <input type="date" name="ngayNhap" class="form-control" value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="form-group">
                <label><strong>Nhân viên lập phiếu</strong></label>
                <input type="hidden" name="maNguoiLap" value="<?php echo htmlspecialchars(isset($user['maNguoiDung']) ? $user['maNguoiDung'] : ''); ?>">
                <input type="text" class="form-control" value="<?php echo htmlspecialchars((isset($user['maNguoiDung']) ? $user['maNguoiDung'] . ' - ' : '') . (isset($user['hoTen']) ? $user['hoTen'] : '')); ?>" readonly>
            </div>
            <div class="form-group">
                <label><strong>Trạng thái</strong></label>
                <select name="trangThai" class="form-control" required>
                    <option value="ChoDuyet" selected>Chờ duyệt</option>
                </select>
            </div>
            <div class="form-group">
                <label><strong>Nguyên liệu</strong></label>
                <select id="maNguyenLieu" name="maNguyenLieu" class="form-control" onchange="capNhatThongTin()" required>
                    <option value="">-- Chọn nguyên liệu --</option>
                    <?php
                    if (!empty($ds_nguyenlieu)) {
                        foreach ($ds_nguyenlieu as $nl) {
                            // Loại bỏ nguyên liệu có tên '--' hoặc không thuộc kế hoạch sản xuất
                            if (trim($nl['tennguyenlieu']) === '--' || empty($nl['makehoach'])) continue;
                            echo '<option value="'.htmlspecialchars($nl['manguyenlieu']).'" '
                                 .'data-ton="'.htmlspecialchars($nl['soluongton']).'" '
                                 .'data-maKho="'.htmlspecialchars($nl['makho']).'" '
                                 .'data-kehoach="'.htmlspecialchars($nl['soluongnguyenlieu']).'">'
                                 .htmlspecialchars($nl['makehoach'].'-'.$nl['manguyenlieu'].'-'.$nl['tennguyenlieu'])
                                 .'</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label><strong>Số lượng theo kế hoạch</strong></label>
                <input type="number" id="soLuong" name="soLuong" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label><strong>Số lượng tồn kho</strong></label>
                <input type="number" id="soLuongTonKho" name="soLuongTonKho" class="form-control" readonly>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Lưu phiếu nhập</button>
                <button type="reset" class="btn btn-secondary"><i class="fa fa-times"></i> Hủy</button>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
function capNhatThongTin() {
    var sel = document.getElementById('maNguyenLieu');
    var opt = sel.options[sel.selectedIndex];
    if (!opt) return;

    document.getElementById('soLuong').value = opt.getAttribute('data-kehoach') || 0;
    document.getElementById('soLuongTonKho').value = opt.getAttribute('data-ton') || 0;

    var maKhoVal = opt.getAttribute('data-maKho') || '';
    var selKho = document.getElementById('maKho');
    for (var i=0; i<selKho.options.length; i++) {
        if (selKho.options[i].value === maKhoVal) {
            selKho.selectedIndex = i;
            break;
        }
    }
}
</script>
</body>
</html>
