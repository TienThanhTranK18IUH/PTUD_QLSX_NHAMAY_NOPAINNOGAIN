<?php
require_once dirname(__FILE__) . '/Database.php';

class PhieuXuatKhoTP {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->conn;
    }

    // Lấy tất cả phiếu xuất
    public function getAll() {
        $sql = "SELECT p.*, t.tenTP, k.tenKho, nd.hoTen AS nguoiLap
                FROM PhieuXuatKhoTP p
                LEFT JOIN ThanhPham t ON p.maTP = t.maTP
                LEFT JOIN Kho k ON p.maKho = k.maKho
                LEFT JOIN NguoiDung nd ON p.maNguoiLap = nd.maNguoiDung
                ORDER BY p.ngayXuat DESC";
        return $this->db->query($sql);
    }

    // Sinh mã phiếu mới PXTP001, PXTP002,...
    public function getNextMaPhieu() {
        $sql = "SELECT maPhieu FROM PhieuXuatKhoTP ORDER BY maPhieu DESC LIMIT 1";
        $rows = $this->db->query($sql);
        if (!empty($rows)) {
            $last = $rows[0]['maPhieu'];
            $num = (int)substr($last, 4) + 1;
            return 'PXTP' . str_pad($num, 3, '0', STR_PAD_LEFT);
        }
        return 'PXTP001';
    }

    // Kiểm tra xem mã phiếu đã tồn tại
    public function checkExistMaPhieu($maPhieu) {
        if (!isset($maPhieu) || $maPhieu === '') {
            return false;
        }
        $stmt = $this->conn->prepare("SELECT maPhieu FROM PhieuXuatKhoTP WHERE maPhieu = ?");
        if (!$stmt) return false;
        $stmt->bind_param('s', $maPhieu);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    // Lấy thông tin đơn hàng
    public function getDonHang($maDonHang) {
        if (!isset($maDonHang) || $maDonHang === '') {
            return null;
        }
        $stmt = $this->conn->prepare("SELECT * FROM DonHang WHERE maDonHang = ?");
        if (!$stmt) return null;
        $stmt->bind_param('s', $maDonHang);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 0) {
            $stmt->close();
            return null;
        }
        // Get column names from result metadata
        $meta = $stmt->result_metadata();
        $fields = array();
        $data = array();
        while ($field = $meta->fetch_field()) {
            $fields[] = &$data[$field->name];
        }
        call_user_func_array(array($stmt, 'bind_result'), $fields);
        $stmt->fetch();
        $stmt->close();
        return $data;
    }

    // Lấy thông tin thành phẩm
    public function getThanhPham($maTP) {
        if (!isset($maTP) || $maTP === '') {
            return null;
        }
        $stmt = $this->conn->prepare("SELECT * FROM ThanhPham WHERE maTP = ?");
        if (!$stmt) return null;
        $stmt->bind_param('s', $maTP);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 0) {
            $stmt->close();
            return null;
        }
        // Get column names from result metadata
        $meta = $stmt->result_metadata();
        $fields = array();
        $data = array();
        while ($field = $meta->fetch_field()) {
            $fields[] = &$data[$field->name];
        }
        call_user_func_array(array($stmt, 'bind_result'), $fields);
        $stmt->fetch();
        $stmt->close();
        return $data;
    }

    // Kiểm tra đơn hàng đã lập phiếu chưa
    public function checkExistDonHang($maDonHang) {
        if (!isset($maDonHang) || $maDonHang === '') {
            return false;
        }
        $stmt = $this->conn->prepare("SELECT maPhieu FROM PhieuXuatKhoTP WHERE maDonHang = ?");
        if (!$stmt) return false;
        $stmt->bind_param('s', $maDonHang);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    // Lưu phiếu xuất
    public function create($data) {
        if (!isset($data) || !is_array($data)) {
            throw new InvalidArgumentException('create() requires an array $data');
        }

        $required = array('maPhieu','maKho','ngayXuat','maNguoiLap','maDonHang','maTP','soLuong');
        foreach ($required as $key) {
            if (!isset($data[$key]) || $data[$key] === '') {
                throw new InvalidArgumentException("Missing required field: $key");
            }
        }

        $stmt = $this->conn->prepare("INSERT INTO PhieuXuatKhoTP 
            (maPhieu, maKho, ngayXuat, maNguoiLap, maDonHang, maTP, soLuong) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) throw new RuntimeException('Prepare failed: ' . $this->conn->error);

        $soLuong = (int)$data['soLuong'];
        $stmt->bind_param('ssssssi',
            $data['maPhieu'], $data['maKho'], $data['ngayXuat'],
            $data['maNguoiLap'], $data['maDonHang'], $data['maTP'], $soLuong
        );
        $ok = $stmt->execute();
        if (!$ok) throw new RuntimeException('Insert failed: ' . $stmt->error);
        $stmt->close();
        return $ok;
    }

    // Cập nhật tồn kho
    public function updateSoLuongTP($maTP, $soLuongXuat) {
        if (!isset($maTP) || $maTP === '') {
            throw new InvalidArgumentException('maTP is required');
        }
        if (!isset($soLuongXuat) || !is_numeric($soLuongXuat)) {
            throw new InvalidArgumentException('soLuongXuat must be numeric');
        }

        $stmt = $this->conn->prepare("UPDATE ThanhPham SET soLuong = soLuong - ? WHERE maTP = ?");
        if (!$stmt) throw new RuntimeException('Prepare failed: ' . $this->conn->error);

        $soLuongXuat = (int)$soLuongXuat;
        $stmt->bind_param('is', $soLuongXuat, $maTP);
        $ok = $stmt->execute();
        if (!$ok) throw new RuntimeException('Update failed: ' . $stmt->error);
        $stmt->close();
        return $ok;
    }

    // Lấy tất cả kho
    public function getAllKho() {
        return $this->db->query("SELECT * FROM Kho");
    }

    // Lấy tất cả thành phẩm
    public function getAllThanhPham() {
        return $this->db->query("SELECT * FROM ThanhPham");
    }

    // Lấy danh sách đơn hàng kèm tên thành phẩm
    public function getAllDonHangWithTP() {
        $sql = "SELECT dh.maDonHang, dh.maTP, dh.soLuong AS soLuongDH, tp.tenTP
                FROM DonHang dh
                LEFT JOIN ThanhPham tp ON dh.maTP = tp.maTP
                ORDER BY dh.maDonHang ASC";
        return $this->db->query($sql);
    }
}
?>