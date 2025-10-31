<?php
$title    = isset($title) ? $title : 'Lập phiếu yêu cầu nguyên liệu';
$msg      = isset($msg) ? $msg : '';
$kehoach  = isset($kehoach) ? $kehoach : null;
$items    = isset($items) ? $items : array();
$user     = isset($user) ? $user : array('maNguoiDung'=>'', 'hoTen'=>'--', 'vaiTro'=>'');
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo htmlspecialchars($title); ?></title>
<style>
/* ======== RESET + FONT ======== */
*{box-sizing:border-box;}
html,body{margin:0;padding:0;}
body{
    font-family:"Segoe UI", Arial, sans-serif;
    background:#f5f6f8;
    color:#222;
    font-size:15px;
    line-height:1.5;
}
h2{
    margin:0 0 18px;
    font-size:22px;
    color:#111827;
    font-weight:700;
}
.wrap{max-width:1100px;margin:22px auto;padding:0 20px;}
.card{
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:10px;
    padding:18px 20px;
    margin:15px 0;
    box-shadow:0 1px 3px rgba(0,0,0,0.05);
}
.small{font-size:13px;color:#6b7280;margin-bottom:4px;}
.bold{font-weight:600;}
.badge{
    display:inline-block;
    background:#eef2ff;
    border:1px solid #c7d2fe;
    color:#3730a3;
    padding:3px 10px;
    border-radius:12px;
    font-weight:600;
    font-size:14px;
}
.grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:15px;}
label{font-size:14px;font-weight:500;color:#374151;}
b{font-weight:600;color:#111;}
.table{width:100%;border-collapse:collapse;margin-top:8px;}
.table th,.table td{
    border:1px solid #e5e7eb;
    padding:9px 10px;
    font-size:14px;
    text-align:left;
}
.table th{
    background:#f9fafb;
    color:#111827;
    font-weight:600;
}
input[type=text],textarea{
    width:100%;
    padding:7px 9px;
    border:1px solid #d1d5db;
    border-radius:6px;
    background:#fff;
    font-size:14px;
}
input[readonly]{background:#f9fafb;color:#333;}
textarea{min-height:100px;resize:vertical;}
.actions{text-align:center;margin-top:18px;}
.btn{
    padding:9px 18px;
    border-radius:6px;
    border:none;
    cursor:pointer;
    font-size:14px;
    font-weight:500;
}
.btn.primary{
    background:#2563eb;
    color:#fff;
    transition:0.25s;
}
.btn.primary:hover{background:#1d4ed8;}
.btn.secondary{
    background:#e5e7eb;
    color:#111;
}
.notice{
    padding:10px 12px;
    border-radius:8px;
    margin:12px 0;
}
.notice.ok{background:#e8fff1;border:1px solid #b7eccb;color:#05603a;}
.notice.err{background:#fff1f1;border:1px solid #f5c2c7;color:#842029;}
</style>
</head>
<body>
<div class="wrap">
  <h2><?php echo htmlspecialchars($title); ?></h2>

  <?php if ($msg !== ''): ?>
    <div class="notice <?php echo (strpos($msg,'✅')!==false)?'ok':'err'; ?>">
      <?php echo $msg; ?>
    </div>
  <?php endif; ?>

  <form method="post" action="">
    <!-- Thông tin chính -->
    <div class="card">
      <div class="grid-3">
        <div>
          <div class="small">Mã yêu cầu</div>
          <div><span class="badge"><?php echo htmlspecialchars($nextCode); ?></span></div>
          <input type="hidden" name="previewCode" value="<?php echo htmlspecialchars($nextCode); ?>">
        </div>
        <div>
          <div class="small">Ngày yêu cầu</div>
          <b><?php echo date('Y-m-d'); ?></b>
          <input type="hidden" name="ngayLap" value="<?php echo date('Y-m-d'); ?>">
        </div>
        <div>
          <div class="small">Người lập phiếu</div>
          <b><?php echo htmlspecialchars(isset($user['hoTen']) ? $user['hoTen'] : '--'); ?></b>
          <?php if (!empty($user['vaiTro'])): ?>
            <span style="color:#6b7280;"> (<?php echo htmlspecialchars($user['vaiTro']); ?>)</span>
          <?php endif; ?>
          <input type="hidden" name="nguoiLap"   value="<?php echo htmlspecialchars(isset($user['hoTen']) ? $user['hoTen'] : ''); ?>">
          <input type="hidden" name="maNguoiLap" value="<?php echo htmlspecialchars(isset($user['maNguoiDung']) ? $user['maNguoiDung'] : ''); ?>">
        </div>
      </div>

      <?php if ($kehoach): ?>
        <div class="grid-3" style="margin-top:15px;">
          <div>
            <div class="small">Tên kế hoạch sản xuất</div>
            <b><?php echo htmlspecialchars($kehoach['TenKH']); ?></b>
          </div>
          <div>
            <div class="small">Tên xưởng</div>
            <b><?php echo htmlspecialchars($kehoach['TenXuong']); ?></b>
          </div>
          <div>
            <div class="small">Mã xưởng</div>
            <b><?php echo htmlspecialchars($kehoach['MaXuong']); ?></b>
          </div>
          <div>
            <div class="small">Ngày bắt đầu</div>
            <b><?php echo htmlspecialchars($kehoach['NgayBatDau']); ?></b>
          </div>
          <div>
            <div class="small">Ngày kết thúc</div>
            <b><?php echo htmlspecialchars($kehoach['NgayKetThuc']); ?></b>
          </div>
        </div>
        <input type="hidden" name="maKeHoach" value="<?php echo htmlspecialchars($kehoach['MaKH']); ?>">
      <?php endif; ?>
    </div>

    <!-- Bảng chi tiết nguyên liệu -->
    <div class="card">
      <div class="bold" style="margin-bottom:8px;">Chi tiết nguyên liệu yêu cầu</div>
      <table class="table">
        <thead>
          <tr>
            <th style="width:200px;">Mã nguyên liệu</th>
            <th>Tên nguyên liệu</th>
            <th style="width:100px;">Đơn vị</th>
            <th style="width:90px;">Số lượng</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($items)) { $r=$items[0]; ?>
          <tr>
            <td><input type="text" name="item[0][ma]"  value="<?php echo htmlspecialchars($r['ma']); ?>" readonly></td>
            <td><input type="text" name="item[0][ten]" value="<?php echo htmlspecialchars($r['ten']); ?>" readonly></td>
            <td><input type="text" name="item[0][dv]"  value="<?php echo htmlspecialchars($r['dv']); ?>"  readonly></td>
            <td><input type="text" name="item[0][sl]"  value="<?php echo (int)$r['sl']; ?>" readonly></td>
          </tr>
          <?php } else { ?>
          <tr>
            <td><input type="text" name="item[0][ma]"  value=""></td>
            <td><input type="text" name="item[0][ten]" value=""></td>
            <td><input type="text" name="item[0][dv]"  value=""></td>
            <td><input type="text" name="item[0][sl]"  value="1"></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>

    <!-- Ghi chú -->
    <div class="card">
      <div class="small">Ghi chú</div>
      <textarea name="ghiChu" placeholder="Nhập ghi chú (nếu có)"></textarea>
    </div>

    <div class="actions">
      <button type="submit" class="btn primary">XÁC NHẬN LẬP PHIẾU</button>
      <button type="reset"  class="btn secondary">HỦY</button>
    </div>
  </form>
</div>
</body>
</html>
