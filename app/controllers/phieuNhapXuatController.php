<?php
require_once dirname(__FILE__) . '/../models/PhieuXuatKhoTP.php';
require_once dirname(__FILE__) . '/../models/Database.php';

class PhieuNhapXuatController {
    private $model;

    public function __construct() {
        $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $db->set_charset('utf8');
        $this->model = new PhieuXuatKhoTP($db);
    }

    // Trang tạo phiếu
    public function taophieu() {
    $maPhieu = $this->model->getNextMaPhieu();
    $ngayXuat = date('d/m/Y');
    $nguoiLap = isset($_SESSION['user']['hoTen']) ? $_SESSION['user']['hoTen'] : 'Không xác định';
    $maNguoiLap = isset($_SESSION['user']['maNguoiDung']) ? $_SESSION['user']['maNguoiDung'] : '';
    $dsTP = $this->model->getThanhPhamDat(); // ✅ Lưu ý tên biến: $dsTP

    require dirname(__FILE__) . '/../views/phieu/PhieuXuatKhoForm.php';
}

    // Lưu phiếu
    public function luuphieu() {
        if ($_POST['soLuongXuat'] > $_POST['soLuongTon']) {
            header("Location: index.php?controller=phieuNhapXuat&action=taophieu&error=1");
            exit;
        }

        $data = array(
            'maPhieu'    => $_POST['maPhieu'],
            'maKho'      => 'K002',
            'ngayXuat'   => date('Y-m-d'),
            'maNguoiLap' => $_SESSION['user']['maNguoiDung'],
            'maTP'       => $_POST['maTP'],
            'soLuong'    => $_POST['soLuongXuat']
        );

        $this->model->insertPhieu($data);
        $this->model->truSoLuong($data['maTP'], $data['soLuong']);

        // Lưu xong -> quay lại danh sách phiếu
        header("Location: index.php?controller=phieuNhapXuat&action=xuatkhotp&ok=1");
        exit;
    }

    // Hiển thị danh sách phiếu xuất kho
    public function xuatkhotp() {
        $dsPhieu = $this->model->getDanhSachPhieu(); // mảng các phiếu xuất
        require dirname(__FILE__) . '/../views/phieu/phieuXuatKhoTP.php';
    }

}
