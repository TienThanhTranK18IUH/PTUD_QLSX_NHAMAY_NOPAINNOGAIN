<?php
require_once dirname(__FILE__).'/../models/DoiCa.php';

class DoiCaController {
    private $model;

    public function __construct(){
        $this->model = new DoiCa();
    }

    // Hiển thị view
    public function index(){
        $congNhan = $this->model->getCongNhan();
        $danhSachCa = $this->model->getDanhSachCa();
        $danhSachXuong = $this->model->getDanhSachXuong();
        include dirname(__FILE__).'/../views/phancong/DoiCaLamViec.php';
    }

    // Tạo ca mới
    public function taoCaMoi(){
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $maNguoiDung = $_POST['maNguoiDung'];
            $maCa = $_POST['maCa'];
            $ngayLam = $_POST['ngayLam'];
            $maXuong = $_POST['maXuong'];

            $res = $this->model->taoCaMoi($maNguoiDung, $ngayLam, $maCa, $maXuong);
            echo json_encode($res); exit;
        }
    }

    // Đổi ca
    public function doiCa(){
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $maLichLam = $_POST['maLichLam'];
            $maCaMoi = $_POST['maCaMoi'];

            $res = $this->model->doiCa($maLichLam,$maCaMoi);
            echo json_encode($res); exit;
        }
    }

    // Lấy lịch làm nhân viên (AJAX)
    public function getLichLamNV(){
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $maNguoiDung = $_POST['maNguoiDung'];
            $res = $this->model->getLichLam($maNguoiDung);
            echo json_encode($res); exit;
        }
    }

    // Lấy xưởng nhân viên (AJAX)
    public function getXuongNV(){
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $maNguoiDung = $_POST['maNguoiDung'];
            $maXuong = $this->model->getXuongNV($maNguoiDung);
            echo json_encode(array('maXuong'=>$maXuong)); exit;
        }
    }
}
?>
