<?php
require_once dirname(__FILE__) . '/Database.php';

class PhieuXuatKhoTP {
    private $db;

    public function __construct($db) {
        $this->db = $db; // $db là mysqli
    }

    // Lấy danh sách thành phẩm Đạt
    public function getThanhPhamDat() {
        $sql = "SELECT maTP, tenTP, maKeHoach, maXuong, soLuong
                FROM thanhpham
                WHERE tinhTrang = 'Đạt'";
        $result = $this->db->query($sql);
        $data = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // Lấy danh sách phiếu xuất kho
    public function getDanhSachPhieu() {
        $sql = "SELECT px.maPhieu, px.ngayXuat, px.maNguoiLap, tp.tenTP, px.soLuong
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

    // Sinh mã phiếu tự động PXTP001, PXTP002...
    public function getNextMaPhieu() {
        $sql = "SELECT maPhieu FROM phieuxuatkhotp ORDER BY maPhieu DESC LIMIT 1";
        $result = $this->db->query($sql);

        $last = '';
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $last = $row['maPhieu'];
        }

        if ($last) {
            $num = (int)substr($last, 4) + 1; // Lấy phần số của PXTPxxx
        } else {
            $num = 1;
        }

        return 'PXTP'.str_pad($num, 3, '0', STR_PAD_LEFT);
    }

    // Lưu phiếu
    public function insertPhieu($data) {
        $stmt = $this->db->prepare("INSERT INTO phieuxuatkhotp (maPhieu, maKho, ngayXuat, maNguoiLap, maTP, soLuong) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $data['maPhieu'], $data['maKho'], $data['ngayXuat'], $data['maNguoiLap'], $data['maTP'], $data['soLuong']);
        $stmt->execute();
        $stmt->close();
    }

    // Trừ số lượng tồn
    public function truSoLuong($maTP, $soLuong) {
        $stmt = $this->db->prepare("UPDATE thanhpham SET soLuong = soLuong - ? WHERE maTP = ?");
        $stmt->bind_param("is", $soLuong, $maTP);
        $stmt->execute();
        $stmt->close();
    }
}
