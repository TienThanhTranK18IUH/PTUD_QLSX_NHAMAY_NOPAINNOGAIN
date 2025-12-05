<?php
/**
 * Model: PhieuYeuCau
 * - Tương thích PHP 5.x
 * - $db là wrapper có ->query($sql) trả về mảng
 * - Ghi (INSERT) dùng mysqli thuần trong dbWrite() để lấy boolean & lỗi
 */
class PhieuYeuCau {
    protected $db;
    protected $lastError = '';

    public static $TBL_HDR_CHOICES = array('phieuyeucaunguyenlieu','phieuyecaunguyenlieu');
    public static $TBL_DTL_CHOICES = array('chitietphieuyeucaunl','chitietphieuyecaunl');

    protected $TBL_HDR = 'phieuyeucaunguyenlieu';
    protected $TBL_DTL = 'chitietphieuyeucaunl';

    public function __construct($db){
        $this->db = $db;
        $hdr = $this->pickExistingTable(self::$TBL_HDR_CHOICES);
        $dtl = $this->pickExistingTable(self::$TBL_DTL_CHOICES);
        if ($hdr) $this->TBL_HDR = $hdr;
        if ($dtl) $this->TBL_DTL = $dtl;
    }

    /* ================= Utils ================= */
    private function safe($v){ if(!isset($v)) $v=''; return addslashes($v); }
    public  function getLastError(){ return $this->lastError; }
    private function resultOrEmpty($rs){ return (is_array($rs) && count($rs)>0) ? $rs : array(); }

    private function dbWrite($sql){
        if(!defined('DB_HOST')) require_once dirname(__FILE__).'/../../config/config.php';
        $cn=@new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        if($cn->connect_error){ $this->lastError='Connect error: '.$cn->connect_error; return false; }
        if(defined('DB_CHARSET') && DB_CHARSET) { @$cn->set_charset(DB_CHARSET); }
        $ok = ($cn->query($sql)===true);
        if(!$ok){ $this->lastError = 'SQL ERROR: '.$cn->error.' | SQL: '.$sql; }
        $cn->close();
        return $ok;
    }

    private function tableExists($name){
        if(!defined('DB_NAME')) require_once dirname(__FILE__).'/../../config/config.php';
        $t = $this->safe($name);
        $rs = $this->db->query(
            "SELECT 1 FROM information_schema.tables
             WHERE table_schema='".DB_NAME."' AND table_name='{$t}' LIMIT 1"
        );
        return (is_array($rs) && count($rs)>0);
    }
    private function pickExistingTable($candidates){
        foreach($candidates as $t){ if($this->tableExists($t)) return $t; }
        return '';
    }
    private function columnExists($table,$col){
        if(!defined('DB_NAME')) require_once dirname(__FILE__).'/../../config/config.php';
        $t=$this->safe($table); $c=$this->safe($col);
        $rs=$this->db->query(
            "SELECT 1 FROM information_schema.columns
             WHERE table_schema='".DB_NAME."'
               AND table_name='{$t}' AND column_name='{$c}' LIMIT 1"
        );
        return (is_array($rs) && count($rs)>0);
    }

    /* =============== Người dùng =============== */
    public function getUserByMa($maNguoiDung){
        $ma = $this->safe($maNguoiDung);
        $rs = $this->db->query(
            "SELECT maNguoiDung, hoTen, vaiTro, tenXuong, trangThai
             FROM nguoidung WHERE maNguoiDung='{$ma}' LIMIT 1"
        );
        return (is_array($rs) && count($rs)) ? $rs[0] : null;
    }
    public function findXuongTruongByXuongName($tenXuong){
        $tx = $this->safe($tenXuong);
        $rs = $this->db->query(
            "SELECT maNguoiDung, hoTen, vaiTro, tenXuong
             FROM nguoidung
             WHERE vaiTro='XuongTruong' AND tenXuong='{$tx}' AND trangThai='HoatDong'
             LIMIT 1"
        );
        return (is_array($rs) && count($rs)) ? $rs[0] : null;
    }

    /* =============== Danh sách kế hoạch =============== */
    public function all($q=''){
        $where='';
        if($q!==''){
            $s=$this->safe($q);
            $where="WHERE k.maKeHoach LIKE '%{$s}%' OR k.maXuong LIKE '%{$s}%'
                    OR IFNULL(x.tenXuong,'') LIKE '%{$s}%'
                    OR IFNULL(d.tenSP,'')   LIKE '%{$s}%'";
        }
        $sql="SELECT 
                k.maKeHoach   AS MaKH,
                IFNULL(d.tenSP,'') AS TenKH,
                k.maXuong     AS MaXuong,
                IFNULL(x.tenXuong,'') AS TenXuong,
                k.ngayBatDau  AS NgayBatDau,
                k.ngayKetThuc AS NgayKetThuc,
                k.trangThai   AS TrangThai
              FROM kehoachsanxuat k
              LEFT JOIN xuong   x ON x.maXuong=k.maXuong
              LEFT JOIN donhang d ON d.maDonHang=k.maDonHang
              $where
              ORDER BY k.ngayBatDau DESC";
        return $this->resultOrEmpty($this->db->query($sql));
    }

    /* =============== Lấy chi tiết 1 kế hoạch =============== */
    public function getKeHoachById($maKH){
        $ma=$this->safe($maKH);
        $sql="SELECT 
                k.maKeHoach AS MaKH,
                IFNULL(d.tenSP,'') AS TenKH,
                k.maXuong AS MaXuong, IFNULL(x.tenXuong,'') AS TenXuong,
                k.ngayBatDau AS NgayBatDau, k.ngayKetThuc AS NgayKetThuc,
                k.maNguyenLieu AS MaNguyenLieu, k.tenNguyenLieu AS TenNguyenLieu,
                k.soLuongNguyenLieu AS SoLuongNL,
                IFNULL(n.donViTinh,'') AS DonViTinh
              FROM kehoachsanxuat k
              LEFT JOIN xuong x    ON x.maXuong=k.maXuong
              LEFT JOIN donhang d  ON d.maDonHang=k.maDonHang
              LEFT JOIN nguyenlieu n ON n.maNguyenLieu=k.maNguyenLieu
              WHERE k.maKeHoach='{$ma}' LIMIT 1";
        $rs=$this->db->query($sql);
        return (is_array($rs) && count($rs)) ? $rs[0] : null;
    }

    /* ========= SINH MÃ: dùng một nguồn sự thật cho preview & save ========= */

    // Lấy số thứ tự tiếp theo trong ngày (max suffix + 1)
    private function nextSeqForToday() {
        $todayYmd = date('ymd'); // yyMMdd
        $rs = $this->db->query(
            "SELECT MAX(CAST(RIGHT(maPhieu,3) AS UNSIGNED)) AS maxseq
             FROM {$this->TBL_HDR}
             WHERE maPhieu LIKE 'P{$todayYmd}%'"
        );
        $max = ($rs && isset($rs[0]['maxseq'])) ? (int)$rs[0]['maxseq'] : 0;
        return $max + 1;
    }

    // Hiển thị mã dự kiến trên form – sẽ khớp với lúc lưu (trừ khi có chèn đồng thời)
    public function previewNextCode(){
        $todayYmd = date('ymd');
        $seq = $this->nextSeqForToday();
        return 'P'.$todayYmd.str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    // Sinh mã khi lưu – nếu có trùng do race condition thì cộng tiếp
    private function newMaPhieu(){
        $todayYmd = date('ymd');
        $seq = $this->nextSeqForToday(); // cùng logic với preview

        for ($i=0; $i<50; $i++) { // tránh vòng lặp vô hạn
            $code = 'P'.$todayYmd.str_pad($seq, 3, '0', STR_PAD_LEFT);
            $rs = $this->db->query(
                "SELECT maPhieu FROM {$this->TBL_HDR}
                 WHERE maPhieu='{$code}' LIMIT 1"
            );
            if (!$rs || count($rs)==0) return $code; // chưa trùng
            $seq++; // có trùng → tăng tiếp
        }
        $this->lastError = 'Không thể tạo mã phiếu (xung đột đồng thời).';
        return '';
    }

    /* =============== Tạo phiếu (header + detail) =============== */
    public function create($data){
        // 1) Sinh mã
        $maPhieu = $this->newMaPhieu();
        if ($maPhieu==='') return '';

        // 2) Input
        $maKH       = isset($data['maKeHoach']) ? $this->safe($data['maKeHoach']) : '';
        $ngayLap    = isset($data['ngayLap'])    ? $this->safe($data['ngayLap'])   : date('Y-m-d');
        $ghiChu     = isset($data['ghiChu'])     ? $this->safe($data['ghiChu'])    : '';
        $maNguoiLap = isset($data['maNguoiLap']) ? $this->safe($data['maNguoiLap']): '';
        $nguoiLap   = isset($data['nguoiLap'])   ? $this->safe($data['nguoiLap'])  : '';

        // 3) Lấy mã xưởng từ kế hoạch
        $maXuong = '';
        $kh = null;
        if ($maKH!==''){
            $kh = $this->getKeHoachById($maKH);
            if($kh && isset($kh['MaXuong'])) $maXuong = $this->safe($kh['MaXuong']);
        }

        // 4) Tổng SL
        $tongSL = 0;
        if ($kh && isset($kh['SoLuongNL'])) $tongSL = (int)$kh['SoLuongNL'];
        if ($tongSL === 0 && isset($data['item']) && is_array($data['item'])) {
            foreach ($data['item'] as $it){
                $tongSL += isset($it['sl']) ? max(0,(int)$it['sl']) : 0;
            }
        }

        // 4b) Lấy mã nguyên liệu chính (nếu có) để lưu vào header nếu bảng có cột
        $maNguyenLieu = '';
        if ($kh && isset($kh['MaNguyenLieu']) && $kh['MaNguyenLieu']!=='') {
            $maNguyenLieu = $this->safe($kh['MaNguyenLieu']);
        } elseif (isset($data['item']) && is_array($data['item']) && count($data['item'])>0) {
            $first = $data['item'][0];
            if (isset($first['ma']) && trim($first['ma'])!=='') $maNguyenLieu = $this->safe(trim($first['ma']));
        }

        // 5) Kiểm tra người lập
        if ($maNguoiLap!=='') {
            $u = $this->getUserByMa($maNguoiLap);
            if (!$u){ $this->lastError='Không tìm thấy người lập.'; return ''; }
            if ($u['vaiTro']!=='XuongTruong'){ $this->lastError='Chỉ Xưởng Trưởng mới được lập phiếu.'; return ''; }
            $nguoiLap = $this->safe($u['hoTen']);
        } else {
            if ($kh && !empty($kh['TenXuong'])){
                $u = $this->findXuongTruongByXuongName($kh['TenXuong']);
                if ($u){ $maNguoiLap=$this->safe($u['maNguoiDung']); $nguoiLap=$this->safe($u['hoTen']); }
            }
            if ($maNguoiLap===''){ $this->lastError='Thiếu người lập (Xưởng Trưởng).'; return ''; }
        }

        // 6) Insert header
        $cols = array('maPhieu','maKeHoach','ghiChu','soLuong','maXuong','ngayLap','trangThai');
        $vals = array("'{$maPhieu}'","'{$maKH}'","'{$ghiChu}'",$tongSL,"'{$maXuong}'","'{$ngayLap}'","'ChoDuyet'");
        if ($this->columnExists($this->TBL_HDR,'maNguyenLieu')) { $cols[]='maNguyenLieu'; $vals[]="'{$maNguyenLieu}'"; }
        if ($this->columnExists($this->TBL_HDR,'maNguoiLap')) { $cols[]='maNguoiLap'; $vals[]="'{$maNguoiLap}'"; }
        if ($this->columnExists($this->TBL_HDR,'nguoiLap'))   { $cols[]='nguoiLap';   $vals[]="'{$nguoiLap}'";   }

        $sqlH = "INSERT INTO {$this->TBL_HDR} (".implode(',',$cols).") VALUES (".implode(',',$vals).")";
        if (!$this->dbWrite($sqlH)) return '';

        // 7) Insert detail
        if($this->TBL_DTL!=='' && isset($data['item']) && is_array($data['item'])){
            foreach ($data['item'] as $it){
                $ma  = isset($it['ma'])  ? $this->safe(trim($it['ma']))  : '';
                $ten = isset($it['ten']) ? $this->safe(trim($it['ten'])) : '';
                $dv  = isset($it['dv'])  ? $this->safe(trim($it['dv']))  : '';
                $sl  = isset($it['sl'])  ? (int)$it['sl'] : 0;
                if($ma==='' && $ten==='') continue;

                $sqlD = "INSERT INTO {$this->TBL_DTL}(maPhieu,maNguyenLieu,tenNguyenLieu,donViTinh,soLuong)
                         VALUES ('{$maPhieu}','{$ma}','{$ten}','{$dv}',{$sl})";
                if (!$this->dbWrite($sqlD)) {
                    // fallback nếu không có cột đơn vị
                    $sqlD2 = "INSERT INTO {$this->TBL_DTL}(maPhieu,maNguyenLieu,tenNguyenLieu,soLuong)
                              VALUES ('{$maPhieu}','{$ma}','{$ten}',{$sl})";
                    $this->dbWrite($sqlD2);
                }
            }
        }

        return $maPhieu;
    }
}
