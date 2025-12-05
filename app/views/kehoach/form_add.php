<h2 style="text-align:center; color:#2c3e50; margin-bottom:25px; font-size:28px;">Lập kế hoạch sản xuất mới</h2>

<?php 
$autoMaKH = $khModel->generateMaKeHoach(); // Tự sinh mã KH
?>
<?php if(isset($_GET['msg'])): ?>
    <div style="margin-bottom:15px; padding:12px 15px; border-radius:6px;
        background:<?php echo ($_GET['type']=="error")?"#fdecea":"#e6f4ea"; ?>;
        border:1px solid <?php echo ($_GET['type']=="error")?"#e74c3c":"#2ecc71"; ?>;
        color:<?php echo ($_GET['type']=="error")?"#e74c3c":"#2ecc71"; ?>;">
        <p style="margin:0; font-weight:500;"><?php echo htmlspecialchars($_GET['msg']); ?></p>
    </div>
<?php endif; ?>
<?php if(!empty($errors)): ?>
    <div style="color:#e74c3c; margin-bottom:15px; border:1px solid #e74c3c; padding:12px 15px; border-radius:6px; background:#fdecea;">
        <?php foreach($errors as $err) echo "<p style='margin:0 0 5px 0;'>$err</p>"; ?>
    </div>
<?php endif; ?>

<?php if(!empty($success)): ?>
    <div style="color:#27ae60; margin-bottom:15px; border:1px solid #27ae60; padding:12px 15px; border-radius:6px; background:#e6f4ea;">
        <p style="margin:0; font-weight:500;"><?php echo $success; ?></p>
        <form method="get" action="index.php" style="text-align:center; margin-top:12px;">
            <input type="hidden" name="controller" value="kehoach">
            <input type="hidden" name="action" value="index">
            <button type="submit" style="padding:8px 18px; background:#3498db; color:white; border:none; border-radius:5px; cursor:pointer;">
                Xem danh sách kế hoạch
            </button>
        </form>
    </div>
<?php endif; ?>

<form method="POST" action="" style="max-width:900px; margin:auto;">
    <div style="display:flex; flex-wrap:wrap; gap:20px; background:#fefefe; padding:25px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.08);">

        <!-- Cột 1: Thông tin cơ bản -->
        <div style="flex:1; min-width:480px; background:#f7f9fa; padding:20px; border-radius:10px;">
            <label style="font-weight:bold; display:block; margin-bottom:5px;">Mã KH</label>
            <input type="text" name="maKeHoach" value="<?php echo $autoMaKH; ?>" readonly placeholder="Tự sinh" style="width:100%; padding:8px; margin-bottom:15px; border-radius:5px; border:1px solid #ccc;">

            <div style="display:flex; gap:15px; margin-bottom:15px;">
                <div style="flex:1;">
                    <label style="font-weight:bold; display:block; margin-bottom:5px;">Đơn hàng</label>
                    <select name="maDonHang" id="maDonHang" required style="width:100%; padding:8px; border-radius:5px; border:1px solid #ccc;">
                        <option value="">--Chọn--</option>
                        <?php foreach($donhangs as $dh): 
                            $soLan = $khModel->countByOrder($dh['maDonHang']);
                            $disabled = ($soLan >= 2) ? "disabled" : "";
                        ?>
                            <option value="<?php echo $dh['maDonHang']; ?>" 
                                    data-ten="<?php echo htmlspecialchars($dh['tenSP']); ?>"
                                    data-tongsl="<?php echo $dh['soLuong']; ?>"
                                    <?php echo $disabled; ?>>
                                <?php echo $dh['maDonHang']; ?> <?php echo ($soLan >= 2 ? " - ĐÃ ĐỦ KẾ HOẠCH" : ""); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="flex:1;">
                    <label style="font-weight:bold; display:block; margin-bottom:5px;">Sản phẩm</label>
                    <input type="text" name="tenSP" id="tenSP" readonly style="width:100%; padding:8px; border-radius:5px; border:1px solid #ccc;">
                </div>

                <div style="flex:0.7;">
                    <label style="font-weight:bold; display:block; margin-bottom:5px;">Tổng SL</label>
                    <input type="number" name="tongSoLuong" id="tongSoLuong" min="1" readonly required style="width:100%; padding:8px; border-radius:5px; border:1px solid #ccc; background:#f0f0f0;">
                </div>
            </div>

            <label style="font-weight:bold; display:block; margin-bottom:5px;">Xưởng</label>
            <select name="maXuong" required style="width:100%; padding:8px; border-radius:5px; border:1px solid #ccc; margin-bottom:15px;">
                <option value="">--Chọn Xưởng--</option>
                <?php foreach($xuongs as $x): 
                    $maDonHang = isset($_POST['maDonHang']) ? $_POST['maDonHang'] : '';
                    $xuongDaCo = $khModel->checkDuplicatePlan(array('maDonHang' => $maDonHang,'maXuong' => $x['maXuong']));
                ?>
                    <option value="<?php echo $x['maXuong']; ?>" <?php echo $xuongDaCo ? "disabled" : ""; ?>>
                        <?php echo $x['tenXuong']; ?> <?php echo $xuongDaCo ? " - Đã có kế hoạch" : ""; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label style="font-weight:bold; display:block; margin-bottom:5px;">Ngày bắt đầu</label>
            <input type="date" name="ngayBatDau" required style="width:100%; padding:8px; margin-bottom:15px; border-radius:5px; border:1px solid #ccc;">

            <label style="font-weight:bold; display:block; margin-bottom:5px;">Ngày kết thúc</label>
            <input type="date" name="ngayKetThuc" required style="width:100%; padding:8px; margin-bottom:15px; border-radius:5px; border:1px solid #ccc;">

            <label style="font-weight:bold; display:block; margin-bottom:5px;">Trạng thái</label>
            <input type="text" name="trangThai" value="Chưa bắt đầu" readonly style="width:100%; padding:8px; margin-bottom:15px; border-radius:5px; border:1px solid #ccc; background:#f0f0f0;">

            <label style="font-weight:bold; display:block; margin-bottom:5px;">Người lập</label>
            <input type="text" name="nguoiLap" value="<?php echo isset($_SESSION['user']['hoTen']) ? $_SESSION['user']['hoTen'] : ''; ?>" readonly style="width:100%; padding:8px; margin-bottom:15px; border-radius:5px; border:1px solid #ccc;">
        </div>

        <!-- Cột 2: Nguyên liệu -->
        <div style="flex:1; min-width:350px; background:#f7f9fa; padding:20px; border-radius:10px;">
            <label style="font-weight:bold; display:block; margin-bottom:10px;">Nguyên liệu</label>
            <div id="nguyenLieuContainer">
                <div class="nl-row" style="margin-bottom:12px; display:flex; gap:8px;">
                    <select name="tenNguyenLieu[]" class="nl-select" required style="flex:2; padding:8px; border-radius:5px; border:1px solid #ccc;">
                        <option value="">--Chọn nguyên liệu--</option>
                        <?php foreach($nguyenlieus as $nl): ?>
                            <option value="<?php echo htmlspecialchars($nl['tenNguyenLieu']); ?>" data-ma="<?php echo $nl['maNguyenLieu']; ?>">
                                <?php echo $nl['tenNguyenLieu']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="maNguyenLieu[]" class="nl-ma" placeholder="Mã NL" readonly style="flex:1; padding:8px; border-radius:5px; border:1px solid #ccc;">
                    <input type="number" name="soLuongNguyenLieu[]" min="1" placeholder="SL" required style="flex:0.7; padding:8px; border-radius:5px; border:1px solid #ccc;">
                </div>
            <!-- </div>
            <button type="button" id="addNL" style="padding:6px 12px; background:#3498db; color:white; border:none; border-radius:5px; cursor:pointer; margin-top:8px;">+ Thêm nguyên liệu</button>
        </div> -->
    </div>

    <div style="text-align:center; margin-top:20px;">
        <button type="submit" style="padding:12px 25px; background:#27ae60; color:white; border:none; border-radius:6px; cursor:pointer; font-size:17px;">Lập kế hoạch</button>
    </div>
</form>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const dhSelect = document.getElementById('maDonHang');
    const spInput = document.getElementById('tenSP');
    const slInput = document.getElementById('tongSoLuong');

    dhSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        spInput.value = selected.dataset.ten || '';
        slInput.value = selected.dataset.tongsl || '';
    });

    function updateNLCode(select) {
    var maInput = select.parentNode.querySelector('.nl-ma');
    var selectedOption = select.options[select.selectedIndex];
    maInput.value = selectedOption.getAttribute('data-ma') || '';
}

// Gán sự kiện cho dropdown hiện tại
document.querySelectorAll('.nl-select').forEach(function(s) {
    s.addEventListener('change', function() { updateNLCode(this); });
});

// Nếu dùng nút thêm nguyên liệu
document.getElementById('addNL').addEventListener('click', function() {
    var container = document.getElementById('nguyenLieuContainer');
    var newRow = container.querySelector('.nl-row').cloneNode(true);
    newRow.querySelectorAll('input').forEach(function(i) { i.value = ''; });
    newRow.querySelector('.nl-select').selectedIndex = 0;
    container.appendChild(newRow);
    newRow.querySelector('.nl-select').addEventListener('change', function() { updateNLCode(this); });
});

    const form = document.querySelector('form');
    const startInput = document.querySelector('input[name="ngayBatDau"]');
    const endInput = document.querySelector('input[name="ngayKetThuc"]');

    form.addEventListener('submit', function(e) {
        // Kiểm tra ngày
        const startDate = new Date(startInput.value);
        const endDate = new Date(endInput.value);
        if (endInput.value && startInput.value && endDate <= startDate) {
            e.preventDefault();
            alert("❌ Ngày kết thúc phải sau ngày bắt đầu!");
            endInput.focus();
        }

        // Cập nhật tên nguyên liệu trước khi submit
        document.querySelectorAll(".nl-select").forEach(function(select) {
            const tenInput = select.parentNode.querySelector(".nl-ten");
            const selectedOption = select.options[select.selectedIndex];
            tenInput.value = selectedOption.dataset.ten || '';
        });
    });
});
</script>
