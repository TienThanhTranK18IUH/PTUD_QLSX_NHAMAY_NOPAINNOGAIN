<?php
require_once 'Database.php';

class Xuong {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Lấy toàn bộ xưởng
    public function getAll() {
        $conn = $this->db->conn;
        $result = $conn->query("SELECT maXuong, tenXuong FROM xuong");
        $data = array();
        if($result && $result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
        }
        return $data;
    }

    // Lấy chi tiết xưởng theo mã
    public function getById($maXuong){
        $conn = $this->db->conn;
        $stmt = $conn->prepare("SELECT * FROM xuong WHERE maXuong=?");
        $stmt->bind_param("s", $maXuong);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
     public function getSanLuongTheoXuong() {
        $sql = "SELECT 
                    x.tenXuong,
                    SUM(kh.tongSoLuong) AS TongSoLuongKeHoach,
                    SUM(kh.ghiNhanSanXuat) AS TongSanLuongDaLam
                FROM kehoachsanxuat kh
                JOIN xuong x ON kh.maXuong = x.maXuong
                GROUP BY x.maXuong, x.tenXuong";
        return $this->db->query($sql);
    }
}
?>
