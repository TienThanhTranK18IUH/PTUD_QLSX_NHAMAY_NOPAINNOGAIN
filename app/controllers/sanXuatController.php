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



        // Role-aware: nếu user là quản lý (manager) thì cho phép chọn xưởng trưởng và lọc công nhân
        // Sử dụng session-based checkRole để tránh trường hợp col 'vaiTro' trong row bị trống/khác nhau
        $isManager = checkRole('manager');

        if ($isManager){
            $dsXuongTruong = array();
            if ($this->conn){
                // Lấy tất cả người dùng đang hoạt động và lọc bằng normalizeRole để nhận diện leader (xưởng trưởng)
                $q = "SELECT n.maNguoiDung, n.hoTen, n.tenXuong, n.vaiTro FROM nguoidung n WHERE n.trangThai='HoatDong' ORDER BY n.hoTen ASC";
                $rs=$this->conn->query($q);
                if($rs && $rs->num_rows>0){
                    while($r=$rs->fetch_assoc()){
                        if (normalizeRole(isset($r['vaiTro'])?$r['vaiTro']:'') === 'leader'){
                            // tìm mã xưởng nếu có
                            $r['maXuong'] = $this->getMaXuongByTen(isset($r['tenXuong'])?$r['tenXuong']:'');
                            $dsXuongTruong[] = $r;
                        }
                    }
                }

                // Fallback: nếu không tìm thấy bằng vai trò, thử lấy theo tenXuong (người có tenXuong được coi là xưởng trưởng)
                if (empty($dsXuongTruong)){
                    $rs2 = $this->conn->query("SELECT maNguoiDung, hoTen, tenXuong, vaiTro FROM nguoidung WHERE tenXuong<>'' AND trangThai='HoatDong' ORDER BY tenXuong, hoTen");
                    $seen = array();
                    if ($rs2 && $rs2->num_rows>0){
                        while($r2=$rs2->fetch_assoc()){
                            $tx = trim($r2['tenXuong']);
                            if ($tx==='' || isset($seen[$tx])) continue;
                            $seen[$tx]=true;
                            $r2['maXuong'] = $this->getMaXuongByTen($tx);
                            $r2['vaiTro'] = 'XuongTruong';
                            $dsXuongTruong[] = $r2;
                        }
                    }
                }

                // Diagnostic: if still empty, collect distinct vaiTro values and sample active users to help debugging
                if (empty($dsXuongTruong)){
                    $roles = array();
                    $sample = array();
                    $r3 = $this->conn->query("SELECT DISTINCT vaiTro FROM nguoidung WHERE trangThai='HoatDong'");
                    if ($r3){ while($row=$r3->fetch_assoc()){ $roles[] = $row['vaiTro']; } }
                    $r4 = $this->conn->query("SELECT maNguoiDung, hoTen, vaiTro, tenXuong FROM nguoidung WHERE trangThai='HoatDong' LIMIT 15");
                    if ($r4){ while($row=$r4->fetch_assoc()){ $sample[] = $row; } }
                    $GLOBALS['diagnostic_roles'] = $roles;
                    $GLOBALS['diagnostic_sample_users'] = $sample;
                }
            }

            // Chọn xưởng mặc định: nếu manager có tenXuong -> dùng, nếu không lấy xưởng đầu tiên
            $selectedTen = isset($xt['tenXuong']) && $xt['tenXuong'] ? $xt['tenXuong'] : (isset($dsXuongTruong[0]) ? $dsXuongTruong[0]['tenXuong'] : '');
            $selectedMaXuong = '';
            if ($selectedTen){
                $dsCongNhan = $this->mNhanVien->listByXuong($selectedTen);
                // tìm mã xưởng tương ứng
                foreach($dsXuongTruong as $s){ if ($s['tenXuong']===$selectedTen){ $selectedMaXuong = isset($s['maXuong'])?$s['maXuong']:''; break; } }
            } else {
                $dsCongNhan = array();
            }

            $GLOBALS['dsXuongTruong']=$dsXuongTruong;
            $GLOBALS['selectedTenXuong']=$selectedTen;
            $GLOBALS['selectedMaXuong']=$selectedMaXuong;
        } else {
            $GLOBALS['selectedTenXuong']=isset($xt['tenXuong'])?$xt['tenXuong']:'';
            $GLOBALS['selectedMaXuong']=$maXuong;
        }

        $GLOBALS['isManager']=$isManager;
        // allow cancel confirmation for leaders and managers
        $GLOBALS['canCancel'] = checkRole(array('manager','leader'));

        $GLOBALS['XT']=array('maNguoiDung'=>$xt['maNguoiDung'],'hoTen'=>$xt['hoTen'],'tenXuong'=>isset($xt['tenXuong'])?$xt['tenXuong']:'','maXuong'=>$maXuong);
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



    public function getWorkers(){
        // SUPPORT: accept POST requests for AJAX to avoid layout being included by front controller
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header('HTTP/1.1 405 Method Not Allowed');
            header('Content-Type: application/json; charset=utf-8', true, 405);
            echo json_encode(array('error'=>'method_not_allowed')); exit;
        }

        // Trả về JSON danh sách công nhân theo tenXuong (chỉ cho quản lý)
        if (!checkRole(array('manager'))){
            header('HTTP/1.1 403 Forbidden');
            header('Content-Type: application/json; charset=utf-8', true, 403);
            echo json_encode(array('error'=>'forbidden'));
            exit;
        }

        $ten = isset($_POST['tenXuong']) ? trim($_POST['tenXuong']) : '';
        if ($ten === ''){ header('Content-Type: application/json; charset=utf-8'); echo json_encode(array()); exit; }
        $tenEsc = $this->conn ? $this->conn->real_escape_string($ten) : $ten;
        $list = $this->mNhanVien->listByXuong($tenEsc);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($list);
        exit;
    }

    public function save(){
        requireRole(array('manager','leader'));
        // Detect AJAX requests from fetch (we set X-Requested-With)
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

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
        // Nếu user là manager và form gửi tenXuong -> dùng posted tenXuong (và validate công nhân thuộc xưởng đó)
        $roleNorm = normalizeRole(isset($xt['vaiTro']) ? $xt['vaiTro'] : '');
        if ($roleNorm === 'manager' && isset($_POST['tenXuong']) && trim($_POST['tenXuong'])!==''){
            $postedTen = $this->conn ? $this->conn->real_escape_string(trim($_POST['tenXuong'])) : trim($_POST['tenXuong']);
            $maXuong = $this->getMaXuongByTen($postedTen);
            // Nếu đã chọn công nhân, kiểm tra nó thuộc xưởng đó
            if ($maCN !== ''){
                $escCN = $this->conn->real_escape_string($maCN);
                $q = "SELECT COUNT(*) AS cnt FROM nguoidung WHERE maNguoiDung='{$escCN}' AND tenXuong='{$postedTen}' AND vaiTro='CongNhan' AND trangThai='HoatDong'";
                $r = $this->conn->query($q);
                $cnt = 0;
                if ($r && $r->num_rows>0){ $rr=$r->fetch_assoc(); $cnt = isset($rr['cnt']) ? intval($rr['cnt']) : 0; }
                if ($cnt === 0){
                    if ($isAjax){ header('Content-Type: application/json; charset=utf-8'); echo json_encode(array('success'=>false,'error'=>'Công nhân không thuộc xưởng đã chọn.')); exit; }
                    $_SESSION['err'] = 'Công nhân không thuộc xưởng đã chọn.'; $this->ghinhan(); return; }
            }
        } else {
            // Lấy maXuong từ tenXuong (fallback nếu maBoPhan không có)
            $maXuong=$this->getMaXuongByTen(isset($xt['tenXuong'])?$xt['tenXuong']:'');
            // Nếu không tìm thấy, thử lấy từ maBoPhan
            if($maXuong==='' && isset($xt['maBoPhan'])){
                $maXuong = $this->getMaXuongByMaBoPhan($xt['maBoPhan']);
            };
        }

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
            if ($isAjax){ header('Content-Type: application/json; charset=utf-8'); echo json_encode(array('success'=>false,'error'=>'Vui lòng chọn Công nhân và Ngày chấm công.')); exit; }
            $_SESSION['err']='Vui lòng chọn Công nhân và Ngày chấm công.';
            // Hiển thị ngay view ghi nhận với thông báo lỗi (không redirect)
            $this->ghinhan();
            return;
        }
        // Kiểm tra ca có tồn tại trong lịch làm việc (nếu đã chọn ca) — nếu không có thì không cho lưu
        if ($data['maCa']!=='' && $data['maNguoiDung']!=='' && $this->conn){
            $maNV=$this->conn->real_escape_string($data['maNguoiDung']);
            $ngay=$this->conn->real_escape_string($data['ngayCham']);
            $maCa=$this->conn->real_escape_string($data['maCa']);
            $q = "SELECT COUNT(*) AS cnt FROM lichlamviec WHERE maNguoiDung='{$maNV}' AND ngayLam='{$ngay}' AND maCa='{$maCa}'";
            $rs=$this->conn->query($q);
            $cntSch = 0;
            if ($rs && $rs->num_rows>0){ $rr=$rs->fetch_assoc(); $cntSch = isset($rr['cnt'])? intval($rr['cnt']):0; }
            if ($cntSch === 0){
                // Không có lịch cho công nhân này vào ngày/ca đó — trả lỗi
                if ($isAjax){ header('Content-Type: application/json; charset=utf-8'); echo json_encode(array('success'=>false,'error'=>'Ngày '.htmlspecialchars($data['ngayCham']).' không có ca làm việc để ghi nhận.')); exit; }
                $_SESSION['err'] = 'Ngày '.htmlspecialchars($data['ngayCham']).' không có ca làm việc để ghi nhận.';
                $this->ghinhan();
                return;
            }

            // Kiểm tra trùng ca cùng ngày (immediate on-screen debug + error)
            $q = "SELECT COUNT(*) AS cnt FROM chamcong WHERE maNguoiDung='{$maNV}' AND ngayCham='{$ngay}' AND maCa='{$maCa}'";
            $rs=$this->conn->query($q);
            if($rs){
                $row=$rs->fetch_assoc();
                $cnt = isset($row['cnt']) ? intval($row['cnt']) : 0;
                if($cnt>0){
                    // Thiết lập thông báo lỗi và hiển thị lại form ghi nhận
                    if ($isAjax){ header('Content-Type: application/json; charset=utf-8'); echo json_encode(array('success'=>false,'error'=> '⚠️ Công nhân này đã ghi nhận ca '.htmlspecialchars($data['maCa']).' vào ngày '.htmlspecialchars($data['ngayCham']).' rồi.')); exit; }
                    $_SESSION['err'] = '⚠️ Công nhân này đã ghi nhận ca '.htmlspecialchars($data['maCa']).' vào ngày '.htmlspecialchars($data['ngayCham']).' rồi.';
                    $this->ghinhan();
                    return;
                }
            } else {
                // Nếu query lỗi, thiết lập thông báo lỗi và hiển thị lại form
                $_SESSION['err'] = 'Lỗi kiểm tra trùng ca: '.htmlspecialchars($this->conn->error);
                $this->ghinhan();
                return;
            }
        }
        // Nếu lưu thành công thì redirect với ok=1, nếu không thì báo lỗi
        if ($this->mChamCong->create($data)) {
            if ($isAjax){ header('Content-Type: application/json; charset=utf-8'); echo json_encode(array('success'=>true)); exit; }
            $this->redirect('index.php?controller=sanxuat&action=ghinhan&ok=1');
        } else {
            if ($isAjax){ header('Content-Type: application/json; charset=utf-8'); echo json_encode(array('success'=>false,'error'=>'Lưu thất bại. Vui lòng thử lại.')); exit; }
            $_SESSION['err']='Lưu thất bại. Vui lòng thử lại.';
            // Hiển thị ngay view ghi nhận với thông báo lỗi (không redirect)
            $this->ghinhan();
            return;
        }
    }
}
