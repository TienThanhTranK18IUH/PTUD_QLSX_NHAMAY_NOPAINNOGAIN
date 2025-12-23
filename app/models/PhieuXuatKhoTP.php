<?php
require_once dirname(__FILE__) . '/Database.php';

class PhieuXuatKhoTP {
    private $db;

    public function __construct($db) {
        $this->db = $db; // mysqli
    }

    // ===============================
    // Thành phẩm đạt
    // ===============================
    public function getThanhPhamDat() {

    $sql = "SELECT tp.maTP, tp.tenTP, tp.maKeHoach, tp.maXuong, tp.soLuong
            FROM thanhpham tp
            WHERE tp.soLuong > 0
              AND EXISTS (
                  SELECT 1 FROM phieukiemtrathanhpham k
                  WHERE k.maTP = tp.maTP AND k.ketQua = 'Đạt'
              )";

    $result = $this->db->query($sql);
    $data = array();

    if ($result) {
        while ($row = $result->fetch_assoc()) {

            // 🔥 LẤY ĐƠN HÀNG THEO maTP
            $stmt = $this->db->prepare(
                "SELECT maDonHang, tenSP
                 FROM donhang
                 WHERE maTP = ?"
            );
            $stmt->bind_param("s", $row['maTP']);
            $stmt->execute();

            $maDonHang = '';
            $tenSP = '';
            $stmt->bind_result($maDonHang, $tenSP);

            $donhang = array();
            while ($stmt->fetch()) {
                $donhang[] = array(
                    'maDonHang' => $maDonHang,
                    'tenSP'     => $tenSP
                );
            }
            $stmt->close();

            // 🔥 GẮN DONHANG VÀO TP
            $row['donhang'] = $donhang;

            $data[] = $row;
        }
    }

    return $data;
}

    // ===============================
    // Danh sách phiếu xuất
    // ===============================
    public function getDanhSachPhieu() {
        $sql = "SELECT px.maPhieu, px.ngayXuat, px.maNguoiLap,
                       tp.tenTP, px.soLuong
                FROM phieuxuatkhotp px
                JOIN thanhpham tp ON px.maTP = tp.maTP
                ORDER BY px.ngayXuat DESC";

        $result = $this->db->query($sql);
        $data = array();

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // ===============================
    // Load đơn hàng theo mã TP
    // ===============================
    public function getDonHangByMaTP($maTP) {
        $sql = "SELECT maDonHang, tenSP
                FROM donhang
                WHERE maTP = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $maTP);
        $stmt->execute();

        $maDonHang = '';
        $tenSP = '';
        $stmt->bind_result($maDonHang, $tenSP);

        $data = array();
        while ($stmt->fetch()) {
            $data[] = array(
                'maDonHang' => $maDonHang,
                'tenSP'     => $tenSP
            );
        }

        $stmt->close();
        return $data;
    }

    // ===============================
    // Sinh mã phiếu
    // ===============================
    public function getNextMaPhieu() {
        $sql = "SELECT maPhieu
                FROM phieuxuatkhotp
                ORDER BY maPhieu DESC
                LIMIT 1";

        $result = $this->db->query($sql);
        $last = '';

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $last = $row['maPhieu'];
        }

        $num = $last ? ((int)substr($last, 4) + 1) : 1;
        return 'PXTP' . str_pad($num, 3, '0', STR_PAD_LEFT);
    }

    // ===============================
    // Lưu phiếu
    // ===============================
    public function insertPhieu($data) {
        $sql = "INSERT INTO phieuxuatkhotp
                (maPhieu, maKho, ngayXuat, maNguoiLap,
                 maDonHang, maTP, soLuong)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param(
            "ssssssi",
            $data['maPhieu'],
            $data['maKho'],
            $data['ngayXuat'],
            $data['maNguoiLap'],
            $data['maDonHang'],
            $data['maTP'],
            $data['soLuong']
        );
        $stmt->execute();
        $stmt->close();
    }

    // ===============================
    // Trừ tồn kho
    // ===============================
    public function truSoLuong($maTP, $soLuong) {
        $stmt = $this->db->prepare(
            "UPDATE thanhpham
             SET soLuong = soLuong - ?
             WHERE maTP = ?"
        );
        $stmt->bind_param("is", $soLuong, $maTP);
        $stmt->execute();
        $stmt->close();
    }
}
