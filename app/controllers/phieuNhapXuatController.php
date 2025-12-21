<?php
require_once dirname(__FILE__) . '/../models/PhieuXuatKhoTP.php';
require_once dirname(__FILE__) . '/../models/Database.php';

class PhieuNhapXuatController {
    private $model;

    public function __construct() {
         // 🔴 CHẶN LOAD LAYOUT KHI GỌI AJAX
        if (isset($_GET['action']) && $_GET['action'] === 'getDonHangByMaTP') {
            if (ob_get_level()) {
                ob_end_clean();
            }
        }

        
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
    //
    public function getDonHangByMaTP() {

    // 🚫 NGĂN MỌI OUTPUT TRƯỚC ĐÓ (sidebar, header...)
    if (ob_get_length()) {
        ob_clean();
    }

    $maTP = isset($_GET['maTP']) ? $_GET['maTP'] : '';

    $data = $this->model->getDonHangByMaTP($maTP);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit; // ⛔ BẮT BUỘC
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
        'maDonHang'  => $_POST['maDonHang'],   // ⭐ THÊM
        'maTP'       => $_POST['maTP'],
        'soLuong'    => $_POST['soLuongXuat']
    );

    $this->model->insertPhieu($data);
    $this->model->truSoLuong($data['maTP'], $data['soLuong']);

    header("Location: index.php?controller=phieuNhapXuat&action=xuatkhotp&ok=1");
    exit;
}
    // Hiển thị danh sách phiếu xuất kho
    public function xuatkhotp() {
        $dsPhieu = $this->model->getDanhSachPhieu(); // mảng các phiếu xuất
        require dirname(__FILE__) . '/../views/phieu/phieuXuatKhoTP.php';
    }

}
