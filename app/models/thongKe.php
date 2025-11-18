<?php
require_once dirname(__FILE__) .'/database.php';

class ThongKe {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Lấy danh sách phiếu QC theo khoảng thời gian
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

    // Lấy dữ liệu cho biểu đồ cột: tổng số kiểm tra / đạt chuẩn theo TP
    public function getChartTP($from, $to){
        $from = $this->db->conn->real_escape_string($from);
        $to   = $this->db->conn->real_escape_string($to);
        $sql = "SELECT maTP, SUM(SL_KiemTra) as tongKiemTra, SUM(SL_DatChuan) as tongDatChuan
                FROM phieukiemtrathanhpham
                WHERE ngayLap BETWEEN '$from' AND '$to'
                GROUP BY maTP";
        $result = $this->db->conn->query($sql);
        $data = array();
        if($result){
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
        }
        return $data;
    }

    // Dữ liệu cho biểu đồ tròn: tỉ lệ Đạt / Không đạt
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
}
?>
