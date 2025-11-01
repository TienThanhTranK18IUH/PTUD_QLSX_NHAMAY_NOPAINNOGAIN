<?php
require_once dirname(__FILE__) . '/../../config/config.php';

class KeHoachSanXuat {
    private $conn;

    public function __construct() {
        $this->conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$this->conn) {
            die('Kết nối CSDL thất bại: ' . mysqli_connect_error());
        }
        mysqli_set_charset($this->conn, 'utf8');
    }

    // Lấy danh sách tất cả kế hoạch
    public function getAll() {
        $sql = "SELECT 
                    kh.maKeHoach,
                    x.tenXuong,
                    dh.tenSP,
                    kh.maDonHang,
                    kh.ngayBatDau,
                    kh.ngayKetThuc,
                    kh.tongSoLuong,
                    kh.trangThai,
                    kh.maNguyenLieu,
                    kh.tenNguyenLieu,
                    kh.soLuongNguyenLieu
                FROM KeHoachSanXuat kh
                JOIN Xuong x ON kh.maXuong = x.maXuong
                JOIN DonHang dh ON kh.maDonHang = dh.maDonHang";


        $result = mysqli_query($this->conn, $sql);
        if (!$result) {
            echo "<p style='color:red'>Lỗi SQL: " . mysqli_error($this->conn) . "</p>";
            return array();
        }
        $data = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }
}
?>