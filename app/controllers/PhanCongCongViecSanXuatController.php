<?php
// Controller: PhanCongCongViecSanXuatController.php — PHP 5.x (WAMP 2.0)

require_once dirname(__FILE__) . '/../models/database.php';
require_once dirname(__FILE__) . '/../models/PhanCongCongViecSanXuat.php';

class PhanCongCongViecSanXuatController {
    private $model;

    public function __construct() {
        $db = new Database(); // Database wrapper của bạn
        $this->model = new PhanCongCongViecSanXuat($db);
    }

    public function index() {
        $quanLy      = $this->model->layQuanLyHoatDong(); // 👈 tự lấy người có vai trò QuanLy
        $keHoachList = $this->model->layDanhSachKeHoach();
        $caList      = $this->model->layDanhSachCa();

        if ($quanLy === null) {
            $error = "Không tìm thấy người dùng có vai trò 'QuanLy' đang hoạt động!";
        }

        include dirname(__FILE__) . '/../views/phancong/PhanCongSanXuat.php';
    }

    public function save() {
        $maNguoiDung  = isset($_POST['maNguoiDung']) ? $_POST['maNguoiDung'] : '';
        $maCa         = isset($_POST['maCa']) ? $_POST['maCa'] : '';
        $maXuong      = isset($_POST['maXuong']) ? $_POST['maXuong'] : '';
        $moTaCongViec = isset($_POST['moTaCongViec']) ? $_POST['moTaCongViec'] : '';
        $soLuong      = isset($_POST['soLuong']) ? $_POST['soLuong'] : 0;
        $ngayBatDau   = isset($_POST['ngayBatDau']) ? $_POST['ngayBatDau'] : '';
        $ngayKetThuc  = isset($_POST['ngayKetThuc']) ? $_POST['ngayKetThuc'] : '';

        $ok = $this->model->luuPhanCong(
            $maNguoiDung, $maCa, $maXuong, $moTaCongViec, $soLuong, $ngayBatDau, $ngayKetThuc
        );

        if ($ok) $success = "✅ Lưu phân công thành công!";
        else $error = "❌ Lỗi khi lưu dữ liệu!";

        $quanLy      = $this->model->layQuanLyHoatDong();
        $keHoachList = $this->model->layDanhSachKeHoach();
        $caList      = $this->model->layDanhSachCa();

        include dirname(__FILE__) . '/../views/phancong/PhanCongSanXuat.php';
    }
}
?>
