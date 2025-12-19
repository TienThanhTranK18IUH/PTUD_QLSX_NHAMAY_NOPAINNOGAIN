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

        <!-- 🔽 ĐÃ SỬA: thêm validate khi submit -->
        <form action="index.php?controller=phieu&action=create_kttp" 
              method="post"
              onsubmit="return validateForm();">

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

            <!-- Số lượng -->
            <div style="display:flex;align-items:center;margin-bottom:15px; gap:10px;">
                <label style="width:150px;">SL KIỂM TRA</label>
                <input type="number" id="SL_KiemTra" name="SL_KiemTra" readonly
                    style="width:100px;padding:6px;border:1px solid #cbd5e1;border-radius:6px;background:#f8fafc;text-align:center;">
                
                <label style="width:120px;">SL ĐẠT CHUẨN</label>
                <input type="number" id="SL_DatChuan" name="SL_DatChuan" required
                    style="width:100px;padding:6px;border:1px solid #cbd5e1;border-radius:6px;text-align:center;">

                <label style="width:60px;">Tỉ lệ</label>
                <input type="text" id="percentDat" readonly
                    style="width:80px;padding:6px;border:1px solid #cbd5e1;border-radius:6px;background:#f1f5f9;text-align:center;">
            </div>

            <!-- 🔽 ĐÃ SỬA: Kết quả kiểm tra -->
            <div style="display:flex;align-items:flex-start;margin-bottom:15px;">
                <label style="width:160px;">KẾT QUẢ KIỂM TRA</label>

                <div style="flex:1;">
                    <label style="margin-right:20px;">
                        <input type="radio" name="ketQua" value="Đạt"> Đạt
                    </label>

                    <label>
                        <input type="radio" name="ketQua" value="Không đạt"> Không đạt
                    </label>

                    <!-- 🔽 ĐÃ SỬA: Ghi chú chỉ hiện khi Không đạt -->
                    <div id="ghiChuBox" style="margin-top:10px;display:none;">
                        <textarea name="ghiChu" rows="3"
                            placeholder="Nhập lý do không đạt...và đề xuất xử lý..."
                            style="width:100%;padding:8px;border:1px solid #cbd5e1;border-radius:6px;"></textarea>
                    </div>
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
    var radiosKetQua = document.querySelectorAll("input[name='ketQua']");
    var ghiChuBox = document.getElementById('ghiChuBox');

    // Load SL kiểm tra
    maTP.addEventListener('change', function() {
        var v = this.value;
        if (!v) {
            slKiemTra.value = '';
            percentDat.value = '';
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

    // Tính % (KHÔNG tự quyết định đạt/không đạt)
    slDatChuan.addEventListener('input', function() {
        var kt = parseInt(slKiemTra.value || '0', 10);
        var dc = parseInt(slDatChuan.value || '0', 10);

        if (dc > kt) {
            alert("⚠ Số lượng đạt không được lớn hơn số lượng kiểm tra!");
            slDatChuan.value = kt;
            dc = kt;
        }

        percentDat.value = kt > 0 ? Math.round((dc / kt) * 100) + " %" : "";
    });

    // Hiện ghi chú khi Không đạt
    radiosKetQua.forEach(function(radio){
        radio.addEventListener('change', function(){
            ghiChuBox.style.display = (this.value === 'Không đạt') ? 'block' : 'none';
        });
    });

});

// Validate bắt buộc chọn kết quả
function validateForm() {
    var checked = document.querySelector("input[name='ketQua']:checked");
    if (!checked) {
        alert("⚠ Vui lòng chọn kết quả kiểm tra!");
        return false;
    }
    return true;
}
</script>
