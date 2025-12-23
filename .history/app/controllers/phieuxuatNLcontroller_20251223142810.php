<?php declare(strict_types=1); 
if (!isset($_SESSION)) session_start();

require_once dirname(__FILE__) . '/../models/phieuxuatNL.php';
require_once dirname(__FILE__) . '/../models/database.php';

class PhieuXuatNLController {
    var $model;

    function __construct() {
        $db = new Database();
        $this->model = new PhieuXuatNL($db);
    }

    // -------------------------------
    // Mặc định: danh sách phiếu xuất
    // -------------------------------
    function index() {
        $ds_phieuxuat = $this->model->layDanhSachPhieuXuat();
        include dirname(__FILE__) . '/../views/phieu/xuatNL.php';
    }

    // -------------------------------
    // Form lập phiếu xuất
    // -------------------------------
    function add() {
        $ds_kho = $this->model->layDanhSachKho();
        $ds_nhanvienkho = $this->model->layDanhSachNhanVienKho();
        $ds_nguyenlieu = $this->model->layDanhSachNguyenLieu();
        $ds_phieuyc = $this->model->layDanhSachPhieuYeuCau();

        $maKho_macdinh = "K001";
        $maNguoiLap_macdinh = "ND004";

        include dirname(__FILE__) . '/../views/phieu/xuatNL.php';
    }

    // -------------------------------
    // Xử lý lưu phiếu xuất
    // -------------------------------
    function xuatPhieu() {
        if (!empty($_POST)) {
            $maKho = $_POST['maKho'];
            $ngayXuat = $_POST['ngayXuat'];
            $maNguoiLap = $_POST['maNguoiLap'];
            $maPhieuYC = isset($_POST['maPhieuYC']) ? $_POST['maPhieuYC'] : 0;
            $maNguyenLieu = $_POST['maNguyenLieu'];
            $soLuongNLYC = $_POST['soLuongNLYC'];
            $soLuongTonKho = $_POST['soLuongTonKho'];

            // Kiểm tra số lượng tồn kho thực tế
            require_once dirname(__FILE__) . '/../models/nguyenLieu.php';
            $nguyenLieuModel = new NguyenLieu();
            $soLuongTonKhoHienTai = $nguyenLieuModel->getSoLuongTonKho($maNguyenLieu);
            if ($soLuongNLYC > $soLuongTonKhoHienTai) {
                header('Content-Type: text/html; charset=UTF-8');
                echo "<script>alert('❌ Không đủ số lượng nguyên liệu trong kho để xuất! Số lượng tồn kho hiện tại: $soLuongTonKhoHienTai'); history.back();</script>";
                return;
            }

            $ok = $this->model->luuPhieuXuat(
                $maKho, 
                $ngayXuat, 
                $maNguoiLap, 
                $maPhieuYC, 
                $maNguyenLieu, 
                $soLuongNLYC, 
                $soLuongTonKhoHienTai
            );

            header('Content-Type: text/html; charset=UTF-8');
            if ($ok) {
                echo "<script>alert('✅ Lưu phiếu xuất thành công!'); window.location='index.php?controller=phieuxuatNL&action=add';</script>";
            } else {
                echo "<script>alert('❌ Lưu phiếu xuất thất bại!'); history.back();</script>";
            }
        }
    }
}
?>
