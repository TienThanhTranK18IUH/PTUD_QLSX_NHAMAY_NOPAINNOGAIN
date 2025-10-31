<?php
// Model: PhanCongCongViecSanXuat.php — PHP 5.x

class PhanCongCongViecSanXuat {
    /** @var Database */
    protected $db;

    public function __construct($db) { $this->db = $db; }

    /* ---------------- Helpers ---------------- */
    private function tableExists($name){
        $name = addslashes($name);
        $rows = $this->db->query("SELECT 1 FROM information_schema.tables
                                  WHERE table_schema='".DB_NAME."' AND table_name='{$name}' LIMIT 1");
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
}
?>
