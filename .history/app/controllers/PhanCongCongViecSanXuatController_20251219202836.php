<?php declare(strict_types=1); 
// Controller: PhanCongCongViecSanXuatController.php — PHP 5.x (WAMP 2.0)

require_once dirname(__FILE__) . '/../models/database.php';
require_once dirname(__FILE__) . '/../models/PhanCongCongViecSanXuat.php';
require_once dirname(__FILE__) . '/../helpers/auth.php';

class PhanCongCongViecSanXuatController {
    private $model;

    public function __construct() {
        $db = new Database(); // Database wrapper của bạn
        $this->model = new PhanCongCongViecSanXuat($db);
    }

    public function index() {
        requireRole(array('manager','leader'));
        $quanLy      = $this->model->layQuanLyHoatDong(); // 👈 tự lấy người có vai trò QuanLy
        $keHoachList = $this->model->layDanhSachKeHoach();
        $caList      = $this->model->layDanhSachCa();
        $bophanList = $this->model->layDanhSachBoPhan();
        if ($quanLy === null) {
            $error = "Không tìm thấy người dùng có vai trò 'QuanLy' đang hoạt động!";
        }

        include dirname(__FILE__) . '/../views/phancong/PhanCongSanXuat.php';
    }

    public function save() {
        requireRole(array('manager','leader'));
        // If accessed via GET, show index (avoid calling header() after layout output)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->index();
        }


        $maNguoiDung  = isset($_POST['maNguoiDung']) ? $_POST['maNguoiDung'] : '';
        $maKeHoach    = isset($_POST['maKeHoach']) ? $_POST['maKeHoach'] : '';
        $maCa         = isset($_POST['maCa']) ? $_POST['maCa'] : '';
        $maXuong      = isset($_POST['maXuong']) ? $_POST['maXuong'] : '';
        $moTaCongViec = isset($_POST['moTaCongViec']) ? $_POST['moTaCongViec'] : '';
        $soLuong      = isset($_POST['soLuong']) ? $_POST['soLuong'] : 0;
        $ngayBatDau   = isset($_POST['ngayBatDau']) ? $_POST['ngayBatDau'] : '';
        $ngayKetThuc  = isset($_POST['ngayKetThuc']) ? $_POST['ngayKetThuc'] : '';

        $ok = $this->model->luuPhanCong(
            $maNguoiDung, $maKeHoach, $maCa, $maXuong, $moTaCongViec, $soLuong, $ngayBatDau, $ngayKetThuc
        );

        // Use PRG: redirect to index so the normal layout (header/sidebar/footer)
        // is rendered on the follow-up GET request. POST is processed by the
        // front controller before loading layouts, so server-side redirect
        // here is safe and avoids missing sidebar.
        $msg = $ok ? 'LuuThanhCong' : 'LuuThatBai';
        header('Location: index.php?controller=PhanCongCongViecSanXuat&action=index&msg=' . urlencode($msg));
        exit;
    }
}
?>
