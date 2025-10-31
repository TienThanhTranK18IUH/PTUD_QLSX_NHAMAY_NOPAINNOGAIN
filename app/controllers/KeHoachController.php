<?php
require_once dirname(__FILE__) . '/../models/KeHoachSanXuat.php';

class KeHoachController {
    private $model;

    public function __construct() {
        $this->model = new KeHoachSanXuat();
    }

    public function index() {
        $kehoachs = $this->model->getAll();
        include dirname(__FILE__) . '/../views/kehoach/index.php';
    }
}
?>