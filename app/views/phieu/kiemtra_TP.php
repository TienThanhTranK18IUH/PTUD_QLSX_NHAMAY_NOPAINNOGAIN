<?php /* View KTTP — QC hiển thị mặc định do controller truyền vào */ ?>
<div class="content" style="margin:20px;">
  <h2 style="margin-bottom:18px;">🧮 Lập phiếu kiểm tra thành phẩm</h2>

  <?php if (isset($_GET['success'])): ?>
    <div style="background:#d4edda;color:#155724;padding:8px 12px;border-radius:6px;margin-bottom:12px;">
      ✅ Lưu phiếu kiểm tra thành công!
    </div>
  <?php elseif (isset($_GET['error'])): ?>
    <div style="background:#f8d7da;color:#721c24;padding:8px 12px;border-radius:6px;margin-bottom:12px;">
      ❌ Có lỗi khi lưu phiếu. Vui lòng kiểm tra lại dữ liệu.
    </div>
  <?php endif; ?>

  <form action="index.php?controller=phieu&action=create_kttp" method="post" style="max-width:680px;">
    <div style="margin-bottom:10px;">
      <label><strong>Mã phiếu:</strong></label><br>
      <input type="text" name="maPhieu" value="<?php echo htmlspecialchars($maPhieu); ?>" readonly
             style="width:100%;padding:8px;border:1px solid #cbd5e1;border-radius:6px;">
    </div>

    <div style="margin-bottom:10px;">
      <label><strong>Thành phẩm:</strong></label><br>
      <select name="maTP" id="maTP" required
              style="width:100%;padding:8px;border:1px solid #cbd5e1;border-radius:6px;">
        <option value="">-- Chọn thành phẩm --</option>
        <?php if (!empty($thanhPhams)) foreach ($thanhPhams as $tp): ?>
          <option value="<?php echo htmlspecialchars($tp['maTP']); ?>">
            <?php echo htmlspecialchars($tp['tenTP']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div style="margin-bottom:10px;">
      <label><strong>Số lượng kiểm tra:</strong></label><br>
      <input type="number" id="SL_KiemTra" name="SL_KiemTra" readonly
             style="width:100%;padding:8px;border:1px solid #cbd5e1;border-radius:6px;">
    </div>

    <div style="margin-bottom:10px;">
      <label><strong>Số lượng đạt chuẩn:</strong></label><br>
      <input type="number" id="SL_DatChuan" name="SL_DatChuan" required
             style="width:100%;padding:8px;border:1px solid #cbd5e1;border-radius:6px;">
    </div>

    <div style="margin-bottom:10px;">
      <label><strong>Kết quả kiểm tra:</strong></label><br>
      <select name="ketQua" required
              style="width:100%;padding:8px;border:1px solid #cbd5e1;border-radius:6px;">
        <option value="Đạt">Đạt</option>
        <option value="Không đạt">Không đạt</option>
      </select>
    </div>

    <div style="margin-bottom:10px;">
      <label><strong>Ngày lập:</strong></label><br>
      <input type="date" name="ngayLap" value="<?php echo date('Y-m-d'); ?>" required
             style="width:100%;padding:8px;border:1px solid #cbd5e1;border-radius:6px;">
    </div>

    <div style="margin-bottom:16px;">
      <label><strong>Nhân viên QC:</strong></label><br>
      <input type="text" value="<?php echo htmlspecialchars($hoTenQC.' ('.$nguoiQC.')'); ?>"
             readonly style="width:100%;padding:8px;border:1px solid #cbd5e1;border-radius:6px;background:#f8fafc;">
    </div>

    <div>
      <button type="submit"
              style="background:#2563eb;color:#fff;padding:10px 16px;border:none;border-radius:6px;">
        💾 Lưu phiếu
      </button>
      <a href="index.php?controller=dashboard"
         style="margin-left:10px;text-decoration:none;background:#e5e7eb;color:#111;padding:10px 16px;border-radius:6px;">
        ⬅ Quay lại
      </a>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var maTP = document.getElementById('maTP');
  var slKiemTra = document.getElementById('SL_KiemTra');
  var slDatChuan = document.getElementById('SL_DatChuan');

  maTP.addEventListener('change', function () {
    var v = this.value;
    if (!v) { slKiemTra.value = ''; return; }

    var xhr = new XMLHttpRequest();
    xhr.open('POST','index.php?controller=phieu&action=getSL', true);
    xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    xhr.onload = function(){
      if (xhr.status === 200) {
        var text = xhr.responseText || '';
        var m = text.match(/(\d+)/);
        if (m) slKiemTra.value = parseInt(m[1],10);
        else { alert('⚠️ Phản hồi không hợp lệ:\n' + text.substring(0,200)); slKiemTra.value=''; }
      }
    };
    xhr.onerror = function(){ alert('🚫 Lỗi kết nối máy chủ!'); };
    xhr.send('maTP='+encodeURIComponent(v));
  });

  slDatChuan.addEventListener('input', function(){
    var kt = parseInt(slKiemTra.value||'0',10);
    var dc = parseInt(slDatChuan.value||'0',10);
    if (dc > kt) { alert('⚠️ Số lượng đạt chuẩn không được lớn hơn số lượng kiểm tra!'); slDatChuan.value = kt; }
  });
});
</script>
