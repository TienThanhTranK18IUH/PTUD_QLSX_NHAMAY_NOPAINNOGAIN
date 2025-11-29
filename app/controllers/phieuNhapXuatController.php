<?php
session_start();
require_once dirname(__FILE__) . '/../models/PhieuXuatKhoTP.php';

class phieuNhapXuatController {

    // ==========================
    //  HIỂN THỊ FORM LẬP PHIẾU
    // ==========================
    public function taophieu() {

        $px = new PhieuXuatKhoTP();

        // Lấy danh sách đơn hàng còn tồn để xuất
        $dsDonHang = $px->getAllDonHangWithTP();

        // Tạo mã phiếu tự động
        $maPhieu = $px->getNextMaPhieu();

        // Ngày lập phiếu (định dạng d-m-Y hiển thị đẹp hơn)
        $ngayXuat = date('d-m-Y');

        // Lấy mã người lập (mặc định ND000)
        $maNguoiLap = isset($_SESSION['user']['maNguoiDung'])
            ? $_SESSION['user']['maNguoiDung']
            : 'ND000';

        require_once dirname(__FILE__) . '/../views/phieu/PhieuXuatKhoForm.php';
    }


    // ======================
    //   XỬ LÝ LƯU PHIẾU
    // ======================
    public function luuphieu() {

        $px = new PhieuXuatKhoTP();

        // BẮT LỖI ĐƠN HÀNG TRỐNG
        if (empty($_POST['maDonHang'])) {
            header("Location: index.php?controller=phieuNhapXuat&action=taophieu&error=1");
            exit;
        }

        $maDonHang = $_POST['maDonHang'];

        // ĐƠN HÀNG ĐÃ LẬP PHIẾU CHƯA?
        if ($px->checkExistDonHang($maDonHang)) {
            header("Location: index.php?controller=phieuNhapXuat&action=taophieu&error=5&maDonHang=".$maDonHang);
            exit;
        }

        // LẤY THÔNG TIN ĐƠN HÀNG
        $donHang = $px->getDonHang($maDonHang);

        if (!$donHang) {
            header("Location: index.php?controller=phieuNhapXuat&action=taophieu&error=3");
            exit;
        }

        // KIỂM TRA SỐ LƯỢNG
        $soLuong = isset($_POST['soLuong']) ? (int)$_POST['soLuong'] : 0;

        if ($soLuong <= 0) {
            header("Location: index.php?controller=phieuNhapXuat&action=taophieu&error=4&tenSP={$donHang['tenSP']}");
            exit;
        }

        if ($soLuong > $donHang['soLuong']) {
            header("Location: index.php?controller=phieuNhapXuat&action=taophieu&error=4&tenSP={$donHang['tenSP']}");
            exit;
        }

        // NGÀY XUẤT đưa về dạng Y-m-d trước khi lưu DB
        $ngayXuat = date('Y-m-d', strtotime($_POST['ngayXuat']));

        // DỮ LIỆU LƯU PHIẾU
        $data = array(
            'maPhieu'     => $_POST['maPhieu'],
            'maKho'       => 'K002',
            'ngayXuat'    => $ngayXuat,
            'maNguoiLap'  => isset($_SESSION['user']['maNguoiDung']) 
                                ? $_SESSION['user']['maNguoiDung'] 
                                : 'ND000',
            'maDonHang'   => $maDonHang,
            'maTP'        => $donHang['maTP'],
            'soLuong'     => $soLuong
        );

        // LƯU PHIẾU
        $px->create($data);

        // CẬP NHẬT TỒN KHO
        $px->updateSoLuongTP($data['maTP'], $soLuong);

        // CHUYỂN VỀ DANH SÁCH
        header("Location: index.php?controller=phieuNhapXuat&action=xuatkhotp&ok=1");
        exit;
    }


    // ===============================
    //   DANH SÁCH PHIẾU XUẤT KHO
    // ===============================
    public function xuatkhotp() {
        $px = new PhieuXuatKhoTP();
        $dsPhieu = $px->getAll();
        require_once dirname(__FILE__) . '/../views/phieu/phieuXuatKhoTP.php';
    }
}
?>