<?php
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
            // TODO: lấy mã người dùng từ session nếu có
            $maNguoiDung   = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'ND006';

            if ($maPhieu == '') {
                // Thêm mới phiếu ghi nhận (nếu người dùng bấm Ghi nhận từ phiếu yêu cầu)
                $ok = $this->model->themPhieu($maPhieuYCSC, $ngayHoanThanh, $noiDung, $maNguoiDung, $trangThai);
            } else {
                // Cập nhật phiếu đã có
                $ok = $this->model->capNhatPhieu($maPhieu, $ngayHoanThanh, $trangThai, $noiDung);
            }

            if ($ok) {
                echo "<script>alert('✔️ Lưu dữ liệu thành công!');location.href='index.php?controller=baotri&action=index';</script>";
                exit;
            } else {
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

        // Gọi view
        include dirname(__FILE__) . '/../views/phieu/ghinhansuachua.php';
    }
}
?>
