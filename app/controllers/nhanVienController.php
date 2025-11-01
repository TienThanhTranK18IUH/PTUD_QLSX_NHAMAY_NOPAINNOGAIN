<?php
require_once dirname(__FILE__) . '/../models/nhanVien.php';

class nhanVienController {
    private $model;

    public function __construct() {
        $this->model = new NhanVien();
    }

    // ====== Danh sách nhân viên ======
    public function index() {
        $nhanviens = $this->model->getAll();
        include(dirname(__FILE__) . '/../views/nhanvien/index.php');
    }

    // ====== Thêm nhân viên ======
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->insert(
                $_POST['tenNhanVien'],
                $_POST['gioiTinh'],
                $_POST['ngaySinh'],
                $_POST['diaChi'],
                $_POST['soDienThoai'],
                $_POST['email'],
                $_POST['chucVu'],
                'HoatDong'
            );
            header("Location: index.php?controller=nhanvien&action=index");
            exit();
        }

        include dirname(__FILE__) . '/../views/nhanvien/form_add.php';

    }

    // ====== Sửa nhân viên ======
    public function edit() {
        if (!isset($_GET['id'])) {
            echo "<div class='content'><h3>❌ Thiếu ID nhân viên!</h3></div>";
            return;
        }

        $id = htmlspecialchars($_GET['id']);
        $nhanvien = $this->model->getById($id);

        if (!$nhanvien) {
            echo "<div class='content'><h3>❌ Không tìm thấy nhân viên!</h3></div>";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->update(
                $id,
                $_POST['tenNhanVien'],
                $_POST['gioiTinh'],
                $_POST['ngaySinh'],
                $_POST['diaChi'],
                $_POST['soDienThoai'],
                $_POST['email'],
                $_POST['chucVu'],
                $_POST['trangThai']
            );
            header("Location: index.php?controller=nhanvien&action=index");
            exit();
        }

        include dirname(__FILE__) . '/../views/nhanvien/form_edit.php';

    }

    // ====== Xóa mềm nhân viên ======
    public function delete() {
        if (isset($_GET['id'])) {
            $id = htmlspecialchars($_GET['id']);
            $this->model->delete($id);
        }
        header("Location: index.php?controller=nhanvien&action=index");
        exit();
    }
}
?>
