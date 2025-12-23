<?php
require_once dirname(__FILE__) . '/../../config/config.php';

class KeHoachSanXuat {
    private $conn;

    public function __construct() {
        $this->conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$this->conn) {
            die('Kết nối CSDL thất bại: ' . mysqli_connect_error());
        }
        mysqli_set_charset($this->conn, 'utf8');
    }
public function getConnection() {
    return $this->conn;
}
    // Lấy danh sách tất cả kế hoạch
    public function getAll() {
        $sql = "SELECT 
                    kh.maKeHoach,
                    x.tenXuong,
                    dh.tenSP,
                    kh.maDonHang,
                    kh.ngayBatDau,
                    kh.ngayKetThuc,
                    kh.tongSoLuong,
                    kh.trangThai,
                    kh.maNguyenLieu,
                    kh.tenNguyenLieu,
                    kh.soLuongNguyenLieu,
                    kh.nguoiLap
                FROM KeHoachSanXuat kh
                JOIN Xuong x ON kh.maXuong = x.maXuong
                JOIN DonHang dh ON kh.maDonHang = dh.maDonHang
                 ORDER BY kh.maDonHang asc";


        $result = mysqli_query($this->conn, $sql);
        if (!$result) {
            echo "<p style='color:red'>Lỗi SQL: " . mysqli_error($this->conn) . "</p>";
            return array();
        }
        $data = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }
    // Lấy tất cả xưởng
public function getAllXuongs() {
    $result = mysqli_query($this->conn, "SELECT maXuong, tenXuong FROM xuong");
    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}
public function countByOrder($maDonHang) {
    $maDonHang = mysqli_real_escape_string($this->conn, $maDonHang);
    $sql = "SELECT COUNT(*) AS total FROM kehoachsanxuat WHERE maDonHang = '$maDonHang'";
    $result = mysqli_query($this->conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return intval($row['total']);
}
// Lấy tất cả sản phẩm
public function getAllSanPhams() {
    $result = mysqli_query($this->conn, "SELECT maSP, tenSP FROM donhang");
    $data =array();
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// Lấy tất cả đơn hàng
public function getAllDonHangs() {
    $result = mysqli_query($this->conn, "SELECT maDonHang FROM donhang");
    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}
public function getAllNguyenLieus() {
    $nguyenlieus = array();
    $sql = "SELECT maNguyenLieu, tenNguyenLieu FROM nguyenlieu ORDER BY tenNguyenLieu ASC";
    $result = mysqli_query($this->conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $nguyenlieus[] = $row;
        }
    }

    return $nguyenlieus;
}
public function updateKeHoach($data) {

    $conn = $this->conn;

    // Escape dữ liệu
    $maKH = mysqli_real_escape_string($conn, $data['maKeHoach']);
    $maXuong = mysqli_real_escape_string($conn, $data['maXuong']);
    $maDonHang = mysqli_real_escape_string($conn, $data['maDonHang']);
    $tenSP = mysqli_real_escape_string($conn, $data['tenSP']);
    $ngayBatDau = mysqli_real_escape_string($conn, $data['ngayBatDau']);
    $ngayKetThuc = mysqli_real_escape_string($conn, $data['ngayKetThuc']);
    $tongSoLuong = intval($data['tongSoLuong']);
    $maNguyenLieu = mysqli_real_escape_string($conn, $data['maNguyenLieu']);
    $tenNguyenLieu = mysqli_real_escape_string($conn, $data['tenNguyenLieu']);
    $soLuongNguyenLieu = intval($data['soLuongNguyenLieu']);
    $trangThai = mysqli_real_escape_string($conn, $data['trangThai']);
    $nguoiLap = isset($data['nguoiLap'])
        ? mysqli_real_escape_string($conn, $data['nguoiLap'])
        : '';

    /* ==========================
       1. Update tên sản phẩm
    ========================== */
    $sqlSP = "UPDATE donhang 
              SET tenSP='$tenSP'
              WHERE maDonHang='$maDonHang'";
    mysqli_query($conn, $sqlSP);

    /* ==========================
       2. Update kế hoạch SX
    ========================== */
    $sqlKH = "UPDATE kehoachsanxuat SET
                maXuong='$maXuong',
                maDonHang='$maDonHang',
                ngayBatDau='$ngayBatDau',
                ngayKetThuc='$ngayKetThuc',
                tongSoLuong=$tongSoLuong,
                maNguyenLieu='$maNguyenLieu',
                tenNguyenLieu='$tenNguyenLieu',
                soLuongNguyenLieu=$soLuongNguyenLieu,
                trangThai='$trangThai',
                nguoiLap='$nguoiLap'
              WHERE maKeHoach='$maKH'";

    if (!mysqli_query($conn, $sqlKH)) {
        throw new Exception("Lỗi cập nhật kế hoạch: " . mysqli_error($conn));
    }

    return true;
}
    public function checkDuplicatePlan($data) {
        $conn = $this->conn;
        $maDonHang = mysqli_real_escape_string($conn, $data['maDonHang']);
        $maXuong = mysqli_real_escape_string($conn, $data['maXuong']);
        $sql = "SELECT * FROM kehoachsanxuat WHERE maDonHang='$maDonHang' AND maXuong='$maXuong'";
        $result = mysqli_query($conn, $sql);
        return ($result && mysqli_num_rows($result) > 0);
    }

// Lấy danh sách đơn hàng chưa lập kế hoạch
public function getPendingOrders() {
    $sql = "
        SELECT dh.*
        FROM donhang dh
        LEFT JOIN (
            SELECT maDonHang, COUNT(*) AS soKH
            FROM kehoachsanxuat
            GROUP BY maDonHang
        ) kh ON dh.maDonHang = kh.maDonHang
        WHERE kh.soKH IS NULL OR kh.soKH < 2
    ";

    $res = mysqli_query($this->conn, $sql);
    $rows = array();
    while ($row = mysqli_fetch_assoc($res)) {
        $rows[] = $row;
    }
    return $rows;
}

public function generateMaKeHoach() {
    $conn = $this->conn;
    $sql = "SELECT maKeHoach FROM kehoachsanxuat ORDER BY maKeHoach DESC LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $newID = "KH001";

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastID = intval(substr($row['maKeHoach'], 2)) + 1;
        $newID = "KH" . str_pad($lastID, 3, "0", STR_PAD_LEFT);
    }
    return $newID;
}
public function createPlan($data) {
    $conn = $this->conn;

    // --- Sinh mã kế hoạch tự động ---
    $maKeHoach = $this->generateMaKeHoach();

    $maDonHang   = mysqli_real_escape_string($conn, $data['maDonHang']);
    $maXuong     = mysqli_real_escape_string($conn, $data['maXuong']);
    $ngayBatDau  = mysqli_real_escape_string($conn, $data['ngayBatDau']);
    $ngayKetThuc = mysqli_real_escape_string($conn, $data['ngayKetThuc']);
    $tongSoLuong = (int)$data['tongSoLuong'];
    $trangThai   = mysqli_real_escape_string($conn, $data['trangThai']);
    $nguoiLap   = mysqli_real_escape_string($conn, $data['nguoiLap']);

    // --- Kiểm tra mảng nguyên liệu ---
    $maNLs  = $data['maNguyenLieu'];
    $tenNLs = $data['tenNguyenLieu'];
    $soLuongNLs = $data['soLuongNguyenLieu'];

    // --- Nếu là mảng, chèn từng nguyên liệu ---
    for ($i = 0; $i < count($maNLs); $i++) {
        $maNL  = mysqli_real_escape_string($conn, $maNLs[$i]);
        $tenNL = mysqli_real_escape_string($conn, $tenNLs[$i]);
        $soLuongNL = (int)$soLuongNLs[$i];

        $sql = "INSERT INTO kehoachsanxuat (
                    maKeHoach, maDonHang, maXuong, ngayBatDau, ngayKetThuc,
                    tongSoLuong, trangThai, maNguyenLieu, tenNguyenLieu, soLuongNguyenLieu,nguoiLap
                ) VALUES (
                    '$maKeHoach', '$maDonHang', '$maXuong', '$ngayBatDau', '$ngayKetThuc',
                    $tongSoLuong, '$trangThai', '$maNL', '$tenNL', $soLuongNL,'$nguoiLap'
                )";
        mysqli_query($conn, $sql);
    }

    return true;
}

public function updateOrderStatus($maDonHang, $newStatus) {
    $conn = $this->conn;
    $sql = "UPDATE donhang SET tinhTrang = '$newStatus' WHERE maDonHang = '$maDonHang'";
    return mysqli_query($conn, $sql);
}
}
?>