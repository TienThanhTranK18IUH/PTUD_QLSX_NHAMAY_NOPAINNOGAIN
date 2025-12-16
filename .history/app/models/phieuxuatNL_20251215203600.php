<?php declare(strict_types=1); 
if (!isset($_SESSION)) session_start();
require_once dirname(__FILE__) . '/database.php';

class PhieuXuatNL {
    var $db;

    function __construct($db) {
        $this->db = $db;
    }

    function layDanhSachKho() {
        $result = $this->db->conn->query("SELECT maKho, tenKho FROM kho");
        $data = array();
        while ($row = $result->fetch_assoc()) { $data[] = $row; }
        return $data;
    }

    function layDanhSachNhanVienKho() {
        $result = $this->db->conn->query(
            "SELECT maNguoiDung, hoTen FROM nguoidung WHERE vaiTro = 'NhanVienKho'"
        );
        $data = array();
        while ($row = $result->fetch_assoc()) { $data[] = $row; }
        return $data;
    }


    function layDanhSachPhieuYeuCau() {
    $result = $this->db->conn->query("
        SELECT maPhieu, maNguyenLieu, soLuong
        FROM phieuyeucaunguyenlieu
        WHERE trangThai = 'DaDuyet'
    ");
    $data = array();
    while ($row = $result->fetch_assoc()) { $data[] = $row; }
    return $data;
    }

    function layDanhSachNguyenLieu() {
        $result = $this->db->conn->query(
            "SELECT maNguyenLieu, tenNguyenLieu, soLuongTon FROM nguyenlieu"
        );
        $data = array();
        while ($row = $result->fetch_assoc()) { $data[] = $row; }
        return $data;
    }

    function luuPhieuXuat($maKho, $ngayXuat, $maNguoiLap, $maPhieuYC, $maNguyenLieu, $soLuongNLYC, $soLuongTonKho) {
        $conn = $this->db->conn;
        $sql = "INSERT INTO phieuxuatkhonl(maKho, ngayXuat, maNguoiLap, maPhieuYC, maNguyenLieu, soLuongNLYC, soLuongTonKho)
                VALUES ('$maKho', '$ngayXuat', '$maNguoiLap', '$maPhieuYC', '$maNguyenLieu', $soLuongNLYC, $soLuongTonKho)";
        return $conn->query($sql);
    }

    function layDanhSachPhieuXuat() {
        $result = $this->db->conn->query("SELECT * FROM phieuxuatkhonl ORDER BY ngayXuat DESC");
        $data = array();
        while ($row = $result->fetch_assoc()) { $data[] = $row; }
        return $data;
    }
}
?>
