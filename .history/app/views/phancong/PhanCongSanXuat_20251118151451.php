<?php declare(strict_types=1); 
// View: PhanCongSanXuat.php — không BOM/ khoảng trắng trước thẻ PHP
?>

<?php
// Hiển thị thông báo sau khi save
if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
    if ($msg === 'LuuThanhCong') {
        echo '<div class="alert alert-success">Lưu phân công thành công!</div>';
    } elseif ($msg === 'LuuThatBai') {
        echo '<div class="alert alert-danger">Lưu phân công thất bại!</div>';
    }
}
?>

<div class="content-wrapper">
  <div class="page-header">
    <h2><i class="fa fa-tasks"></i> PHÂN CÔNG SẢN XUẤT</h2>
    <p class="subtitle">Phân công nhân sự theo kế hoạch và ca làm việc.</p>
    <hr>
  </div>

  <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>
  <?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <form method="post" action="index.php?controller=PhanCongCongViecSanXuat&amp;action=save">

        <!-- 🧍 NGƯỜI DÙNG: tự lấy từ DB (vai trò QuanLy) — không cho nhập/chọn -->
        <div class="form-group">
          <label><strong>Người dùng</strong></label>
          <?php if (isset($quanLy) && $quanLy): ?>
            <input type="hidden" name="maNguoiDung"
                   value="<?php echo htmlspecialchars($quanLy['maNguoiDung']); ?>">
            <input type="text" class="form-control" readonly
                   value="<?php
                     echo htmlspecialchars(
                       $quanLy['maNguoiDung'].' - '.$quanLy['hoTen'].
                       ' ('.$quanLy['vaiTro'].($quanLy['tenXuong'] ? ' - '.$quanLy['tenXuong'] : '').')'
                     );
                   ?>">
          <?php else: ?>
            <input type="text" class="form-control" readonly value="(Không tìm thấy Quản lý hoạt động)">
          <?php endif; ?>
        </div>

        <!-- 🧭 KẾ HOẠCH SẢN XUẤT -->
        <div class="form-group">
          <label><strong>Kế hoạch sản xuất</strong></label>
          <select id="maKeHoach" class="form-control" onchange="capNhatThongTinKeHoach()" required>
            <option value="">-- Chọn kế hoạch sản xuất --</option>
            <?php if (!empty($keHoachList)): foreach ($keHoachList as $kh): ?>
              <option
                value="<?php echo htmlspecialchars($kh['maXuong']); ?>"
                data-tongsoluong="<?php echo isset($kh['tongSoLuong']) ? htmlspecialchars($kh['tongSoLuong']) : 0; ?>"
                data-batdau="<?php echo htmlspecialchars($kh['ngayBatDau']); ?>"
                data-ketthuc="<?php echo htmlspecialchars($kh['ngayKetThuc']); ?>"
              >
                <?php echo htmlspecialchars($kh['maKeHoach'].' - Xưởng '.$kh['maXuong']); ?>
              </option>
            <?php endforeach; endif; ?>
          </select>
        </div>

        <!-- ⏰ CA LÀM VIỆC -->
        <div class="form-group">
          <label><strong>Ca làm việc</strong></label>
          <select name="maCa" class="form-control" required>
            <option value="">-- Chọn ca --</option>
            <?php if (!empty($caList)): foreach ($caList as $ca): ?>
              <option value="<?php echo htmlspecialchars($ca['maCa']); ?>">
                <?php
                  $bd = isset($ca['gioBatDau']) ? $ca['gioBatDau'] : (isset($ca['thoiGianBatDau']) ? $ca['thoiGianBatDau'] : '');
                  $kt = isset($ca['gioKetThuc']) ? $ca['gioKetThuc'] : (isset($ca['thoiGianKetThuc']) ? $ca['thoiGianKetThuc'] : '');
                  echo htmlspecialchars($ca['maCa'].' ('.$bd.' - '.$kt.')');
                ?>
              </option>
            <?php endforeach; endif; ?>
          </select>
        </div>

        <div class="form-group">
          <label><strong>Mã xưởng</strong></label>
          <input type="text" id="maXuong" name="maXuong" class="form-control" readonly required>
        </div>

              <!-- MÔ TẢ CÔNG VIỆC (chọn từ Bộ phận theo xưởng) -->
      <div class="form-group">
        <label><strong>Mô tả công việc</strong></label>
        <select id="moTaCongViec" name="moTaCongViec" class="form-control" required>
          <option value="">-- Chọn công việc / bộ phận --</option>
          <?php
            // Lấy danh sách bộ phận từ DB (nếu đã có $bophanList từ controller)
            // $bophanList = array(
            //    array('maBoPhan'=>'BP001','tenBoPhan'=>'Cắt da giày','maXuong'=>'X001'),
            //    ...
            // );
            if (!empty($bophanList)):
              foreach ($bophanList as $bp):
          ?>
            <option value="<?php echo htmlspecialchars($bp['tenBoPhan']); ?>"
                    data-maxuong="<?php echo htmlspecialchars($bp['maXuong']); ?>">
              <?php echo htmlspecialchars($bp['tenBoPhan']); ?>
            </option>
          <?php endforeach; endif; ?>
        </select>
      </div>


        <div class="form-group">
          <label><strong>Số lượng</strong></label>
          <input type="number" id="soLuong" name="soLuong" class="form-control" readonly>
        </div>

        <div class="form-group">
          <label><strong>Ngày bắt đầu</strong></label>
          <input type="date" id="ngayBatDau" name="ngayBatDau" class="form-control" readonly>
        </div>

        <div class="form-group">
          <label><strong>Ngày kết thúc</strong></label>
          <input type="date" id="ngayKetThuc" name="ngayKetThuc" class="form-control" readonly>
        </div>

        <div class="form-actions text-center mt-4">
          <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Lưu phân công</button>
          <button type="reset" class="btn btn-secondary"><i class="fa fa-times"></i> Hủy</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
function capNhatThongTinKeHoach() {
  var select = document.getElementById('maKeHoach');
  if (!select) return;
  var option = select.options[select.selectedIndex] || null;
  if (!option) return;

  var maXuong = option.value || '';
  document.getElementById('maXuong').value     = maXuong;
  document.getElementById('soLuong').value     = option.getAttribute('data-tongsoluong') || 0;
  document.getElementById('ngayBatDau').value  = option.getAttribute('data-batdau') || '';
  document.getElementById('ngayKetThuc').value = option.getAttribute('data-ketthuc') || '';

  // Lọc công việc theo maXuong
  var moTaSelect = document.getElementById('moTaCongViec');
  for (var i = 0; i < moTaSelect.options.length; i++) {
    var opt = moTaSelect.options[i];
    if (i === 0) { // giữ option "-- Chọn ..."
      opt.style.display = '';
      continue;
    }
    if (opt.getAttribute('data-maxuong') === maXuong) {
      opt.style.display = '';
    } else {
      opt.style.display = 'none';
    }
  }
  moTaSelect.value = ''; // reset chọn
}
</script>


<style>
  .content-wrapper {padding:30px;background:#f8f9fa;border-radius:8px;}
  .page-header h2 {font-weight:700;color:#2c3e50;margin-bottom:5px;}
  .subtitle {color:#666;margin-bottom:20px;}
  .card {background:#fff;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.08);padding:25px;}
  .form-group {margin-bottom:18px;}
  .form-control {width:100%;padding:10px 12px;border:1px solid #ccc;border-radius:6px;font-size:15px;}
  .form-control:focus {border-color:#007bff;box-shadow:0 0 3px rgba(0,123,255,0.4);outline:none;}
  .btn {padding:10px 20px;border-radius:6px;border:none;font-size:15px;cursor:pointer;}
  .btn-primary {background:#007bff;color:#fff;} .btn-primary:hover {background:#0056b3;}
  .btn-secondary {background:#6c757d;color:#fff;} .btn-secondary:hover {background:#5a6268;}
  .alert {padding:10px 15px;border-radius:6px;margin-bottom:15px;}
  .alert-danger {background:#f8d7da;color:#721c24;} .alert-success {background:#d4edda;color:#155724;}
</style>
