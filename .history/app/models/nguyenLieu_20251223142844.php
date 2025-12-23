<?php
require_once 'app/models/Database.php';

class NguyenLieu {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->conn; // dùng kết nối từ Database.php
    }

    // ✅ Lấy toàn bộ nguyên liệu
    public function getAll() {
        $sql = "SELECT maNL, tenNL, donViTinh, soLuongTon FROM nguyenlieu";
        $result = $this->conn->query($sql);

        $data = array();
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

     // ✅ Lấy toàn bộ nguyên liệu đúng tên cột trong CSDL
    public function getAllNguyenLieus() {
        $sql = "SELECT maNguyenLieu, tenNguyenLieu FROM nguyenlieu ORDER BY tenNguyenLieu ASC";
        return $this->db->query($sql);
    }

    // ✅ Lấy nguyên liệu theo mã
    public function getById($maNguyenLieu) {
        $sql = "SELECT * FROM nguyenlieu WHERE maNguyenLieu = '" . $this->db->conn->real_escape_string($maNguyenLieu) . "'";
        $result = $this->db->query($sql);
        return !empty($result) ? $result[0] : null;
    }

    // ✅ Cập nhật tồn kho
    public function updateSoLuong($maNguyenLieu, $soLuongMoi) {
        $conn = $this->db->conn;
        $stmt = $conn->prepare("UPDATE nguyenlieu SET soLuongTon = ? WHERE maNguyenLieu = ?");
        $stmt->bind_param("is", $soLuongMoi, $maNguyenLieu);
        return $stmt->execute();
    }

        // Lấy số lượng tồn kho hiện tại của một nguyên liệu
    public function getSoLuongTonKho($maNguyenLieu) {
        $sql = "SELECT soLuongTon FROM nguyenlieu WHERE maNguyenLieu = '" . $this->db->conn->real_escape_string($maNguyenLieu) . "'";
        $result = $this->db->query($sql);
        return !empty($result) ? (int)$result[0]['soLuongTon'] : 0;
    }

}
?>
