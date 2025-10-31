<?php
// ====== LẤY THAM SỐ HIỆN TẠI ======
$c = isset($_GET['controller']) ? trim($_GET['controller']) : 'dashboard';
$a = isset($_GET['action'])     ? trim($_GET['action'])     : 'index';

// ====== NHÓM CONTROLLER THUỘC "QUẢN LÝ PHIẾU" ======
$phieuControllers = array('phieu', 'phieuYeuCau', 'phieuNhapXuat', 'phieuKTTP', 'phieuSuaChua');

// Mở submenu nếu đang ở 1 trong các controller trên
// hoặc đang ở phieu&suachua (trường hợp gom sửa chữa vào PhieuController)
$inPhieuGroup = in_array($c, $phieuControllers) || ($c === 'phieu' && $a === 'suachua');

// Hàm helper active (tương thích PHP 5.2)
if (!function_exists('nav_active')) {
    function nav_active($cond) { return $cond ? 'active' : ''; }
}

// Active riêng cho mục “Phiếu bảo trì & sửa chữa”
$isSCActive = ($c === 'phieu' && $a === 'suachua') || ($c === 'phieuSuaChua');
?>
<div class="container">
  <nav class="sidebar">
    <ul>
      <li>
        <a href="index.php?controller=dashboard" class="<?php echo nav_active($c==='dashboard'); ?>">
          🏠 Trang chủ
        </a>
      </li>

      <li>
        <a href="index.php?controller=nhanVien" class="<?php echo nav_active($c==='nhanVien'); ?>">
          👷 Nhân sự
        </a>
      </li>

      <li>
        <a href="index.php?controller=keHoach" class="<?php echo nav_active($c==='keHoach'); ?>">
          🗂 Kế hoạch sản xuất
        </a>
      </li>

      <li>
        <a href="index.php?controller=kho" class="<?php echo nav_active($c==='kho'); ?>">
          🏢 Quản lý kho
        </a>
      </li>

      <!-- Quản lý phiếu -->
      <li class="has-submenu <?php echo $inPhieuGroup ? 'open' : ''; ?>">
        <a href="#">📋 Quản lý phiếu ▾</a>
        <ul class="submenu">
          <li>
            <a href="index.php?controller=phieu&amp;action=index"
               class="<?php echo nav_active($c==='phieu' || $c==='phieuYeuCau'); ?>">
              🧾 Phiếu yêu cầu nguyên liệu
            </a>
          </li>

          <li>
            <a href="index.php?controller=phieuNhapXuat&amp;action=nhapKhoNL"
               class="<?php echo nav_active($c==='phieuNhapXuat' && $a==='nhapKhoNL'); ?>">
              📦 Phiếu nhập kho nguyên liệu
            </a>
          </li>

          <li>
            <a href="index.php?controller=phieuNhapXuat&amp;action=xuatKhoNL"
               class="<?php echo nav_active($c==='phieuNhapXuat' && $a==='xuatKhoNL'); ?>">
              🚚 Phiếu xuất kho nguyên liệu
            </a>
          </li>

          <li>
            <a href="index.php?controller=phieuNhapXuat&amp;action=nhapKhoTP"
               class="<?php echo nav_active($c==='phieuNhapXuat' && $a==='nhapKhoTP'); ?>">
              📥 Phiếu nhập kho thành phẩm
            </a>
          </li>

          <li>
            <a href="index.php?controller=phieuNhapXuat&amp;action=xuatKhoTP"
               class="<?php echo nav_active($c==='phieuNhapXuat' && $a==='xuatKhoTP'); ?>">
              📤 Phiếu xuất kho thành phẩm
            </a>
          </li>

          <li>
            <a href="index.php?controller=phieuKTTP"
               class="<?php echo nav_active($c==='phieuKTTP'); ?>">
              🧮 Phiếu kiểm tra thành phẩm
            </a>
          </li>

          <!-- Khuyên dùng: controller=phieu&action=suachua -->
          <li>
            <a href="index.php?controller=phieu&amp;action=suachua"
               class="<?php echo nav_active($isSCActive); ?>">
              🔧 Phiếu bảo trì &amp; sửa chữa
            </a>
          </li>

          <!-- Nếu vẫn muốn URL cũ controller=phieuSuaChua, có thể dùng thêm link dưới (tùy chọn):
          <li>
            <a href="index.php?controller=phieuSuaChua"
               class="<?php echo nav_active($c==='phieuSuaChua'); ?>">
              🔧 Phiếu bảo trì &amp; sửa chữa (alias)
            </a>
          </li>
          -->
        </ul>
      </li>

      <li>
        <a href="index.php?controller=sanXuat" class="<?php echo nav_active($c==='sanXuat'); ?>">
          ⚙️ Phân công &amp; sản xuất
        </a>
      </li>

      <li>
        <a href="index.php?controller=baoTri" class="<?php echo nav_active($c==='baoTri'); ?>">
          🔧 Bảo trì &amp; sửa chữa
        </a>
      </li>

      <li>
        <a href="index.php?controller=thongKe" class="<?php echo nav_active($c==='thongKe'); ?>">
          📊 Thống kê &amp; báo cáo
        </a>
      </li>
    </ul>
  </nav>

  <!-- CHỈ MỞ, KHÔNG ĐÓNG! Router sẽ include view vào đây -->
  <main class="content">
    <script>
      // Toggle submenu
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
