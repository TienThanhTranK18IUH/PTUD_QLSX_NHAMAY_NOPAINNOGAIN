<?php
require_once dirname(__FILE__) . '/database.php';

class ThongKe {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Lấy phiếu QC theo khoảng thời gian
    public function getPhieuQC($from, $to) {
        $from = $this->db->conn->real_escape_string($from);
        $to   = $this->db->conn->real_escape_string($to);
        $sql = "SELECT * FROM phieukiemtrathanhpham 
                WHERE ngayLap BETWEEN '$from' AND '$to'
                ORDER BY ngayLap DESC";
        $result = $this->db->conn->query($sql);
        $data = array();
        if($result){
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getDonHangTheoNgay($from, $to) {
    $from = $this->db->conn->real_escape_string($from);
    $to   = $this->db->conn->real_escape_string($to);

    // Lấy tổng đơn hàng theo ngàyDat
    $sql = "SELECT *,
                   COUNT(*) AS tongDH,
                   SUM(CASE WHEN tinhTrang='Đã giao' THEN 1 ELSE 0 END) AS dhHoanThanh,
                   SUM(CASE WHEN tinhTrang!='Đã giao' THEN 1 ELSE 0 END) AS dhChuaHoanThanh
            FROM donhang
            WHERE ngayDat BETWEEN '$from' AND '$to'
            GROUP BY ngayDat
            ORDER BY ngayDat ASC";

    $result = $this->db->conn->query($sql);
    $data = array();
    if($result){
        while($row = $result->fetch_assoc()){
            $data[] = $row;
        }
    }
    return $data;
}
    // Dữ liệu biểu đồ tròn QC
    public function getChartPie($from, $to){
        $from = $this->db->conn->real_escape_string($from);
        $to   = $this->db->conn->real_escape_string($to);
        $sql = "SELECT ketQua, COUNT(*) as soLuong
                FROM phieukiemtrathanhpham
                WHERE ngayLap BETWEEN '$from' AND '$to'
                GROUP BY ketQua";
        $result = $this->db->conn->query($sql);
        $data = array();
        if($result){
            while($row = $result->fetch_assoc()){
                $data[$row['ketQua']] = $row['soLuong'];
            }
        }
        return $data;
    }

   public function getChartTP($from, $to) {
    $from = $this->db->conn->real_escape_string($from);
    $to   = $this->db->conn->real_escape_string($to);

    $sql = "SELECT dh.ngayDat, SUM(dh.soLuong) AS tongSL
            FROM donHang dh
            WHERE dh.ngayDat BETWEEN '$from' AND '$to'
            GROUP BY dh.ngayDat
            ORDER BY dh.ngayDat ASC";

    $result = $this->db->conn->query($sql);
    $data = array();
    if($result){
        while($row = $result->fetch_assoc()){
            $data[$row['ngayDat']] = $row['tongSL'];
        }
    }
    return $data;
}
}
?>
