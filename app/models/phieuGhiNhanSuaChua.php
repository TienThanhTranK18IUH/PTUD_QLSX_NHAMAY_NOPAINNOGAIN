<?php declare(strict_types=1); 
require_once dirname(__FILE__) . '/../../config/config.php';
require_once dirname(__FILE__) . '/Database.php';

class PhieuGhiNhanSuaChua {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /* ========================== DANH SÁCH ========================== */

    // 1️⃣ Lấy danh sách phiếu yêu cầu sửa chữa
    public function layTatCaYeuCau() {
        $sql = "
            SELECT yc.maPhieu, yc.maTB, tb.tenTB, yc.trangThai
            FROM PhieuYeuCauSuaChua yc
            LEFT JOIN ThietBi tb ON yc.maTB = tb.maTB
            ORDER BY yc.maPhieu DESC
        ";
        return $this->db->query($sql);
    }

    // 2️⃣ Lấy danh sách phiếu ghi nhận sửa chữa
    public function layTatCaPhieuGhiNhan() {
        $sql = "
            SELECT p.maPhieu, p.maPhieuYCSC, p.noiDung, p.ngayHoanThanh,
                   p.maNguoiDung, p.trangThai, p.maThietBi, p.tenThietBi,
                   u.hoTen AS hoTenNguoiDung
            FROM PhieuGhiNhanSuaChua p
            LEFT JOIN nguoidung u ON p.maNguoiDung = u.maNguoiDung
            ORDER BY p.maPhieu DESC
        ";
        return $this->db->query($sql);
    }

    /* ========================== CHI TIẾT ========================== */

    // 3️⃣ Lấy chi tiết phiếu ghi nhận theo mã
    public function layTheoMa($maPhieu) {
        $maPhieu = $this->db->conn->real_escape_string($maPhieu);
        $sql = "SELECT * FROM PhieuGhiNhanSuaChua WHERE maPhieu = '$maPhieu' LIMIT 1";
        $data = $this->db->query($sql);
        return !empty($data) ? $data[0] : null;
    }

    /* ========================== CẬP NHẬT / THÊM ========================== */

    // 4️⃣ Thêm phiếu mới (khi ghi nhận từ phiếu yêu cầu)
    public function themPhieu($maPhieuYCSC, $ngayHoanThanh, $noiDung, $maNguoiDung, $trangThai, $maThietBi = '', $tenThietBi = '') {
        $maPhieuYCSC   = $this->db->conn->real_escape_string($maPhieuYCSC);
        $ngayHoanThanh = $this->db->conn->real_escape_string($ngayHoanThanh);
        $noiDung       = $this->db->conn->real_escape_string($noiDung);
        $maNguoiDung   = $this->db->conn->real_escape_string($maNguoiDung);
        $trangThai     = $this->db->conn->real_escape_string($trangThai);
        $maThietBi     = $this->db->conn->real_escape_string($maThietBi);
        $tenThietBi    = $this->db->conn->real_escape_string($tenThietBi);

        $sql = "
            INSERT INTO PhieuGhiNhanSuaChua (maPhieuYCSC, maThietBi, tenThietBi, noiDung, ngayHoanThanh, trangThai, maNguoiDung)
            VALUES ('$maPhieuYCSC', '$maThietBi', '$tenThietBi', '$noiDung', '$ngayHoanThanh', '$trangThai', '$maNguoiDung')
        ";
        return $this->db->conn->query($sql);
    }

    // 5️⃣ Cập nhật phiếu ghi nhận có sẵn
    public function capNhatPhieu($maPhieu, $ngayHoanThanh, $trangThai, $noiDung) {
        $maPhieu       = $this->db->conn->real_escape_string($maPhieu);
        $ngayHoanThanh = $this->db->conn->real_escape_string($ngayHoanThanh);
        $trangThai     = $this->db->conn->real_escape_string($trangThai);
        $noiDung       = $this->db->conn->real_escape_string($noiDung);

        $sql = "
            UPDATE PhieuGhiNhanSuaChua 
            SET ngayHoanThanh = '$ngayHoanThanh', 
                trangThai = '$trangThai', 
                noiDung = '$noiDung'
            WHERE maPhieu = '$maPhieu'
        ";
        return $this->db->conn->query($sql);
    }
}
?>
