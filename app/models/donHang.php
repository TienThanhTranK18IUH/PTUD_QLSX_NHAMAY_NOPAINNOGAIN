<?php
require_once 'Database.php';

class DonHang {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // ✅ Lấy toàn bộ đơn hàng
    public function getAll() {
        $conn = $this->db->conn;
        $sql = "SELECT maDonHang, tenSP, soLuong, hanGiaoHang FROM donhang";
        $result = mysqli_query($conn, $sql);

        $data = array();
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // ✅ Lấy các đơn hàng chưa có kế hoạch
    public function getPendingOrders() {
        $conn = $this->db->conn;
        $sql = "SELECT dh.maDonHang, dh.tenSP, dh.soLuong, dh.ngayGiao 
                FROM donhang dh
                LEFT JOIN kehoachsanxuat kh ON dh.maDonHang = kh.maDonHang
                WHERE kh.maDonHang IS NULL
                ORDER BY dh.ngayGiao ASC";

        $result = mysqli_query($conn, $sql);
        $data = array();
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // ✅ Cập nhật trạng thái đơn hàng (phiên bản cũ)
    public function updateTrangThaiDonHang($maDonHang, $trangThaiMoi) {
        $conn = $this->db->conn;
        $maDonHang = mysqli_real_escape_string($conn, $maDonHang);
        $trangThaiMoi = mysqli_real_escape_string($conn, $trangThaiMoi);

        $sql = "UPDATE donhang SET trangThai='$trangThaiMoi' WHERE maDonHang='$maDonHang'";
        $result = mysqli_query($conn, $sql);

        if (!$result) {
            die("❌ Lỗi khi cập nhật trạng thái: " . mysqli_error($conn));
        }
        return true;
    }

    // ✅ Lấy chi tiết đơn hàng theo mã
    public function getById($maDonHang) {
        $conn = $this->db->conn;
        $maDonHang = mysqli_real_escape_string($conn, $maDonHang);
        $sql = "SELECT * FROM donhang WHERE maDonHang='$maDonHang'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
        return null;
    }

    // ✅ Lấy danh sách nguyên liệu theo đơn hàng
    public function getNguyenLieuByDonHang($maDonHang) {
        $conn = $this->db->conn;
        $maDonHang = mysqli_real_escape_string($conn, $maDonHang);

        $sql = "SELECT nl.maNL, nl.tenNL, dhnl.soLuong
                FROM donhang_nguyenlieu dhnl
                JOIN nguyenlieu nl ON dhnl.maNL = nl.maNL
                WHERE dhnl.maDonHang='$maDonHang'";

        $result = mysqli_query($conn, $sql);
        $data = array();
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }
}
?>
