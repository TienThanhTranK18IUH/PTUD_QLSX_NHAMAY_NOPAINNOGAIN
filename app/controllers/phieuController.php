<?php
require_once dirname(__FILE__).'/../models/database.php';
require_once dirname(__FILE__).'/../models/phieuYeuCau.php';
require_once dirname(__FILE__).'/../models/PhieuSuaChua.php';
require_once dirname(__FILE__).'/../models/PhieuKTTP.php';
require_once dirname(__FILE__) . '/../models/PhieuNhapKho.php';
require_once dirname(__FILE__) . '/../helpers/auth.php';

class PhieuController {

    protected $db;
    protected $phieu;
    protected $scModel;
    protected $modelKTTP;

    // duy nhất 1 redirect() ở đây
    protected function redirect($url) {
        if (!ob_get_level()) ob_start();

        if (!headers_sent()) {
            header('Location: ' . $url);
            exit;
        }

        echo '<script type="text/javascript">';
        echo 'window.location.href=' . json_encode($url) . ';';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url=' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">';
        echo '</noscript>';
        exit;
    }

    public function __construct($db = null) {
        if ($db) $this->db = $db; else $this->db = new Database();
        $this->phieu     = new PhieuYeuCau($this->db);
        $this->scModel   = new PhieuSuaChua();
        $this->modelKTTP = new PhieuKTTP();
        if (session_id() === '') @session_start();
    }

    /* ================== YÊU CẦU NGUYÊN LIỆU ================== */
    public function index() {
        requireRole(array('manager','leader'));
        $title = 'Danh sách yêu cầu nhận nguyên liệu';
        $notice_ok   = (isset($_GET['ok']) && $_GET['ok'] === '1');
        $notice_code = isset($_GET['code']) ? trim($_GET['code']) : '';
        $q    = isset($_GET['q']) ? trim($_GET['q']) : '';
        $list = $this->phieu->all($q);
        include 'app/views/phieu/yeucau_nguyenlieu_index.php';
    }

    public function yeucau_nguyenlieu() {
        requireRole(array('manager','leader'));
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

    /* ================== SỬA CHỮA ================== */
    public function suachua() {
        requireRole(array('manager','leader'));
        $title  = 'Phiếu bảo trì & sửa chữa';
        $phieus = $this->scModel->getAll();
        include 'app/views/phieu/suachua_index.php';
    }

    public function add_suachua() {
        requireRole(array('manager','leader'));
        $title     = 'Lập phiếu yêu cầu sửa chữa';
        $thietbis  = $this->scModel->getAllThietBi();
        $nhanviens = $this->scModel->getAllNguoiDung();
        include 'app/views/phieu/suachua_add.php';
    }

    public function save_suachua() {
        requireRole(array('manager','leader'));
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=phieu&action=suachua');
            return;
        }
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
        if (session_id() === '') session_start();

        $thanhPhams = $this->modelKTTP->getThanhPhamChoKiemTra();
        $maPhieu    = $this->modelKTTP->getNextMaPhieu();
        $hoTenQC    = $_SESSION['user']['hoTen'];
        $nguoiQC    = $_SESSION['user']['maNguoiDung'];

        include 'app/views/phieu/kiemtra_TP.php';
    }

    public function create_kttp() {

        if (session_id() === '') session_start();

        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?controller=phieu&action=kttp");
            exit;
        }

        $maQC = $_SESSION['user']['maNguoiDung'];

        // 🔽 ĐÃ SỬA: nhận thêm ghiChu
        $data = array(
            'maPhieu'      => isset($_POST['maPhieu']) ? trim($_POST['maPhieu']) : '',
            'maTP'         => isset($_POST['maTP']) ? trim($_POST['maTP']) : '',
            'SL_KiemTra'   => isset($_POST['SL_KiemTra']) ? (int)$_POST['SL_KiemTra'] : 0,
            'SL_DatChuan'  => isset($_POST['SL_DatChuan']) ? (int)$_POST['SL_DatChuan'] : 0,
            'ketQua'       => isset($_POST['ketQua']) ? trim($_POST['ketQua']) : '',
            'ghiChu'       => isset($_POST['ghiChu']) ? trim($_POST['ghiChu']) : null,
            'ngayLap'      => isset($_POST['ngayLap']) ? trim($_POST['ngayLap']) : date('Y-m-d'),
            'maNhanVienQC' => $maQC
        );

        // ❌ Kiểm tra dữ liệu bắt buộc
        if (
            $data['maPhieu'] === '' ||
            $data['maTP'] === '' ||
            $data['ketQua'] === '' ||
            $data['SL_DatChuan'] > $data['SL_KiemTra']
        ) {
            header("Location: index.php?controller=phieu&action=kttp&error=1");
            exit;
        }

        // ❌ Không đạt thì BẮT BUỘC có ghi chú
        if ($data['ketQua'] === 'Không đạt' && empty($data['ghiChu'])) {
            header("Location: index.php?controller=phieu&action=kttp&error=1");
            exit;
        }

        $ok = $this->modelKTTP->addPhieuKT($data);

        header("Location: index.php?controller=phieu&action=kttp".($ok ? "&success=1" : "&error=1"));
        exit;
    }

    /* ================== AJAX ================== */

    public function getSL() {
        if (session_id() === '') session_start();

        while (ob_get_level() > 0) { @ob_end_clean(); }

        header('Content-Type: text/plain; charset=utf-8');

        $sl = 0;
        if (isset($_POST['maTP'])) {
            $sl = (int)$this->modelKTTP->getSLTheoTP($_POST['maTP']);
        }

        echo $sl;
        exit;
    }

    /* ================== PHIẾU NHẬP KHO THÀNH PHẨM ================== */
    public function pnk_index() {
        $modelPNK = new PhieuNhapKho($this->db);
        $title = 'Danh sách Phiếu Nhập Kho Thành Phẩm';
        $dsPhieu = $modelPNK->getAllPhieu();
        include 'app/views/phieu/danhSachPhieu.php';
    }

    public function pnk_taoPhieu() {
    $modelPNK = new PhieuNhapKho($this->db);
    $title = 'Lập Phiếu Nhập Kho Thành Phẩm';

    $maPhieu = $modelPNK->getNextMaPhieu();

    // ====== XƯỞNG (chỉ X001 | X002) ======
    $maXuong = '';
    if (isset($_GET['maXuong']) && ($_GET['maXuong'] == 'X001' || $_GET['maXuong'] == 'X002')) {
        $maXuong = $_GET['maXuong'];
    }

    // ====== LẤY TP THEO XƯỞNG ======
    $dsThanhPham = array();
    if ($maXuong != '') {
        $allTP = $modelPNK->getAllThanhPhamByXuong($maXuong);

        foreach ($allTP as $tp) {
            if (!$modelPNK->checkExistTP($tp['maTP'])) {
                $dsThanhPham[] = $tp;
            }
        }
    }

    // ====== KHO ======
    $dsKho = $modelPNK->getAllKho();

    // ====== USER ======
    $nguoiLap = '--';
    $maNguoiLap = '';
    if (!empty($_SESSION['user'])) {
        $nguoiLap   = $_SESSION['user']['hoTen'];
        $maNguoiLap = $_SESSION['user']['maNguoiDung'];
    }

    include 'app/views/phieu/PhieuNhapKho.php';
}

public function pnk_luuPhieu() {
    $modelPNK = new PhieuNhapKho($this->db);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect('index.php?controller=phieu&action=pnk_index');
    }

    $maPhieu = isset($_POST['maPhieu']) ? trim($_POST['maPhieu']) : '';
    $maTP    = isset($_POST['maTP']) ? trim($_POST['maTP']) : '';
    $maKho   = isset($_POST['maKho']) ? trim($_POST['maKho']) : 'K002';
    $soLuong = isset($_POST['soLuong']) ? intval($_POST['soLuong']) : 0;

    // Lấy thông tin user hiện tại
    if (!empty($_SESSION['user'])) {
        $maNguoiLap = $_SESSION['user']['maNguoiDung'];
    } else {
        $maNguoiLap = 'ND004';
    }

    $ngayNhap = date('Y-m-d');

    if ($maPhieu == '' || $maTP == '' || $soLuong <= 0) {
        $this->redirect('index.php?controller=phieu&action=pnk_taoPhieu&error=1');
    }

    // Kiểm tra 1 thành phẩm chỉ lập 1 phiếu
    if ($modelPNK->checkExistTP($maTP)) {
        $this->redirect('index.php?controller=phieu&action=pnk_taoPhieu&error=3');
    }

    $data = array(
        'maPhieu'    => $maPhieu,
        'maTP'       => $maTP,
        'maKho'      => $maKho,
        'soLuong'    => $soLuong,
        'ngayNhap'   => $ngayNhap,
        'maNguoiLap' => $maNguoiLap,
        'trangThai'  => 'Đã nhập'
    );

    $ok = $modelPNK->create($data);

    if ($ok) {
        $this->redirect('index.php?controller=phieu&action=pnk_index&ok=1');
    } else {
        $this->redirect('index.php?controller=phieu&action=pnk_taoPhieu&error=2');
    }
}





}