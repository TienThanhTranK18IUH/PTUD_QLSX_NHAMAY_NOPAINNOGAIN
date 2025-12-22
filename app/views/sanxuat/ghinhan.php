<?php
$XT         = isset($GLOBALS['XT']) ? $GLOBALS['XT'] : array();
$dsCongNhan = isset($GLOBALS['dsCongNhan']) ? $GLOBALS['dsCongNhan'] : array();
$dsCa       = isset($GLOBALS['dsCa']) ? $GLOBALS['dsCa'] : array();
$CA_MAP     = isset($GLOBALS['CA_MAP']) ? $GLOBALS['CA_MAP'] : array();
$notice_ok  = isset($GLOBALS['notice_ok']) ? $GLOBALS['notice_ok'] : '';

// Chuẩn hóa CA_MAP về HH:MM thay vì HH:MM:SS để điền vào input time
$_CA_JS = array();
foreach ($CA_MAP as $ma => $pair) {
  $start = isset($pair[0]) ? substr($pair[0],0,5) : '';
  $end   = isset($pair[1]) ? substr($pair[1],0,5) : '';
  $_CA_JS[$ma] = array($start, $end);
}
?>
<div class="container my-4">
  <div class="form-shell">
    <div class="form-header">
      <h2>🧾 Ghi nhận sản xuất</h2>
    </div>

    <div id="msgBox">
      <?php if (isset($_SESSION['err']) && $_SESSION['err']): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['err']; unset($_SESSION['err']); ?></div>
      <?php elseif (!empty($notice_ok)): ?>
        <div class="alert alert-success">✅ Dữ liệu đã được lưu thành công!</div>
      <?php endif; ?>
    </div>



    <form id="ghiNhanForm" method="post" action="index.php?controller=sanxuat&action=save">
      <?php if (isset($GLOBALS['isManager']) && $GLOBALS['isManager']): ?>
      <div class="grid-2">
        <div class="form-group">
          <label>Xưởng trưởng</label>
          <select id="xuongTruongSelect" class="input" name="tenXuong">
            <option value="">-- Chọn xưởng trưởng --</option>
            <?php foreach(isset($dsXuongTruong)?$dsXuongTruong:array() as $xtRow): 
              $sel = (isset($GLOBALS['selectedTenXuong']) && $GLOBALS['selectedTenXuong']==$xtRow['tenXuong']) ? 'selected' : '';
            ?>
              <option value="<?php echo htmlspecialchars($xtRow['tenXuong']); ?>" data-maxuong="<?php echo isset($xtRow['maXuong'])?$xtRow['maXuong']:''; ?>" <?php echo $sel; ?> >
                <?php echo htmlspecialchars($xtRow['hoTen'].' — '.$xtRow['tenXuong']); ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?php if (empty($dsXuongTruong)): ?>
            <div class="alert alert-danger" style="margin-top:8px;">⚠️ Không tìm thấy xưởng trưởng (vui lòng kiểm tra trường <code>vaiTro</code> và <code>tenXuong</code> trong bảng <code>nguoidung</code>).</div>
          <?php endif; ?>
        </div>
        <div class="form-group">
          <label>Mã xưởng</label>
          <input type="text" class="input" id="maXuongField" name="maXuong" value="<?php echo isset($GLOBALS['selectedMaXuong'])?$GLOBALS['selectedMaXuong']:(isset($GLOBALS['XT']['maXuong'])?$GLOBALS['XT']['maXuong']:''); ?>" readonly>
        </div>
      </div>
      <?php else: ?>
      <div class="grid-2">
        <div class="form-group">
          <label>Xưởng trưởng</label>
          <input type="text" class="input" value="<?php echo isset($XT['hoTen'])?$XT['hoTen']:''; ?>" readonly>
        </div>
        <div class="form-group">
          <label>Mã xưởng</label>
          <input type="text" class="input" value="<?php echo isset($XT['maXuong'])?$XT['maXuong']:''; ?>" readonly>
        </div>
      </div>
      <?php endif; ?>

      <div class="form-group">
        <label>Chọn công nhân <span class="req">*</span></label>
        <select name="maNguoiDung" id="maNguoiDung" class="input" required>
          <option value="">-- Chọn công nhân thuộc xưởng --</option>
          <?php foreach($dsCongNhan as $cn): ?>
            <option value="<?php echo $cn['maNguoiDung']; ?>">
              <?php echo $cn['maNguoiDung'].' — '.$cn['hoTen']; ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="grid-2">
        <div class="form-group">
          <label>Ngày chấm công <span class="req">*</span></label>
          <input type="date" name="ngayCham" id="ngayCham" class="input" required>
        </div>
        <div class="form-group">
          <label>Ca làm việc</label>
          <select name="maCa" id="maCa" class="input">
            <option value="">-- Chọn ca --</option>
            <?php foreach($dsCa as $ca): ?>
              <option value="<?php echo $ca['maCa']; ?>">
                <?php echo $ca['maCa'].' ('.substr($ca['thoiGianBatDau'],0,5).' - '.substr($ca['thoiGianKetThuc'],0,5).')'; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="grid-3">
        <div class="form-group">
          <label>Giờ vào</label>
          <!-- ép 24h -->
          <input type="time" name="gioVao" id="gioVao" class="input" lang="en-GB" step="60">
        </div>
        <div class="form-group">
          <label>Giờ ra</label>
          <input type="time" name="gioRa" id="gioRa" class="input" lang="en-GB" step="60">
        </div>
        <div class="form-group">
          <label>Số giờ làm (tự tính)</label>
          <input type="text" name="soGioLam" id="soGioLam" class="input" readonly>
        </div>
      </div>

      <div class="grid-2">
        <div class="form-group">
          <label>Loại ngày</label>
          <select name="loaiNgay" id="loaiNgay" class="input">
            <option value="NgayThuong">Ngày thường</option>
            <option value="NghiCoLuong">Nghỉ có lương</option>
            <option value="NghiKhongLuong">Nghỉ không lương</option>
            <option value="NgayLe">Ngày lễ</option>
            <option value="ChuNhat">Chủ nhật</option>
            <option value="TangCa">Tăng ca</option>
          </select>
        </div>
        <div class="form-group">
          <label>Sản lượng hoàn thành</label>
          <input type="number" min="0" step="1" name="sanLuongHoanThanh" id="sanLuongHoanThanh" class="input" placeholder="VD: 30">
        </div>
      </div>

      <div class="actions">
        <button type="submit" class="btn-primary">💾 Lưu ghi nhận</button>
        <a href="index.php?controller=sanxuat&action=ghinhan" class="btn-ghost" id="cancelBtn">Hủy</a>
      </div>
    </form>
  </div>
</div>

<style>
  .form-shell{
    max-width: 1100px; margin: 0 auto; background:#fff;
    border-radius:18px; padding:32px 34px; box-shadow:0 6px 24px rgba(0,0,0,.08);
  }
  .form-header h2{font-size:2rem;font-weight:800;color:#0d47a1;margin:0 0 10px}
  .req{color:#d32f2f}

  .grid-2,.grid-3{display:grid;gap:22px;align-items:stretch;margin-top:6px;margin-bottom:6px}
  .grid-2{grid-template-columns:repeat(2,minmax(0,1fr))}
  .grid-3{grid-template-columns:repeat(3,minmax(0,1fr))}
  @media(max-width:992px){.grid-2,.grid-3{grid-template-columns:1fr}}

  .form-group{display:flex;flex-direction:column}
  .form-group label{font-weight:700;margin:4px 0 8px;color:#212529}
  .input{
    box-sizing:border-box;width:100%;height:52px;min-height:52px;
    border:1px solid #dcdfe3;border-radius:10px;
    padding:10px 12px;font-size:15px;background:#fff;line-height:30px;
  }
  .input:focus{outline:none;border-color:#0d6efd;box-shadow:0 0 0 3px rgba(13,110,253,.18)}
  .input[readonly]{background:#f8f9fa}
  select.input{-webkit-appearance:none;-moz-appearance:none;appearance:none;
    background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>");
    background-repeat:no-repeat;background-position:right 12px center;padding-right:34px;
  }
  .alert{padding:12px 14px;border-radius:8px;margin-top:14px}
  .alert-danger{background:#fdeaea;color:#b02a37}
  .alert-success{background:#eaf7ec;color:#1e7e34}
  .actions{margin-top:20px}
  .btn-primary{background:#0d6efd;border:none;color:#fff;font-weight:700;
    padding:12px 22px;border-radius:10px;cursor:pointer;font-size:15px}
  .btn-primary:hover{background:#0b5ed7}
  .btn-ghost{padding:12px 18px;border-radius:10px;border:1px solid #dee2e6;margin-left:10px;color:#333;text-decoration:none;background:#f8f9fa}
  .btn-ghost:hover{background:#e9ecef}
</style>

<script>
(function(){
  // Map ca -> [HH:MM, HH:MM]
  var CA_MAP = <?php echo json_encode($_CA_JS); ?>;

  // Lấy ngày hôm nay theo local (YYYY-MM-DD)
  function todayYMD() {
    var d = new Date();
    d.setMinutes(d.getMinutes() - d.getTimezoneOffset()); // chuẩn hóa local
    return d.toISOString().slice(0,10);
  }

  // set default today cho ngayCham (khi load & sau reset)
  var ngayCham = document.getElementById('ngayCham');
  if (ngayCham && !ngayCham.value) {
    ngayCham.value = todayYMD();
  }

  function calcHours(){
    var gv=document.getElementById('gioVao').value,
        gr=document.getElementById('gioRa').value;
    if(!gv||!gr){ document.getElementById('soGioLam').value=''; return; }
    var sv=new Date('1970-01-01T'+gv+':00'),
        sr=new Date('1970-01-01T'+gr+':00');
    var diff=(sr-sv)/3600000; if(diff<0) diff+=24;
    document.getElementById('soGioLam').value=(Math.round(diff*100)/100).toFixed(2);
  }

  // chọn ca -> tự điền giờ; bỏ chọn -> clear giờ
  var sel=document.getElementById('maCa');
  if(sel){ sel.addEventListener('change',function(){
    var v=this.value;
    if(CA_MAP[v]){
      document.getElementById('gioVao').value=CA_MAP[v][0];
      document.getElementById('gioRa').value =CA_MAP[v][1];
      calcHours();
    } else {
      document.getElementById('gioVao').value='';
      document.getElementById('gioRa').value='';
      document.getElementById('soGioLam').value='';
    }
  });}

  var g1=document.getElementById('gioVao'), g2=document.getElementById('gioRa');
  if(g1) g1.addEventListener('change',calcHours);
  if(g2) g2.addEventListener('change',calcHours);

  <?php if (isset($GLOBALS['isManager']) && $GLOBALS['isManager']): ?>
  // Hàm load công nhân theo tenXuong (AJAX) — chỉ cho quản lý
  function loadWorkersFor(tenXuong){
    var sel = document.getElementById('maNguoiDung');
    if(!sel) return;
    sel.innerHTML = '<option value="">-- Đang tải công nhân... --</option>';
    (function(){

      // Use POST so front controller will run without layout and return pure JSON
      fetch('index.php?controller=sanxuat&action=getWorkers', { method: 'POST', credentials: 'same-origin', headers: {'Content-Type':'application/x-www-form-urlencoded'}, body: 'tenXuong='+encodeURIComponent(tenXuong) })
        .then(function(r){
          return r.text().then(function(t){
            var s = 'HTTP/'+r.status+' '+r.statusText+' — body len='+ (t? t.length : 0);
            try {
              var json = t ? JSON.parse(t) : null;
            } catch(e){
              // nếu response không phải JSON, hiển thị body
              throw new Error('invalid-json:'+t);
            }
            if (!r.ok){
              throw new Error('server:'+r.status+': '+(typeof json==='object'?JSON.stringify(json):t));
            }
            return json;
          });
        })
        .then(function(data){
          sel.innerHTML = '<option value="">-- Chọn công nhân thuộc xưởng --</option>';
          if(Array.isArray(data) && data.length>0){
            data.forEach(function(c){
              var opt = document.createElement('option');
              opt.value = c.maNguoiDung;
              opt.textContent = c.maNguoiDung + ' — ' + c.hoTen;
              sel.appendChild(opt);
            });
          } else {
            sel.innerHTML = '<option value="">-- Không tìm thấy công nhân --</option>';
          }
        })
        .catch(function(err){ console.error('Lỗi tải công nhân:', err); sel.innerHTML = '<option value="">-- Lỗi khi tải danh sách --</option>'; });
    })();
  }

  // Cập nhật mã xưởng và load công nhân khi chọn xưởng trưởng
  var xuongSel = document.getElementById('xuongTruongSelect');
  var maXuongFld = document.getElementById('maXuongField');
  if(xuongSel){
    var initTen = xuongSel.value ? xuongSel.value : '<?php echo isset($GLOBALS['selectedTenXuong'])?$GLOBALS['selectedTenXuong']:''; ?>';
    var initMa = xuongSel.options[xuongSel.selectedIndex] ? xuongSel.options[xuongSel.selectedIndex].getAttribute('data-maxuong') : '<?php echo isset($GLOBALS['selectedMaXuong'])?$GLOBALS['selectedMaXuong']:''; ?>';
    if(maXuongFld && initMa) maXuongFld.value = initMa;
    if(initTen) loadWorkersFor(initTen);

    xuongSel.addEventListener('change', function(){
      var ten = this.value;
      var ma = this.options[this.selectedIndex] ? this.options[this.selectedIndex].getAttribute('data-maxuong') : '';
      if(maXuongFld) maXuongFld.value = ma;
      loadWorkersFor(ten);
    });
  }
  <?php endif; ?>



  // Sau khi submit: lưu, hiện thông báo, reset toàn bộ (kể cả CA), để lại ngayCham = hôm nay
  const form = document.getElementById('ghiNhanForm');
  form.addEventListener('submit', function(e){
    e.preventDefault();
    // send AJAX with X-Requested-With header so controller can return JSON
    fetch(this.action, {method:'POST', body:new FormData(this), headers:{'X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin'})
      .then(function(r){
        // prefer to parse JSON when possible
        var ct = r.headers.get('Content-Type') || '';
        return r.text().then(function(t){ return { ok: r.ok, status: r.status, body: t, isJson: ct.indexOf('application/json')!==-1 }; });
      })
      .then(function(res){
        if (!res.ok){ alert('❌ Có lỗi xảy ra khi lưu dữ liệu! (HTTP '+res.status+')'); return; }
        if (res.isJson){
          try{ var json = JSON.parse(res.body); }catch(e){ json = null; }
          if (json && json.success){
            document.getElementById('msgBox').innerHTML = "<div class='alert alert-success'>✅ Dữ liệu đã được lưu thành công!</div>";
            form.reset();
            document.getElementById('maCa').value = '';            // reset chọn ca
            document.getElementById('gioVao').value = '';          // clear giờ
            document.getElementById('gioRa').value  = '';
            document.getElementById('soGioLam').value = '';
            document.getElementById('ngayCham').value = todayYMD(); // hôm nay
          } else if (json && !json.success){
            document.getElementById('msgBox').innerHTML = "<div class='alert alert-danger'>❌ "+(json.error? json.error : 'Lưu thất bại')+"</div>";
          } else {
            // Unexpected JSON shape
            document.getElementById('msgBox').innerHTML = "<div class='alert alert-danger'>❌ Lỗi máy chủ (không thể phân tích phản hồi)</div>";
          }
        } else {
          // Non-JSON (probably HTML view) -> replace current document body
          var parser = new DOMParser();
          var doc = parser.parseFromString(res.body, 'text/html');
          if (doc && doc.body){ document.body.innerHTML = doc.body.innerHTML; }
        }
      })
      .catch(function(){ alert('❌ Có lỗi xảy ra khi lưu dữ liệu!'); });
  });

  // Confirm cancel action for xưởng trưởng và quản lý
  (function(){
    var cancel = document.getElementById('cancelBtn');
    if (!cancel) return;
    var canCancel = <?php echo (isset($GLOBALS['canCancel']) && $GLOBALS['canCancel']) ? 'true' : 'false'; ?>;
    if(!canCancel) return;
    cancel.addEventListener('click', function(e){
      e.preventDefault();
      if (confirm('Bạn có chắc muốn hủy lập phiếu không? Dữ liệu đã nhập sẽ không được lưu.')) {
        location.href = this.getAttribute('href');
      }
    });
  })();
})();
</script>
