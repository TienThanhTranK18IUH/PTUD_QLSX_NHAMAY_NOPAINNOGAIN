<?php declare(strict_types=1); 
// ================================
// sidebar.php — Thanh menu bên trái (PHP 5.x)
// ================================

// ⛔ KHÔNG dùng declare(strict_types=1) trên PHP 5.x

// Lấy controller/action hiện tại
$c = isset($_GET['controller']) ? trim($_GET['controller']) : 'dashboard';
$a = isset($_GET['action'])     ? trim($_GET['action'])     : 'index';

$cl = strtolower($c);
$al = strtolower($a);

// Bắt đầu session
if (session_id() === '') @session_start();

// include auth helper if not loaded
if (!function_exists('isLoggedIn')) {
    require_once dirname(__FILE__) . '/../../helpers/auth.php';
}

// == Hồ sơ người dùng (nếu cần cho menu nhân viên) ==
$profileUrl = 'index.php?controller=nhanVien&action=thongtin';
if (isset($_SESSION['user'])) {
  $u = $_SESSION['user'];
  if (isset($u['ma_nv']))      $profileUrl = 'index.php?controller=nhanVien&action=thongtin&id=' . urlencode($u['ma_nv']);
  elseif (isset($u['MaNV']))   $profileUrl = 'index.php?controller=nhanVien&action=thongtin&id=' . urlencode($u['MaNV']);
}

// Nhóm "Quản lý phiếu"
$phieuControllers = array('phieu','phieunhapxuat','baotri');
$inPhieuGroup = in_array($cl, $phieuControllers) || ($cl==='phieu' && in_array($al, array('index','kttp','suachua')));

// helper active
if (!function_exists('nav_active')) {
  function nav_active($cond) { return $cond ? 'active' : ''; }
}

// Active riêng sửa chữa
$isSCActive = ($cl==='phieu' && $al==='suachua');
?>
<div class="container">
    <nav class="sidebar">
        <ul>
            <li><a href="index.php?controller=dashboard" class="<?php echo nav_active($cl==='dashboard'); ?>">🏠 Trang
                    chủ</a></li>

                <?php if (checkRole(array('manager'))): ?>
                <li><a href="index.php?controller=nhanVien"
                    class="<?php echo nav_active($cl==='nhanvien' && $al==='index'); ?>">👷 Nhân sự</a></li>
                <?php endif; ?>

            <!-- 📅 Xem lịch làm & giờ công (CHỈ công nhân) -->
            <?php if (checkRole(array('worker'))): ?>
            <li>
                <a href="index.php?controller=lich&action=index" class="<?php echo nav_active($cl==='lich'); ?>">
                    📅 Xem lịch làm &amp; giờ công
                </a>
            </li>
            <?php endif; ?>

            <!-- 📝 Ghi nhận sản xuất -->
            <?php if (checkRole(array('manager','leader'))): ?>
            <li>
                <a href="index.php?controller=sanxuat&action=ghinhan"
                    class="<?php echo ($cl==='sanxuat') ? 'active' : ''; ?>">
                    📝 Ghi nhận sản xuất
                </a>
            </li>
            <li><a href="index.php?controller=ghinhanthanhpham"  class="<?php echo nav_active($cl==='ghinhanthanhpham'); ?>">🏭 Ghi nhận thành phẩm</a></li>
            <?php endif; ?>
            <!-- Kế hoạch sản xuất (submenu) -->
            <?php if (checkRole(array('manager','leader','planner'))): ?>
                        <li class="has-submenu <?php echo ($cl==='kehoach') ? 'open' : ''; ?>">
          <a href="#">🗂 Kế hoạch sản xuất ▾</a>
          <ul class="submenu">
                        <li>
                            <a href="index.php?controller=keHoach&action=index"
                                class="<?php echo nav_active($cl==='kehoach' && $al==='index'); ?>">
                                📋 Xem
                            </a>
                        </li>

                        <?php if (checkRole(array('manager','planner'))): ?>
                        <li>
                            <a href="index.php?controller=keHoach&action=form_edit"
                                class="<?php echo nav_active($cl==='kehoach' && $al==='form_edit'); ?>">
                                ✏️ Cập nhật
                            </a>
                        </li>
                        <li>
                        <a href="index.php?controller=keHoach&action=lapKeHoach"
                            class="<?php echo nav_active($cl==='kehoach' && $al==='lapkehoach'); ?>">
                            🆕 Lập KHSX
                        </a>
                    </li>
                        <?php endif; ?>
          </ul>
        </li>
            <?php endif; ?>
            <?php if (checkRole(array('manager','leader','warehouse','qc','technician'))): ?>
            <li class="has-submenu <?php echo $inPhieuGroup ? 'open' : ''; ?>">
                <a href="#">📋 Quản lý phiếu ▾</a>
                <ul class="submenu">
                    <?php if (checkRole(array('manager','leader'))): ?>
                    <li>
                        <a href="index.php?controller=phieu&amp;action=index"
class="<?php echo nav_active($cl==='phieu' && $al==='index'); ?>">
                            🧾 Phiếu yêu cầu nguyên liệu
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (checkRole(array('manager','warehouse'))): ?>
                    <li>
                        <a href="index.php?controller=phieunhapNL&amp;action=formNhapPhieu"
                            class="<?php echo nav_active($cl==='phieunhapnl' && $al==='formnhapphieu'); ?>">
                            📦 Phiếu nhập kho nguyên liệu
                        </a>
                    </li>

                    <li>
                        <a href="index.php?controller=phieuxuatNL&amp;action=add"
                            class="<?php echo nav_active($cl==='phieuxuatnl' && $al==='add'); ?>">
                            🚚 Phiếu xuất kho nguyên liệu
                        </a>
                    </li>

                    <li>
                        <a href="index.php?controller=phieu&amp;action=pnk_index"
                            class="<?php echo nav_active($cl==='phieunhapxuat' && $al==='pnk_index'); ?>">
                            📥 Phiếu nhập kho thành phẩm
                        </a>
                    </li>
                    <li>
                        <a href="index.php?controller=phieuNhapXuat&amp;action=xuatkhotp"
                            class="<?php echo nav_active($cl==='phieunhapxuat' && $al==='xuatkhotp'); ?>">
                            📤 Phiếu xuất kho thành phẩm
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (checkRole(array('manager','qc'))): ?>
                    <li>
                        <a href="index.php?controller=phieu&amp;action=kttp"
                            class="<?php echo nav_active($cl==='phieu' && $al==='kttp'); ?>">
                            🧮 Phiếu kiểm tra thành phẩm
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (checkRole(array('manager','leader'))): ?>
                    <li>
                        <a href="index.php?controller=phieu&amp;action=suachua"
                            class="<?php echo nav_active($isSCActive); ?>">
                            🔧 Phiếu yêu cầu sửa chữa
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (checkRole(array('manager','technician'))): ?>
                    <li>
                        <a href="index.php?controller=baotri&amp;action=index"
                            class="<?php echo nav_active($cl==='baotri'); ?>">
                            🪛 Phiếu ghi nhận sửa chữa
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>

            <!-- ⚙️ Phân công & sản xuất -->
            <?php if (checkRole(array('manager','leader'))): ?>
            <li
                class="has-submenu <?php echo ($cl==='phancongcongviecsanxuat' || $cl==='phancongdoica') ? 'open' : ''; ?>">
                <a href="#">⚙️ Phân công &amp; sản xuất ▾</a>
                <ul class="submenu">
                    <li>
                        <a href="index.php?controller=PhanCongCongViecSanXuat"
class="<?php echo nav_active($cl==='phancongcongviecsanxuat' || $cl==='PhanCongCongViecSanXuat'); ?>">
                            🧰 Phân công sản xuất
                        </a>
                    </li>
                    <li>
                        <a href="index.php?controller=PhanCongDoiCa"
                            class="<?php echo nav_active($cl==='phancongdoica' || $cl==='PhanCongDoiCa'); ?>">
                            🔄 Phân công, đổi ca công việc cho công nhân
                        </a>
                    </li>
                </ul>
            </li>
            <?php endif; ?>

            <?php if (checkRole(array('manager','leader'))): ?>
            <li><a href="index.php?controller=thongKe" class="<?php echo nav_active($cl==='thongke'); ?>">📊 Thống kê
                &amp; báo cáo</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Router sẽ include view vào <main> -->
    <main class="content">
        <script>
        document.addEventListener('DOMContentLoaded', function() {
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
        .sidebar ul {
            list-style: none;
            margin: 0;
            padding: 0
        }

        .sidebar .submenu {
            display: none;
            margin-left: 15px
        }

        .sidebar .has-submenu.open .submenu {
            display: block
        }

        .sidebar a.active {
            font-weight: bold
        }
        </style>