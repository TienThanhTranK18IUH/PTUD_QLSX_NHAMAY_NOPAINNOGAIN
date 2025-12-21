<?php
require_once dirname(__FILE__).'/database.php';

class DoiCa {
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    // Sinh mã lịch làm mới
    public function sinhMaLichLam(){
        $res = $this->db->query("SELECT maLichLam FROM lichlamviec ORDER BY maLichLam DESC LIMIT 1");
        if(count($res)>0){
            $last = substr($res[0]['maLichLam'],2);
            $new = intval($last)+1;
            return 'LL'.str_pad($new,3,'0',STR_PAD_LEFT);
        }
        return 'LL001';
    }

    // Tạo ca mới
    public function taoCaMoi($maNguoiDung,$ngayLam,$maCa,$maXuong){
        $maLichLam = $this->sinhMaLichLam();
        $today = date('Y-m-d');

        if($ngayLam<$today){
            return array('success'=>false,'message'=>'Ngày làm phải từ hôm nay trở đi.');
        }

        // Kiểm tra trùng ca
        $resDup = $this->db->query("SELECT * FROM lichlamviec WHERE maNguoiDung='$maNguoiDung' AND ngayLam='$ngayLam' AND maCa='$maCa'");
        if(count($resDup)>0){
            return array('success'=>false,'message'=>'Đã tồn tại lịch làm cho nhân viên này với ca này.');
        }

        // Thực hiện INSERT trực tiếp
        $sql = "INSERT INTO lichlamviec(maLichLam, maNguoiDung, ngayLam, maCa, maXuong)
                VALUES ('$maLichLam','$maNguoiDung','$ngayLam','$maCa','$maXuong')";

        if($this->db->conn->query($sql)===TRUE){
            return array('success'=>true,'message'=>'Tạo ca mới thành công', 'maLichLam'=>$maLichLam);
        } else {
            return array('success'=>false,'message'=>'Không thể lưu dữ liệu. Lỗi: '.$this->db->conn->error);
        }
    }

    // Lấy danh sách công nhân
    public function getCongNhan(){
        $sql = "SELECT DISTINCT maNguoiDung, tenCongNhan FROM chamcong ORDER BY maNguoiDung";
        return $this->db->query($sql);
    }

    // Lấy danh sách ca
    public function getDanhSachCa(){
        $sql = "SELECT maCa, thoiGianBatDau, thoiGianKetThuc FROM calamviec ORDER BY maCa";
        return $this->db->query($sql);
    }

    // Lấy danh sách xưởng
    public function getDanhSachXuong(){
        $sql = "SELECT maXuong, tenXuong FROM xuong ORDER BY maXuong";
        return $this->db->query($sql);
    }

    // Lấy xưởng của nhân viên (theo lịch mới nhất)
    public function getXuongNV($maNguoiDung){
        $res = $this->db->query("SELECT maXuong FROM lichlamviec WHERE maNguoiDung='$maNguoiDung' ORDER BY ngayLam DESC LIMIT 1");
        return count($res)>0 ? $res[0]['maXuong'] : null;
    }

    // Lấy lịch làm nhân viên từ hôm nay trở đi
    public function getLichLam($maNguoiDung){
        $sql = "SELECT maLichLam, ngayLam, maCa, maXuong FROM lichlamviec
                WHERE maNguoiDung='$maNguoiDung' AND ngayLam>=CURDATE()
                ORDER BY ngayLam, maCa";
        return $this->db->query($sql);
    }

    // Đổi ca
    public function doiCa($maLichLam, $maCaMoi){
        $res = $this->db->query("SELECT * FROM lichlamviec WHERE maLichLam='$maLichLam' LIMIT 1");
        if(count($res)==0) return array('success'=>false,'message'=>'Mã lịch làm không tồn tại.');
        $ll = $res[0];

        if($ll['ngayLam']<date('Y-m-d')){
            return array('success'=>false,'message'=>'Đổi ca thất bại: Ca làm việc đã qua ngày hiện tại.');
        }

        $oldCa = $ll['maCa'];
        $sqlUp = "UPDATE lichlamviec SET maCa='$maCaMoi' WHERE maLichLam='$maLichLam'";
        if($this->db->conn->query($sqlUp)===TRUE){
            return array('success'=>true,'message'=>'Đổi ca thành công: '.$oldCa.' → '.$maCaMoi);
        } else {
            return array('success'=>false,'message'=>'Không thể lưu dữ liệu. Lỗi: '.$this->db->conn->error);
        }
    }
}
?>
