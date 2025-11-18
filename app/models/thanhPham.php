<?php
require_once 'database.php';

class ThanhPham {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Lấy danh sách thành phẩm
    public function getAll() {
        $sql = "SELECT * FROM thanhpham ORDER BY maTP ASC";
        return $this->db->query($sql);
    }

    // Tự sinh maTP dạng TP001, TP002…
    public function generateMaTP() {
        $sql = "SELECT maTP FROM thanhpham ORDER BY maTP DESC LIMIT 1";
        $result = $this->db->query($sql);
        if (!empty($result)) {
            $last = $result[0]['maTP'];
            $num = (int) substr($last, 2) + 1;
        } else {
            $num = 1;
        }
        return 'TP' . str_pad($num, 3, '0', STR_PAD_LEFT);
    }
     public function getSanPhamByKeHoach($maKeHoach) {
        $maKeHoach = $this->db->escape_string($maKeHoach);
        $sql = "SELECT dh.tenSP, dh.maDonHang 
                FROM donhang dh
                INNER JOIN kehoachsanxuat kh ON kh.maDonHang = dh.maDonHang
                WHERE kh.maKeHoach = '$maKeHoach'";
        return $this->db->query($sql);
    }
    // Lưu thành phẩm
    public function insert($data) {
        $maTP = $this->generateMaTP();
        $tenTP = $data['tenTP'];
        $soLuong = $data['soLuong'];
        $tinhTrang = $data['tinhTrang'];
        $maKeHoach = $data['maKeHoach'];
        $maKho = $data['maKho'];
        $tenKho = $data['tenKho'];
        $maXuong = $data['maXuong'];

        $sql = "INSERT INTO thanhpham (maTP, tenTP, soLuong, tinhTrang, maKeHoach, maKho, tenKho, maXuong)
                VALUES ('$maTP', '$tenTP', $soLuong, '$tinhTrang', '$maKeHoach', '$maKho', '$tenKho', '$maXuong')";
        return $this->db->query($sql);
    }
     // Lấy theo khoảng ngày
    public function getByDateRange($from, $to){
        $sql = "SELECT tp.maTP, tp.tenTP, tp.soLuong, tp.tinhTrang, tp.maKeHoach, kh.maXuong, tp.ngayLap
                FROM thanhpham tp
                LEFT JOIN kehoachsanxuat kh ON tp.maKeHoach = kh.maKeHoach
                WHERE tp.ngayLap BETWEEN '$from' AND '$to'
                ORDER BY tp.ngayLap ASC";
        return $this->db->query($sql);
    }
}
?>