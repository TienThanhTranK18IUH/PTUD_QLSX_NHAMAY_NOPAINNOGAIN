<?php
// app/models/phieuGhiNhanSuaChua.php
// Bảng: PhieuGhiNhanSuaChua
require_once dirname(__FILE__) . '/../../config/config.php';

class PhieuSuaChua {
    var $db;

    // PHP 5.x constructor
    function __construct($db) { $this->db = $db; }
    function PhieuSuaChua($db) { $this->__construct($db); }

    /* ===== Helpers ===== */

    // SELECT qua wrapper -> trả mảng
    function fetchAll($sql){
        $rows = $this->db->query($sql);   // Database.php của bạn trả mảng cho SELECT
        if (is_array($rows)) return $rows;

        // Phòng xa nếu wrapper đổi
        $out = array();
        if (is_object($rows) && (get_class($rows)==='mysqli_result' || $rows instanceof mysqli_result)) {
            while ($r = @mysqli_fetch_assoc($rows)) { $out[] = $r; }
        }
        return $out;
    }

    // Lấy 1 dòng (hoặc null)
    function fetchOne($sql){
        $rows = $this->fetchAll($sql);
        return (count($rows) > 0) ? $rows[0] : null;
    }

    // UPDATE/INSERT/DELETE — kết nối riêng, không đụng Database.php
    function execNonQuery($sql){
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) return false;
        if (defined('DB_CHARSET')) $conn->set_charset(DB_CHARSET);
        $ok = $conn->query($sql) ? true : false;
        $conn->close();
        return $ok;
    }

    /* ===== APIs ===== */

    // Danh sách phiếu (giao diện 1)
    function getAllRequests() {
        $sql = "SELECT maPhieu, maPhieuYCSC, noiDung, ngayHoanThanh,
                       maNhanVienKyThuat, maThietBi, tenThietBi, trangThai
                  FROM PhieuGhiNhanSuaChua
              ORDER BY maPhieu DESC";
        return $this->fetchAll($sql);
    }

    // Lấy 1 phiếu theo mã (form sửa – giao diện 2)
    function getById($maPhieu) {
        $maPhieu = addslashes($maPhieu);
        $sql = "SELECT * FROM PhieuGhiNhanSuaChua WHERE maPhieu = '".$maPhieu."' LIMIT 1";
        return $this->fetchOne($sql);
    }

    // Cập nhật thông tin phiếu (bấm “Lưu thay đổi”)
    function update($maPhieu, $ngayHoanThanh, $trangThai, $noiDung) {
        $maPhieu       = addslashes($maPhieu);
        $ngayHoanThanh = addslashes($ngayHoanThanh);
        $trangThai     = addslashes($trangThai);
        $noiDung       = addslashes($noiDung);

        $sql = "UPDATE PhieuGhiNhanSuaChua
                   SET ngayHoanThanh = '".$ngayHoanThanh."',
                       trangThai     = '".$trangThai."',
                       noiDung       = '".$noiDung."'
                 WHERE maPhieu      = '".$maPhieu."'";
        return $this->execNonQuery($sql);
    }
}
