<?php declare(strict_types=1); 
// Model: PhanCongCongViecSanXuat.php — PHP 5.x

class PhanCongCongViecSanXuat {
    /** @var Database */
    protected $db;

    public function __construct($db) { $this->db = $db; }

    /* ---------------- Helpers ---------------- */
    private function tableExists($name){
        $name = addslashes($name);
        $rows = $this->db->query(
            "SELECT 1 FROM information_schema.tables
             WHERE table_schema='".DB_NAME."' AND table_name='{$name}' LIMIT 1"
        );
        return !empty($rows);
    }
    private function pickTable($candidates){
        foreach ($candidates as $t) if ($this->tableExists($t)) return $t;
        return '';
    }

    /* --------- 1) Lấy 1 quản lý đang hoạt động --------- */
    public function layQuanLyHoatDong() {
        $tbl = $this->pickTable(array('nguoidung','NguoiDung','nguoi_dung'));
        if ($tbl==='') return null;

        $rows = $this->db->query("
            SELECT * FROM {$tbl}
            WHERE (vaiTro='QuanLy' OR VaiTro='QuanLy')
              AND (trangThai='HoatDong' OR TrangThai='HoatDong' OR TrangThai IS NULL)
            ORDER BY 1 LIMIT 1
        ");
        if (empty($rows)) return null;

        $r = $rows[0];
        return array(
            'maNguoiDung' => isset($r['maNguoiDung']) ? $r['maNguoiDung'] : (isset($r['MaNguoiDung'])?$r['MaNguoiDung']:''),
            'hoTen'       => isset($r['hoTen']) ? $r['hoTen'] : (isset($r['HoTen'])?$r['HoTen']:''),
            'vaiTro'      => isset($r['vaiTro']) ? $r['vaiTro'] : (isset($r['VaiTro'])?$r['VaiTro']:''),
            'tenXuong'    => isset($r['tenXuong']) ? $r['tenXuong'] : (isset($r['TenXuong'])?$r['TenXuong']:'')
        );
    }

    /* --------- 2) Danh sách kế hoạch (chuẩn hoá keys) --------- */
    public function layDanhSachKeHoach() {
        $tbl = $this->pickTable(array('kehoachsanxuat','KeHoachSanXuat','kehoach_sanxuat'));
        if ($tbl==='') return array();

        $rows = $this->db->query("SELECT * FROM {$tbl} ORDER BY 1 DESC");
        $out = array();
        foreach ($rows as $r) {
            $item = array(
                'maKeHoach'   => isset($r['maKeHoach'])   ? $r['maKeHoach']   : (isset($r['MaKeHoach'])?$r['MaKeHoach']:''),
                'maXuong'     => isset($r['maXuong'])     ? $r['maXuong']     : (isset($r['MaXuong'])?$r['MaXuong']:''),
                'ngayBatDau'  => isset($r['ngayBatDau'])  ? $r['ngayBatDau']  : (isset($r['NgayBatDau'])?$r['NgayBatDau']:''),
                'ngayKetThuc' => isset($r['ngayKetThuc']) ? $r['ngayKetThuc'] : (isset($r['NgayKetThuc'])?$r['NgayKetThuc']:''),
                'tongSoLuong' => isset($r['tongSoLuong']) ? $r['tongSoLuong'] :
                                 (isset($r['TongSoLuong'])?$r['TongSoLuong'] :
                                 (isset($r['soLuongNguyenLieu'])?$r['soLuongNguyenLieu'] :
                                 (isset($r['SoLuongNguyenLieu'])?$r['SoLuongNguyenLieu']:0)))
            );
            if ($item['maKeHoach']!=='') $out[] = $item;
        }
        return $out;
    }

    /* --------- 3) Danh sách ca làm việc --------- */
    public function layDanhSachCa() {
        $tbl = $this->pickTable(array('calamviec','CaLamViec','ca_lam_viec'));
        if ($tbl==='') return array();

        $rows = $this->db->query("SELECT * FROM {$tbl} ORDER BY 1");
        $out = array();
        foreach ($rows as $r) {
            $bd = isset($r['gioBatDau']) ? $r['gioBatDau'] :
                  (isset($r['thoiGianBatDau']) ? $r['thoiGianBatDau'] :
                  (isset($r['GioBatDau']) ? $r['GioBatDau'] : (isset($r['ThoiGianBatDau'])?$r['ThoiGianBatDau']:'')));
            $kt = isset($r['gioKetThuc']) ? $r['gioKetThuc'] :
                  (isset($r['thoiGianKetThuc']) ? $r['thoiGianKetThuc'] :
                  (isset($r['GioKetThuc']) ? $r['GioKetThuc'] : (isset($r['ThoiGianKetThuc'])?$r['ThoiGianKetThuc']:'')));
            $ma = isset($r['maCa']) ? $r['maCa'] : (isset($r['MaCa'])?$r['MaCa']:(isset($r['ma_ca'])?$r['ma_ca']:''));
            $ten= isset($r['tenCa'])? $r['tenCa']: (isset($r['TenCa'])?$r['TenCa']:(isset($r['ten_ca'])?$r['ten_ca']:''));

            if ($ma!=='') $out[] = array('maCa'=>$ma,'tenCa'=>$ten,'gioBatDau'=>$bd,'gioKetThuc'=>$kt);
        }
        return $out;
    }

    /* --------- 4) Lưu phân công --------- */
    public function luuPhanCong($maNguoiDung, $maCa, $maXuong, $moTaCongViec, $soLuong, $ngayBatDau, $ngayKetThuc) {
        $tbl = $this->pickTable(array('phancong','PhanCong','phan_cong'));
        if ($tbl==='') $tbl = 'phancong';

        $maNguoiDung  = addslashes($maNguoiDung);
        $maCa         = addslashes($maCa);
        $maXuong      = addslashes($maXuong);
        $moTaCongViec = addslashes($moTaCongViec);
        $soLuong      = (int)$soLuong;
        $ngayBatDau   = addslashes($ngayBatDau);
        $ngayKetThuc  = addslashes($ngayKetThuc);

        $this->db->query("INSERT INTO {$tbl}
            (maNguoiDung, maCa, maXuong, moTaCongViec, soLuong, ngayBatDau, ngayKetThuc)
            VALUES ('{$maNguoiDung}','{$maCa}','{$maXuong}','{$moTaCongViec}',{$soLuong},'{$ngayBatDau}','{$ngayKetThuc}')");
        return true;
    }

    /* --------- 4b) Lấy ngày phân công gần nhất của 1 NV --------- */
    public function getLastAssignedDay($maNguoiDung){
        $tbl = $this->pickTable(array('phancong','PhanCong','phan_cong'));
        if ($tbl==='') $tbl = 'phancong';
        $nv = addslashes($maNguoiDung);
        $rows = $this->db->query("
            SELECT GREATEST(IFNULL(MAX(ngayBatDau),'0000-00-00'),
                            IFNULL(MAX(ngayKetThuc),'0000-00-00')) AS lastDay
            FROM {$tbl}
            WHERE maNguoiDung='{$nv}'
        ");
        if (!empty($rows) && isset($rows[0]['lastDay']) && $rows[0]['lastDay']!='0000-00-00') {
            return $rows[0]['lastDay'];
        }
        return '';
    }

        public function layDanhSachBoPhan() {
        $tbl = $this->pickTable(array('bophan','BoPhan','bo_phan'));
        if ($tbl==='') return array();

        $rows = $this->db->query("SELECT * FROM {$tbl} ORDER BY maBoPhan ASC");
        $out = array();
        foreach ($rows as $r) {
            $out[] = array(
                'maBoPhan' => isset($r['maBoPhan']) ? $r['maBoPhan'] : '',
                'tenBoPhan'=> isset($r['tenBoPhan']) ? $r['tenBoPhan'] : '',
                'maXuong'  => isset($r['maXuong']) ? $r['maXuong'] : ''
            );
        }
        return $out;
    }

    /* --------- 5) Lịch làm theo khoảng ngày (expand từng ngày) --------- */
    public function layLichLamTheoKhoang($maNguoiDung, $fromYmd, $toYmd) {
        $tblPC = $this->pickTable(array('phancong','PhanCong','phan_cong'));
        if ($tblPC==='') $tblPC = 'phancong';
        $tblCa = $this->pickTable(array('calamviec','CaLamViec','ca_lam_viec'));
        $tblX  = $this->pickTable(array('xuong','Xuong','xuong_sx'));

        $maNguoiDung = addslashes($maNguoiDung);
        $fromYmd     = addslashes($fromYmd);
        $toYmd       = addslashes($toYmd);

        $sql = "
            SELECT
                p.*
                ".($tblCa ? ", c.*" : "")."
                ".($tblX  ? ", x.*" : "")."
            FROM {$tblPC} p
            ".($tblCa ? "LEFT JOIN {$tblCa} c ON c.maCa = p.maCa " : "")."
            ".($tblX  ? "LEFT JOIN {$tblX}  x ON x.maXuong = p.maXuong " : "")."
            WHERE p.maNguoiDung = '{$maNguoiDung}'
              AND p.ngayBatDau <= '{$toYmd}'
              AND p.ngayKetThuc >= '{$fromYmd}'
            ORDER BY p.ngayBatDau ASC, p.maPhanCong ASC
        ";
        $rows = $this->db->query($sql);
        if (empty($rows)) return array();

        $out = array();
        foreach ($rows as $r) {
            $nbd = isset($r['ngayBatDau'])  ? $r['ngayBatDau']  : (isset($r['NgayBatDau']) ? $r['NgayBatDau']  : '');
            $nkt = isset($r['ngayKetThuc']) ? $r['ngayKetThuc'] : (isset($r['NgayKetThuc'])? $r['NgayKetThuc'] : '');
            if ($nbd==='' || $nkt==='') continue;

            $start = max(strtotime($fromYmd), strtotime($nbd));
            $end   = min(strtotime($toYmd),   strtotime($nkt));
            if ($start > $end) continue;

            $maCa = isset($r['maCa']) ? $r['maCa'] : (isset($r['MaCa']) ? $r['MaCa'] : '');

            $gioBD = isset($r['gioBatDau'])       ? $r['gioBatDau']       :
                     (isset($r['thoiGianBatDau']) ? $r['thoiGianBatDau'] :
                     (isset($r['GioBatDau'])      ? $r['GioBatDau']      :
                     (isset($r['ThoiGianBatDau']) ? $r['ThoiGianBatDau'] : '')));

            $gioKT = isset($r['gioKetThuc'])       ? $r['gioKetThuc']       :
                     (isset($r['thoiGianKetThuc']) ? $r['thoiGianKetThuc'] :
                     (isset($r['GioKetThuc'])      ? $r['GioKetThuc']      :
                     (isset($r['ThoiGianKetThuc']) ? $r['ThoiGianKetThuc'] : '')));

            for ($d = $start; $d <= $end; $d += 86400) {
                $out[] = array(
                    'ngay'         => date('Y-m-d', $d),
                    'maCa'         => $maCa,
                    'ngayBatDau'   => $nbd,
                    'ngayKetThuc'  => $nkt,
                    'gioBD'        => $gioBD,
                    'gioKT'        => $gioKT
                );
            }
        }

        // Sắp xếp theo ngày + ca (PHP 5.x)
        usort($out, array($this, '_cmpNgayCa'));
        return $out;
    }

    private function _cmpNgayCa($a, $b) {
        $k1 = $a['ngay'].' '.$a['maCa'];
        $k2 = $b['ngay'].' '.$b['maCa'];
        if ($k1 == $k2) return 0;
        return ($k1 < $k2) ? -1 : 1;
    }
}
?>
