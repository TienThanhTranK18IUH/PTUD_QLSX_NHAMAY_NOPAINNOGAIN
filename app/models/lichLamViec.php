<?php
// app/models/lichLamViec.php — PHP 5.x (WAMP 2.0)
require_once dirname(__FILE__) . '/database.php';

class LichLamViec {
    protected $db; protected $conn;

    public function __construct($db = null){
        if ($db) { $this->db=$db; if (property_exists($db,'conn')) $this->conn=$db->conn; }
        else { $this->db = new Database(); if (property_exists($this->db,'conn')) $this->conn=$this->db->conn; }
    }

    private function safe($v){ if(!isset($v)) $v=''; return addslashes(trim($v)); }

    public function getRange($maNguoiDung, $fromYmd, $toYmd){
        $m=$this->safe($maNguoiDung); $f=$this->safe($fromYmd); $t=$this->safe($toYmd);
        $sql = "SELECT ngayLam, maCa, maXuong
                FROM lichlamviec
                WHERE maNguoiDung='{$m}' AND ngayLam BETWEEN '{$f}' AND '{$t}'
                ORDER BY ngayLam, maCa";
        $rs = $this->conn->query($sql);
        $out=array(); if($rs && $rs->num_rows>0) while($r=$rs->fetch_assoc()) $out[]=$r;
        return $out;
    }

    // (dùng cho hiển thị lịch theo ca; giờ công tháng sẽ tính ở ChamCong)
    public function monthStatsFromLLV($maNguoiDung, $fromYmd, $toYmd){
        $m=$this->safe($maNguoiDung); $f=$this->safe($fromYmd); $t=$this->safe($toYmd);
        $sql = "SELECT
                  IFNULL(SUM(
                    CASE
                      WHEN TIME_TO_SEC(COALESCE(ca.gioKetThuc, ca.thoiGianKetThuc)) <
                           TIME_TO_SEC(COALESCE(ca.gioBatDau,  ca.thoiGianBatDau))
                      THEN (TIME_TO_SEC(COALESCE(ca.gioKetThuc, ca.thoiGianKetThuc))+86400
                           - TIME_TO_SEC(COALESCE(ca.gioBatDau, ca.thoiGianBatDau)))/3600
                      ELSE (TIME_TO_SEC(COALESCE(ca.gioKetThuc, ca.thoiGianKetThuc))
                           - TIME_TO_SEC(COALESCE(ca.gioBatDau,  ca.thoiGianBatDau)))/3600
                    END
                  ),0) AS tongGio,
                  COUNT(*) AS soCa,
                  SUM(llv.maCa='CA3') AS caDem
                FROM lichlamviec llv
                JOIN calamviec ca ON ca.maCa = llv.maCa
                WHERE llv.maNguoiDung='{$m}' AND llv.ngayLam BETWEEN '{$f}' AND '{$t}'";
        $rs = $this->conn->query($sql);
        if ($rs && $rs->num_rows>0){
            $r=$rs->fetch_assoc();
            return array(
              'tongGio'=>floatval($r['tongGio']),
              'soCa'=>intval($r['soCa']),
              'nghiCoLuong'=>0,'nghiKhongLuong'=>0,
              'caDem'=>intval($r['caDem'])
            );
        }
        return array('tongGio'=>0,'soCa'=>0,'nghiCoLuong'=>0,'nghiKhongLuong'=>0,'caDem'=>0);
    }
}
