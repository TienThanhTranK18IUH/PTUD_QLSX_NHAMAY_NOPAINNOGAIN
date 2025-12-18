<?php
require_once dirname(__FILE__) . '/Database.php';

class PhieuNhapKho {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // =============================
    // Lấy mã phiếu kế tiếp
    // =============================
    public function getNextMaPhieu() {
        $rows = $this->db->query(
            "SELECT maPhieu FROM PhieuNhapKhoTP ORDER BY maPhieu DESC LIMIT 1"
        );

        if (!empty($rows)) {
            $last = $rows[0]['maPhieu'];
            $num  = intval(substr($last, 4)) + 1;
            return 'PNTP' . str_pad($num, 3, '0', STR_PAD_LEFT);
        }
        return 'PNTP001';
    }

    // =============================
    // Danh sách phiếu nhập
    // =============================
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

    // =============================
    // Danh sách kho TP
    // =============================
    public function getAllKho() {
        return $this->db->query("
            SELECT maKho, tenKho
            FROM Kho
            WHERE loaiKho = 'ThanhPham'
        ");
    }

    // =============================
    // TP theo xưởng (CHỈ X001 | X002)
    // =============================
    public function getAllThanhPhamByXuong($maXuong) {

        // Chặn giá trị lạ
        if ($maXuong != 'X001' && $maXuong != 'X002') {
            return array();
        }

        $sql = "
            SELECT maTP, tenTP, soLuong
            FROM ThanhPham
            WHERE tinhTrang = 'Chờ kiểm tra'
              AND maXuong = '$maXuong'
        ";

        return $this->db->query($sql);
    }

    // =============================
    // Kiểm tra TP đã lập phiếu chưa
    // =============================
    public function checkExistTP($maTP) {
        $rows = $this->db->query("
            SELECT maTP
            FROM PhieuNhapKhoTP
            WHERE maTP = '$maTP'
        ");
        return !empty($rows);
    }

    // =============================
    // Lưu phiếu nhập kho
    // =============================
    public function create($data) {

        $maPhieu    = $data['maPhieu'];
        $maKho      = $data['maKho'];
        $maTP       = $data['maTP'];
        $soLuong    = intval($data['soLuong']);
        $ngayNhap   = $data['ngayNhap'];
        $maNguoiLap = $data['maNguoiLap'];
        $trangThai  = $data['trangThai'];

        $sql = "
            INSERT INTO PhieuNhapKhoTP
            (maPhieu, maKho, ngayNhap, maNguoiLap, trangThai, maTP, soLuong)
            VALUES
            ('$maPhieu', '$maKho', '$ngayNhap', '$maNguoiLap', '$trangThai', '$maTP', $soLuong)
        ";

        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $conn->set_charset(DB_CHARSET);

        if ($conn->query($sql)) {
            return true;
        }

        echo "<pre style='color:red'>❌ SQL ERROR: {$conn->error}</pre>";
        return false;
    }
}
?>
