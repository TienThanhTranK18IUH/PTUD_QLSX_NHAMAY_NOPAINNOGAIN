<?php
require_once dirname(__FILE__).'/../../config/config.php';

class PhieuKTTP {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->conn->connect_error) die('Kết nối thất bại: '.$this->conn->connect_error);
        if (defined('DB_CHARSET') && DB_CHARSET) $this->conn->set_charset(DB_CHARSET); else $this->conn->set_charset('utf8');
    }

    /* ===== helpers ===== */
    private function getTableColumns($table) {
        $cols = array();
        $rs = $this->conn->query("SHOW COLUMNS FROM `".$this->conn->real_escape_string($table)."`");
        if ($rs) while ($r = $rs->fetch_assoc()) $cols[strtolower($r['Field'])] = $r['Field'];
        return $cols;
    }
    private function pick($cands, $cols, $fallback) {
        foreach ($cands as $c) if (isset($cols[strtolower($c)])) return $cols[strtolower($c)];
        return $fallback;
    }

    /* ===== queries ===== */
    public function getThanhPhamChoKiemTra() {
        // Trả về các thành phẩm chưa từng được lập phiếu kiểm tra (chưa có trong phieukiemtrathanhpham)
        $sql = "SELECT tp.maTP, tp.tenTP, tp.soLuong
                FROM thanhpham tp
                WHERE NOT EXISTS (
                    SELECT 1 FROM phieukiemtrathanhpham k WHERE k.maTP = tp.maTP
                )
                AND tp.soLuong > 0
                ORDER BY tp.tenTP ASC";
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
    $sql = "SELECT maPhieu 
            FROM phieukiemtrathanhpham 
            WHERE maPhieu LIKE 'KP%' 
            ORDER BY CAST(SUBSTRING(maPhieu, 3) AS UNSIGNED) DESC 
            LIMIT 1";

    $rs = $this->conn->query($sql);

    if ($rs && ($row = $rs->fetch_assoc())) {
        $num = (int)substr($row['maPhieu'], 2); // chỉ lấy số sau KP
        $num++;
    } else {
        $num = 1;
    }

    return 'KP'.str_pad($num, 3, '0', STR_PAD_LEFT);
}


    public function addPhieuKT($data) {
    $cols = $this->getTableColumns('phieukiemtrathanhpham');

    $c_maPhieu  = isset($cols['maphieu']) ? $cols['maphieu'] : 'maPhieu';
    $c_maTP     = isset($cols['matp']) ? $cols['matp'] : 'maTP';
    $c_slkt     = $this->pick(array('sl_kiemtra','slkiemtra','soluongkiemtra'), $cols, 'SL_KiemTra');
    $c_sldc     = $this->pick(array('sl_datchuan','sldatchuan','soluongdatchuan'), $cols, 'SL_DatChuan');
    $c_ketqua   = isset($cols['ketqua']) ? $cols['ketqua'] : 'ketQua';
    $c_ghichu   = $this->pick(array('ghichu','ghi_chu','note'), $cols, 'ghiChu'); // 🔽 THÊM
    $c_ngaylap  = $this->pick(array('ngaylap','ngay_lap','created_at'), $cols, 'ngayLap');
    $c_maQC     = $this->pick(array('manhanvienqc','ma_nhan_vien_qc','manvqc','maqc'), $cols, 'maNhanVienQC');

    $maPhieu      = $this->conn->real_escape_string($data['maPhieu']);
    $maTP         = $this->conn->real_escape_string($data['maTP']);
    $SL_KiemTra   = (int)$data['SL_KiemTra'];
    $SL_DatChuan  = (int)$data['SL_DatChuan'];
    $ketQua       = $this->conn->real_escape_string($data['ketQua']);
    $ghiChu       = isset($data['ghiChu']) ? $this->conn->real_escape_string($data['ghiChu']) : null; // 🔽 THÊM
    $ngayLap      = $this->conn->real_escape_string($data['ngayLap']);
    $maNhanVienQC = $this->conn->real_escape_string($data['maNhanVienQC']);

    if ($SL_DatChuan > $SL_KiemTra) return false;

    // 🔽 THÊM ghiChu vào INSERT
    $sql = "INSERT INTO phieukiemtrathanhpham
            (`$c_maPhieu`,`$c_maTP`,`$c_slkt`,`$c_sldc`,`$c_ketqua`,`$c_ghichu`,`$c_ngaylap`,`$c_maQC`)
            VALUES
            ('$maPhieu', '$maTP', $SL_KiemTra, $SL_DatChuan, '$ketQua', ".
            ($ghiChu === null ? "NULL" : "'$ghiChu'").",
            '$ngayLap', '$maNhanVienQC')";

    $ok = $this->conn->query($sql);
    if (!$ok) return false;

    // Lưu phiếu kiểm tra thành công (không cập nhật cột tinhTrang vì đã bị xóa)
    return true;
}

}
