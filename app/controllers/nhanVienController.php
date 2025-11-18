<?php
require_once dirname(__FILE__) . '/../models/NhanVien.php';

class NhanVienController {
    private $model;

    // Map bộ phận => tên xưởng
    private $boPhanXuong = array(
        'BP001' => 'Xưởng Cắt May',
        'BP002' => 'Xưởng Hoàn Thiện',
        'BP003' => 'Xưởng Kho',
        'BP004' => 'Xưởng Kiểm Định',
        'BP005' => 'Xưởng Kỹ Thuật'
    );

    public function __construct() {
        $this->model = new NhanVien();
    }

    // ==================== DANH SÁCH NHÂN VIÊN ====================
    public function index() {
        $nhanviens = $this->model->getAll();
        include dirname(__FILE__) . '/../views/nhanvien/index.php';
    }

    // ==================== THÊM NHÂN VIÊN ====================
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Tự động thêm tên xưởng dựa trên bộ phận
            if (!empty($_POST['maBoPhan'])) {
                $maBoPhan = $_POST['maBoPhan'];
                $_POST['tenXuong'] = isset($this->boPhanXuong[$maBoPhan]) ? $this->boPhanXuong[$maBoPhan] : '';
            }

            $result = $this->model->insert($_POST);

            if ($result) {
                header("Location: index.php?controller=nhanvien&action=index&msg=added");
                exit();
            }

            echo "<div class='content'><h3>❌ Lỗi thêm nhân viên!</h3></div>";
        }

        include dirname(__FILE__) . '/../views/nhanvien/form_add.php';
    }

    // ==================== SỬA NHÂN VIÊN ====================
    public function edit() {
        if (empty($_GET['id'])) {
            echo "<div class='content'><h3>❌ Thiếu mã nhân viên!</h3></div>";
            return;
        }

        $id = $_GET['id'];
        $nhanvien = $this->model->getById($id);

        if (!$nhanvien) {
            echo "<div class='content'><h3>❌ Không tìm thấy nhân viên!</h3></div>";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Tự động thêm tên xưởng dựa trên bộ phận
            if (!empty($_POST['maBoPhan'])) {
                $maBoPhan = $_POST['maBoPhan'];
                $_POST['tenXuong'] = isset($this->boPhanXuong[$maBoPhan]) ? $this->boPhanXuong[$maBoPhan] : '';
            }

            $result = $this->model->update($id, $_POST);

            if ($result) {
                header("Location: index.php?controller=nhanvien&action=index&msg=updated");
                exit();
            }

            echo "<div class='content'><h3>❌ Lỗi cập nhật nhân viên!</h3></div>";
        }

        include dirname(__FILE__) . '/../views/nhanvien/form_edit.php';
    }

    // ==================== XOÁ MỀM NHÂN VIÊN ====================
    public function delete() {
        if (!empty($_GET['id'])) {
            $this->model->delete($_GET['id']);
        }

        $url = 'index.php?controller=nhanvien&action=index&msg=deleted';
        echo "<script>window.location.replace('" . htmlspecialchars($url, ENT_QUOTES) . "');</script>";
        echo "<noscript><meta http-equiv=\"refresh\" content=\"0;url={$url}\"></noscript>";
        exit();
    }
}
?>
