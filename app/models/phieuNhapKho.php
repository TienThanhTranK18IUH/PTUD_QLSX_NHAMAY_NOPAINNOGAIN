<?php
require_once dirname(__FILE__) . '/Database.php';

class PhieuNhapKho {

    private $db;

    public function __construct($db = null) {
        if ($db !== null) {
            $this->db = $db;
        } else {
            $this->db = new Database();
        }
    }

    /* ================= PHIẾU ================= */

    public function getAllPhieu() {
        $sql = "
            SELECT 
                pnk.*,
                tp.tenTP,
                k.tenKho
            FROM PhieuNhapKhoTP pnk
            JOIN ThanhPham tp ON pnk.maTP = tp.maTP
            JOIN Kho k ON pnk.maKho = k.maKho
            ORDER BY pnk.ngayNhap DESC
        ";
        return $this->db->query($sql);
    }

    // SINH MÃ PHIẾU
    public function getNextMaPhieu() {
    $sql = "SELECT MAX(maPhieu) AS maxMa FROM PhieuNhapKhoTP";
    $data = $this->db->query($sql);

    if (!empty($data) && !empty($data[0]['maxMa'])) {
        // Lấy số từ ký tự thứ 5 trở đi (PNTP001 -> '001')
        $num = intval(substr($data[0]['maxMa'], 4)) + 1;
        return 'PNTP' . str_pad($num, 3, '0', STR_PAD_LEFT);
    }
    // Nếu chưa có phiếu nào, bắt đầu từ PNTP001
    return 'PNTP001';
}

    /* ================= THÀNH PHẨM ================= */

    public function getThanhPhamDatChuan() {
        $sql = "
            SELECT 
                tp.maTP,
                tp.tenTP,
                kt.SL_DatChuan AS soLuong
            FROM ThanhPham tp
            JOIN PhieuKiemTraThanhPham kt ON tp.maTP = kt.maTP
            LEFT JOIN PhieuNhapKhoTP pn ON tp.maTP = pn.maTP
            WHERE kt.ketQua = 'Đạt'
              AND pn.maTP IS NULL
        ";
        return $this->db->query($sql);
    }

    public function checkExistTP($maTP) {
        $sql = "SELECT maTP FROM PhieuNhapKhoTP WHERE maTP = '$maTP'";
        $data = $this->db->query($sql);
        return !empty($data);
    }

    /* ================= KHO ================= */

    public function getAllKho() {
        $sql = "SELECT maKho, tenKho FROM Kho WHERE loaiKho = 'ThanhPham'";
        return $this->db->query($sql);
    }

    /* ================= LƯU PHIẾU ================= */

    public function create($data) {
        $sql = "
            INSERT INTO PhieuNhapKhoTP (
                maPhieu,
                maTP,
                maKho,
                soLuong,
                ngayNhap,
                maNguoiLap,
                trangThai
            ) VALUES (
                '{$data['maPhieu']}',
                '{$data['maTP']}',
                '{$data['maKho']}',
                {$data['soLuong']},
                '{$data['ngayNhap']}',
                '{$data['maNguoiLap']}',
                '{$data['trangThai']}'
            )
        ";

        return $this->db->conn->query($sql);
    }
}
?>
