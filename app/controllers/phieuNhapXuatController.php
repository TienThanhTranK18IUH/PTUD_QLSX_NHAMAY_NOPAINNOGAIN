<?php
require_once dirname(__FILE__) . '/../models/PhieuXuatKhoTP.php';

class phieuNhapXuatController {

    // Xử lý lưu phiếu
public function luuphieu() {
    $px = new PhieuXuatKhoTP();

    if (!isset($_POST['maDonHang']) || $_POST['maDonHang'] == '') {
        header('Location: index.php?controller=phieuNhapXuat&action=xuatkhotp&error=1');
        exit;
    }

    $maDonHang = $_POST['maDonHang'];

    // Kiểm tra đơn hàng đã lập phiếu chưa
    if ($px->checkExistDonHang($maDonHang)) {
        header('Location: index.php?controller=phieuNhapXuat&action=xuatkhotp&error=5&maDonHang='.$maDonHang);
        exit;
    }

    // -----------------------------
    // Lấy thông tin từ đơn hàng
    $donHang = $px->getDonHang($maDonHang);
    if (!$donHang) {
        header('Location: index.php?controller=phieuNhapXuat&action=xuatkhotp&error=3');
        exit;
    }

    // Gán dữ liệu từ DonHang
    $data['maTP']   = $donHang['maTP'];
    $data['tenTP']  = $donHang['tenSP'];  // dùng để hiển thị
    $data['soLuong'] = $_POST['soLuong']; // hoặc $donHang['soLuong'] nếu muốn xuất hết
    $data['maPhieu'] = $_POST['maPhieu'];
    $data['maKho'] = 'K002'; // Kho thành phẩm mặc định
    $data['ngayXuat']= $_POST['ngayXuat'];
    $data['maNguoiLap'] = $_POST['maNguoiLap'];
    $data['maDonHang']  = $maDonHang;
    // -----------------------------

    // Kiểm tra số lượng
    if ($donHang['soLuong'] < $data['soLuong']) {
        header('Location: index.php?controller=phieuNhapXuat&action=xuatkhotp&error=4&tenSP='.$donHang['tenSP']);
        exit;
    }

    // Lưu phiếu
    $px->create($data);

    // Cập nhật tồn kho
    $px->updateSoLuongTP($data['maTP'], $data['soLuong']);

    header('Location: index.php?controller=phieuNhapXuat&action=xuatkhotp&success=1');
}



    // Hiển thị form lập phiếu
    public function taophieu() {
        // Handle POST requests BEFORE any view is included
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->luuphieu();
            return; // Never reach here if luuphieu() processes the request
        }

        $px = new PhieuXuatKhoTP();

        // Lấy danh sách đơn hàng kèm tên thành phẩm
        $dsDonHang = $px->getAllDonHangWithTP();

        // Sinh mã phiếu mới
        $maPhieu = $px->getNextMaPhieu();

        // Lấy ngày hiện tại
        $ngayXuat = date('Y-m-d');

        // Người lập (có thể hardcode hoặc lấy từ session)
        $maNguoiLap = 'ND004';

        // Gọi view (only for GET requests)
        require_once dirname(__FILE__) . '/../views/phieu/PhieuXuatKhoForm.php';
    }

    // Hiển thị danh sách phiếu xuất (alias)
    public function xuatkhotp() {
        $this->taophieu();
    }
}
?>