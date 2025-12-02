<h2 style="text-align:center; color:#2c3e50; margin-bottom:20px;">Lập kế hoạch sản xuất mới</h2>

<?php if(!empty($errors)): ?>
    <div style="color:red; margin-bottom:15px; border:1px solid red; padding:10px; border-radius:5px; background:#fdecea;">
        <?php foreach($errors as $err) echo "<p>$err</p>"; ?>
    </div>
<?php endif; ?>

<?php if(!empty($success)): ?>
    <div style="color:green; margin-bottom:15px; border:1px solid green; padding:10px; border-radius:5px; background:#e6f4ea;">
        <p><?php echo $success; ?></p>
        <!-- Nút xem danh sách kế hoạch -->
        <form method="get" action="index.php" style="text-align:center; margin-top:10px;">
            <input type="hidden" name="controller" value="kehoach">
            <input type="hidden" name="action" value="index">
            <button type="submit" style="padding:8px 15px; background:#3498db; color:white; border:none; border-radius:5px; cursor:pointer;">
                Xem danh sách kế hoạch
            </button>
        </form>
    </div>
<?php endif; ?>

<form method="POST" action="" style="max-width:auto; margin:auto;">  <!-- ✅ form nhỏ lại -->
    <div style="display:flex; flex-wrap:wrap; gap:20px; background:#f9f9f9; padding:20px; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.05);">

        <!-- Cột 1: Thông tin cơ bản -->
        <div style="flex:1; min-width:500px; background:#ecf0f1; padding:15px; border-radius:8px;">
            <label style="font-weight:bold;">Mã KH</label>
            <input type="text" name="maKeHoach" value="" readonly placeholder="Tự sinh" style="width:100%; padding:5px; margin-bottom:10px;">

            <!-- ✅ Gom 3 ô vào cùng hàng -->
            <div style="display:flex; gap:15px; margin-bottom:10px;">
                <div style="flex:1;">
                    <label style="font-weight:bold;">Đơn hàng</label>
                    <select name="maDonHang" id="maDonHang" required style="width:100%; padding:5px;">
                        <option value="">--Chọn--</option>
                        <?php foreach($donhangs as $dh): ?>
                            <option value="<?php echo $dh['maDonHang']; ?>"
                                    data-ten="<?php echo htmlspecialchars($dh['tenSP']); ?>"
                                    data-tongsl="<?php echo $dh['soLuong']; ?>">
                                <?php echo $dh['maDonHang']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="flex:1;">
                    <label style="font-weight:bold;">Sản phẩm</label>
                    <input type="text" name="tenSP" id="tenSP" readonly style="width:100%; padding:5px;">
                </div>

                <div style="flex:0.7;">
                    <label style="font-weight:bold;">Tổng SL</label>
                    <input type="number" name="tongSoLuong" id="tongSoLuong" min="1" readonly required style="width:100%; padding:5px; background:#f5f5f5;">
                </div>
            </div>

            <label style="font-weight:bold;">Xưởng</label>
            <select name="maXuong" required style="width:100%; padding:5px; margin-bottom:10px;">
                <option value="">--Chọn Xưởng--</option>
                <?php foreach($xuongs as $x): ?>
                    <option value="<?php echo $x['maXuong']; ?>"><?php echo $x['tenXuong']; ?></option>
                <?php endforeach; ?>
            </select>

            <label style="font-weight:bold;">Ngày bắt đầu</label>
            <input type="date" name="ngayBatDau" required style="width:100%; padding:5px; margin-bottom:10px;">

            <label style="font-weight:bold;">Ngày kết thúc</label>
            <input type="date" name="ngayKetThuc" required style="width:100%; padding:5px; margin-bottom:10px;">

            <label style="font-weight:bold;">Trạng thái</label>
            <input type="text" name="trangThai" value="Chưa bắt đầu" readonly style="width:100%; padding:5px; margin-bottom:10px;">
            
            <label style="font-weight:bold;">Người lập</label>
            <input type="text" name="nguoiLap" value="<?php echo isset($_SESSION['user']['hoTen']) ? $_SESSION['user']['hoTen'] : ''; ?>" readonly style="width:100%; padding:5px; margin-bottom:10px;">
        </div>

        <!-- Cột 2: Nguyên liệu -->
        <div style="flex:1; min-width:350px; background:#ecf0f1; padding:15px; border-radius:8px;">
            <label style="font-weight:bold;">Nguyên liệu</label>
            <div id="nguyenLieuContainer">
                <div class="nl-row" style="margin-bottom:10px; display:flex; gap:5px;">
                    <select name="maNguyenLieu[]" class="nl-select" required style="flex:2; padding:5px;">
                        <option value="">--Chọn nguyên liệu--</option>
                        <?php foreach($nguyenlieus as $nl): ?>
                            <option value="<?php echo $nl['maNguyenLieu']; ?>" data-ten="<?php echo htmlspecialchars($nl['tenNguyenLieu']); ?>">
                                <?php echo $nl['tenNguyenLieu']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="tenNguyenLieu[]" class="nl-ten" placeholder="Tên NL" readonly style="flex:1; padding:5px;">
                    <input type="number" name="soLuongNguyenLieu[]" min="1" placeholder="SL" required style="flex:0.7; padding:5px;">
                </div>
            </div>
            
            <!-- <button type="button" id="addNL" style="padding:5px 10px; background:#3498db; color:white; border:none; border-radius:5px; cursor:pointer;">+ Thêm nguyên liệu</button> -->
        </div>

    </div>

    <br>
    <div style="text-align:center;">
        <button type="submit" style="padding:10px 20px; background:#27ae60; color:white; border:none; border-radius:5px; cursor:pointer;">Lập kế hoạch</button>
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

    function updateNLName(select) {
        const tenInput = select.parentNode.querySelector('.nl-ten');
        const selectedOption = select.options[select.selectedIndex];
        tenInput.value = selectedOption.dataset.ten || '';
    }

    document.querySelectorAll('.nl-select').forEach(s => s.addEventListener('change', function() {
        updateNLName(this);
    }));

    document.getElementById('addNL').addEventListener('click', function() {
        const container = document.getElementById('nguyenLieuContainer');
        const newRow = container.querySelector('.nl-row').cloneNode(true);
        newRow.querySelectorAll('input').forEach(i => i.value = '');
        newRow.querySelector('.nl-select').selectedIndex = 0;
        container.appendChild(newRow);
        newRow.querySelector('.nl-select').addEventListener('change', function() { updateNLName(this); });
    });

    document.querySelector("form").addEventListener("submit", function() {
        document.querySelectorAll(".nl-select").forEach(function(select) {
            const tenInput = select.parentNode.querySelector(".nl-ten");
            const selectedOption = select.options[select.selectedIndex];
            tenInput.value = selectedOption.dataset.ten || '';
        });
    });

    const form = document.querySelector('form');
    const startInput = document.querySelector('input[name="ngayBatDau"]');
    const endInput = document.querySelector('input[name="ngayKetThuc"]');

    form.addEventListener('submit', function(e) {
        const startDate = new Date(startInput.value);
        const endDate = new Date(endInput.value);
        if (endInput.value && startInput.value && endDate <= startDate) {
            e.preventDefault();
            alert("❌ Ngày kết thúc phải sau ngày bắt đầu!");
            endInput.focus();
        }
    });
});
</script>
