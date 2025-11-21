<?php
// app/controllers/sanXuatController.php — PHP 5.x
require_once dirname(__FILE__).'/../models/database.php';
require_once dirname(__FILE__).'/../models/chamCong.php';  // <— sửa C thường
require_once dirname(__FILE__).'/../models/nhanVien.php';
require_once dirname(__FILE__) . '/../helpers/auth.php';

class SanXuatController {
    private $db,$conn,$mChamCong,$mNhanVien;

    public function __construct(){
        if (session_id()==='') @session_start();
        $this->db = new Database();
        $this->mChamCong = new ChamCong($this->db);
        $this->mNhanVien = new NhanVien();
        if (property_exists($this->db,'conn')) $this->conn=$this->db->conn;
    }

    private function redirect($url){
        if (!headers_sent()) header('Location: '.$url);
        else { echo '<script>location.href="'.htmlspecialchars($url,ENT_QUOTES,'UTF-8').'";</script>';
               echo '<noscript><meta http-equiv="refresh" content="0;url='.$url.'"></noscript>'; }
        exit;
    }

    private function getMaXuongByTen($tenXuong){
        if (!$this->conn || $tenXuong==='') return '';
        $ten=$this->conn->real_escape_string($tenXuong);
        $rs=$this->conn->query("SELECT maXuong FROM xuong WHERE tenXuong='$ten' LIMIT 1");
        if($rs && $rs->num_rows>0){ $r=$rs->fetch_assoc(); return $r['maXuong']; }
        return '';
    }

    public function ghinhan(){
        requireRole(array('manager','leader'));
        $xt=array('maNguoiDung'=>'ND001','hoTen'=>'Xuong Truong','vaiTro'=>'XuongTruong','tenXuong'=>'');
        if ($this->conn){
            $rs=$this->conn->query("SELECT maNguoiDung,hoTen,vaiTro,tenXuong
                                    FROM nguoidung
                                    WHERE vaiTro='XuongTruong' AND trangThai='HoatDong'
                                    ORDER BY maNguoiDung ASC LIMIT 1");
            if($rs && $rs->num_rows>0) $xt=$rs->fetch_assoc();
        }
        $maXuong=$this->getMaXuongByTen(isset($xt['tenXuong'])?$xt['tenXuong']:'');

        $dsCongNhan=$this->mNhanVien->listByXuong(isset($xt['tenXuong'])?$xt['tenXuong']:'');
        $dsCa=$this->mChamCong->getDanhSachCa();
        $CA_MAP=array();
        foreach($dsCa as $row){
            $CA_MAP[$row['maCa']]=array(substr($row['thoiGianBatDau'],0,5), substr($row['thoiGianKetThuc'],0,5));
        }

        $GLOBALS['XT']=array('maNguoiDung'=>$xt['maNguoiDung'],'hoTen'=>$xt['hoTen'],
                             'tenXuong'=>isset($xt['tenXuong'])?$xt['tenXuong']:'','maXuong'=>$maXuong);
        $GLOBALS['dsCongNhan']=$dsCongNhan;
        $GLOBALS['dsCa']=$dsCa;
        $GLOBALS['CA_MAP']=$CA_MAP;
        $GLOBALS['notice_ok']=isset($_GET['ok'])?$_GET['ok']:'';
        include dirname(__FILE__).'/../views/sanxuat/ghinhan.php';
    }

    public function save(){
        requireRole(array('manager','leader'));
        $maCN = isset($_POST['maNguoiDung'])?$_POST['maNguoiDung']:'';
        $tenCN=''; if ($maCN!=='' && $this->conn){
            $esc=$this->conn->real_escape_string($maCN);
            $r=$this->conn->query("SELECT hoTen FROM nguoidung WHERE maNguoiDung='$esc' LIMIT 1");
            if($r && $r->num_rows>0){ $row=$r->fetch_assoc(); $tenCN=$row['hoTen']; }
        }
        $xt=array('tenXuong'=>''); if($this->conn){
            $r=$this->conn->query("SELECT tenXuong FROM nguoidung WHERE vaiTro='XuongTruong' AND trangThai='HoatDong' ORDER BY maNguoiDung ASC LIMIT 1");
            if($r && $r->num_rows>0) $xt=$r->fetch_assoc();
        }
        $maXuong=$this->getMaXuongByTen(isset($xt['tenXuong'])?$xt['tenXuong']:'');

        $data=array(
            'maNguoiDung'=>$maCN,'tenCongNhan'=>$tenCN,'maXuong'=>$maXuong,
            'ngayCham'=>isset($_POST['ngayCham'])?$_POST['ngayCham']:'',
            'gioVao'=>isset($_POST['gioVao'])?$_POST['gioVao']:'',
            'gioRa' =>isset($_POST['gioRa']) ?$_POST['gioRa'] :'',
            'soGioLam'=>'',
            'loaiNgay'=>isset($_POST['loaiNgay'])?$_POST['loaiNgay']:'NgayThuong',
            'maCa'=>isset($_POST['maCa'])?$_POST['maCa']:'',
            'sanLuongHoanThanh'=>isset($_POST['sanLuongHoanThanh'])?$_POST['sanLuongHoanThanh']:0
        );

        if ($data['maNguoiDung']==='' || $data['ngayCham']===''){
            $_SESSION['err']='Vui lòng chọn Công nhân và Ngày chấm công.'; $this->redirect('index.php?controller=sanxuat&action=ghinhan');
        }
        if ($this->mChamCong->create($data)) $this->redirect('index.php?controller=sanxuat&action=ghinhan&ok=1');
        $_SESSION['err']='Lưu thất bại. Vui lòng thử lại.'; $this->redirect('index.php?controller=sanxuat&action=ghinhan');
    }
}
