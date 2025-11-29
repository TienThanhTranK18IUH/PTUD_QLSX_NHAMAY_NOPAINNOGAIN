<?php
require_once dirname(__FILE__).'/../helpers/auth.php';

class GhiNhanThanhPhamController {
    private $db;
    public function __construct($db){
        $this->db = $db;
    }

    public function index(){
        requireRole(array('manager','leader'));

        // Lấy danh sách kế hoạch chưa ghi nhận
        $keHoachList = $this->db->query("
            SELECT kh.maKeHoach, kh.maXuong
            FROM kehoachsanxuat kh
            LEFT JOIN thanhpham tp ON kh.maKeHoach = tp.maKeHoach
            WHERE tp.maKeHoach IS NULL
        ");

        // Sinh mã TP tự động
        $lastTP = $this->db->query("SELECT maTP FROM thanhpham ORDER BY maTP DESC LIMIT 1");
        $lastCode = (!empty($lastTP)) ? substr($lastTP[0]['maTP'],2) : 0;
        $maTP = 'TP'.str_pad($lastCode+1,3,'0',STR_PAD_LEFT);

        // Check popup
        $success = isset($_GET['success']) ? true : false;
        $tenTP = isset($_GET['tenTP']) ? $_GET['tenTP'] : '';

        include 'app/views/thanhpham/ghinhanthanhpham.php';
    }

    public function save(){
        requireRole(array('manager','leader'));

        $maTP = $_POST['maTP'];
        $tenTP = $_POST['tenTP'];
        $soLuong = $_POST['soLuong'];
        $tinhTrang = $_POST['tinhTrang'];
        $maKeHoach = $_POST['maKeHoach'];
        $maKho = $_POST['maKho'];
        $tenKho = $_POST['tenKho'];
        $maXuong = $_POST['maXuong'];

        $sql = "INSERT INTO thanhpham(maTP, tenTP, soLuong, tinhTrang, maKeHoach, maKho, tenKho, maXuong)
                VALUES ('$maTP','$tenTP','$soLuong','$tinhTrang','$maKeHoach','$maKho','$tenKho','$maXuong')";
        $result = $this->db->conn->query($sql);

        if($result){
            // Redirect về index kèm thông tin popup
            header("Location: index.php?controller=ghinhanthanhpham&action=index&success=1&tenTP=".urlencode($tenTP));
            exit;
        } else {
            echo "<script>alert('Ghi nhận thất bại! Lỗi: ".$this->db->conn->error."');window.history.back();</script>";
            exit;
        }
    }

    // Lấy tên TP theo mã kế hoạch
    public function getTenThanhPham(){
        requireRole(array('manager','leader'));
        $maKH = $_POST['maKeHoach'];
        $sql = "SELECT dh.tenSP 
                FROM donhang dh
                INNER JOIN kehoachsanxuat kh ON kh.maDonHang = dh.maDonHang
                WHERE kh.maKeHoach = '$maKH' LIMIT 1";
        $result = $this->db->query($sql);
        $tenSP = !empty($result) ? $result[0]['tenSP'] : '';
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('tenSP'=>$tenSP));
        exit;
    }
}
?>