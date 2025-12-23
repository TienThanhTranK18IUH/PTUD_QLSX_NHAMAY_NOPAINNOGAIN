<?php
require_once dirname(__FILE__) . '/../models/KeHoachSanXuat.php';
require_once dirname(__FILE__) . '/../models/donHang.php';
require_once dirname(__FILE__) . '/../models/xuong.php';
require_once dirname(__FILE__) . '/../models/nguyenLieu.php';

class KeHoachController {
    private $model;

    public function __construct() {
        require_once dirname(__FILE__) . '/../helpers/auth.php';
        $this->model = new KeHoachSanXuat();
    }

    // Danh sách kế hoạch
    public function index() {
        $kehoachs = $this->model->getAll();
        include dirname(__FILE__) . '/../views/kehoach/index.php';
    }

    // Form edit kế hoạch
    public function form_edit() {
        // Only managers, leaders and planners can access the edit form
        requireRole(array('manager','leader','planner'));
        $kehoachs = $this->model->getAll();
        $xuongs = $this->model->getAllXuongs();
        $sanphams = $this->model->getAllSanPhams();
        $donhangs = $this->model->getAllDonHangs();
        $nguyenlieus = $this->model->getAllNguyenLieus();
        $message = '';
        include dirname(__FILE__) . '/../views/kehoach/form_edit.php';
    }

  // Cập nhật kế hoạch
public function update() {
    // Only planners and managers may update plans
    requireRole(array('manager','planner'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['maKeHoach'])) {

        // Lấy tên người lập từ session
        $_POST['nguoiLap'] = isset($_SESSION['user']['hoTen'])
            ? $_SESSION['user']['hoTen']
            : '';

        try {
            // GỌI MODEL – KHÔNG SQL Ở CONTROLLER
            $this->model->updateKeHoach($_POST);

            // LƯU THÀNH CÔNG → LOAD LẠI TRANG
            header("Location: index.php?controller=keHoach&action=form_edit");
            exit;

        } catch (Exception $e) {
            // Nếu lỗi, quay lại form edit (có thể gán message nếu muốn)
            $error = $e->getMessage();

            $kehoachs = $this->model->getAll();
            $xuongs = $this->model->getAllXuongs();
            $sanphams = $this->model->getAllSanPhams();
            $donhangs = $this->model->getAllDonHangs();
            $nguyenlieus = $this->model->getAllNguyenLieus();

            include dirname(__FILE__) . '/../views/kehoach/form_edit.php';
        }
    }
}
    // Tạo kế hoạch mới
    public function lapKeHoach() {
        // Only planners and managers may create new plans
        requireRole(array('manager','planner'));

        $khModel = new KeHoachSanXuat();
        $dhModel = new DonHang();
        $xuongModel = new Xuong();
        $nlModel = new NguyenLieu();

        $donhangs = $dhModel->getPendingOrders();
        $xuongs = $xuongModel->getAll();

        // linh hoạt gọi hàm lấy nguyên liệu
        if (method_exists($nlModel, 'getAllNguyenLieus')) {
            $nguyenlieus = $nlModel->getAllNguyenLieus();
        } elseif (method_exists($nlModel, 'getAll')) {
            $nguyenlieus = $nlModel->getAll();
        } else {
            $nguyenlieus = array();
        }

        $errors = array();
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            // Kiểm tra số lần lập kế hoạch cho đơn hàng
                $soLan = $khModel->countByOrder($_POST['maDonHang']);

                if ($soLan >= 2) {
                    $errors[] = "❌ Đơn hàng này đã được lập đủ 2 kế hoạch (Cắt may & Hoàn thiện). Không thể lập thêm.";
                } 
            // Lấy tên người lập từ session
            $nguoiLap = isset($_SESSION['user']['hoTen']) ? $_SESSION['user']['hoTen'] : '';

            $data = array(
                'maDonHang' => $_POST['maDonHang'],
                'maXuong' => $_POST['maXuong'],
                'ngayBatDau' => $_POST['ngayBatDau'],
                'ngayKetThuc' => $_POST['ngayKetThuc'],
                'tongSoLuong' => $_POST['tongSoLuong'],
                'trangThai' => 'Chưa bắt đầu',
                'maNguyenLieu' => $_POST['maNguyenLieu'],
                'tenNguyenLieu' => $_POST['tenNguyenLieu'],
                'soLuongNguyenLieu' => $_POST['soLuongNguyenLieu'],
                'nguoiLap' => $nguoiLap
            );

    // Kiểm tra trùng
    if ($this->model->checkDuplicatePlan($data)) {
        $msg = urlencode("Kế hoạch cho đơn hàng này và xưởng này đã tồn tại!");
    header("Location: index.php?controller=kehoach&action=lapKeHoach&msg=$msg&type=error");
    exit();
} else {
    if ($this->model->createPlan($data)) {
        $soLan = $this->model->countByOrder($data['maDonHang']);
        if ($soLan == 1) {
            $this->model->updateOrderStatus($data['maDonHang'], 'Đang lập kế hoạch');
        }
        $msg = urlencode("Lập kế hoạch thành công!");
        header("Location: index.php?controller=kehoach&action=lapKeHoach&msg=$msg&type=success");
        exit();
    } else {
        $msg = urlencode("Không thể lưu kế hoạch.");
        header("Location: index.php?controller=kehoach&action=lapKeHoach&msg=$msg&type=error");
        exit();
    }
    }
}

include dirname(__FILE__) . '/../views/kehoach/form_add.php';

    }
}
?>
