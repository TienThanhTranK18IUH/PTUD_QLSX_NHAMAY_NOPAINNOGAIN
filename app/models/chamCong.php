<?php
// app/models/chamCong.php — PHP 5.x (WAMP 2.0)
require_once dirname(__FILE__) . '/database.php';

class ChamCong {
    protected $db;
    protected $conn;

    public function __construct($db = null) {
        if ($db) {
            $this->db = $db;
            if (property_exists($db, 'conn')) $this->conn = $db->conn;
        } else {
            $this->db = new Database();
            if (property_exists($this->db, 'conn')) $this->conn = $this->db->conn;
        }
    }

    private function safe($v) { if (!isset($v)) $v=''; return addslashes(trim($v)); }

    // Tính số giờ giữa HH:MM[:SS] (qua ngày +24h)
    public function tinhSoGio($gioVao, $gioRa){
        if (!$gioVao || !$gioRa) return 0.00;
        $s = strtotime('1970-01-01 '.$gioVao);
        $e = strtotime('1970-01-01 '.$gioRa);
        if ($e < $s) $e += 24*3600;
        return round(($e-$s)/3600.0, 2);
    }

    // Danh sách ca (chấp nhận các tên cột khác nhau)
    public function getDanhSachCa(){
        $rows = array();
        if (!$this->conn) return $rows;

        $sql = "SELECT maCa,
                       COALESCE(gioBatDau, thoiGianBatDau)  AS thoiGianBatDau,
                       COALESCE(gioKetThuc, thoiGianKetThuc) AS thoiGianKetThuc
                FROM calamviec ORDER BY maCa";
        $rs = $this->conn->query($sql);
        if ($rs && $rs->num_rows>0) while($r=$rs->fetch_assoc()) $rows[]=$r;

        if (count($rows)===0){
            $rows = array(
                array('maCa'=>'CA1','thoiGianBatDau'=>'07:00:00','thoiGianKetThuc'=>'11:30:00'),
                array('maCa'=>'CA2','thoiGianBatDau'=>'13:00:00','thoiGianKetThuc'=>'17:30:00'),
                array('maCa'=>'CA3','thoiGianBatDau'=>'17:30:00','thoiGianKetThuc'=>'22:00:00'),
            );
        }
        return $rows;
    }

    /* ============ CREATE: ghi nhận chấm công ============ */
    public function create($data){
        $maNguoiDung = $this->safe($data['maNguoiDung']);
        $tenCongNhan = $this->safe($data['tenCongNhan']);
        $maXuong     = $this->safe($data['maXuong']);
        $ngayCham    = $this->safe($data['ngayCham']);   // yyyy-mm-dd
        $gioVao      = $this->safe($data['gioVao']);     // HH:MM hoặc HH:MM:SS
        $gioRa       = $this->safe($data['gioRa']);      // "
        $loaiNgay    = $this->safe($data['loaiNgay']);   // enum
        $maCa        = $this->safe($data['maCa']);
        $sanLuong    = isset($data['sanLuongHoanThanh']) ? (int)$data['sanLuongHoanThanh'] : 0;

        if ($gioVao!=='' && strlen($gioVao)==5) $gioVao .= ':00';
        if ($gioRa !=='' && strlen($gioRa )==5) $gioRa  .= ':00';

        $soGioLam = (isset($data['soGioLam']) && $data['soGioLam']!=='')
            ? floatval($data['soGioLam'])
            : $this->tinhSoGio($gioVao, $gioRa);

        // Map maLichLam từ lichlamviec theo (NV + ngày + ca)
        $maLichLam = '';
        if ($maNguoiDung!=='' && $ngayCham!=='' && $maCa!==''){
            $q = "SELECT maLichLam
                  FROM lichlamviec
                  WHERE maNguoiDung='{$maNguoiDung}'
                    AND ngayLam='{$ngayCham}'
                    AND maCa='{$maCa}'
                  LIMIT 1";
            $rs = $this->conn->query($q);
            if ($rs && $rs->num_rows>0){
                $row = $rs->fetch_assoc();
                $maLichLam = isset($row['maLichLam']) ? $row['maLichLam'] : '';
            }
        }

        $sql = "INSERT INTO chamcong
                (maNguoiDung, tenCongNhan, sanLuongHoanThanh, maXuong, ngayCham,
                 gioVao, gioRa, soGioLam, loaiNgay, maCa, maLichLam)
                VALUES (
                 '{$maNguoiDung}','{$tenCongNhan}',{$sanLuong},'{$maXuong}','{$ngayCham}',
                 '{$gioVao}','{$gioRa}',{$soGioLam},'{$loaiNgay}',
                 ".($maCa=='' ? "NULL" : "'{$maCa}'").",
                 ".($maLichLam=='' ? "NULL" : "'{$maLichLam}'")."
                )";
        $ok = $this->conn->query($sql);
        if (!$ok){
            echo "<pre style='color:red'>Lỗi SQL: ".$this->conn->error."</pre>";
            echo "<pre style='color:gray'>".$sql."</pre>";
        }
        return $ok ? true : false;
    }

    /* ============ API cho xem lịch/giờ công ============ */
    public function getRange($maNguoiDung, $fromYmd, $toYmd){
        $m=$this->safe($maNguoiDung); $f=$this->safe($fromYmd); $t=$this->safe($toYmd);
        $sql = "SELECT ngayCham, gioVao, gioRa, soGioLam, loaiNgay, maCa, maLichLam
                FROM chamcong
                WHERE maNguoiDung='{$m}' AND ngayCham BETWEEN '{$f}' AND '{$t}'
                ORDER BY ngayCham, gioVao";
        $rs = $this->conn->query($sql);
        $out = array(); if ($rs && $rs->num_rows>0) while($r=$rs->fetch_assoc()) $out[]=$r;
        return $out;
    }

    // Giờ công tháng: TÍNH TỪ BẢNG CHẤM CÔNG
    public function monthStats($maNguoiDung, $fromYmd, $toYmd){
        $m=$this->safe($maNguoiDung); $f=$this->safe($fromYmd); $t=$this->safe($toYmd);
        $sql = "SELECT
                  IFNULL(SUM(CASE
                      WHEN loaiNgay IN ('NgayThuong','TangCa','NgayLe','ChuNhat')
                      THEN IFNULL(soGioLam,0) ELSE 0 END),0) AS tongGio,
                  SUM(CASE WHEN loaiNgay IN ('NgayThuong','TangCa','NgayLe','ChuNhat')
                           AND IFNULL(soGioLam,0) > 0 THEN 1 ELSE 0 END) AS soCa,
                  SUM(loaiNgay='NghiCoLuong')     AS nghiCoLuong,
                  SUM(loaiNgay='NghiKhongLuong')  AS nghiKhongLuong
                FROM chamcong
                WHERE maNguoiDung='{$m}' AND ngayCham BETWEEN '{$f}' AND '{$t}'";
        $rs = $this->conn->query($sql);
        if ($rs && $rs->num_rows>0){ $r=$rs->fetch_assoc();
            return array(
              'tongGio'=>floatval($r['tongGio']),
              'soCa'=>intval($r['soCa']),
              'nghiCoLuong'=>intval($r['nghiCoLuong']),
              'nghiKhongLuong'=>intval($r['nghiKhongLuong'])
            );
        }
        return array('tongGio'=>0,'soCa'=>0,'nghiCoLuong'=>0,'nghiKhongLuong'=>0);
    }

    public function payrollAggregate($maNguoiDung, $fromYmd, $toYmd){
        $u=$this->safe($maNguoiDung); $f=$this->safe($fromYmd); $t=$this->safe($toYmd);
        $sql = "SELECT
                  IFNULL(SUM(soGioLam),0) AS gioTong,
                  IFNULL(SUM(CASE WHEN loaiNgay='NgayThuong' THEN soGioLam ELSE 0 END),0) AS gioNgayThuong,
                  IFNULL(SUM(CASE WHEN loaiNgay='ChuNhat'    THEN soGioLam ELSE 0 END),0) AS gioChuNhat,
                  IFNULL(SUM(CASE WHEN loaiNgay='NgayLe'     THEN soGioLam ELSE 0 END),0) AS gioNgayLe,
                  IFNULL(SUM(CASE WHEN loaiNgay='TangCa'     THEN soGioLam ELSE 0 END),0) AS gioTangCa,
                  IFNULL(SUM(CASE WHEN maCa='CA3'            THEN soGioLam ELSE 0 END),0) AS gioDem,
                  IFNULL(SUM(loaiNgay='NghiCoLuong'),0)    AS ngayNghiCoLuong,
                  IFNULL(SUM(loaiNgay='NghiKhongLuong'),0) AS ngayNghiKhongLuong
                FROM chamcong
                WHERE maNguoiDung='{$u}' AND ngayCham BETWEEN '{$f}' AND '{$t}'";
        $rs = $this->conn->query($sql);
        if ($rs && $rs->num_rows>0) return $rs->fetch_assoc();
        return array(
            'gioTong'=>0,'gioNgayThuong'=>0,'gioChuNhat'=>0,'gioNgayLe'=>0,'gioTangCa'=>0,
            'gioDem'=>0,'ngayNghiCoLuong'=>0,'ngayNghiKhongLuong'=>0
        );
    }

    public function countNightShifts($maNguoiDung, $fromYmd, $toYmd){
        $m=$this->safe($maNguoiDung); $f=$this->safe($fromYmd); $t=$this->safe($toYmd);
        $sql = "SELECT COUNT(*) AS cnt
                FROM chamcong
                WHERE maNguoiDung='{$m}'
                  AND ngayCham BETWEEN '{$f}' AND '{$t}'
                  AND maCa='CA3' AND IFNULL(soGioLam,0) > 0";
        $rs = $this->conn->query($sql);
        if ($rs && $rs->num_rows>0){ $r=$rs->fetch_assoc(); return intval($r['cnt']); }
        return 0;
    }
}
