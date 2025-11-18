<div style="
    display:flex;
    justify-content:center;
    padding-top:20px;
">
    <div style="
        width:600px;
        background:white;
        padding:25px;
        border-radius:12px;
        box-shadow:0 4px 12px rgba(0,0,0,0.1);
    ">
        
        <h2 style="margin-bottom:18px;text-align:center;color:#1e293b;">
            🧮 Lập phiếu kiểm tra thành phẩm
        </h2>

        <?php if (isset($_GET['success'])): ?>
        <div style="background:#d4edda;color:#155724;padding:10px;border-radius:8px;margin-bottom:12px;text-align:center;">
            ✅ Lưu phiếu kiểm tra thành công!
        </div>
        <?php elseif (isset($_GET['error'])): ?>
        <div style="background:#f8d7da;color:#721c24;padding:10px;border-radius:8px;margin-bottom:12px;text-align:center;">
            ❌ Có lỗi khi lưu phiếu. Vui lòng kiểm tra lại.
        </div>
        <?php endif; ?>

        <form action="index.php?controller=phieu&action=create_kttp" method="post">

            <!-- Mã phiếu -->
            <div style="margin-bottom:12px;">
                <label><strong>Mã phiếu</strong></label>
                <input type="text" name="maPhieu" 
                    value="<?php echo htmlspecialchars($maPhieu); ?>" readonly
                    style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:6px;background:#f8fafc;">
            </div>

            <!-- Thành phẩm -->
            <div style="margin-bottom:12px;">
                <label><strong>Thành phẩm</strong></label>
                <select name="maTP" id="maTP" required
                    style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:6px;">
                    <option value="">-- Chọn thành phẩm --</option>
                    <?php foreach ($thanhPhams as $tp): ?>
                    <option value="<?php echo $tp['maTP']; ?>">
                        <?php echo htmlspecialchars($tp['tenTP']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- SL kiểm tra -->
            <div style="margin-bottom:12px;">
                <label><strong>Số lượng kiểm tra</strong></label>
                <input type="number" id="SL_KiemTra" name="SL_KiemTra" readonly
                    style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:6px;background:#f8fafc;">
            </div>

            <!-- SL đạt chuẩn -->
            <div style="margin-bottom:12px;">
                <label><strong>Số lượng đạt chuẩn</strong></label>
                <input type="number" id="SL_DatChuan" name="SL_DatChuan" required
                    style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:6px;">
            </div>

            <!-- Kết quả kiểm tra (badge) -->
            <div style="margin-bottom:15px;">
                <label><strong>Kết quả kiểm tra</strong></label><br>
                <span id="ketQuaBadge"
                    style="display:inline-block;margin-top:6px;padding:8px 14px;border-radius:20px;
                    background:#e2e8f0;color:#1e293b;font-weight:bold;">
                    Chưa xác định
                </span>

                <input type="hidden" name="ketQua" id="ketQuaInput" value="Đạt">
            </div>

            <!-- Ngày lập -->
            <div style="margin-bottom:12px;">
                <label><strong>Ngày lập</strong></label>
                <input type="date" name="ngayLap" value="<?php echo date('Y-m-d'); ?>" required
                    style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:6px;">
            </div>

            <!-- Nhân viên QC -->
            <div style="margin-bottom:16px;">
                <label><strong>Nhân viên QC</strong></label>
                <input type="text" value="<?php echo htmlspecialchars($hoTenQC.' ('.$nguoiQC.')'); ?>" readonly
                    style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:6px;background:#f1f5f9;">
            </div>

            <!-- Buttons -->
            <div style="text-align:center;">
                <button type="submit"
                    style="background:#2563eb;color:white;padding:10px 18px;border:none;border-radius:6px;font-size:15px;">
                    💾 Lưu phiếu
                </button>
                <a href="index.php?controller=dashboard"
                    style="margin-left:10px;background:#e5e7eb;color:#111;padding:10px 18px;border-radius:6px;text-decoration:none;">
                    ⬅ Quay lại
                </a>
            </div>

        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    var maTP = document.getElementById('maTP');
    var slKiemTra = document.getElementById('SL_KiemTra');
    var slDatChuan = document.getElementById('SL_DatChuan');
    var badge = document.getElementById('ketQuaBadge');
    var ketQuaInput = document.getElementById('ketQuaInput');

    // Load số lượng kiểm tra
    maTP.addEventListener('change', function() {
        var v = this.value;
        if (!v) {
            slKiemTra.value = '';
            return;
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'index.php?controller=phieu&action=getSL', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            var m = xhr.responseText.match(/(\d+)/);
            slKiemTra.value = m ? parseInt(m[1], 10) : 0;
        };
        xhr.send('maTP=' + encodeURIComponent(v));
    });

    // Tự đánh giá kết quả
    slDatChuan.addEventListener('input', function() {

        var kt = parseInt(slKiemTra.value || '0', 10);
        var dc = parseInt(slDatChuan.value || '0', 10);

        if (dc > kt) {
            alert("⚠ Số lượng đạt không được lớn hơn số lượng kiểm tra!");
            slDatChuan.value = kt;
            dc = kt;
        }

        if (kt === 0) return;

        var percent = (dc / kt) * 100;

        if (percent >= 90) {
            badge.innerText = "Đạt";
            badge.style.background = "#d1fae5";
            badge.style.color = "#065f46";
            ketQuaInput.value = "Đạt";
        } else {
            badge.innerText = "Không đạt";
            badge.style.background = "#fee2e2";
            badge.style.color = "#b91c1c";
            ketQuaInput.value = "Không đạt";
        }
    });
});
</script>
