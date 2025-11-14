<?php
require_once dirname(__FILE__) . '/database.php';

class PhanCongDoiCa {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Lấy danh sách công nhân chưa được phân ca
    public function getCongNhanChuaPhanCa() {
        $sql = "SELECT nv.maNguoiDung, nv.hoTen
                FROM nguoidung nv
                LEFT JOIN phancong p ON nv.maNguoiDung = p.maNguoiDung
                WHERE nv.vaiTro='CongNhan' AND p.maCa IS NULL";
        return $this->db->query($sql);
    }

    // Lấy danh sách công nhân đã được phân ca
    public function getCongNhanDaPhanCa() {
        $sql = "SELECT nv.maNguoiDung, nv.hoTen, p.maCa
                FROM nguoidung nv
                JOIN phancong p ON nv.maNguoiDung = p.maNguoiDung
                WHERE nv.vaiTro='CongNhan' AND p.maCa IS NOT NULL";
        return $this->db->query($sql);
    }

    // Kiểm tra trùng ca
    public function kiemTraTrungCa($maCa, $maNguoiDung) {
        $sql = "SELECT * FROM phancong WHERE maCa='$maCa' AND maNguoiDung='$maNguoiDung'";
        $result = $this->db->query($sql);
        return count($result) > 0;
    }

    // Phân công mới hoặc đổi ca
    public function capNhatCa($maNguoiDung, $maCa) {
        $sqlCheck = "SELECT maCa FROM phancong WHERE maNguoiDung='$maNguoiDung'";
        $current = $this->db->query($sqlCheck);

        if(count($current) > 0 && $current[0]['maCa'] == $maCa) {
            return array('success' => false, 'message' => 'Ca mới trùng với ca hiện tại. Vui lòng chọn lại.');
        }

        if(count($current) > 0) {
            // Đổi ca
            $sql = "UPDATE phancong SET maCa='$maCa' WHERE maNguoiDung='$maNguoiDung'";
        } else {
            // Phân công mới
            $sql = "INSERT INTO phancong (maNguoiDung, maCa) VALUES ('$maNguoiDung','$maCa')";
        }

        $this->db->query($sql);
        return array('success' => true, 'message' => (count($current) > 0 ? 'Đổi ca thành công.' : 'Phân công ca thành công.'));
    }

    // Danh sách các ca có sẵn
    public function getDanhSachCa() {
        return array('C1','C2','C3'); // ví dụ các ca
    }
}
?>