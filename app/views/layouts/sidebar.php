<?php
// Lấy controller/action hiện tại
$c = isset($_GET['controller']) ? trim($_GET['controller']) : 'dashboard';
$a = isset($_GET['action'])     ? trim($_GET['action'])     : 'index';

// So sánh không phân biệt hoa/thường cho chắc
$cl = strtolower($c);
$al = strtolower($a);

// Nhóm "Quản lý phiếu"
$phieuControllers = array('phieu','phieuyecau','phieunhapxuat','phieukttp','phieusuachua');
$inPhieuGroup = in_array($cl, $phieuControllers) || ($cl==='phieu' && $al==='suachua');

// helper active (tương thích PHP 5.2)
if (!function_exists('nav_active')) {
  function nav_active($cond) { return $cond ? 'active' : ''; }
}

// Active riêng sửa chữa
$isSCActive = ($cl==='phieu' && $al==='suachua') || ($cl==='phieusuachua');
?>
<div class="container">
  <nav class="sidebar">
    <ul>
      <li><a href="index.php?controller=dashboard" class="<?php echo nav_active($cl==='dashboard'); ?>">🏠 Trang chủ</a></li>
      <li><a href="index.php?controller=nhanVien"  class="<?php echo nav_active($cl==='nhanvien'); ?>">👷 Nhân sự</a></li>
      <li><a href="index.php?controller=keHoach"   class="<?php echo nav_active($cl==='kehoach'); ?>">🗂 Kế hoạch sản xuất</a></li>
      <li><a href="index.php?controller=kho"       class="<?php echo nav_active($cl==='kho'); ?>">🏢 Quản lý kho</a></li>

      <li class="has-submenu <?php echo $inPhieuGroup ? 'open' : ''; ?>">
        <a href="#">📋 Quản lý phiếu ▾</a>
        <ul class="submenu">
          <li>
            <a href="index.php?controller=phieu&amp;action=index"
               class="<?php echo nav_active($cl==='phieu' || $cl==='phieuyecau'); ?>">
               🧾 Phiếu yêu cầu nguyên liệu
            </a>
          </li>
          <li><a href="index.php?controller=phieuNhapXuat&amp;action=nhapKhoNL" class="<?php echo nav_active($cl==='phieunhapxuat' && $al==='nhapkhoNL'); ?>">📦 Phiếu nhập kho nguyên liệu</a></li>
          <li><a href="index.php?controller=phieuNhapXuat&amp;action=xuatKhoNL" class="<?php echo nav_active($cl==='phieunhapxuat' && $al==='xuatkhoNL'); ?>">🚚 Phiếu xuất kho nguyên liệu</a></li>
          <li><a href="index.php?controller=phieuNhapXuat&amp;action=nhapKhoTP" class="<?php echo nav_active($cl==='phieunhapxuat' && $al==='nhapkhoTP'); ?>">📥 Phiếu nhập kho thành phẩm</a></li>
          <li><a href="index.php?controller=phieuNhapXuat&amp;action=xuatKhoTP" class="<?php echo nav_active($cl==='phieunhapxuat' && $al==='xuatkhoTP'); ?>">📤 Phiếu xuất kho thành phẩm</a></li>
          <li><a href="index.php?controller=phieuKTTP" class="<?php echo nav_active($cl==='phieukttp'); ?>">🧮 Phiếu kiểm tra thành phẩm</a></li>

          <!-- Gợi ý: gom sửa chữa vào PhieuController -->
          <li>
            <a href="index.php?controller=phieu&amp;action=suachua" class="<?php echo nav_active($isSCActive); ?>">
              🔧 Phiếu bảo trì &amp; sửa chữa
            </a>
          </li>
        </ul>
      </li>

      <!-- Phân công & sản xuất -->
      <li>
        <a href="index.php?controller=PhanCongCongViecSanXuat"
           class="<?php echo nav_active($c==='PhanCongCongViecSanXuat' || $cl==='phancongcongviecsanxuat'); ?>">
          ⚙️ Phân công &amp; sản xuất
        </a>
      </li>

      <li><a href="index.php?controller=baoTri"  class="<?php echo nav_active($cl==='baotri'); ?>">🔧 Bảo trì &amp; sửa chữa</a></li>
      <li><a href="index.php?controller=thongKe" class="<?php echo nav_active($cl==='thongke'); ?>">📊 Thống kê &amp; báo cáo</a></li>
    </ul>
  </nav>

  <!-- CHỈ MỞ, KHÔNG ĐÓNG! Router sẽ include view vào đây -->
  <main class="content">
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var links = document.querySelectorAll('.has-submenu > a');
        for (var i = 0; i < links.length; i++) {
          links[i].addEventListener('click', function(e) {
            e.preventDefault();
            this.parentNode.classList.toggle('open');
          });
        }
      });
    </script>
    <style>
      .sidebar ul{list-style:none;margin:0;padding:0}
      .sidebar .submenu{display:none;margin-left:15px}
      .sidebar .has-submenu.open .submenu{display:block}
      .sidebar a.active{font-weight:bold}
    </style>
