<?php
require_once dirname(__FILE__) . '/../models/KeHoachSanXuat.php';
require_once dirname(__FILE__) . '/../models/donHang.php';
require_once dirname(__FILE__) . '/../models/xuong.php';
require_once dirname(__FILE__) . '/../models/nguyenLieu.php';
class KeHoachController {
    private $model;

    public function __construct() {
        $this->model = new KeHoachSanXuat();
    }

    // Danh sách kế hoạch
    public function index() {
        $kehoachs = $this->model->getAll();
        include dirname(__FILE__) . '/../views/kehoach/index.php';
    }

    // Form edit kế hoạch
    public function form_edit() {
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
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['maKeHoach'])) {
        $maKH = $_POST['maKeHoach'];
        $maDonHang = $_POST['maDonHang'];
        $tenSP = $_POST['tenSP'];
        $tenXuong = $_POST['tenXuong'];
        $ngayBatDau = $_POST['ngayBatDau'];
        $ngayKetThuc = $_POST['ngayKetThuc'];
        $tongSL = intval($_POST['tongSoLuong']);
        $maNL = $_POST['maNguyenLieu'];
        $tenNL = $_POST['tenNguyenLieu'];
        $slNL = intval($_POST['soLuongNguyenLieu']);
        $trangThai = $_POST['trangThai'];

        $conn = $this->model->getConnection();

        // --- Lấy mã xưởng từ tên xưởng ---
        $tenXuongEsc = mysqli_real_escape_string($conn, $tenXuong);
        $resX = mysqli_query($conn, "SELECT maXuong FROM xuong WHERE tenXuong='$tenXuongEsc' LIMIT 1");
        $rowX = mysqli_fetch_assoc($resX);
        $maXuong = $rowX ? $rowX['maXuong'] : null;

        // --- Cập nhật tên sản phẩm trong DonHang ---
        $tenSPEsc = mysqli_real_escape_string($conn, $tenSP);
        $maDonHangEsc = mysqli_real_escape_string($conn, $maDonHang);
        $sqlDH = "UPDATE donhang SET tenSP='$tenSPEsc' WHERE maDonHang='$maDonHangEsc'";
        mysqli_query($conn, $sqlDH);

        // --- Cập nhật kế hoạch ---
        $sqlKH = "UPDATE kehoachsanxuat SET 
                    maXuong='$maXuong',
                    maDonHang='$maDonHangEsc',
                    ngayBatDau='$ngayBatDau',
                    ngayKetThuc='$ngayKetThuc',
                    tongSoLuong=$tongSL,
                    maNguyenLieu='$maNL',
                    tenNguyenLieu='".mysqli_real_escape_string($conn, $tenNL)."',
                    soLuongNguyenLieu=$slNL,
                    trangThai='".mysqli_real_escape_string($conn, $trangThai)."'
                  WHERE maKeHoach='$maKH'";
        mysqli_query($conn, $sqlKH);

        mysqli_close($conn);
header("Location: index.php?controller=kehoach&action=index");
exit();
    }

}
    public function lapKeHoach() {

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
    $data = array(
        'maDonHang' => $_POST['maDonHang'],
        'maXuong' => $_POST['maXuong'],
        'ngayBatDau' => $_POST['ngayBatDau'],
        'ngayKetThuc' => $_POST['ngayKetThuc'],
        'tongSoLuong' => $_POST['tongSoLuong'],
        'trangThai' => 'Chưa bắt đầu',
        'maNguyenLieu' => $_POST['maNguyenLieu'],
        'tenNguyenLieu' => $_POST['tenNguyenLieu'],
        'soLuongNguyenLieu' => $_POST['soLuongNguyenLieu']
    );

    // Kiểm tra trùng
    if ($this->model->checkDuplicatePlan($data)) {
        $errors[] = "Kế hoạch cho đơn hàng này và xưởng này đã tồn tại!";
    } else {
        if ($this->model->createPlan($data)) {
            // ✅ Cập nhật trạng thái đơn hàng
            $this->model->updateOrderStatus($data['maDonHang'], 'Đã lập kế hoạch');
            $success = "Lập kế hoạch thành công!";
        } else {
            $errors[] = "Không thể lưu kế hoạch.";
        }
    
}
    }

    include dirname(__FILE__) . '/../views/kehoach/form_add.php';
}
}
?>
