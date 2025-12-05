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

    private function getMaXuongByMaBoPhan($maBoPhan){
        if (!$this->conn || $maBoPhan==='') return '';
        $bp=$this->conn->real_escape_string($maBoPhan);
        // Tìm xưởng dựa trên bộ phận (vì bảng xuong có loaiXuong là BP001, BP002, ...)
        $rs=$this->conn->query("SELECT maXuong FROM xuong WHERE loaiXuong='$bp' LIMIT 1");
        if($rs && $rs->num_rows>0){ $r=$rs->fetch_assoc(); return $r['maXuong']; }
        // Fallback: tìm bằng tên xưởng nếu loaiXuong không có
        return '';
    }

    public function ghinhan(){
        requireRole(array('manager','leader'));
        $xt=array('maNguoiDung'=>'ND001','hoTen'=>'Xuong Truong','vaiTro'=>'XuongTruong','tenXuong'=>'','maBoPhan'=>'');
        
        // Lấy thông tin từ người dùng đang đăng nhập
        if (isset($_SESSION['user']) && !empty($_SESSION['user']['maNguoiDung']) && $this->conn){
            $maCurUser = $this->conn->real_escape_string($_SESSION['user']['maNguoiDung']);
            $rs=$this->conn->query("SELECT maNguoiDung,hoTen,vaiTro,tenXuong,maBoPhan
                                    FROM nguoidung
                                    WHERE maNguoiDung='{$maCurUser}' LIMIT 1");
            if($rs && $rs->num_rows>0) $xt=$rs->fetch_assoc();
        }
        // Fallback: nếu không có session, lấy XuongTruong đầu tiên
        if(empty($xt['tenXuong'])){
            if ($this->conn){
                $rs=$this->conn->query("SELECT maNguoiDung,hoTen,vaiTro,tenXuong,maBoPhan
                                        FROM nguoidung
                                        WHERE vaiTro='XuongTruong' AND trangThai='HoatDong'
                                        ORDER BY maNguoiDung ASC LIMIT 1");
                if($rs && $rs->num_rows>0) $xt=$rs->fetch_assoc();
            }
        }
        // Lấy maXuong từ tenXuong (fallback nếu maBoPhan không có)
        $maXuong = $this->getMaXuongByTen(isset($xt['tenXuong'])?$xt['tenXuong']:'');
        // Nếu không tìm thấy, thử lấy từ maBoPhan
        if($maXuong==='' && isset($xt['maBoPhan'])){
            $maXuong = $this->getMaXuongByMaBoPhan($xt['maBoPhan']);
        };

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
        // Chỉ set notice_ok nếu không có lỗi
        if (isset($_GET['ok']) && !isset($_SESSION['err'])) {
            $GLOBALS['notice_ok'] = $_GET['ok'];
        } else {
            $GLOBALS['notice_ok'] = '';
        }
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
        $xt=array('tenXuong'=>'','maBoPhan'=>'');
        
        // Lấy thông tin từ người dùng đang đăng nhập
        if (isset($_SESSION['user']) && !empty($_SESSION['user']['maNguoiDung']) && $this->conn){
            $maCurUser = $this->conn->real_escape_string($_SESSION['user']['maNguoiDung']);
            $r=$this->conn->query("SELECT tenXuong,maBoPhan FROM nguoidung WHERE maNguoiDung='{$maCurUser}' LIMIT 1");
            if($r && $r->num_rows>0) $xt=$r->fetch_assoc();
        }
        // Fallback: nếu không có session, lấy XuongTruong đầu tiên
        if(empty($xt['tenXuong'])){
            if($this->conn){
                $r=$this->conn->query("SELECT tenXuong,maBoPhan FROM nguoidung WHERE vaiTro='XuongTruong' AND trangThai='HoatDong' ORDER BY maNguoiDung ASC LIMIT 1");
                if($r && $r->num_rows>0) $xt=$r->fetch_assoc();
            }
        }
        // Lấy maXuong từ tenXuong (fallback nếu maBoPhan không có)
        $maXuong=$this->getMaXuongByTen(isset($xt['tenXuong'])?$xt['tenXuong']:'');
        // Nếu không tìm thấy, thử lấy từ maBoPhan
        if($maXuong==='' && isset($xt['maBoPhan'])){
            $maXuong = $this->getMaXuongByMaBoPhan($xt['maBoPhan']);
        };

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
            $_SESSION['err']='Vui lòng chọn Công nhân và Ngày chấm công.';
            // Hiển thị ngay view ghi nhận với thông báo lỗi (không redirect)
            $this->ghinhan();
            return;
        }
        // Kiểm tra trùng ca cùng ngày (immediate on-screen debug + error)
        if ($data['maCa']!=='' && $data['maNguoiDung']!=='' && $this->conn){
            $maNV=$this->conn->real_escape_string($data['maNguoiDung']);
            $ngay=$this->conn->real_escape_string($data['ngayCham']);
            $maCa=$this->conn->real_escape_string($data['maCa']);
            $q = "SELECT COUNT(*) AS cnt FROM chamcong WHERE maNguoiDung='{$maNV}' AND ngayCham='{$ngay}' AND maCa='{$maCa}'";
            $rs=$this->conn->query($q);
            if($rs){
                $row=$rs->fetch_assoc();
                $cnt = isset($row['cnt']) ? intval($row['cnt']) : 0;
                if($cnt>0){
                    // Hiển thị lỗi trực tiếp trên trang (không redirect)
                    echo '<div class="container my-4"><div class="form-shell">';
                    echo '<div class="alert alert-danger">⚠️ Công nhân này đã ghi nhận ca '.htmlspecialchars($data['maCa']).' vào ngày '.htmlspecialchars($data['ngayCham']).' rồi.</div>';
                    echo '<p><strong>DEBUG:</strong> maNguoiDung='.htmlspecialchars($maNV).', ngayCham='.htmlspecialchars($ngay).', maCa='.htmlspecialchars($maCa).', cnt='.$cnt.'</p>';
                    echo '<p><a href="index.php?controller=sanxuat&action=ghinhan">Quay lại</a></p>';
                    echo '</div></div>';
                    return;
                }
            } else {
                // Nếu query lỗi, hiện thông báo lỗi trên trang
                echo '<div class="container my-4"><div class="form-shell">';
                echo '<div class="alert alert-danger">Lỗi kiểm tra trùng ca: '.htmlspecialchars($this->conn->error).'</div>';
                echo '<p><a href="index.php?controller=sanxuat&action=ghinhan">Quay lại</a></p>';
                echo '</div></div>';
                return;
            }
        }
        // Nếu lưu thành công thì redirect với ok=1, nếu không thì báo lỗi
        if ($this->mChamCong->create($data)) {
            $this->redirect('index.php?controller=sanxuat&action=ghinhan&ok=1');
        } else {
            $_SESSION['err']='Lưu thất bại. Vui lòng thử lại.';
            // Hiển thị ngay view ghi nhận với thông báo lỗi (không redirect)
            $this->ghinhan();
            return;
        }
    }
}
