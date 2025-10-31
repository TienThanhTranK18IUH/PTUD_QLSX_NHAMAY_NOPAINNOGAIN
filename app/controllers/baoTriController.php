<?php
/**
 * BaoTriController — Danh sách + Sửa inline phiếu ghi nhận sửa chữa
 */
require_once dirname(__FILE__) . '/../../config/config.php';
require_once dirname(__FILE__) . '/../models/Database.php';            // wrapper của bạn
require_once dirname(__FILE__) . '/../models/phieuGhiNhanSuaChua.php'; // class PhieuSuaChua

class BaoTriController {
    var $model;

    // PHP 5.x constructor
    function BaoTriController() {
        $db = new Database();
        $this->model = new PhieuSuaChua($db);
    }

    function index() {
        // Lưu cập nhật
        if (isset($_POST['btnSave'])) {
            $maPhieu       = isset($_POST['maPhieu'])       ? $_POST['maPhieu']       : '';
            $ngayHoanThanh = isset($_POST['ngayHoanThanh']) ? $_POST['ngayHoanThanh'] : '';
            $trangThai     = isset($_POST['trangThai'])     ? $_POST['trangThai']     : '';
            $noiDung       = isset($_POST['noiDung'])       ? $_POST['noiDung']       : '';

            $ok = $this->model->update($maPhieu, $ngayHoanThanh, $trangThai, $noiDung);
            if ($ok) {
                echo "<script>alert('✔️ Cập nhật thành công!');location.href='index.php?controller=baotri&action=index';</script>";
            } else {
                echo "<script>alert('❌ Cập nhật thất bại!');location.href='index.php?controller=baotri&action=index&maPhieu="
                     . urlencode($maPhieu) . "';</script>";
            }
            exit;
        }

        // Nếu chọn Sửa
        $phieuEdit = null;
        if (!empty($_GET['maPhieu'])) {
            $phieuEdit = $this->model->getById($_GET['maPhieu']);
        }

        // Danh sách
        $dsPhieu = $this->model->getAllRequests();

        // View
        include dirname(__FILE__) . '/../views/phieu/ghinhansuachua.php';
    }
}
