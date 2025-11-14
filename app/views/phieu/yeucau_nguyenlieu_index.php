<?php /* partial view: KHÔNG bọc <html> */ ?>
<h2><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h2>

<?php if (!empty($notice_ok)): ?>
  <div style="padding:10px 12px;border-radius:8px;margin:12px 0;background:#e8fff1;border:1px solid #b7eccb;color:#05603a;">
    ✅ Đã lập phiếu thành công. Mã phiếu: <b><?php echo htmlspecialchars($notice_code); ?></b>
  </div>
<?php endif; ?>

<form method="get" action="index.php" style="margin:10px 0;">
  <input type="hidden" name="controller" value="phieu"/>
  <input type="hidden" name="action" value="index"/>
  <input type="text" name="q" placeholder="Tìm kiếm theo mã kế hoạch / Xưởng"
         value="<?php echo isset($_GET['q'])?htmlspecialchars($_GET['q']):''; ?>"
         style="padding:6px 10px;width:320px;"/>
  <button type="submit">Tìm</button>
</form>

<div class="card">
  <table class="table" border="1" cellpadding="6" cellspacing="0" width="100%">
    <tr style="background:#f1f1f1;">
      <th>Mã Kế Hoạch</th><th>Tên kế hoạch sản xuất</th>
      <th>Mã Xưởng</th><th>Tên Xưởng</th>
      <th>Ngày Bắt Đầu</th><th>Ngày Kết Thúc</th><th>Trạng thái</th>
    </tr>
    <?php foreach ($list as $r) { ?>
      <tr>
        <td>
          <a href="index.php?controller=phieu&action=yeucau_nguyenlieu&id=<?php echo urlencode($r['MaKH']); ?>"
             style="text-decoration:underline;">
            <?php echo htmlspecialchars($r['MaKH']); ?>
          </a>
        </td>
        <td><?php echo htmlspecialchars($r['TenKH']); ?></td>
        <td><?php echo htmlspecialchars($r['MaXuong']); ?></td>
        <td><?php echo htmlspecialchars($r['TenXuong']); ?></td>
        <td><?php echo htmlspecialchars($r['NgayBatDau']); ?></td>
        <td><?php echo htmlspecialchars($r['NgayKetThuc']); ?></td>
        <td><?php echo htmlspecialchars($r['TrangThai']); ?></td>
      </tr>
    <?php } ?>
  </table>
</div>
