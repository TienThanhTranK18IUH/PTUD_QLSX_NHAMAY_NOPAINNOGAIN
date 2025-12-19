<?php declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

if (!isset($_SESSION)) session_start();

require_once dirname(__FILE__) . '/../models/phieunhapNL.php';
require_once dirname(__FILE__) . '/../models/database.php';

class PhieuNhapNLController {
    var $model;

    function __construct() {
        $db = new Database();
        $this->model = new PhieuNhapNL($db);
    }

    // Hiển thị form nhập phiếu
    function formNhapPhieu() {
        $ds_nguyenlieu = $this->model->layDanhSachNguyenLieu();
        $ds_nhanvienkho = $this->model->layDanhSachNhanVienKho();
        include dirname(__FILE__) . '/../views/phieu/nhapNL.php';
    }

    // Xử lý lưu phiếu nhập
    function nhapPhieu() {
        $maKho = isset($_POST['maKho']) ? $_POST['maKho'] : '';
        $ngayNhap = isset($_POST['ngayNhap']) ? $_POST['ngayNhap'] : date('Y-m-d');
        $maNguoiLap = isset($_POST['maNguoiLap']) ? $_POST['maNguoiLap'] : '';
        $trangThai = isset($_POST['trangThai']) ? $_POST['trangThai'] : 'ChoDuyet';
        $maNguyenLieu = isset($_POST['maNguyenLieu']) ? $_POST['maNguyenLieu'] : '';
        $maKeHoach = isset($_POST['maKeHoach']) ? $_POST['maKeHoach'] : '';
        $soLuong = isset($_POST['soLuong']) ? intval($_POST['soLuong']) : 0;
        $soLuongTonKho = isset($_POST['soLuongTonKho']) ? intval($_POST['soLuongTonKho']) : 0;

        if ($maKho == '' || $maNguoiLap == '' || $maNguyenLieu == '') {
            echo "<script>alert('⚠️ Vui lòng nhập đầy đủ thông tin phiếu!'); window.history.back();</script>";
            exit;
        }

        $ketqua = $this->model->luuPhieuNhap($maKho, $ngayNhap, $maNguoiLap, $trangThai, $maNguyenLieu, $maKeHoach, $soLuong, $soLuongTonKho);

        header('Content-Type: text/html; charset=UTF-8');
        if ($ketqua) {
            echo "<script>alert('✅ Lưu phiếu nhập thành công!'); window.location='index.php?controller=phieunhapNL&action=formNhapPhieu';</script>";
        } else {
            echo "<script>alert('❌ Lưu phiếu nhập thất bại!'); window.history.back();</script>";
        }
    }
}

// // Front Controller
// if (isset($_GET['action'])) {
//     $action = $_GET['action'];
//     $controller = new PhieuNhapNLController();
//     if (method_exists($controller, $action)) {
//         $controller->$action();
//     } else {
//         echo '❌ Action không tồn tại!';
//     }
// }
?>
