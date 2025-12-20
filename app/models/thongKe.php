<?php
require_once dirname(__FILE__) . '/database.php';

class ThongKe {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /* ================= QC THÀNH PHẨM ================= */
    public function getChartPie($from, $to){
        $from = $this->db->conn->real_escape_string($from);
        $to   = $this->db->conn->real_escape_string($to);

        $sql = "SELECT ketQua, COUNT(*) AS soLuong
                FROM phieukiemtrathanhpham
                WHERE ngayLap BETWEEN '$from' AND '$to'
                GROUP BY ketQua";

        $rs = $this->db->conn->query($sql);
        $data = array();
        if ($rs) {
            while ($row = $rs->fetch_assoc()) {
                $data[$row['ketQua']] = (int)$row['soLuong'];
            }
        }
        return $data;
    }

    /* ============ ĐƠN HÀNG THEO THÁNG ============ */
    /*
        Quy ước:
        - Đơn hàng đạt      : tinhTrang = 'Đã giao'
        - Đơn hàng chưa đạt: còn lại
    */
    public function getDonHangTheoThang($from, $to) {
        $from = $this->db->conn->real_escape_string($from);
        $to   = $this->db->conn->real_escape_string($to);

        $sql = "SELECT 
                    DATE_FORMAT(ngayDat, '%Y-%m') AS thang,
                    COUNT(*) AS tongDon,

                    SUM(
                        CASE 
                            WHEN tinhTrang = 'Đã giao' 
                            THEN 1 ELSE 0 
                        END
                    ) AS donDat,

                    SUM(
                        CASE 
                            WHEN tinhTrang <> 'Đã giao' OR tinhTrang IS NULL 
                            THEN 1 ELSE 0 
                        END
                    ) AS donChuaDat

                FROM donhang
                WHERE ngayDat BETWEEN '$from' AND '$to'
                GROUP BY DATE_FORMAT(ngayDat, '%Y-%m')
                ORDER BY thang ASC";

        $rs = $this->db->conn->query($sql);
        $data = array();
        if ($rs) {
            while ($row = $rs->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }
}
?>
