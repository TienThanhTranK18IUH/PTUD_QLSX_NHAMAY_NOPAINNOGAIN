<?php
// View: Lịch & Giờ công — PHP 5.x
if (!isset($tab)) $tab = 'lich';

$qsBase = 'index.php?controller=lich&action=index'
        .'&nv='.urlencode($nv)
        .'&d='.urlencode(isset($d)?$d:$from);

function tabActive($cur,$tab){ return $cur==$tab ? ' class="tab-active"' : ''; }
$thangLabel = date('m/Y', strtotime($d));
?>
<div class="card">
  <div class="title">📅 Lịch làm &amp; Giờ công (Công nhân)</div>

  <form id="frmFilter" method="get" action="index.php" class="toolbar">
    <input type="hidden" name="controller" value="lich"/>
    <input type="hidden" name="action" value="index"/>
    <input type="hidden" name="tab" id="tab" value="<?php echo htmlspecialchars($tab); ?>"/>

    <label>Công nhân:</label>
    <?php if (count($dsNV)===1 && isset($_SESSION['user']) && isset($_SESSION['user']['maNguoiDung']) && $_SESSION['user']['maNguoiDung']===$nv):
            $r = $dsNV[0]; ?>
      <span style="font-weight:600;"><?php echo htmlspecialchars($r['maNguoiDung'].' - '.(isset($r['hoTen'])?$r['hoTen']:'')); ?></span>
      <input type="hidden" name="nv" id="nv" value="<?php echo htmlspecialchars($r['maNguoiDung']); ?>" />
    <?php else: ?>
    <select name="nv" id="nv">
      <?php for($i=0;$i<count($dsNV);$i++):
        $r=$dsNV[$i]; $sel=($r['maNguoiDung']==$nv)?' selected="selected"':''; ?>
        <option value="<?php echo htmlspecialchars($r['maNguoiDung']); ?>"<?php echo $sel; ?>>
          <?php echo htmlspecialchars($r['maNguoiDung'].' - '.(isset($r['hoTen'])?$r['hoTen']:'')); ?>
        </option>
      <?php endfor; ?>
    </select>
    <?php endif; ?>

    <label style="margin-left:10px;">Chọn ngày:</label>
    <input type="date" name="d" id="d" value="<?php echo htmlspecialchars(isset($d)?$d:date('Y-m-d')); ?>"/>

    <div class="btn-row">
      <button type="button" class="btn btn-primary" id="btnToday">Hiện tại</button>
      <button type="button" class="btn" id="btnPrint">In lịch</button>
      <button type="button" class="btn" id="btnPrev">‹ Trở về</button>
      <button type="button" class="btn" id="btnNext">Tiếp ›</button>
    </div>

    <span class="week-note">
      (Tuần: <?php echo date('d/m/Y', strtotime($from)); ?> – <?php echo date('d/m/Y', strtotime($to)); ?>)
    </span>
  </form>

  <div class="tabs">
    <a href="<?php echo $qsBase.'&tab=lich'; ?>"><span<?php echo tabActive('lich',$tab); ?>>Lịch làm việc</span></a>
    <a href="<?php echo $qsBase.'&tab=gio';  ?>"><span<?php echo tabActive('gio',$tab);  ?>>Giờ công</span></a>
  </div>

  <?php if ($tab==='lich'): ?>
    <!-- giữ nguyên bảng lịch tuần của bạn -->
    <div class="panel">
      <div class="panel-title">Lịch làm chi tiết theo Tuần</div>
      <table class="tbl">
        <thead>
          <tr>
            <th style="width:140px;">Ngày</th>
            <th style="width:140px;">Thứ</th>
            <th style="width:180px;">Ca làm</th>
            <th style="width:280px;">Thời gian</th>
            <th style="width:120px;">Giờ công</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($lichTuan)): ?>
          <tr><td colspan="5" class="muted">Không có lịch làm trong tuần này.</td></tr>
        <?php else: for ($i=0;$i<count($lichTuan);$i++): $r=$lichTuan[$i]; ?>
          <tr>
            <td><?php echo date('d/m/Y', strtotime($r['ngay'])); ?></td>
            <td><?php echo htmlspecialchars($r['thu']); ?></td>
            <td><?php echo $r['ca'];   ?></td>
            <td><?php echo $r['time']; ?></td>
            <td style="text-align:right;"><?php echo isset($r['gioCong'])?$r['gioCong']:'0.00'; ?></td>
          </tr>
        <?php endfor; endif; ?>
        </tbody>
      </table>
    </div>

  <?php else: ?>
    <!-- GIỜ CÔNG THEO THÁNG -->
    <?php
      $tongGio = isset($mstats['tongGio']) ? $mstats['tongGio'] : 0;
      $soCa    = isset($mstats['soCa']) ? $mstats['soCa'] : 0;
      $nghiCL  = isset($mstats['nghiCoLuong']) ? $mstats['nghiCoLuong'] : 0;
      $nghiKCL = isset($mstats['nghiKhongLuong']) ? $mstats['nghiKhongLuong'] : 0;
      $night   = isset($nightCa) ? intval($nightCa) : 0;
    ?>
    <div class="panel">
      <div class="panel-title">Thông tin giờ công (Tháng <?php echo $thangLabel; ?>)</div>
      <p><b>Tổng số giờ làm việc:</b> <?php echo number_format($tongGio,2,'.',''); ?> giờ</p>
      <p><b>Số ca làm:</b> <?php echo intval($soCa); ?> ca</p>
      <p><b>Số ca đêm (CA3):</b> <?php echo $night; ?> ca</p>
      <p><b>Số ngày nghỉ có lương:</b> <?php echo intval($nghiCL); ?> ngày</p>
      <p><b>Số ngày nghỉ không lương:</b> <?php echo intval($nghiKCL); ?> ngày</p>
      <p class="muted">Khoảng tính: <?php echo date('d/m/Y', strtotime($mFrom)); ?> – <?php echo date('d/m/Y', strtotime($mTo)); ?></p>
    </div>
  <?php endif; ?>
</div>

<style>
  .card{background:#fff;border:1px solid #e4e7eb;border-radius:8px;padding:14px;margin:10px 15px}
  .title{font-size:22px;font-weight:700;margin-bottom:12px}
  .toolbar{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
  .btn-row{display:inline-flex;gap:8px;margin-left:8px}
  .btn{padding:6px 10px;border:1px solid #d0d6e0;background:#f7f9fc;border-radius:6px;cursor:pointer}
  .btn-primary{background:#2684ff;border-color:#2684ff;color:#fff}
  .week-note{margin-left:10px;color:#666}
  .tabs{margin:12px 0}
  .tabs a{margin-right:24px;text-decoration:none;color:#1f5fbf}
  .tabs .tab-active{border-bottom:3px solid #1f5fbf;font-weight:700;padding-bottom:3px}
  .panel{background:#fff;border:1px solid #e4e7eb;border-radius:6px;padding:12px 14px;margin-bottom:16px}
  .panel-title{font-size:18px;font-weight:700;margin-bottom:10px}
  .tbl{width:100%;border-collapse:collapse}
  .tbl th,.tbl td{border:1px solid #e5e7ea;padding:10px;vertical-align:top}
  .tbl thead th{background:#f5f7fb}
  .muted{color:#777;text-align:left}
</style>

<script>
(function(){
  var nv = document.getElementById('nv');
  var d  = document.getElementById('d');
  var frm= document.getElementById('frmFilter');

  function submitWith(dateStr){
    if (dateStr) d.value = dateStr;
    frm.submit();
  }
  d.onchange = function(){ submitWith(); };
  nv.onchange = function(){ submitWith(); };

  document.getElementById('btnToday').onclick = function(){
    var t = new Date();
    var m = (t.getMonth()+1); if (m<10) m='0'+m;
    var day = t.getDate();    if (day<10) day='0'+day;
    submitWith(t.getFullYear()+'-'+m+'-'+day);
  };
  document.getElementById('btnPrint').onclick = function(){ window.print(); };

  function addDays(dateStr, days){
    var t = new Date(dateStr.replace(/-/g,'/'));
    t.setDate(t.getDate()+days);
    var m = (t.getMonth()+1); if (m<10) m='0'+m;
    var day = t.getDate();    if (day<10) day='0'+day;
    return t.getFullYear()+'-'+m+'-'+day;
  }
  document.getElementById('btnPrev').onclick = function(){ submitWith(addDays(d.value, -7)); };
  document.getElementById('btnNext').onclick = function(){ submitWith(addDays(d.value, 7)); };
})();
</script>
