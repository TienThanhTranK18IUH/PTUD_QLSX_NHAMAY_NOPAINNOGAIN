:<div class="content" style="max-width:1200px; margin:auto; padding:20px; font-family:Arial, sans-serif; color:#2c3e50;">
    <h2 style="text-align:center; margin-bottom:25px;">📋 Danh sách Kế hoạch sản xuất</h2>

    <?php
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['maKeHoach'])){
        $maKH = $_POST['maKeHoach'];
        $maXuong = $_POST['maXuong'];
        $maDonHang = $_POST['maDonHang'];
        $tenSP = $_POST['tenSP']; // nhập thủ công
        $ngayBatDau = $_POST['ngayBatDau'];
        $ngayKetThuc = $_POST['ngayKetThuc'];
        $tongSL = intval($_POST['tongSoLuong']);
        $maNL = $_POST['maNguyenLieu'];
        $tenNL = $_POST['tenNguyenLieu'];
        $slNL = intval($_POST['soLuongNguyenLieu']);
        $trangThai = $_POST['trangThai'];

        $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if(!$conn) die("Kết nối thất bại: " . mysqli_connect_error());
        mysqli_set_charset($conn, "utf8");

        // Escape dữ liệu
        $maKH = mysqli_real_escape_string($conn, $maKH);
        $maXuong = mysqli_real_escape_string($conn, $maXuong);
        $maDonHang = mysqli_real_escape_string($conn, $maDonHang);
        $tenSP = mysqli_real_escape_string($conn, $tenSP);
        $ngayBatDau = mysqli_real_escape_string($conn, $ngayBatDau);
        $ngayKetThuc = mysqli_real_escape_string($conn, $ngayKetThuc);
        $maNL = mysqli_real_escape_string($conn, $maNL);
        $tenNL = mysqli_real_escape_string($conn, $tenNL);
        $trangThai = mysqli_real_escape_string($conn, $trangThai);

        // --- Update tên sản phẩm trong DonHang ---
        $sqlSP = "UPDATE donhang SET tenSP='$tenSP' WHERE maDonHang='$maDonHang'";
        mysqli_query($conn, $sqlSP);

        // --- Update kế hoạch ---
        $sqlKH = "UPDATE kehoachsanxuat SET
                    maXuong='$maXuong',
                    maDonHang='$maDonHang',
                    ngayBatDau='$ngayBatDau',
                    ngayKetThuc='$ngayKetThuc',
                    tongSoLuong=$tongSL,
                    maNguyenLieu='$maNL',
                    tenNguyenLieu='$tenNL',
                    soLuongNguyenLieu=$slNL,
                    trangThai='$trangThai'
                  WHERE maKeHoach='$maKH'";

        if(mysqli_query($conn, $sqlKH)){
            mysqli_close($conn);
            // PRG: chuyển về GET để sidebar và URL đồng nhất
            header('Location: index.php?controller=keHoach&action=form_edit');
            exit;
        } else {
            echo '<div style="padding:10px; background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; margin-bottom:15px; border-radius:5px;">
                    Lỗi khi cập nhật kế hoạch: '.mysqli_error($conn).'
                  </div>';
        }
        mysqli_close($conn);
    }
    ?>

    <table style="width:100%; border-collapse:collapse; box-shadow:0 2px 8px rgba(0,0,0,0.05); border-radius:8px; overflow:hidden;">
        <thead style="background:#f0f0f0; text-align:left;">
            <tr>
                <?php 
$headers = array('Mã KH','Xưởng','Sản phẩm','Đơn hàng','Ngày bắt đầu','Ngày kết thúc','Tổng SL','Trạng thái','Mã NL','Tên NL','SL NL','Thao tác');
                foreach($headers as $h){
                    echo "<th style='padding:10px; border-bottom:1px solid #ddd;'>$h</th>";
                }
                ?>
            </tr>
        </thead>
        <tbody>
        <?php
        if(!empty($kehoachs)){
            foreach($kehoachs as $kh){
                echo '<tr style="transition:background 0.2s;" onmouseover="this.style.background=\'#f9f9f9\'" onmouseout="this.style.background=\'\'">';
                echo '<td style="padding:8px;">'.$kh['maKeHoach'].'</td>';
                echo '<td style="padding:8px;">'.$kh['tenXuong'].'</td>';
                echo '<td style="padding:8px;">'.$kh['tenSP'].'</td>';
                echo '<td style="padding:8px;">'.$kh['maDonHang'].'</td>';
                echo '<td style="padding:8px;">'.$kh['ngayBatDau'].'</td>';
                echo '<td style="padding:8px;">'.$kh['ngayKetThuc'].'</td>';
                echo '<td style="padding:8px; text-align:center;">'.$kh['tongSoLuong'].'</td>';
                echo '<td style="padding:8px;">'.$kh['trangThai'].'</td>';
                echo '<td style="padding:8px;">'.$kh['maNguyenLieu'].'</td>';
                echo '<td style="padding:8px;">'.$kh['tenNguyenLieu'].'</td>';
                echo '<td style="padding:8px; text-align:center;">'.$kh['soLuongNguyenLieu'].'</td>';
                echo '<td style="padding:8px; text-align:center;">
                        <button onclick="toggleEditForm(\''.$kh['maKeHoach'].'\')" style="padding:5px 10px; background:#3498db; color:#fff; border:none; border-radius:4px; cursor:pointer;">SỬA</button>
                      </td>';
                echo '</tr>';

                // Form chỉnh sửa
                echo '<tr id="editForm_'.$kh['maKeHoach'].'" style="display:none; background:#fafafa;">
                        <td colspan="12" style="padding:10px;">
                            <form method="post" action="" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:12px; align-items:center; border:1px solid #ddd; padding:12px; border-radius:6px; background:#fff;">
                                <input type="hidden" name="maKeHoach" value="'.$kh['maKeHoach'].'">

                                <select name="maXuong" style="padding:6px; border-radius:4px; border:1px solid #ccc;display:none;">';
                                foreach($xuongs as $x){
                                    $sel = $x['maXuong']==$kh['maXuong']?'selected':'';
                                    echo "<option value=\"{$x['maXuong']}\" $sel>{$x['tenXuong']}</option>";
                                }
                echo            '</select>

                                <input type="text" name="tenSP" value="'.$kh['tenSP'].'" placeholder="Tên sản phẩm" style="padding:6px; border-radius:4px; border:1px solid #ccc;">

                                <select name="maDonHang" style="padding:6px; border-radius:4px; border:1px solid #ccc;display:none;">';
                                foreach($donhangs as $dh){
                                    $sel = $dh['maDonHang']==$kh['maDonHang']?'selected':'';
                                    echo "<option value=\"{$dh['maDonHang']}\" $sel>{$dh['maDonHang']}</option>";
                                }
                echo            '</select>

                                <input type="date" name="ngayBatDau" value="'.$kh['ngayBatDau'].'" style="padding:6px; border-radius:4px; border:1px solid #ccc;">
                                <input type="date" name="ngayKetThuc" value="'.$kh['ngayKetThuc'].'" style="padding:6px; border-radius:4px; border:1px solid #ccc;">
                                <input type="number" name="tongSoLuong" value="'.$kh['tongSoLuong'].'" placeholder="Tổng SL" style="padding:6px; border-radius:4px; border:1px solid #ccc;">

                                <select name="trangThai" style="padding:6px; border-radius:4px; border:1px solid #ccc;">
                                    <option '.($kh['trangThai']=='Chưa bắt đầu'?'selected':'').'>Chưa bắt đầu</option>
                                    <option '.($kh['trangThai']=='Đang thực hiện'?'selected':'').'>Đang thực hiện</option>
                                    <option '.($kh['trangThai']=='Hoàn thành'?'selected':'').'>Hoàn thành</option>
                                    <option '.($kh['trangThai']=='Tạm dừng'?'selected':'').'>Tạm dừng</option>
                                </select>
                                <select name="maNguyenLieu" onchange="this.nextElementSibling.value=this.options[this.selectedIndex].getAttribute(\'data-ten\')" style="padding:6px; border-radius:4px; border:1px solid #ccc;">
                                    <option value="">--Chọn Mã NL--</option>';
                                    foreach($nguyenlieus as $nl){
                                        $sel = $nl['maNguyenLieu']==$kh['maNguyenLieu']?'selected':'';
                                        echo "<option value=\"{$nl['maNguyenLieu']}\" data-ten=\"{$nl['tenNguyenLieu']}\" $sel>{$nl['maNguyenLieu']}</option>";
                                    }
                echo                '</select>

                                <input type="text" name="tenNguyenLieu" value="'.$kh['tenNguyenLieu'].'" placeholder="Tên NL" readonly style="padding:6px; border-radius:4px; border:1px solid #ccc;">
                                <input type="number" name="soLuongNguyenLieu" value="'.$kh['soLuongNguyenLieu'].'" placeholder="SL NL" style="padding:6px; border-radius:4px; border:1px solid #ccc;">

                                

                                <div style="grid-column:span 4; text-align:right; margin-top:5px;">
                                    <button type="submit" style="padding:6px 12px; background:#4CAF50; color:#fff; border:none; border-radius:4px; cursor:pointer;">💾 Lưu</button>
                                    <button type="button" onclick="toggleEditForm(\''.$kh['maKeHoach'].'\')" style="padding:6px 12px; background:#f44336; color:#fff; border:none; border-radius:4px; cursor:pointer;">❌ Hủy</button>
                                </div>
                            </form>
                        </td>
                      </tr>';
            }
        } else {
            echo '<tr><td colspan="12" style="text-align:center; padding:12px;">Không có dữ liệu kế hoạch</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
function toggleEditForm(maKeHoach){
    var row = document.getElementById('editForm_'+maKeHoach);
    if(row.style.display === 'none' || row.style.display === ''){
        row.style.display = 'table-row';
        row.scrollIntoView({behavior:'smooth', block:'center'});
    } else {
        row.style.display = 'none';
    }
}
</script>
