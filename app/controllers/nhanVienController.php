<?php
require_once dirname(__FILE__) . '/../models/NhanVien.php';

class nhanVienController {
    private $model;

    public function __construct() {
        $this->model = new NhanVien();
    }

    // ===== Danh sách nhân viên =====
    public function index() {
        $nhanviens = $this->model->getAll();
        include dirname(__FILE__) . '/../views/nhanvien/index.php';
    }

    // ===== Thêm nhân viên =====
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->model->insert($_POST)) {
                header("Location: index.php?controller=nhanvien&action=index&msg=added");
            } else {
                echo "<div class='content'><h3>❌ Lỗi thêm nhân viên!</h3></div>";
            }
            exit();
        }
        include dirname(__FILE__) . '/../views/nhanvien/form_add.php';
    }

    // ===== Sửa nhân viên =====
    public function edit() {
        if (!isset($_GET['id'])) {
            echo "<div class='content'><h3>❌ Thiếu mã nhân viên!</h3></div>";
            return;
        }
        $id = $_GET['id'];
        $nhanvien = $this->model->getById($id);
        if (!$nhanvien) {
            echo "<div class='content'><h3>❌ Không tìm thấy nhân viên!</h3></div>";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->model->update($id, $_POST)) {
                header("Location: index.php?controller=nhanvien&action=index&msg=updated");
            } else {
                echo "<div class='content'><h3>❌ Lỗi cập nhật!</h3></div>";
            }
            exit();
        }

        include dirname(__FILE__) . '/../views/nhanvien/form_edit.php';
    }

    // ===== Xóa mềm nhân viên =====
    public function delete() {
        if (isset($_GET['id'])) {
            $this->model->delete($_GET['id']);
        }
        header("Location: index.php?controller=nhanvien&action=index&msg=deleted");
        exit();
    }
}
?>
