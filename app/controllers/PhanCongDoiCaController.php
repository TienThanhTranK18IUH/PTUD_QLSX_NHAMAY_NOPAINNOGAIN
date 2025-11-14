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
        if(!isset($_POST['maNguoiDung']) || !isset($_POST['maCa'])) {
            header("Location: index.php?controller=PhanCongDoiCa");
            exit;
        }

        $maNguoiDung = $_POST['maNguoiDung'];
        $maCa = $_POST['maCa'];

        $result = $this->model->capNhatCa($maNguoiDung, $maCa);
        $message = $result['message'];

        // Reload danh sách để hiển thị
        $congNhanChuaPhanCa = $this->model->getCongNhanChuaPhanCa();
        $congNhanDaPhanCa = $this->model->getCongNhanDaPhanCa();
        $danhSachCa = $this->model->getDanhSachCa();
        include dirname(__FILE__) . '/../views/phancong/PhanCongDoiCaLamViec.php';
    }
}
?>