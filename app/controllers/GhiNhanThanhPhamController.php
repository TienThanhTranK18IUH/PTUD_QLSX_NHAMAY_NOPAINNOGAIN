<?php
class GhiNhanThanhPhamController {
    private $db;
    public function __construct($db){
        $this->db = $db;
    }

    public function index(){
        // Lấy danh sách kế hoạch sản xuất
        $keHoachList = $this->db->query("SELECT maKeHoach, maXuong FROM kehoachsanxuat");
        // Sinh maTP tự động
        $lastTP = $this->db->query("SELECT maTP FROM thanhpham ORDER BY maTP DESC LIMIT 1");
        $lastCode = (!empty($lastTP)) ? substr($lastTP[0]['maTP'],2) : 0;
        $maTP = 'TP'.str_pad($lastCode+1,3,'0',STR_PAD_LEFT);
        include 'app/views/thanhpham/ghinhanthanhpham.php';
    }
     public function getTenThanhPham(){
    $maKH = $_POST['maKeHoach'];
    $sql = "SELECT dh.tenSP 
            FROM donhang dh
            INNER JOIN kehoachsanxuat kh ON kh.maDonHang = dh.maDonHang
            WHERE kh.maKeHoach = '$maKH' LIMIT 1";
    $result = $this->db->query($sql);
    $tenSP = !empty($result) ? $result[0]['tenSP'] : '';
    echo json_encode(array('tenSP'=>$tenSP));
    exit;
}
    public function save(){
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

        $message = $result ? "Thành phẩm $tenTP đã được ghi nhận!" : "Ghi nhận thất bại!";

        // load lại bảng thành phẩm
        $allTP = $this->db->query("SELECT * FROM thanhpham ORDER BY maTP DESC");
        $htmlTable = '<table border="1" cellpadding="6"><tr>
            <th>MaTP</th><th>TenTP</th><th>SoLuong</th><th>TinhTrang</th>
            <th>MaKeHoach</th><th>MaKho</th><th>TenKho</th><th>MaXuong</th>
        </tr>';
        foreach($allTP as $tp){
            $htmlTable .= "<tr>
                <td>{$tp['maTP']}</td>
                <td>{$tp['tenTP']}</td>
                <td>{$tp['soLuong']}</td>
                <td>{$tp['tinhTrang']}</td>
                <td>{$tp['maKeHoach']}</td>
                <td>{$tp['maKho']}</td>
                <td>{$tp['tenKho']}</td>
                <td>{$tp['maXuong']}</td>
            </tr>";
        }
        $htmlTable .= '</table>';

        echo json_encode(array('message'=>$message, 'htmlTable'=>$htmlTable));
        exit;
    }
}
?>