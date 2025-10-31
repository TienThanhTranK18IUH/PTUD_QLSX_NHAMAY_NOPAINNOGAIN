<?php
require_once 'app/models/NhanVien.php';

class NhanvienController {
    private $model;

    public function __construct() {
        $this->model = new NhanVien();
    }

    public function index() {
        $nhanviens = $this->model->getAll();
        include 'app/views/nhanvien/index.php';
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->model->insert($_POST['tenDangNhap'], $_POST['matKhau'], $_POST['hoTen'], $_POST['vaiTro']);
            header("Location: index.php?controller=nhanvien&action=index");
            exit();
        }
        include 'app/views/nhanvien/form_add.php';
    }

    public function edit() {
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $nhanvien = $this->model->getById($id);

            if (!$nhanvien) {
                echo "<div class='content'><h3>❌ Không tìm thấy nhân viên!</h3></div>";
                return;
            }

            // Nếu submit form
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $hoTen = $_POST['hoTen'];
                $vaiTro = $_POST['vaiTro'];
                $trangThai = $_POST['trangThai'];
                $this->model->update($id, $hoTen, $vaiTro, $trangThai);
                header("Location: index.php?controller=nhanvien&action=index");
                exit();
            }

            include 'app/views/nhanvien/form_edit.php';
        } else {
            echo "<div class='content'><h3>❌ Thiếu ID nhân viên!</h3></div>";
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $this->model->delete($_GET['id']);
        }
        header("Location: index.php?controller=nhanvien&action=index");
    }
}
?>