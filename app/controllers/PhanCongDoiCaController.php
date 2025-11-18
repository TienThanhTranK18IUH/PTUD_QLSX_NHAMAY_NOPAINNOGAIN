<?php
require_once dirname(__FILE__) . '/../models/PhanCongDoiCa.php';

class PhanCongDoiCaController {
    private $model;

    public function __construct() {
        $this->model = new PhanCongDoiCa();
    }

    public function index() {
        $congNhanChuaPhanCa = $this->model->getCongNhanChuaPhanCa();
        $congNhanDaPhanCa = $this->model->getCongNhanDaPhanCa();
        $danhSachCa = $this->model->getDanhSachCa();
        $message = '';
        include dirname(__FILE__) . '/../views/phancong/PhanCongDoiCaLamViec.php';
    }

    public function capNhat() {
        // Only handle updates on POST. If called via GET, show the index view
        // (avoid calling PHP header() after layout has been output).
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->index();
        }

        // Now we are in POST. Validate required fields.
        if (!isset($_POST['maNguoiDung']) || !isset($_POST['maCa'])) {
            $message = 'Thiếu dữ liệu cập nhật.';
            $congNhanChuaPhanCa = $this->model->getCongNhanChuaPhanCa();
            $congNhanDaPhanCa = $this->model->getCongNhanDaPhanCa();
            $danhSachCa = $this->model->getDanhSachCa();
            include dirname(__FILE__) . '/../views/phancong/PhanCongDoiCaLamViec.php';
            return;
        }

        $maNguoiDung = $_POST['maNguoiDung'];
        $maCa = $_POST['maCa'];

        $result = $this->model->capNhatCa($maNguoiDung, $maCa);
        // Use PRG: redirect to the index action with a message so the layout (sidebar) is rendered.
        $msg = isset($result['message']) ? $result['message'] : 'updated';
        $url = 'index.php?controller=PhanCongDoiCa&action=index&msg=' . urlencode($msg);
        header("Location: " . $url);
        exit;
    }
}
?>