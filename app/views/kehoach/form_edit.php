<div class="content">
    <h2>📋 Danh sách Kế hoạch sản xuất</h2>

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

    <table border="1" cellpadding="8" cellspacing="0" width="100%" style="border-collapse:collapse;">
        <thead style="background-color:#f0f0f0;">
            <tr>
                <th>Mã KH</th>
                <th>Xưởng</th>
                <th>Sản phẩm</th>
                <th>Đơn hàng</th>
                <th>Ngày bắt đầu</th>
                <th>Ngày kết thúc</th>
                <th>Tổng SL</th>
                <th>Trạng thái</th>
                <th>Mã NL</th>
                <th>Tên nguyên liệu</th>
                <th>SL Nguyên liệu</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(!empty($kehoachs)){
            foreach($kehoachs as $kh){
                echo '<tr>';
                echo '<td>'.$kh['maKeHoach'].'</td>';
                echo '<td>'.$kh['tenXuong'].'</td>';
                echo '<td>'.$kh['tenSP'].'</td>';
                echo '<td>'.$kh['maDonHang'].'</td>';
                echo '<td>'.$kh['ngayBatDau'].'</td>';
                echo '<td>'.$kh['ngayKetThuc'].'</td>';
                echo '<td align="center">'.$kh['tongSoLuong'].'</td>';
                echo '<td>'.$kh['trangThai'].'</td>';
                echo '<td>'.$kh['maNguyenLieu'].'</td>';
                echo '<td>'.$kh['tenNguyenLieu'].'</td>';
                echo '<td align="center">'.$kh['soLuongNguyenLieu'].'</td>';
                echo '<td>
                        <button onclick="toggleEditForm(\''.$kh['maKeHoach'].'\')">SỬA</button>
                      </td>';
                echo '</tr>';

                // Form chỉnh sửa
                echo '<tr id="editForm_'.$kh['maKeHoach'].'" style="display:none; background:#fafafa;">
                        <td colspan="12">
                            <form method="post" action="" style="display:grid; grid-template-columns:repeat(4,1fr); gap:10px; align-items:center; padding:10px; border:1px solid #ddd; border-radius:5px;">
                                <input type="hidden" name="maKeHoach" value="'.$kh['maKeHoach'].'">

                                <select name="maXuong">';
                                foreach($xuongs as $x){
                                    $sel = $x['maXuong']==$kh['maXuong']?'selected':'';
                                    echo "<option value=\"{$x['maXuong']}\" $sel>{$x['tenXuong']}</option>";
                                }
                echo            '</select>

                                <input type="text" name="tenSP" value="'.$kh['tenSP'].'" placeholder="Tên sản phẩm">

                                <select name="maDonHang">';
                                foreach($donhangs as $dh){
                                    $sel = $dh['maDonHang']==$kh['maDonHang']?'selected':'';
                                    echo "<option value=\"{$dh['maDonHang']}\" $sel>{$dh['maDonHang']}</option>";
                                }
                echo            '</select>

                                <input type="date" name="ngayBatDau" value="'.$kh['ngayBatDau'].'">
                                <input type="date" name="ngayKetThuc" value="'.$kh['ngayKetThuc'].'">
                                <input type="number" name="tongSoLuong" value="'.$kh['tongSoLuong'].'" placeholder="Tổng SL">

                                <select name="maNguyenLieu" onchange="this.nextElementSibling.value=this.options[this.selectedIndex].getAttribute(\'data-ten\')">
                                    <option value="">--Chọn Mã NL--</option>';
                                    foreach($nguyenlieus as $nl){
                                        $sel = $nl['maNguyenLieu']==$kh['maNguyenLieu']?'selected':'';
                                        echo "<option value=\"{$nl['maNguyenLieu']}\" data-ten=\"{$nl['tenNguyenLieu']}\" $sel>{$nl['maNguyenLieu']}</option>";
                                    }
                echo                '</select>

                                <input type="text" name="tenNguyenLieu" value="'.$kh['tenNguyenLieu'].'" placeholder="Tên NL" readonly>
                                <input type="number" name="soLuongNguyenLieu" value="'.$kh['soLuongNguyenLieu'].'" placeholder="SL NL">

                                <select name="trangThai">
                                    <option '.($kh['trangThai']=='Chưa bắt đầu'?'selected':'').'>Chưa bắt đầu</option>
                                    <option '.($kh['trangThai']=='Đang thực hiện'?'selected':'').'>Đang thực hiện</option>
                                    <option '.($kh['trangThai']=='Hoàn thành'?'selected':'').'>Hoàn thành</option>
                                    <option '.($kh['trangThai']=='Tạm dừng'?'selected':'').'>Tạm dừng</option>
                                </select>

                                <div style="grid-column:span 4; text-align:right;">
                                    <button type="submit" style="padding:5px 10px; background:#4CAF50; color:#fff; border:none; border-radius:4px;">💾 Lưu</button>
                                    <button type="button" onclick="toggleEditForm(\''.$kh['maKeHoach'].'\')" style="padding:5px 10px; background:#f44336; color:#fff; border:none; border-radius:4px;">❌ Hủy</button>
                                </div>
                            </form>
                        </td>
                      </tr>';
            }
        } else {
            echo '<tr><td colspan="12" align="center">Không có dữ liệu kế hoạch</td></tr>';
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
