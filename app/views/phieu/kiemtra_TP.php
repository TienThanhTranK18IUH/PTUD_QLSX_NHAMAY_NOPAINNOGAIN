<div style="display:flex;justify-content:center;padding:20px;background:#f1f5f9;">
    <div style="width:750px;background:white;padding:25px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);">

        <h2 style="text-align:center;margin-bottom:25px;color:#1e293b;border-bottom:1px solid #cbd5e1;padding-bottom:10px;">
            PHIẾU KIỂM TRA THÀNH PHẨM
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
            <div style="display:flex;align-items:center;margin-bottom:15px;">
                <label style="width:160px;">MÃ PHIẾU</label>
                <input type="text" name="maPhieu" value="<?php echo htmlspecialchars($maPhieu); ?>" readonly
                    style="flex:1;padding:8px;border:1px solid #cbd5e1;border-radius:6px;background:#f8fafc;">
            </div>

            <!-- Thành phẩm -->
            <div style="display:flex;align-items:center;margin-bottom:15px;">
                <label style="width:160px;">THÀNH PHẨM</label>
                <select name="maTP" id="maTP" required
                    style="flex:1;padding:8px;border:1px solid #cbd5e1;border-radius:6px;">
                    <option value="">-- Chọn thành phẩm --</option>
                    <?php foreach ($thanhPhams as $tp): ?>
                        <option value="<?php echo $tp['maTP']; ?>">
                            <?php echo htmlspecialchars($tp['tenTP']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Số lượng kiểm tra & đạt chuẩn & % -->
            <div style="display:flex;align-items:center;margin-bottom:15px; gap:10px;">
                <label style="width:120px;">SL KIỂM TRA</label>
                <input type="number" id="SL_KiemTra" name="SL_KiemTra" readonly
                    style="width:100px;padding:6px;border:1px solid #cbd5e1;border-radius:6px;background:#f8fafc;text-align:center;">
                
                <label style="width:120px;">SL ĐẠT CHUẨN</label>
                <input type="number" id="SL_DatChuan" name="SL_DatChuan" required
                    style="width:100px;padding:6px;border:1px solid #cbd5e1;border-radius:6px;text-align:center;">

                <label style="width:60px;">Tỉ lệ</label>
                <input type="text" id="percentDat" readonly
                    style="width:80px;padding:6px;border:1px solid #cbd5e1;border-radius:6px;background:#f1f5f9;text-align:center;">
            </div>


            <!-- Kết quả kiểm tra -->
            <div style="display:flex;align-items:center;margin-bottom:15px;">
                <label style="width:160px;">KẾT QUẢ KIỂM TRA</label>
                <div style="flex:1;">
                    <span id="ketQuaBadge"
                        style="display:inline-block;margin-left:15px;padding:5px 12px;border-radius:20px;
                        background:#e2e8f0;color:#1e293b;font-weight:bold;">
                        Chưa xác định
                    </span>

                    <input type="hidden" name="ketQua" id="ketQuaInput" value="">
                </div>
            </div>

            <!-- Ngày lập -->
            <div style="display:flex;align-items:center;margin-bottom:15px;">
                <label style="width:160px;">NGÀY LẬP</label>
                <input type="date" name="ngayLap" value="<?php echo date('Y-m-d'); ?>" required
                    style="flex:1;padding:8px;border:1px solid #cbd5e1;border-radius:6px;">
            </div>

            <!-- Nhân viên QC -->
            <div style="display:flex;align-items:center;margin-bottom:25px;">
                <label style="width:160px;">NHÂN VIÊN QC</label>
                <input type="text" value="<?php echo htmlspecialchars($hoTenQC.' ('.$nguoiQC.')'); ?>" readonly
                    style="flex:1;padding:8px;border:1px solid #cbd5e1;border-radius:6px;background:#f1f5f9;">
            </div>

            <!-- Buttons -->
            <div style="text-align:center;">
                <button type="submit"
                    style="background:#2563eb;color:white;padding:10px 18px;border:none;border-radius:6px;font-size:15px;">
                    💾 XÁC NHẬN LƯU PHIẾU
                </button>
                <a href="index.php?controller=dashboard"
                    style="margin-left:10px;background:#e5e7eb;color:#111;padding:10px 18px;border-radius:6px;text-decoration:none;">
                    HỦY
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
    var percentDat = document.getElementById('percentDat');
    var badge = document.getElementById('ketQuaBadge');
    var ketQuaInput = document.getElementById('ketQuaInput');
    var radios = document.querySelectorAll("input[name='chonKetQua']");

    // Load SL kiểm tra từ server
    maTP.addEventListener('change', function() {
        var v = this.value;
        if (!v) {
            slKiemTra.value = '';
            updateKetQua();
            return;
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'index.php?controller=phieu&action=getSL', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            var m = xhr.responseText.match(/(\d+)/);
            slKiemTra.value = m ? parseInt(m[1], 10) : 0;
            updateKetQua(); // tự cập nhật kết quả sau khi load
        };
        xhr.send('maTP=' + encodeURIComponent(v));
    });

    // Cập nhật kết quả khi nhập SL đạt chuẩn
    slDatChuan.addEventListener('input', updateKetQua);

    function updateKetQua() {
        var kt = parseInt(slKiemTra.value || '0', 10);
        var dc = parseInt(slDatChuan.value || '0', 10);

        if (dc > kt) {
            alert("⚠ Số lượng đạt không được lớn hơn số lượng kiểm tra!");
            slDatChuan.value = kt;
            dc = kt;
        }

        if (kt === 0) {
            badge.innerText = "Chưa xác định";
            badge.style.background = "#e2e8f0";
            badge.style.color = "#1e293b";
            ketQuaInput.value = "";
            percentDat.value = "";
            radios.forEach(r => r.checked = false);
            return;
        }

        var percent = Math.round((dc / kt) * 100);
        percentDat.value = percent + " %"; // cập nhật ô % đạt

        if (percent >= 90) {
            setKetQua("Đạt");
        } else {
            setKetQua("Không đạt");
        }
    }

    function setKetQua(val) {
        ketQuaInput.value = val;
        badge.innerText = val;
        if (val === "Đạt") {
            badge.style.background = "#d1fae5";
            badge.style.color = "#065f46";
        } else {
            badge.style.background = "#fee2e2";
            badge.style.color = "#b91c1c";
        }

        // Đồng bộ radio
        var radio = document.querySelector("input[name='chonKetQua'][value='" + val + "']");
        if (radio) radio.checked = true;
    }

    // Radio vẫn có thể chỉnh thủ công
    radios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            setKetQua(this.value);
        });
    });

});
</script>
