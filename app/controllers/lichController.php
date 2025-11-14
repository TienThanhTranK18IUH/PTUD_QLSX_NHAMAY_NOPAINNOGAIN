<?php
// app/controllers/lichController.php — PHP 5.x
require_once dirname(__FILE__).'/../models/database.php';
require_once dirname(__FILE__).'/../models/nhanVien.php';
require_once dirname(__FILE__).'/../models/chamCong.php';
require_once dirname(__FILE__).'/../models/PhanCongCongViecSanXuat.php';
require_once dirname(__FILE__).'/../models/lichLamViec.php';

class LichController {
    private $mNhanVien, $mChamCong, $mPhanCong, $mLLV;

    public function __construct(){
        $this->mNhanVien = new NhanVien();
        $this->mChamCong = new ChamCong();
        $this->mPhanCong = new PhanCongCongViecSanXuat(new Database());
        $this->mLLV      = new LichLamViec(new Database());
        if (session_id()==='') @session_start();
    }

    private function thuVN($ymd){ $w=(int)date('w',strtotime($ymd));
        $map=array('Chủ nhật','Thứ Hai','Thứ Ba','Thứ Tư','Thứ Năm','Thứ Sáu','Thứ Bảy'); return $map[$w]; }
    private function getWeekBounds($ymd){
        $ts=strtotime($ymd); $w=(int)date('w',$ts); $offset=($w==0)?6:($w-1);
        $mon=$ts - $offset*86400; $sun=$mon+6*86400; return array(date('Y-m-d',$mon),date('Y-m-d',$sun));
    }
    private function getMonthBounds($ymd){ return array(date('Y-m-01',strtotime($ymd)), date('Y-m-t',strtotime($ymd))); }
    private function fmtHM($v){ if($v===null||$v==='') return ''; return strlen($v)>=5?substr($v,0,5):$v; }
    private function isWorker($r){ $role = isset($r['vaiTro'])?$r['vaiTro']:(isset($r['VaiTro'])?$r['VaiTro']:'');
        $role=strtolower($role); return ($role==='congnhan' || $role==='công nhân'); }

    private function lapLichTuanTuLLV($rowsLLV,$from,$to,$caMap){
        $byDate=array(); foreach($rowsLLV as $r){ if(!isset($r['ngayLam'])) continue;
            $d=$r['ngayLam']; if(!isset($byDate[$d])) $byDate[$d]=array(); $byDate[$d][]=$r; }
        $out=array();
        for($ts=strtotime($from);$ts<=strtotime($to);$ts+=86400){
            $d=date('Y-m-d',$ts);
            if(empty($byDate[$d])){ $out[] = array('ngay'=>$d,'thu'=>$this->thuVN($d),'ca'=>'Nghỉ','time'=>'-','gioCong'=>'0.00'); continue; }
            $perCa=array(); foreach($byDate[$d] as $r){
                $maC=isset($r['maCa'])?$r['maCa']:''; if($maC===''||!isset($caMap[$maC])) continue;
                $bd=$this->fmtHM($caMap[$maC]['bd']); $kt=$this->fmtHM($caMap[$maC]['kt']);
                $gio=0.0; if($bd!==''&&$kt!==''){ $p1=explode(':',$bd); $p2=explode(':',$kt);
                    if(count($p1)>=2&&count($p2)>=2){ $t1=intval($p1[0])*3600+intval($p1[1])*60;
                        $t2=intval($p2[0])*3600+intval($p2[1])*60; if($t2<$t1) $t2+=86400; $gio=round(($t2-$t1)/3600,2); } }
                if(!isset($perCa[$maC])) $perCa[$maC]=array('bd'=>$bd,'kt'=>$kt,'gio'=>$gio);
                else if($gio>$perCa[$maC]['gio']) $perCa[$maC]['gio']=$gio;
            }
            $caList=array(); $timeList=array(); $sum=0.0;
            foreach($perCa as $maC=>$inf){ $caList[]=$maC; if($inf['bd']!==''&&$inf['kt']!=='') $timeList[]=$inf['bd'].' - '.$inf['kt'].' ('.$maC.')'; $sum+=$inf['gio']; }
            $out[] = array('ngay'=>$d,'thu'=>$this->thuVN($d),'ca'=>(empty($caList)?'-':implode('-',$caList)),
                           'time'=>(empty($timeList)?'-':implode('<br>',$timeList)),
                           'gioCong'=>number_format($sum,2,'.',''));
        }
        return $out;
    }

    private function lapLichTuanTuChamCong($rowsCC,$from,$to,$caMap){
        $byDate=array(); foreach($rowsCC as $r){ if(!isset($r['ngayCham'])) continue;
            $d=$r['ngayCham']; if(!isset($byDate[$d])) $byDate[$d]=array(); $byDate[$d][]=$r; }
        $out=array();
        for($ts=strtotime($from);$ts<=strtotime($to);$ts+=86400){
            $d=date('Y-m-d',$ts);
            if(empty($byDate[$d])){ $out[] = array('ngay'=>$d,'thu'=>$this->thuVN($d),'ca'=>'Nghỉ','time'=>'-','gioCong'=>'0.00'); continue; }
            $rows=$byDate[$d]; $caSet=array(); $timeList=array(); $sum=0.0;
            foreach($rows as $r){
                $maCa=isset($r['maCa'])?$r['maCa']:'';
                if($maCa!=='') $caSet[$maCa]=true;
                $in  = (isset($r['gioVao']) && $r['gioVao']!='') ? $r['gioVao'] : (isset($caMap[$maCa])?$caMap[$maCa]['bd']:'');
                $outT= (isset($r['gioRa'])  && $r['gioRa']!='')  ? $r['gioRa']  : (isset($caMap[$maCa])?$caMap[$maCa]['kt']:'');
                $inHM=$this->fmtHM($in); $outHM=$this->fmtHM($outT);
                if($inHM!=='' && $outHM!==''){
                    $timeList[]=$inHM.' - '.$outHM.($maCa!==''?' ('.$maCa.')':'');
                    $p1=explode(':',$inHM); $p2=explode(':',$outHM);
                    if(count($p1)>=2&&count($p2)>=2){ $t1=intval($p1[0])*3600+intval($p1[1])*60;
                        $t2=intval($p2[0])*3600+intval($p2[1])*60; if($t2<$t1) $t2+=86400; $sum+=round(($t2-$t1)/3600,2); }
                } elseif(isset($r['soGioLam']) && $r['soGioLam']!==''){ $sum+=floatval($r['soGioLam']); }
            }
            $out[] = array('ngay'=>$d,'thu'=>$this->thuVN($d),
                           'ca'=>(!empty($caSet)?implode('-',array_keys($caSet)):'-'),
                           'time'=>(!empty($timeList)?implode('<br>',$timeList):'-'),
                           'gioCong'=>number_format($sum,2,'.',''));
        }
        return $out;
    }

    public function index(){
        $nv  = isset($_GET['nv']) ? trim($_GET['nv']) : '';
        $d   = isset($_GET['d'])  ? trim($_GET['d'])  : date('Y-m-d');
        $tab = isset($_GET['tab'])? trim($_GET['tab']): 'lich';

        // chỉ lấy công nhân
        $all=$this->mNhanVien->getAll(); $dsNV=array();
        foreach($all as $r){ if($this->isWorker($r)) $dsNV[]=$r; }
        if($nv=='' && !empty($dsNV)) $nv=$dsNV[0]['maNguoiDung'];

        list($from,$to) = $this->getWeekBounds($d);

        // map giờ ca
        $dsCa=$this->mPhanCong->layDanhSachCa(); if(!is_array($dsCa)) $dsCa=array();
        $caMap=array(); foreach($dsCa as $r){
            if(!isset($r['maCa'])) continue;
            $caMap[$r['maCa']] = array(
              'bd'=> isset($r['gioBatDau'])?$r['gioBatDau']:(isset($r['thoiGianBatDau'])?$r['thoiGianBatDau']:''),
              'kt'=> isset($r['gioKetThuc'])?$r['gioKetThuc']:(isset($r['thoiGianKetThuc'])?$r['thoiGianKetThuc']:'')
            );
        }

        // Lịch tuần: ưu tiên từ LLV, không có thì rớt về chấm công
        $rowsLLV=$this->mLLV->getRange($nv,$from,$to);
        if(!empty($rowsLLV)) $lichTuan=$this->lapLichTuanTuLLV($rowsLLV,$from,$to,$caMap);
        else { $rowsCC=$this->mChamCong->getRange($nv,$from,$to);
               $lichTuan=$this->lapLichTuanTuChamCong($rowsCC,$from,$to,$caMap); }

        // Giờ công THÁNG: TÍNH TỪ CHẤM CÔNG
        list($mFrom,$mTo) = $this->getMonthBounds($d);
        $mstats  = $this->mChamCong->monthStats($nv,$mFrom,$mTo);
        $nightCa = $this->mChamCong->countNightShifts($nv,$mFrom,$mTo);

        $data = array(
          'dsNV'=>$dsNV,'nv'=>$nv,'d'=>$d,'from'=>$from,'to'=>$to,'tab'=>$tab,
          'lichTuan'=>$lichTuan,'mFrom'=>$mFrom,'mTo'=>$mTo,'mstats'=>$mstats,'nightCa'=>$nightCa
        );
        $title='Lịch làm & Giờ công (Công nhân)';
        include dirname(__FILE__).'/../views/lich/lich_index.php';
    }
}
