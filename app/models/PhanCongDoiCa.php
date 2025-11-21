<?php
require_once dirname(__FILE__) . '/database.php';

class PhanCongDoiCa {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Lấy công nhân chưa phân ca
    public function getCongNhanChuaPhanCa() {
        $sql = "SELECT cc.maNguoiDung, cc.tenCongNhan, cc.maXuong
                FROM chamcong cc
                LEFT JOIN phancong p ON cc.maNguoiDung = p.maNguoiDung
                WHERE p.maCa IS NULL
                GROUP BY cc.maNguoiDung";
        return $this->db->query($sql);
    }

    // Lấy công nhân đã phân ca
    public function getCongNhanDaPhanCa() {
        $sql = "SELECT p.maNguoiDung, cc.tenCongNhan, cc.maXuong, p.maCa
                FROM phancong p
                JOIN chamcong cc ON p.maNguoiDung = cc.maNguoiDung
                GROUP BY p.maNguoiDung";
        return $this->db->query($sql);
    }

    // Lấy danh sách ca từ bảng calamviec
    public function getDanhSachCa() {
        $sql = "SELECT maCa, thoiGianBatDau, thoiGianKetThuc FROM calamviec ORDER BY maCa";
        return $this->db->query($sql);
    }

    // Cập nhật phân công / đổi ca
    public function capNhatCa($maNguoiDung, $maCa, $maXuong = null, $ngayBatDau = null, $ngayKetThuc = null) {
    $maNguoiDungEsc = $this->db->conn->real_escape_string($maNguoiDung);
    $maCaEsc        = $this->db->conn->real_escape_string($maCa);
    $maXuongEsc     = $maXuong !== null ? $this->db->conn->real_escape_string($maXuong) : '';
    $ngayBatDauEsc  = $ngayBatDau !== null ? $ngayBatDau : '';
    $ngayKetThucEsc = $ngayKetThuc !== null ? $ngayKetThuc : '';

    $sqlCheck = "SELECT maCa FROM phancong WHERE maNguoiDung='$maNguoiDungEsc'";
    $current = $this->db->query($sqlCheck);

    if(count($current) > 0) {
        $sql = "UPDATE phancong SET 
                    maCa='$maCaEsc',
                    maXuong='$maXuongEsc',
                    ngayBatDau='$ngayBatDauEsc',
                    ngayKetThuc='$ngayKetThucEsc'
                WHERE maNguoiDung='$maNguoiDungEsc'";
    } else {
        $sql = "INSERT INTO phancong (maNguoiDung, maCa, maXuong, ngayBatDau, ngayKetThuc)
                VALUES ('$maNguoiDungEsc','$maCaEsc','$maXuongEsc','$ngayBatDauEsc','$ngayKetThucEsc')";
    }

    // Thực thi SQL với try-catch
    try {
        if ($this->db->conn->query($sql) === TRUE) {
            return array(
                'success'=>true,
                'message'=> count($current)>0 ? 'Đổi ca thành công.' : 'Phân công ca thành công.'
            );
        } else {
            // Nếu có lỗi SQL, không in ra trực tiếp
            return array(
                'success'=>false,
                'message'=> 'Không thể lưu dữ liệu. Vui lòng kiểm tra thông tin ca hoặc dữ liệu khác.'
            );
        }
    } catch(Exception $e) {
        // Bắt tất cả exception, không hiện lỗi SQL
        return array(
            'success'=>false,
            'message'=> 'Có lỗi xảy ra khi lưu dữ liệu. Vui lòng kiểm tra lại.'
        );
    }
}

}
?>