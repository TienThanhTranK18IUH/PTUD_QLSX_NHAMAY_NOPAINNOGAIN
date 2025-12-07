<?php
require_once dirname(__FILE__) . '/../../config/config.php';

class PhieuSuaChua {
    private $conn;

    public function __construct() {
        $this->conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$this->conn) die('DB connect error: ' . mysqli_connect_error());
        if (defined('DB_CHARSET') && DB_CHARSET)
            mysqli_set_charset($this->conn, DB_CHARSET);
        else
            mysqli_set_charset($this->conn, 'utf8');
    }

    /* ===== Helpers ===== */
    private function esc($s){ return mysqli_real_escape_string($this->conn, $s); }

    public function generateNextMa(){
        // Mã rút gọn kiểu P + yymmdd + số thứ tự 3 chữ số
        $prefix = 'P' . date('ymd');
        $sql = "SELECT maPhieu FROM PhieuYeuCauSuaChua
                WHERE maPhieu LIKE '{$prefix}%'
                ORDER BY maPhieu DESC LIMIT 1";
        $res = mysqli_query($this->conn, $sql);
        $n = 1;
        if ($res && ($r = mysqli_fetch_assoc($res))) {
            $last = $r['maPhieu'];
            $num  = intval(substr($last, -3));
            $n    = $num + 1;
        }
        return $prefix . str_pad($n, 3, '0', STR_PAD_LEFT);
    }

    /* ===== Queries ===== */
    public function getAll() {
        $sql = "SELECT sc.maPhieu, sc.moTaSuCo, sc.ngayLap, sc.trangThai,
                       tb.maTB, tb.tenTB, tb.dayChuyen, tb.maXuong,
                       nd.maNguoiDung, nd.hoTen
                FROM PhieuYeuCauSuaChua sc
                LEFT JOIN ThietBi tb   ON sc.maTB = tb.maTB
                LEFT JOIN nguoidung nd ON sc.maNguoiLap = nd.maNguoiDung
                ORDER BY sc.ngayLap DESC";
        $res = mysqli_query($this->conn, $sql);
        $rows = array();
        if (!$res) {
            error_log("SQL ERROR in getAll(): " . mysqli_error($this->conn) . " | SQL: " . $sql);
            return $rows;
        }
        if ($res) while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;
        return $rows;
    }

    /* ===== FIX QUAN TRỌNG: Lấy đủ thông tin thiết bị ===== */
    public function getAllThietBi() {
        $sql = "SELECT 
                    tb.maTB,
                    tb.tenTB,
                    tb.dayChuyen,
                    tb.maXuong,
                    x.tenXuong
                FROM ThietBi tb
                LEFT JOIN xuong x ON tb.maXuong = x.maXuong
                ORDER BY tb.maTB";
        $res = mysqli_query($this->conn, $sql);
        $rows = array();
        if ($res) while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;
        return $rows;
    }

    public function getAllNguoiDung() {
        $sql = "SELECT maNguoiDung AS maNV, hoTen 
                FROM NguoiDung 
                ORDER BY maNguoiDung";
        $res = mysqli_query($this->conn, $sql);
        $rows = array();
        if ($res) while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;
        return $rows;
    }

    public function add($maPhieu, $maTB, $moTaSuCo, $ngayLap, $trangThai, $maNguoiLap) {
        $maPhieu    = $this->esc($maPhieu);
        $maTB       = $this->esc($maTB);
        $moTaSuCo   = $this->esc($moTaSuCo);
        $ngayLap    = $this->esc($ngayLap);
        $trangThai  = $this->esc($trangThai);
        $maNguoiLap = $this->esc($maNguoiLap);

        $sql = "INSERT INTO PhieuYeuCauSuaChua
                (maPhieu, maTB, moTaSuCo, ngayLap, trangThai, maNguoiLap)
                VALUES
                ('$maPhieu','$maTB','$moTaSuCo','$ngayLap','$trangThai','$maNguoiLap')";
        return mysqli_query($this->conn, $sql);
    }

    public function delete($maPhieu) {
        $maPhieu = $this->esc($maPhieu);
        $sql = "DELETE FROM PhieuYeuCauSuaChua 
                WHERE maPhieu='$maPhieu' 
                LIMIT 1";
        return mysqli_query($this->conn, $sql);
    }
}
