<?php
require_once dirname(__FILE__) . '/Database.php';

class NhanVien {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->conn;
    }

    // ====== Lấy tất cả nhân viên đang hoạt động ======
    public function getAll() {
        $sql = "SELECT maNguoiDung AS maNhanVien, hoTen AS tenNhanVien, gioiTinh, ngaySinh, diaChi, 
                       soDienThoai, email, vaiTro AS chucVu, trangThai 
                FROM NguoiDung 
                WHERE trangThai = 'HoatDong'
                ORDER BY maNguoiDung ASC";
        return $this->db->query($sql);
    }

    // ====== Lấy 1 nhân viên theo ID ======
    public function getById($id) {
        $id = $this->conn->real_escape_string($id);
        $sql = "SELECT maNguoiDung AS maNhanVien, hoTen AS tenNhanVien, gioiTinh, ngaySinh, diaChi, 
                       soDienThoai, email, vaiTro AS chucVu, trangThai 
                FROM NguoiDung WHERE maNguoiDung='$id' LIMIT 1";
        $rows = $this->db->query($sql);
        return !empty($rows) ? $rows[0] : null;
    }

    // ====== Thêm nhân viên ======
    public function insert($tenNhanVien, $gioiTinh, $ngaySinh, $diaChi, $soDienThoai, $email, $chucVu, $trangThai = 'HoatDong') {
        $tenNhanVien = $this->conn->real_escape_string($tenNhanVien);
        $gioiTinh = $this->conn->real_escape_string($gioiTinh);
        $ngaySinh = $this->conn->real_escape_string($ngaySinh);
        $diaChi = $this->conn->real_escape_string($diaChi);
        $soDienThoai = $this->conn->real_escape_string($soDienThoai);
        $email = $this->conn->real_escape_string($email);
        $chucVu = $this->conn->real_escape_string($chucVu);
        $trangThai = $this->conn->real_escape_string($trangThai);

        $sql = "INSERT INTO NguoiDung (hoTen, gioiTinh, ngaySinh, diaChi, soDienThoai, email, vaiTro, trangThai)
                VALUES ('$tenNhanVien', '$gioiTinh', '$ngaySinh', '$diaChi', '$soDienThoai', '$email', '$chucVu', '$trangThai')";
        return $this->db->query($sql);
    }

    // ====== Cập nhật nhân viên ======
    public function update($id, $tenNhanVien, $gioiTinh, $ngaySinh, $diaChi, $soDienThoai, $email, $chucVu, $trangThai) {
        $id = $this->conn->real_escape_string($id);
        $tenNhanVien = $this->conn->real_escape_string($tenNhanVien);
        $gioiTinh = $this->conn->real_escape_string($gioiTinh);
        $ngaySinh = $this->conn->real_escape_string($ngaySinh);
        $diaChi = $this->conn->real_escape_string($diaChi);
        $soDienThoai = $this->conn->real_escape_string($soDienThoai);
        $email = $this->conn->real_escape_string($email);
        $chucVu = $this->conn->real_escape_string($chucVu);
        $trangThai = $this->conn->real_escape_string($trangThai);

        $sql = "UPDATE NguoiDung
                SET hoTen='$tenNhanVien', gioiTinh='$gioiTinh', ngaySinh='$ngaySinh',
                    diaChi='$diaChi', soDienThoai='$soDienThoai', email='$email',
                    vaiTro='$chucVu', trangThai='$trangThai'
                WHERE maNguoiDung='$id'";
        return $this->db->query($sql);
    }

    // ====== Xóa mềm (đổi trạng thái sang 'Ngung') ======
    public function delete($id) {
        $id = $this->conn->real_escape_string($id);
        $sql = "UPDATE NguoiDung SET trangThai='Ngung' WHERE maNguoiDung='$id'";
        return $this->db->query($sql);
    }
}
?>
