<h2>📝 Lập Phiếu Nhập Kho Thành Phẩm</h2>

<?php
// Hiển thị thông báo lưu phiếu
if (isset($_GET['ok']) && $_GET['ok'] == 1) {
    echo '<p style="color:green;font-weight:bold;">✅ Phiếu nhập kho đã được lưu thành công!</p>';
} elseif (isset($_GET['error'])) {
    if ($_GET['error'] == 1) {
        echo '<p style="color:red;">❌ Dữ liệu không hợp lệ. Vui lòng kiểm tra lại.</p>';
    } elseif ($_GET['error'] == 2) {
        echo '<p style="color:red;">❌ Lỗi khi lưu dữ liệu vào cơ sở dữ liệu.</p>';
    }
}
?>

<form method="post" action="index.php?controller=phieu&action=pnk_luuPhieu"
    style="max-width:500px; background:#fafafa; padding:20px; border-radius:8px; box-shadow:0 0 6px rgba(0,0,0,0.1);">

    <!-- Mã phiếu -->
    <input type="text" name="maPhieu" value="<?php echo htmlspecialchars(isset($maPhieu) ? $maPhieu : ''); ?>" readonly
        style="width:100%;">


    <!-- Ngày nhập -->
    <label><b>Ngày nhập:</b></label><br>
    <input type="text" name="ngayNhap" value="<?php echo date('Y-m-d'); ?>" readonly style="width:100%;"><br><br>

    <!-- Chọn kho -->
    <label><b>Kho:</b></label><br>
    <select name="maKho" required style="width:100%;">
        <option value="">-- Chọn kho --</option>
        <?php 
    $defaultKho = 'K002'; // mã kho mặc định là Kho thành phẩm
    if (!empty($dsKho)): 
        foreach ($dsKho as $k): 
            $selected = ($k['maKho'] === $defaultKho) ? 'selected' : '';
    ?>
        <option value="<?php echo htmlspecialchars($k['maKho']); ?>" <?php echo $selected; ?>>
            <?php echo htmlspecialchars($k['tenKho']); ?>
        </option>
        <?php 
        endforeach; 
    else: 
    ?>
        <option disabled>⚠️ Không có kho nào khả dụng</option>
        <?php endif; ?>
    </select><br><br>

    <!-- Chọn thành phẩm -->
    <label><b>Thành phẩm:</b></label><br>
    <select name="maTP" id="maTP" onchange="layThongTinTP()" required style="width:100%;">
        <option value="">-- Chọn thành phẩm --</option>
        <?php if (!empty($dsThanhPham)): ?>
        <?php foreach ($dsThanhPham as $tp): ?>
        <option value="<?php echo htmlspecialchars($tp['maTP']); ?>"
            data-ten="<?php echo htmlspecialchars($tp['tenTP']); ?>" data-soluong="<?php echo (int)$tp['soLuong']; ?>">
            <?php echo htmlspecialchars($tp['tenTP']); ?>
        </option>
        <?php endforeach; ?>
        <?php else: ?>
        <option disabled>⚠️ Không có thành phẩm khả dụng</option>
        <?php endif; ?>
    </select><br><br>

    <!-- Ẩn để lưu -->
    <input type="hidden" name="tenTP" id="tenTP">
    <input type="hidden" name="soLuong" id="soLuong">

    <!-- Hiển thị thông tin TP -->
    <label><b>Tên thành phẩm:</b></label><br>
    <input type="text" id="tenTP_display" readonly style="width:100%; background:#eee;"><br><br>

    <label><b>Số lượng đạt chuẩn:</b></label><br>
    <input type="text" id="soLuong_display" readonly style="width:100%; background:#eee;"><br><br>

    <!-- Người lập -->
    <label><b>Người lập:</b></label><br>
    <input type="text" name="maNguoiLap" value="ND004" readonly style="width:100%; background:#eee;"><br><br>

    <!-- Trạng thái -->
    <label><b>Trạng thái:</b></label><br>
    <select name="trangThai" style="width:100%;">
        <option value="Đã nhập">Đã nhập</option>
        <option value="Chờ duyệt">Chờ duyệt</option>
    </select><br><br>

    <!-- Nút hành động -->
    <button type="submit" style="background:#4CAF50;color:white;padding:8px 12px;border:none;border-radius:5px;">
        ✅ Lưu phiếu
    </button>

    <a href="index.php?controller=phieu&action=pnk_index"
        style="margin-left:10px;text-decoration:none;background:#ccc;padding:8px 12px;border-radius:5px;color:black;">
        ⬅ Quay lại
    </a>
</form>

<script>
function layThongTinTP() {
    var select = document.getElementById('maTP');
    var opt = select.options[select.selectedIndex];
    if (opt && opt.value !== '') {
        var ten = opt.getAttribute('data-ten') || '';
        var sl = opt.getAttribute('data-soluong') || '';
        document.getElementById('tenTP').value = ten;
        document.getElementById('soLuong').value = sl;
        document.getElementById('tenTP_display').value = ten;
        document.getElementById('soLuong_display').value = sl;
    } else {
        document.getElementById('tenTP').value = '';
        document.getElementById('soLuong').value = '';
        document.getElementById('tenTP_display').value = '';
        document.getElementById('soLuong_display').value = '';
    }
}
</script>