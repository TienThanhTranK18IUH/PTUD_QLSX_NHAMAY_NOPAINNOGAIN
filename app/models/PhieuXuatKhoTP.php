<?php
require_once dirname(__FILE__) . '/Database.php';

class PhieuXuatKhoTP {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->conn;
    }

    // Lấy tất cả phiếu xuất (kèm tên thành phẩm, tên kho, họ tên người lập)
public function getAll() {
    $sql = "SELECT 
                p.maPhieu, 
                p.maKho, 
                p.ngayXuat, 
                p.maNguoiLap, 
                p.maDonHang, 
                p.maTP, 
                t.tenTP AS tenTP, 
                k.tenKho AS tenKho, 
                nd.hoTen AS nguoiLapName,
                p.soLuong
            FROM PhieuXuatKhoTP p
            LEFT JOIN ThanhPham t ON p.maTP = t.maTP
            LEFT JOIN Kho k ON p.maKho = k.maKho
            LEFT JOIN NguoiDung nd ON p.maNguoiLap = nd.maNguoiDung
            ORDER BY p.ngayXuat DESC";

    return $this->db->query($sql);
}

    // Sinh mã phiếu mới PXTP001, PXTP002,...
    public function getNextMaPhieu() {
        $sql = "SELECT maPhieu FROM PhieuXuatKhoTP ORDER BY maPhieu DESC LIMIT 1";
        $rows = $this->db->query($sql);
        if (!empty($rows)) {
            $last = $rows[0]['maPhieu'];
            $num = (int)substr($last, 4) + 1;
            return 'PXTP' . str_pad($num, 3, '0', STR_PAD_LEFT);
        }
        return 'PXTP001';
    }

    // Kiểm tra đơn hàng đã lập phiếu chưa
    public function checkExistDonHang($maDonHang) {
        if (!isset($maDonHang) || $maDonHang === '') return false;
        $stmt = $this->conn->prepare("SELECT maPhieu FROM PhieuXuatKhoTP WHERE maDonHang = ?");
        if (!$stmt) return false;
        $stmt->bind_param('s', $maDonHang);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    // Lấy thông tin đơn hàng
    public function getDonHang($maDonHang) {
        if (!isset($maDonHang) || $maDonHang === '') return null;
        $stmt = $this->conn->prepare("SELECT * FROM DonHang WHERE maDonHang = ?");
        if (!$stmt) return null;
        $stmt->bind_param('s', $maDonHang);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 0) { $stmt->close(); return null; }

        $meta = $stmt->result_metadata();
        $fields = $data = array();
        while ($field = $meta->fetch_field()) {
            $fields[] = &$data[$field->name];
        }
        call_user_func_array(array($stmt, 'bind_result'), $fields);
        $stmt->fetch();
        $stmt->close();
        return $data;
    }

    // Lưu phiếu xuất
    public function create($data) {
        $stmt = $this->conn->prepare("INSERT INTO PhieuXuatKhoTP 
            (maPhieu, maKho, ngayXuat, maNguoiLap, maDonHang, maTP, soLuong) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $soLuong = (int)$data['soLuong'];
        $stmt->bind_param('ssssssi',
            $data['maPhieu'], $data['maKho'], $data['ngayXuat'],
            $data['maNguoiLap'], $data['maDonHang'], $data['maTP'], $soLuong
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // Cập nhật tồn kho
    public function updateSoLuongTP($maTP, $soLuongXuat) {
        $stmt = $this->conn->prepare("UPDATE ThanhPham SET soLuong = soLuong - ? WHERE maTP = ?");
        $soLuongXuat = (int)$soLuongXuat;
        $stmt->bind_param('is', $soLuongXuat, $maTP);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // Lấy danh sách đơn hàng chưa xuất
    public function getAllDonHangWithTP() {
        $sql = "SELECT maDonHang, maTP, tenSP AS tenTP, soLuong AS soLuongDH
                FROM DonHang
                WHERE maDonHang NOT IN (SELECT maDonHang FROM PhieuXuatKhoTP)
                ORDER BY maDonHang ASC";
        return $this->db->query($sql);
    }
}
?>