<?php
require_once dirname(__FILE__) . '/Database.php';

class NhanVien {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->conn;
    }

    // ===== Lấy tất cả nhân viên đang hoạt động =====
    public function getAll() {
        $sql = "SELECT maNguoiDung, hoTen, gioiTinh, ngaySinh, diaChi, soDienThoai, email, vaiTro,tenXuong, trangThai 
                FROM nguoidung 
                WHERE trangThai = 'HoatDong' 
                ORDER BY maNguoiDung ASC";
        $result = $this->conn->query($sql);
        $data = array();
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // ===== Lấy 1 nhân viên theo ID =====
    public function getById($id) {
        $id = $this->conn->real_escape_string($id);
        $sql = "SELECT * FROM nguoidung WHERE maNguoiDung='$id' LIMIT 1";
        $result = $this->conn->query($sql);
        return ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;
    }

    // ===== Thêm nhân viên từ form =====
    public function insert($data) {
        foreach ($data as $key => $value) {
            $data[$key] = $this->conn->real_escape_string($value);
        }

        // Nếu người dùng không nhập mã, tự động tạo NDxxx
        if (empty($data['maNguoiDung'])) {
            $result = $this->conn->query("SELECT MAX(CAST(SUBSTRING(maNguoiDung,3) AS UNSIGNED)) AS maxID FROM nguoidung");
            $row = $result->fetch_assoc();
            $nextId = $row['maxID'] + 1;
            $data['maNguoiDung'] = 'ND' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        }

        $sql = "INSERT INTO nguoidung (
                    maNguoiDung, tenDangNhap, matKhau, hoTen, gioiTinh, ngaySinh,
                    maBoPhan, vaiTro, tenXuong, diaChi, email, soDienThoai, trangThai
                ) VALUES (
                    '{$data['maNguoiDung']}',
                    '{$data['tenDangNhap']}',
                    '{$data['matKhau']}',
                    '{$data['hoTen']}',
                    '{$data['gioiTinh']}',
                    '{$data['ngaySinh']}',
                    '{$data['maBoPhan']}',
                    '{$data['vaiTro']}',
                    '{$data['tenXuong']}',
                    '{$data['diaChi']}',
                    '{$data['email']}',
                    '{$data['soDienThoai']}',
                    '{$data['trangThai']}'
                )";

        return $this->conn->query($sql);
    }

    // ===== Cập nhật nhân viên =====
    public function update($id, $data) {
        $id = $this->conn->real_escape_string($id);
        foreach ($data as $key => $value) {
            $data[$key] = $this->conn->real_escape_string($value);
        }

        $sql = "UPDATE nguoidung SET 
                    tenDangNhap='{$data['tenDangNhap']}',
                    hoTen='{$data['hoTen']}',
                    gioiTinh='{$data['gioiTinh']}',
                    ngaySinh='{$data['ngaySinh']}',
                    diaChi='{$data['diaChi']}',
                    soDienThoai='{$data['soDienThoai']}',
                    email='{$data['email']}',
                    vaiTro='{$data['vaiTro']}',
                    tenXuong='{$data['tenXuong']}',
                    trangThai='{$data['trangThai']}'
                WHERE maNguoiDung='$id'";

        $result = $this->conn->query($sql);

        if (!$result) {
            echo "<pre style='color:red;'>Lỗi SQL: " . $this->conn->error . "</pre>";
            echo "<pre style='color:gray;'>Câu lệnh: " . $sql . "</pre>";
        }

        return $result;
    }

    // ===== Xóa mềm (đổi trạng thái sang 'Ngung') =====
    public function delete($id) {
        $id = $this->conn->real_escape_string($id);
        $sql = "UPDATE nguoidung SET trangThai='Ngung' WHERE maNguoiDung='$id'";
        return $this->conn->query($sql);
    }

    /* =======================
       ✅ BỔ SUNG CHO MODULE CHẤM CÔNG & LƯƠNG
       ======================= */

    // --- Lấy 1 nhân viên theo mã (phục vụ phần lương, chấm công)
    public function findById($maNguoiDung) {
        $maNguoiDung = $this->conn->real_escape_string($maNguoiDung);
        $sql = "SELECT * FROM nguoidung WHERE maNguoiDung = '$maNguoiDung' LIMIT 1";
        $result = $this->conn->query($sql);
        return ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;
    }

    // --- Liệt kê công nhân theo xưởng (dành cho xưởng trưởng xem danh sách)
    public function listByXuong($tenXuong) {
        $tenXuong = $this->conn->real_escape_string($tenXuong);
        $sql = "SELECT maNguoiDung, hoTen, vaiTro, tenXuong, trangThai
                FROM nguoidung
                WHERE tenXuong = '$tenXuong' AND vaiTro = 'CongNhan' AND trangThai = 'HoatDong'
                ORDER BY hoTen ASC";
        $result = $this->conn->query($sql);
        $data = array();
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }
    public function getMaXuongTheoTen($tenXuong){
    $ten = $this->conn->real_escape_string($tenXuong);
    $sql = "SELECT maXuong FROM xuong WHERE tenXuong='$ten' LIMIT 1";
    $res = $this->conn->query($sql);
    if($res && $res->num_rows>0){ $r=$res->fetch_assoc(); return $r['maXuong']; }
    return '';
}

    
}
?>
