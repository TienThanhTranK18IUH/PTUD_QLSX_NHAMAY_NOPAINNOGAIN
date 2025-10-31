<div class="content">
  <h2>🔨 Quản lý phiếu ghi nhận sửa chữa thiết bị</h2>

  <!-- BẢNG DANH SÁCH -->
  <table class="tbl" width="100%">
    <thead>
      <tr>
        <th>Mã phiếu</th>
        <th>Mã phiếu yêu cầu SC</th>
        <th>Nội dung</th>
        <th>Ngày hoàn thành</th>
        <th>Mã NV kỹ thuật</th>
        <th>Mã thiết bị</th>
        <th>Tên thiết bị</th>
        <th>Trạng thái</th>
        <th>Thao tác</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($dsPhieu)) { foreach ($dsPhieu as $r) { ?>
      <tr>
        <td><?php echo htmlspecialchars($r['maPhieu']); ?></td>
        <td><?php echo htmlspecialchars($r['maPhieuYCSC']); ?></td>
        <td><?php echo htmlspecialchars($r['noiDung']); ?></td>
        <td><?php echo htmlspecialchars($r['ngayHoanThanh']); ?></td>
        <td><?php echo htmlspecialchars($r['maNhanVienKyThuat']); ?></td>
        <td><?php echo htmlspecialchars($r['maThietBi']); ?></td>
        <td><?php echo htmlspecialchars($r['tenThietBi']); ?></td>
        <td><?php echo htmlspecialchars($r['trangThai']); ?></td>
        <td>
          <a href="index.php?controller=baotri&action=index&maPhieu=<?php echo urlencode($r['maPhieu']); ?>">✏️ Sửa</a>
        </td>
      </tr>
      <?php } } else { ?>
      <tr><td colspan="9" align="center">Không có phiếu sửa chữa nào.</td></tr>
      <?php } ?>
    </tbody>
  </table>

  <?php if (!empty($phieuEdit)) { ?>
  <hr>
  <h3>✏️ Chỉnh sửa phiếu: <?php echo htmlspecialchars($phieuEdit['maPhieu']); ?></h3>

  <!-- CHỈ FORM — KHÔNG BẢNG CHI TIẾT -->
  <form method="post" action="index.php?controller=baotri&action=index" class="form-edit" style="max-width:640px;">
    <input type="hidden" name="maPhieu" value="<?php echo htmlspecialchars($phieuEdit['maPhieu']); ?>" />

    <p>
      <label><b>Ngày hoàn thành:</b></label><br/>
      <input type="date" name="ngayHoanThanh"
             value="<?php echo htmlspecialchars($phieuEdit['ngayHoanThanh']); ?>" />
    </p>

    <p>
      <label><b>Trạng thái:</b></label><br/>
      <select name="trangThai">
        <?php
          $states = array('Chờ xử lý','Đang xử lý','Hoàn thành');
          $cur = isset($phieuEdit['trangThai']) ? $phieuEdit['trangThai'] : '';
          foreach ($states as $st) {
            $sel = ($st == $cur) ? 'selected' : '';
            echo '<option value="'.htmlspecialchars($st).'" '.$sel.'>'.htmlspecialchars($st).'</option>';
          }
        ?>
      </select>
    </p>

    <p>
      <label><b>Nội dung:</b></label><br/>
      <textarea name="noiDung" rows="4" style="width:100%;"><?php
        echo htmlspecialchars($phieuEdit['noiDung']);
      ?></textarea>
    </p>

    <p>
      <button type="submit" name="btnSave">💾 Lưu thay đổi</button>
      <a href="index.php?controller=baotri&action=index">↩ Quay lại</a>
    </p>
  </form>
  <?php } ?>
</div>

<style>
.tbl { border-collapse: collapse; margin-top:6px; }
.tbl th, .tbl td { border:1px solid #cfcfd4; padding:8px; }
.tbl thead th { background:#f2f2f2; }
.form-edit label { display:inline-block; margin-bottom:4px; }
.form-edit input[type=date], .form-edit select, .form-edit textarea { max-width:420px; width:100%; }
.form-edit button { padding:6px 10px; }
</style>
