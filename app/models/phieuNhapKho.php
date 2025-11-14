<?php
require_once dirname(__FILE__) . '/Database.php';

class PhieuNhapKho {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Lấy mã phiếu kế tiếp: PNTP001, PNTP002, ...
    public function getNextMaPhieu() {
        $rows = $this->db->query("SELECT maPhieu FROM PhieuNhapKhoTP ORDER BY maPhieu DESC LIMIT 1");
        if (!empty($rows)) {
            $last = $rows[0]['maPhieu'];
            $num = (int)substr($last, 5) + 1;
            return 'PNTP' . str_pad($num, 3, '0', STR_PAD_LEFT);
        }
        return 'PNTP001';
    }

    // Danh sách phiếu nhập
    public function getAllPhieu() {
        return $this->db->query("
            SELECT p.*, t.tenTP, k.tenKho, nd.hoTen AS nguoiLap
            FROM PhieuNhapKhoTP p
            LEFT JOIN ThanhPham t ON p.maTP = t.maTP
            LEFT JOIN Kho k ON p.maKho = k.maKho
            LEFT JOIN NguoiDung nd ON p.maNguoiLap = nd.maNguoiDung
            ORDER BY p.ngayNhap DESC
        ");
    }

    // Danh sách kho thành phẩm
    public function getAllKho() {
        return $this->db->query("SELECT maKho, tenKho FROM Kho WHERE loaiKho = 'ThanhPham'");
    }

    // Danh sách thành phẩm
    public function getAllThanhPham() {
        return $this->db->query("SELECT maTP, tenTP, soLuong FROM ThanhPham");
    }

    // Lưu phiếu nhập kho thành phẩm
public function create($data) {
    $maPhieu    = $data['maPhieu'];
    $maKho      = $data['maKho'];
    $ngayNhap   = $data['ngayNhap'];
    $maNguoiLap = $data['maNguoiLap'];
    $trangThai  = $data['trangThai'];
    $maTP       = $data['maTP'];
    $soLuong    = (int)$data['soLuong'];

    $sql = "
        INSERT INTO PhieuNhapKhoTP (maPhieu, maKho, ngayNhap, maNguoiLap, trangThai, maTP, soLuong)
        VALUES ('$maPhieu', '$maKho', '$ngayNhap', '$maNguoiLap', N'$trangThai', '$maTP', $soLuong)
    ";

    // ✅ Tạo kết nối riêng cho lệnh INSERT (không dùng query() mặc định)
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset(DB_CHARSET);
    $result = $conn->query($sql);
    
    if ($result === TRUE) {
        return true;
    } else {
        echo "<pre style='color:red;'>❌ Lỗi SQL: $sql</pre>";
        echo "<pre style='color:red;'>MySQL báo: " . $conn->error . "</pre>";
        return false;
    }
}


}
?>