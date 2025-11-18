<?php
require_once dirname(__FILE__) .'/../models/thongKe.php';

class ThongKeController {
    private $model;

    public function __construct(){
        $this->model = new ThongKe();
    }

    public function index(){
        // Lấy ngày từ GET hoặc mặc định
        $from = isset($_GET['from']) ? $_GET['from'] : date('Y-m-01');
        $to   = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');

        $phieuQC = $this->model->getPhieuQC($from, $to);
        $chartTP = $this->model->getChartTP($from, $to);
        $chartPie= $this->model->getChartPie($from, $to);

        require_once dirname(__FILE__) . '/../views/thongke/index.php';
    }
}
?>
