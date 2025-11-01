<?php
require_once dirname(__FILE__).'/../models/database.php';
require_once dirname(__FILE__).'/../models/phieuYeuCau.php';
require_once dirname(__FILE__).'/../models/PhieuSuaChua.php';
require_once dirname(__FILE__).'/../models/PhieuKTTP.php';

class PhieuController {
    protected $db;
    protected $phieu;
    protected $scModel;
    protected $modelKTTP;

    public function __construct($db = null) {
        if ($db) $this->db = $db; else $this->db = new Database();
        $this->phieu     = new PhieuYeuCau($this->db);
        $this->scModel   = new PhieuSuaChua();
        $this->modelKTTP = new PhieuKTTP();
        if (session_id() === '') @session_start();
    }

    /* ================== YÊU CẦU NGUYÊN LIỆU (giữ nguyên) ================== */
    public function index() {
        $title = 'Danh sách yêu cầu nhận nguyên liệu';
        $notice_ok   = (isset($_GET['ok']) && $_GET['ok'] === '1');
        $notice_code = isset($_GET['code']) ? trim($_GET['code']) : '';
        $q    = isset($_GET['q']) ? trim($_GET['q']) : '';
        $list = $this->phieu->all($q);
        include 'app/views/phieu/yeucau_nguyenlieu_index.php';
    }

    public function yeucau_nguyenlieu() {
        $title    = 'Thông tin lập phiếu yêu cầu nhận nguyên liệu';
        $msg      = '';
        $kehoach  = null;
        $items    = array();
        $nextCode = $this->phieu->previewNextCode();

        if (!empty($_GET['id'])) {
            $kehoach = $this->phieu->getKeHoachById($_GET['id']);
            if ($kehoach) {
                $items[] = array(
                    'ma'  => isset($kehoach['MaNguyenLieu'])  ? $kehoach['MaNguyenLieu']  : '',
                    'ten' => isset($kehoach['TenNguyenLieu']) ? $kehoach['TenNguyenLieu'] : '',
                    'dv'  => isset($kehoach['DonViTinh'])     ? $kehoach['DonViTinh']     : '',
                    'sl'  => isset($kehoach['SoLuongNL'])     ? (int)$kehoach['SoLuongNL'] : 1
                );
            }
        }

        $user = array('maNguoiDung'=>'', 'hoTen'=>'--', 'vaiTro'=>'', 'tenXuong'=>'');
        if (!empty($_SESSION['user']['maNguoiDung'])) {
            $u = $this->phieu->getUserByMa($_SESSION['user']['maNguoiDung']);
            if ($u) $user = $u;
        }
        if ($user['maNguoiDung']==='' && $kehoach && !empty($kehoach['TenXuong'])) {
            $u = $this->phieu->findXuongTruongByXuongName($kehoach['TenXuong']);
            if ($u) $user = $u;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST['maNguoiLap'] = isset($user['maNguoiDung']) ? $user['maNguoiDung'] : '';
            $_POST['nguoiLap']   = isset($user['hoTen'])       ? $user['hoTen']       : '';
            $maPhieu = $this->phieu->create($_POST);
            if ($maPhieu !== '') {
                $this->redirect('index.php?controller=phieu&action=index&ok=1&code='.urlencode($maPhieu));
                return;
            } else {
                $msg = '❌ Lập phiếu thất bại. '.htmlspecialchars($this->phieu->getLastError(),ENT_QUOTES,'UTF-8');
            }
        }

        include 'app/views/phieu/yeucau_nguyenlieu.php';
    }

    /* ================== SỬA CHỮA (giữ nguyên) ================== */
    public function suachua() {
        $title  = 'Phiếu bảo trì & sửa chữa';
        $phieus = $this->scModel->getAll();
        include 'app/views/phieu/suachua_index.php';
    }
    public function add_suachua() {
        $title     = 'Lập phiếu yêu cầu sửa chữa';
        $thietbis  = $this->scModel->getAllThietBi();
        $nhanviens = $this->scModel->getAllNguoiDung();
        include 'app/views/phieu/suachua_add.php';
    }
    public function save_suachua() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('index.php?controller=phieu&action=suachua'); return; }
        $maPhieu    = isset($_POST['maPhieu'])    ? trim($_POST['maPhieu'])    : '';
        $maTB       = isset($_POST['maTB'])       ? trim($_POST['maTB'])       : '';
        $moTaSuCo   = isset($_POST['moTaSuCo'])   ? trim($_POST['moTaSuCo'])   : '';
        $ngayLap    = isset($_POST['ngayLap'])    ? trim($_POST['ngayLap'])    : date('Y-m-d');
        $trangThai  = isset($_POST['trangThai'])  ? trim($_POST['trangThai'])  : 'Chờ xử lý';
        $maNguoiLap = isset($_POST['maNguoiLap']) ? trim($_POST['maNguoiLap']) : '';
        if ($maPhieu === '') $maPhieu = $this->scModel->generateNextMa();
        $ok = $this->scModel->add($maPhieu, $maTB, $moTaSuCo, $ngayLap, $trangThai, $maNguoiLap);
        $this->redirect('index.php?controller=phieu&action=suachua'.($ok?'':'&error=1'));
    }
    public function delete_suachua() {
        if (!empty($_GET['id'])) $this->scModel->delete($_GET['id']);
        $this->redirect('index.php?controller=phieu&action=suachua');
    }

    /* ================== PHIẾU KIỂM TRA THÀNH PHẨM ================== */
    public function kttp() {
        $thanhPhams = $this->modelKTTP->getThanhPhamChoKiemTra();
        $maPhieu    = $this->modelKTTP->getNextMaPhieu();

        // QC mặc định để test (không cần login)
        $nguoiQC = 'ND005';
        $hoTenQC = 'Hoàng Mỹ QC';

        include 'app/views/phieu/kiemtra_TP.php';
    }

    public function create_kttp() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=phieu&action=kttp'); return;
        }

        // QC mặc định (không cần login)
        $maQC = 'ND005';

        $data = array(
            'maPhieu'      => isset($_POST['maPhieu']) ? trim($_POST['maPhieu']) : '',
            'maTP'         => isset($_POST['maTP']) ? trim($_POST['maTP']) : '',
            'SL_KiemTra'   => isset($_POST['SL_KiemTra']) ? (int)$_POST['SL_KiemTra'] : 0,
            'SL_DatChuan'  => isset($_POST['SL_DatChuan']) ? (int)$_POST['SL_DatChuan'] : 0,
            'ketQua'       => isset($_POST['ketQua']) ? trim($_POST['ketQua']) : '',
            'ngayLap'      => isset($_POST['ngayLap']) ? trim($_POST['ngayLap']) : date('Y-m-d'),
            'maNhanVienQC' => $maQC
        );

        if ($data['maTP'] === '' || $data['maPhieu'] === '') {
            $this->redirect('index.php?controller=phieu&action=kttp&error=1'); return;
        }
        if ($data['SL_DatChuan'] > $data['SL_KiemTra']) {
            $this->redirect('index.php?controller=phieu&action=kttp&error=1'); return;
        }

        $ok = $this->modelKTTP->addPhieuKT($data);
        $this->redirect('index.php?controller=phieu&action=kttp'.($ok?'&success=1':'&error=1'));
    }

    // AJAX: trả về số lượng theo mã TP
    public function getSL() {
        while (ob_get_level() > 0) { @ob_end_clean(); }
        header('Content-Type: text/plain; charset=utf-8');
        $sl = 0;
        if (isset($_POST['maTP'])) $sl = (int)$this->modelKTTP->getSLTheoTP($_POST['maTP']);
        echo $sl;
        flush();
        exit;
    }

    private function redirect($url) {
        if (!headers_sent()) { header('Location: '.$url); exit; }
        echo '<script>location.href='.json_encode($url).';</script>';
        echo '<noscript><meta http-equiv="refresh" content="0;url='.htmlspecialchars($url,ENT_QUOTES,'UTF-8').'"></noscript>';
        exit;
    }
/* ================== PHIẾU NHẬP KHO THÀNH PHẨM ================== */
public function pnk_index() {
    require_once dirname(__FILE__) . '/../models/PhieuNhapKho.php';
    $modelPNK = new PhieuNhapKho($this->db);

    $title = 'Danh sách Phiếu Nhập Kho Thành Phẩm';
    $dsPhieu = $modelPNK->getAllPhieu();

    include 'app/views/phieu/danhSachPhieu.php';
}

public function pnk_taoPhieu() {
    require_once dirname(__FILE__) . '/../models/PhieuNhapKho.php';
    $modelPNK = new PhieuNhapKho($this->db);

    $title = 'Lập Phiếu Nhập Kho Thành Phẩm';
    $maPhieu = $modelPNK->getNextMaPhieu();
    $dsThanhPham = $modelPNK->getAllThanhPham();
    $dsKho = $modelPNK->getAllKho();

    include 'app/views/phieu/PhieuNhapKho.php';
}

public function pnk_luuPhieu() {
    require_once dirname(__FILE__) . '/../models/PhieuNhapKho.php';
    $modelPNK = new PhieuNhapKho($this->db);

    // Kiểm tra request hợp lệ
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?controller=phieu&action=pnk_index');
        exit;
    }

    // Lấy dữ liệu từ form
    $maPhieu     = isset($_POST['maPhieu']) ? trim($_POST['maPhieu']) : '';
    $maTP        = isset($_POST['maTP']) ? trim($_POST['maTP']) : '';
    $tenTP       = isset($_POST['tenTP']) ? trim($_POST['tenTP']) : '';
    $maKho       = isset($_POST['maKho']) ? trim($_POST['maKho']) : 'K002';
    $soLuong     = isset($_POST['soLuong']) ? intval($_POST['soLuong']) : 0;
    $maNguoiLap  = isset($_POST['maNguoiLap']) ? trim($_POST['maNguoiLap']) : 'ND004';
    $ngayNhap    = date('Y-m-d');

    // Kiểm tra dữ liệu hợp lệ
    if ($maPhieu == '' || $maTP == '' || $soLuong <= 0) {
        header('Location: index.php?controller=phieu&action=pnk_taoPhieu&error=1');
        exit;
    }

    // Chuẩn bị dữ liệu
    $data = array(
        'maPhieu'    => $maPhieu,
        'maTP'       => $maTP,
       
        'maKho'      => $maKho,
        'soLuong'    => $soLuong,
        'ngayNhap'   => $ngayNhap,
        'maNguoiLap' => $maNguoiLap,
        'trangThai'  => 'Đã nhập'
    );

    // Gọi model để lưu
    $ok = $modelPNK->create($data);

    if ($ok) {
        header('Location: index.php?controller=phieu&action=pnk_index&ok=1');
        exit;
    } else {
        header('Location: index.php?controller=phieu&action=pnk_taoPhieu&error=2');
        exit;
    }
}




}
