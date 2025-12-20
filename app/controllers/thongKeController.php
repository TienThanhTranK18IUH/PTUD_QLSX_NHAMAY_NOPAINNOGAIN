<?php
require_once dirname(__FILE__) . '/../models/thongKe.php';
require_once dirname(__FILE__) . '/../helpers/auth.php';

class ThongKeController {
    private $model;

    public function __construct() {
        $this->model = new ThongKe();
    }

    public function index() {
        requireRole(array('manager','leader'));

        $from = isset($_GET['from']) ? $_GET['from'] : date('Y-01-01');
        $to   = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');

        $chartPie = $this->model->getChartPie($from, $to);
        $donHangTheoThang = $this->model->getDonHangTheoThang($from, $to);

        require_once dirname(__FILE__) . '/../views/thongke/index.php';
    }
}
?>
