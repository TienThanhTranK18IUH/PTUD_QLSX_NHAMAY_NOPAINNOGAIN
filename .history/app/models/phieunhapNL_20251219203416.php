<?php declare(strict_types=1);
// Model: PhieuNhapNL.php
// Đồng bộ với Database.php, tương thích PHP 5.2 — tất cả table chữ thường

class PhieuNhapNL {
    var $db;

    function __construct($db) {
        $this->db = $db;
    }

    // -------------------------------
    // Helper: thực thi INSERT/UPDATE/DELETE
    // -------------------------------
    function exec($sql) {
        if (is_object($this->db) && method_exists($this->db, 'getConnection')) {
            $conn = $this->db->getConnection();
        } else {
            if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
                $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                if ($conn->connect_error) {
                    echo '<pre>Kết nối fallback thất bại: ' . $conn->connect_error . '</pre>';
                    return false;
                }
                $conn->set_charset(defined('DB_CHARSET') ? DB_CHARSET : 'utf8');
                $closeAfter = true;
            } else {
                echo '<pre>Không thể lấy kết nối để thực thi câu lệnh.</pre>';
                return false;
            }
        }

        $res = $conn->query($sql);
        if ($res === false) {
            echo '<pre>Lỗi SQL: ' . $conn->error . "\nSQL: " . $sql . '</pre>';
            if (isset($closeAfter) && $closeAfter) $conn->close();
            return false;
        }
        if (isset($closeAfter) && $closeAfter) $conn->close();
        return true;
    }

    // -------------------------------
    // Lấy danh sách nguyên liệu
    // -------------------------------
    function layDanhSachNguyenLieu() {
        $sql = "SELECT k.makehoach, k.manguyenlieu, k.tennguyenlieu, n.soluongton, n.makho, k.soluongnguyenlieu 
                FROM nguyenlieu n 
                LEFT JOIN kehoachsanxuat k ON n.manguyenlieu = k.manguyenlieu";
        return $this->db->query($sql);
    }

    // -------------------------------
    // Lấy danh sách nhân viên kho
    // -------------------------------
    function layDanhSachNhanVienKho() {
        $sql = "SELECT maNguoiDung, hoTen 
                FROM nguoidung 
                WHERE vaiTro='NhanVienKho' AND trangThai='HoatDong'";
        return $this->db->query($sql);
    }

    // -------------------------------
    // Lưu phiếu nhập
    // -------------------------------
    function luuPhieuNhap($maKho, $ngayNhap, $maNguoiLap, $trangThai, $maNguyenLieu, $maKeHoach, $soLuong, $soLuongTonKho) {
    $conn = $this->db->conn;
    if (!$conn) {
        echo '<pre>Không thể kết nối DB</pre>';
        return false;
    }

    $maKho = $conn->real_escape_string($maKho);
    $ngayNhap = $conn->real_escape_string($ngayNhap);
    $maNguoiLap = $conn->real_escape_string($maNguoiLap);
    $trangThai = $conn->real_escape_string($trangThai);
    $maNguyenLieu = $conn->real_escape_string($maNguyenLieu);
    $maKeHoach = $conn->real_escape_string($maKeHoach);
    $soLuong = intval($soLuong);
    $soLuongTonKho = intval($soLuongTonKho);

        $sql = "INSERT INTO phieunhapkhonl
            (makho, ngaynhap, maNguoiLap, trangthai, manguyenlieu, makehoach, soluong, soluongtonkho)
            VALUES
            ('{$maKho}', '{$ngayNhap}', '{$maNguoiLap}', '{$trangThai}', '{$maNguyenLieu}', '{$maKeHoach}', {$soLuong}, {$soLuongTonKho})";

    $res = $conn->query($sql);
    if (!$res) {
        echo '<pre>Lỗi SQL: '.$conn->error."\nSQL: ".$sql.'</pre>';
        return false;
    }

    // Cập nhật tồn kho
    $sql_update = "UPDATE nguyenlieu SET soluongton = soluongton + {$soLuong} WHERE manguyenlieu = '{$maNguyenLieu}'";
    $res2 = $conn->query($sql_update);
    if (!$res2) {
        echo '<pre>Lỗi SQL update: '.$conn->error.'</pre>';
        return false;
    }

    return true;
}


    // -------------------------------
    // Lấy danh sách phiếu nhập
    // -------------------------------
    function layDanhSachPhieuNhap() {
        $sql = "SELECT * FROM phieunhapkhonl ORDER BY ngaynhap DESC";
        return $this->db->query($sql);
    }
}
?>
