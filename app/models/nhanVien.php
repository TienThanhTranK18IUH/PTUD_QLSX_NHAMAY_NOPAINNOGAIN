<?php
require_once dirname(__FILE__) . '/Database.php';
require_once dirname(__FILE__) . '/../../config/config.php';

class NhanVien {

    /** @var Database */
    private $db;     // dùng cho SELECT -> trả mảng
    /** @var mysqli  */
    private $conn;   // dùng cho INSERT/UPDATE/DELETE (prepare/bind)

    public function __construct() {
        // Database.php hiện tại KHÔNG có getInstance()/getConnection()
        // => ta chủ động tạo 1 Database cho SELECT
        $this->db = new Database();

        // và tự mở 1 kết nối mysqli riêng cho ghi (INSERT/UPDATE/DELETE)
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->conn->connect_error) {
            die('DB connect error: ' . $this->conn->connect_error);
        }
        if (defined('DB_CHARSET') && DB_CHARSET) {
            @$this->conn->set_charset(DB_CHARSET);
        }
    }

    // Lấy tất cả nhân viên
    public function getAll() {
        $sql = "SELECT maNguoiDung, tenDangNhap, hoTen, vaiTro, trangThai
                FROM NguoiDung
                ORDER BY maNguoiDung ASC";
        return $this->db->query($sql); // trả về mảng
    }

    // Lấy nhân viên theo ID
    public function getById($id) {
        $id = intval($id);
        $rows = $this->db->query("SELECT * FROM NguoiDung WHERE maNguoiDung = {$id} LIMIT 1");
        return (is_array($rows) && count($rows) > 0) ? $rows[0] : null;
    }

    // Thêm nhân viên mới
    public function insert($tenDangNhap, $matKhau, $hoTen, $vaiTro) {
        $sql = "INSERT INTO NguoiDung (tenDangNhap, matKhau, hoTen, vaiTro)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param("ssss", $tenDangNhap, $matKhau, $hoTen, $vaiTro);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // Cập nhật thông tin nhân viên
    public function update($id, $hoTen, $vaiTro, $trangThai) {
        $sql = "UPDATE NguoiDung SET hoTen = ?, vaiTro = ?, trangThai = ?
                WHERE maNguoiDung = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;
        $id = intval($id);
        $stmt->bind_param("sssi", $hoTen, $vaiTro, $trangThai, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // Xóa nhân viên
    public function delete($id) {
        $sql = "DELETE FROM NguoiDung WHERE maNguoiDung = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;
        $id = intval($id);
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function __destruct() {
        if ($this->conn) { @$this->conn->close(); }
        // $this->db sẽ tự đóng trong destructor của Database.php
    }
}
?>
