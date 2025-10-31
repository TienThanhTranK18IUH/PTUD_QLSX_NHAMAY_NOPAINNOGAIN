<?php
require_once dirname(__FILE__).'/../../config/config.php';

class PhieuKTTP {
    /** @var mysqli */
    private $conn;

    public function __construct() {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->conn->connect_error) die('Kết nối thất bại: '.$this->conn->connect_error);
        $this->conn->set_charset('utf8');
    }

    public function getThanhPhamChoKiemTra() {
        $sql = "SELECT maTP, tenTP, soLuong, tinhTrang
                FROM thanhpham
                WHERE tinhTrang='Chờ kiểm tra'";
        $rs = $this->conn->query($sql);
        $out = array();
        if ($rs) while ($row = $rs->fetch_assoc()) $out[] = $row;
        return $out;
    }

    public function getSLTheoTP($maTP) {
        $maTP = $this->conn->real_escape_string($maTP);
        $sql  = "SELECT soLuong FROM thanhpham WHERE maTP='{$maTP}' LIMIT 1";
        $rs   = $this->conn->query($sql);
        if ($rs && ($row=$rs->fetch_assoc())) return (int)$row['soLuong'];
        return 0;
    }

    public function getNextMaPhieu() {
        $rs = $this->conn->query("SELECT maPhieu FROM phieukiemtrathanhpham ORDER BY maPhieu DESC LIMIT 1");
        if ($rs && ($row=$rs->fetch_assoc())) {
            $num = (int)substr($row['maPhieu'], 2); $num++;
        } else $num = 1;
        return 'KP'.str_pad($num, 3, '0', STR_PAD_LEFT);
    }

    public function addPhieuKT($data) {
        $maPhieu      = $this->conn->real_escape_string($data['maPhieu']);
        $maTP         = $this->conn->real_escape_string($data['maTP']);
        $SL_KiemTra   = (int)$data['SL_KiemTra'];
        $SL_DatChuan  = (int)$data['SL_DatChuan'];
        $ketQua       = $this->conn->real_escape_string($data['ketQua']);
        $ngayLap      = $this->conn->real_escape_string($data['ngayLap']);
        $maNhanVienQC = $this->conn->real_escape_string($data['maNhanVienQC']);

        if ($SL_DatChuan > $SL_KiemTra) return false;

        $sql = "INSERT INTO phieukiemtrathanhpham
                (maPhieu, maTP, SL_KiemTra, SL_DatChuan, ketQua, ngayLap, maNhanVienQC)
                VALUES
                ('{$maPhieu}','{$maTP}',{$SL_KiemTra},{$SL_DatChuan},
                 '{$ketQua}','{$ngayLap}','{$maNhanVienQC}')";
        $ok = $this->conn->query($sql);

        if ($ok) {
            $tinhTrang = ($ketQua === 'Đạt') ? 'Đạt' : 'Không đạt';
            $this->conn->query("UPDATE thanhpham SET tinhTrang='{$tinhTrang}' WHERE maTP='{$maTP}'");
            return true;
        }
        return false;
    }
}
