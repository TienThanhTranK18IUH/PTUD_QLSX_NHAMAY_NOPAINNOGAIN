<?php
require_once dirname(__FILE__) . '/../models/PhanCongDoiCa.php';

class PhanCongDoiCaController {
    private $model;

    public function __construct() {
        $this->model = new PhanCongDoiCa();
    }

    // Hiển thị giao diện
    public function index() {
        $congNhanChuaPhanCa = $this->model->getCongNhanChuaPhanCa();
        $congNhanDaPhanCa   = $this->model->getCongNhanDaPhanCa();
        $danhSachCa         = $this->model->getDanhSachCa();
        $message            = isset($_GET['msg']) ? $_GET['msg'] : '';

        include dirname(__FILE__) . '/../views/phancong/PhanCongDoiCaLamViec.php';
    }

    // Cập nhật phân công / đổi ca
    public function capNhat() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->index();
        }

        if(!isset($_POST['maNguoiDung']) || !isset($_POST['maCa']) || !isset($_POST['maXuong'])) {
            $msg = 'Thiếu dữ liệu bắt buộc.';
            header("Location: index.php?controller=PhanCongDoiCa&action=index&msg=".urlencode($msg));
            exit;
        }

        $maNguoiDung = $_POST['maNguoiDung'];
        $maCa        = $_POST['maCa'];
        $maXuong     = $_POST['maXuong'];
        $ngayBatDau  = isset($_POST['ngayBatDau']) ? $_POST['ngayBatDau'] : '';
        $ngayKetThuc = isset($_POST['ngayKetThuc']) ? $_POST['ngayKetThuc'] : '';

        $result = $this->model->capNhatCa($maNguoiDung, $maCa, $maXuong, $ngayBatDau, $ngayKetThuc);

        $msg = isset($result['message']) ? $result['message'] : 'Cập nhật thành công';
        header("Location: index.php?controller=PhanCongDoiCa&action=index&msg=".urlencode($msg));
        exit;
    }
}
?>