<div class="content">
  <h2>🔧 Quản lý phiếu sửa chữa thiết bị</h2>

  <div style="display:flex; gap:20px;">
    <!-- DANH SÁCH PHIẾU YÊU CẦU -->
    <div style="flex:1;">
      <h3>📋 Danh sách phiếu yêu cầu sửa chữa</h3>
      <table class="tbl" width="100%">
        <thead>
          <tr>
            <th>Mã phiếu YC</th>
            <th>Mã thiết bị</th>
            <th>Tên thiết bị</th>
            <th>Trạng thái YC</th>
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($dsYeuCau)) {
            foreach ($dsYeuCau as $r) { ?>
          <tr>
            <td><?php echo htmlspecialchars($r['maPhieu']); ?></td>
            <td><?php echo htmlspecialchars($r['maTB']); ?></td>
            <td><?php echo htmlspecialchars($r['tenTB']); ?></td>
            <td><?php echo htmlspecialchars($r['trangThai']); ?></td>
            <td>
              <a href="index.php?controller=baotri&action=index&maPhieuYCSC=<?php echo urlencode($r['maPhieu']); ?>">📝 Ghi nhận</a>
            </td>
          </tr>
          <?php }
          } else { ?>
          <tr><td colspan="5" align="center">Không có phiếu yêu cầu sửa chữa.</td></tr>
          <?php } ?>
        </tbody>
      </table>
    </div>

    <!-- DANH SÁCH PHIẾU GHI NHẬN -->
    <div style="flex:1;">
      <h3>🧾 Danh sách phiếu ghi nhận sửa chữa</h3>
      <table class="tbl" width="100%">
        <thead>
          <tr>
            <th>Mã phiếu GN</th>
            <th>Mã phiếu YC</th>
            <th>Nội dung</th>
            <th>Ngày hoàn thành</th>
            <th>Mã NV kỹ thuật</th>
            <th>Trạng thái</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($dsGhiNhan)) {
            foreach ($dsGhiNhan as $r) { ?>
          <tr>
            <td><?php echo htmlspecialchars($r['maPhieu']); ?></td>
            <td><?php echo htmlspecialchars($r['maPhieuYCSC']); ?></td>
            <td><?php echo htmlspecialchars($r['noiDung']); ?></td>
            <td><?php echo htmlspecialchars($r['ngayHoanThanh']); ?></td>
            <td><?php echo htmlspecialchars($r['maNguoiDung']); ?></td>
            <td><?php echo htmlspecialchars($r['trangThai']); ?></td>
          </tr>
          <?php }
          } else { ?>
          <tr><td colspan="6" align="center">Không có phiếu ghi nhận sửa chữa.</td></tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- FORM GHI NHẬN MỚI -->
  <?php if (!empty($_GET['maPhieuYCSC'])) { ?>
  <hr>
  <h3>📝 Tạo phiếu ghi nhận cho phiếu yêu cầu: <?php echo htmlspecialchars($_GET['maPhieuYCSC']); ?></h3>
  <form method="post" action="index.php?controller=baotri&action=index" class="form-edit" style="max-width:640px;">
    <input type="hidden" name="maPhieu" value="" />
    <input type="hidden" name="maPhieuYCSC" value="<?php echo htmlspecialchars($_GET['maPhieuYCSC']); ?>" />

    <p>
      <label><b>Ngày hoàn thành:</b></label><br/>
      <input type="date" name="ngayHoanThanh" required />
    </p>

    <p>
      <label><b>Trạng thái:</b></label><br/>
      <select name="trangThai">
        <option value="Chờ xử lý">Chờ xử lý</option>
        <option value="Đang xử lý" selected>Đang xử lý</option>
        <option value="Hoàn thành">Hoàn thành</option>
      </select>
    </p>

    <p>
      <label><b>Nội dung:</b></label><br/>
      <textarea name="noiDung" rows="4" style="width:100%;" placeholder="Nhập nội dung ghi nhận..."></textarea>
    </p>

    <p>
      <button type="submit" name="btnSave">💾 Lưu phiếu ghi nhận</button>
      <a href="index.php?controller=baotri&action=index">↩ Quay lại</a>
    </p>
  </form>
  <?php } ?>
</div>

<style>
.tbl { border-collapse: collapse; margin-top:6px; width:100%; }
.tbl th, .tbl td { border:1px solid #cfcfd4; padding:8px; }
.tbl thead th { background:#f2f2f2; }
.form-edit label { display:inline-block; margin-bottom:4px; }
.form-edit input[type=date], .form-edit select, .form-edit textarea { max-width:420px; width:100%; }
.form-edit button { padding:6px 10px; }
</style>
