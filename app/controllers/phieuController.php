<?php
class PhieuController {
    protected $db;
    protected $phieu;        // model PhieuYeuCau (đã có)
    protected $scModel;      // model PhieuSuaChua (file dưới)

    public function __construct($db) {
        $this->db = $db;

        require_once dirname(__FILE__).'/../models/phieuYeuCau.php';
        $this->phieu = new PhieuYeuCau($this->db);

        require_once dirname(__FILE__).'/../models/PhieuSuaChua.php';
        $this->scModel = new PhieuSuaChua();  // model này tự kết nối mysqli theo config.php
        if (session_id() === '') @session_start();
    }

    /* ================== YÊU CẦU NGUYÊN LIỆU ================== */

    // Danh sách kế hoạch (điểm vào chính để lập phiếu)
    public function index() {
        $title = 'Danh sách yêu cầu nhận nguyên liệu';
        $notice_ok   = (isset($_GET['ok']) && $_GET['ok'] === '1');
        $notice_code = isset($_GET['code']) ? trim($_GET['code']) : '';

        $q    = isset($_GET['q']) ? trim($_GET['q']) : '';
        $list = $this->phieu->all($q);

        include 'app/views/phieu/yeucau_nguyenlieu_index.php';
    }

    // Form lập phiếu
    public function yeucau_nguyenlieu() {
        $title    = 'Thông tin lập phiếu yêu cầu nhận nguyên liệu';
        $msg      = '';
        $kehoach  = null;
        $items    = array();
        $nextCode = $this->phieu->previewNextCode();   // hiển thị mã dự kiến

        // Lấy kế hoạch đổ form
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

        // Xác định người lập
        $user = array('maNguoiDung'=>'', 'hoTen'=>'--', 'vaiTro'=>'', 'tenXuong'=>'');
        if (!empty($_SESSION['user']['maNguoiDung'])) {
            $u = $this->phieu->getUserByMa($_SESSION['user']['maNguoiDung']);
            if ($u) $user = $u;
        }
        if ($user['maNguoiDung']==='' && $kehoach && !empty($kehoach['TenXuong'])) {
            $u = $this->phieu->findXuongTruongByXuongName($kehoach['TenXuong']);
            if ($u) $user = $u;
        }

        // Submit
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST['maNguoiLap'] = isset($user['maNguoiDung']) ? $user['maNguoiDung'] : '';
            $_POST['nguoiLap']   = isset($user['hoTen'])       ? $user['hoTen']       : '';

            $maPhieu = $this->phieu->create($_POST); // model tự sinh mã dạng PyyMMddNNN

            if ($maPhieu !== '') {
                $this->redirect('index.php?controller=phieu&action=index&ok=1&code='.urlencode($maPhieu));
                return;
            } else {
                $msg = '❌ Lập phiếu thất bại. '.htmlspecialchars($this->phieu->getLastError(),ENT_QUOTES,'UTF-8');
            }
        }

        include 'app/views/phieu/yeucau_nguyenlieu.php';
    }

    /* ================== BẢO TRÌ & SỬA CHỮA ================== */

    // Danh sách phiếu sửa chữa
    public function suachua() {
        $title  = 'Phiếu bảo trì & sửa chữa';
        $phieus = $this->scModel->getAll();
        include 'app/views/phieu/suachua_index.php';
    }

    // Form lập phiếu sửa chữa
    public function add_suachua() {
        $title     = 'Lập phiếu yêu cầu sửa chữa';
        $thietbis  = $this->scModel->getAllThietBi();
        $nhanviens = $this->scModel->getAllNguoiDung();

        include 'app/views/phieu/suachua_add.php';
    }

    // Lưu phiếu sửa chữa
    public function save_suachua() {
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

        if ($maPhieu === '') {
            // nếu bạn muốn tự sinh mã: SCyyMMddNNN
            $maPhieu = $this->scModel->generateNextMa(); // có trong model PhieuSuaChua
        }

        $ok = $this->scModel->add($maPhieu, $maTB, $moTaSuCo, $ngayLap, $trangThai, $maNguoiLap);
        $this->redirect('index.php?controller=phieu&action=suachua'.($ok?'':'&error=1'));
    }

    // Xóa phiếu sửa chữa
    public function delete_suachua() {
        if (empty($_GET['id'])) {
            $this->redirect('index.php?controller=phieu&action=suachua');
            return;
        }
        $this->scModel->delete($_GET['id']);
        $this->redirect('index.php?controller=phieu&action=suachua');
    }

    /* ================== Helper ================== */
    private function redirect($url) {
        if (!headers_sent()) { header('Location: '.$url); exit; }
        echo '<script>location.href='.json_encode($url).';</script>';
        echo '<noscript><meta http-equiv="refresh" content="0;url='.htmlspecialchars($url,ENT_QUOTES,'UTF-8').'"></noscript>';
        exit;
    }
}
