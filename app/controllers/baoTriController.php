<?php declare(strict_types=1); 
require_once dirname(__FILE__) . '/../../config/config.php';
require_once dirname(__FILE__) . '/../models/Database.php';
require_once dirname(__FILE__) . '/../models/phieuGhiNhanSuaChua.php';

class BaoTriController {
    private $model;

    public function __construct() {
        require_once dirname(__FILE__) . '/../helpers/auth.php';
        // Allow managers, xưởng trưởng (leader) and technicians to access maintenance records
        requireRole(array('manager','leader','technician'));
        $this->model = new PhieuGhiNhanSuaChua();
    }

    public function index() {
        // Nếu lưu form sửa / thêm
        if (isset($_POST['btnSave'])) {
            $maPhieuYCSC   = isset($_POST['maPhieuYCSC']) ? $_POST['maPhieuYCSC'] : '';
            $maPhieu       = isset($_POST['maPhieu']) ? $_POST['maPhieu'] : '';
            $ngayHoanThanh = isset($_POST['ngayHoanThanh']) ? $_POST['ngayHoanThanh'] : '';
            $trangThai     = isset($_POST['trangThai']) ? $_POST['trangThai'] : '';
            $noiDung       = isset($_POST['noiDung']) ? $_POST['noiDung'] : '';
            $maThietBi     = isset($_POST['maThietBi']) ? $_POST['maThietBi'] : '';
            $tenThietBi    = isset($_POST['tenThietBi']) ? $_POST['tenThietBi'] : '';
            // Lấy mã người dùng từ session hoặc từ hidden input form
            $maNguoiDung   = isset($_POST['maNguoiLap']) ? $_POST['maNguoiLap'] : (isset($_SESSION['user']['maNguoiDung']) ? $_SESSION['user']['maNguoiDung'] : 'ND006');

            if ($maPhieu == '') {
                // Thêm mới phiếu ghi nhận (nếu người dùng bấm Ghi nhận từ phiếu yêu cầu)
                $ok = $this->model->themPhieu($maPhieuYCSC, $ngayHoanThanh, $noiDung, $maNguoiDung, $trangThai, $maThietBi, $tenThietBi);
            } else {
                // Cập nhật phiếu đã có
                $ok = $this->model->capNhatPhieu($maPhieu, $ngayHoanThanh, $trangThai, $noiDung);
            }

            if ($ok) {
                // Không hiển thị alert — chỉ chuyển hướng về trang chính
                header('Location: index.php?controller=baotri&action=index');
                exit;
            } else {
                // Nếu muốn, vẫn có thể giữ thông báo lỗi (hiển thị qua JS)
                echo "<script>alert('❌ Lưu thất bại!');</script>";
            }
        }

        // Nếu bấm "Sửa" phiếu ghi nhận (đến từ danh sách ghi nhận)
        $phieuEdit = null;
        if (!empty($_GET['maPhieu'])) {
            $phieuEdit = $this->model->layTheoMa($_GET['maPhieu']);
        }

        // Nếu bấm "Ghi nhận" trên dòng phiếu yêu cầu => mở form để thêm ghi nhận
        if (!empty($_GET['maPhieuYCSC'])) {
            $phieuEdit = array(
                'maPhieu' => '',
                'maPhieuYCSC' => $_GET['maPhieuYCSC'],
                'ngayHoanThanh' => '',
                'trangThai' => 'Đang xử lý',
                'noiDung' => ''
            );
        }

        // Lấy dữ liệu hiển thị: tách 2 danh sách
        $dsYeuCau  = $this->model->layTatCaYeuCau();         // danh sách từ PhieuYeuCauSuaChua
        $dsGhiNhan = $this->model->layTatCaPhieuGhiNhan();   // danh sách từ PhieuGhiNhanSuaChua

        // Nếu đang tạo mới (bấm Ghi nhận từ phiếu yêu cầu) -> cố gắng lấy thông tin thiết bị
        if ($phieuEdit && !empty($phieuEdit['maPhieuYCSC']) && (!isset($phieuEdit['maThietBi']) || $phieuEdit['maThietBi'] == '')) {
            foreach ($dsYeuCau as $r) {
                if ($r['maPhieu'] == $phieuEdit['maPhieuYCSC']) {
                    $phieuEdit['maThietBi'] = isset($r['maTB']) ? $r['maTB'] : '';
                    $phieuEdit['tenThietBi'] = isset($r['tenTB']) ? $r['tenTB'] : '';
                    break;
                }
            }
        }

        // Gọi view
        include dirname(__FILE__) . '/../views/phieu/ghinhansuachua.php';
    }
}
?>
