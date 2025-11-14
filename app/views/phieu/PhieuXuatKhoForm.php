<h2>📝 LẬP PHIẾU XUẤT KHO THÀNH PHẨM</h2>

<?php
if (isset($_GET['error'])) {
    $tenSP = isset($_GET['tenSP']) ? $_GET['tenSP'] : '';
    switch($_GET['error']) {
        case 1: echo '<p style="color:red;">❌ Đơn hàng không hợp lệ.</p>'; break;
        case 2: echo '<p style="color:red;">❌ Thành phẩm đã xuất kho!</p>'; break;
        case 3: echo '<p style="color:red;">❌ Thành phẩm không tồn tại.</p>'; break;
        case 4: echo '<p style="color:red;">❌ Số lượng '.$tenSP.' không đủ để xuất kho!</p>'; break;
    }
}

if (isset($_GET['error'])) {
    $tenSP = isset($_GET['tenSP']) ? $_GET['tenSP'] : '';
    $maDonHang = isset($_GET['maDonHang']) ? $_GET['maDonHang'] : '';
    switch($_GET['error']) {
        case 1: echo '<p style="color:red;">❌ Đơn hàng không hợp lệ.</p>'; break;
        case 2: echo '<p style="color:red;">❌ Thành phẩm đã xuất kho!</p>'; break;
        case 3: echo '<p style="color:red;">❌ Thành phẩm không tồn tại.</p>'; break;
        case 4: echo '<p style="color:red;">❌ Số lượng '.$tenSP.' không đủ để xuất kho!</p>'; break;
        case 5: echo '<p style="color:red;">❌ Đơn hàng '.$maDonHang.' đã xuất kho thành công. Vui lòng kiểm tra lại!</p>'; break;
    }
}

if (isset($_GET['success']) && $_GET['success']==1) {
    echo "<script>alert('✅ Lập phiếu xuất kho thành phẩm thành công!');</script>";
}

// Lấy danh sách phiếu xuất kho hiện tại
$pxObj = new PhieuXuatKhoTP();
$dsPhieu = $pxObj->getAll();
?>

<form method="post" action="index.php?controller=phieuNhapXuat&action=luuphieu"
    style="max-width:600px;background:#fafafa;padding:20px;border-radius:8px;">

    <p><b>Mã phiếu:</b> <input type="text" name="maPhieu" value="<?php echo $maPhieu; ?>" readonly></p>
    <p><b>Ngày lập phiếu:</b> <input type="text" name="ngayXuat" value="<?php echo $ngayXuat; ?>" readonly></p>
    <p><b>Mã kho:</b> <input type="text" name="maKho" value="K002- Kho Thành Phẩm" readonly></p>
    <p><b>Người lập:</b> <input type="text" name="maNguoiLap" value="<?php echo $maNguoiLap; ?>" readonly></p>

    <label>Chọn mã đơn hàng:</label>
    <select name="maDonHang" id="maDonHang" onchange="layThongTinDonHang()" required style="width:100%;">
        <option value="">-- Chọn đơn hàng --</option>
        <?php
        if (!empty($dsDonHang)) {
            foreach ($dsDonHang as $dh) {
                $maDH = $dh['maDonHang'];
                $maTP_opt = $dh['maTP'];
                $tenTP_opt = $dh['tenTP'];
                $soLuong_opt = $dh['soLuongDH'];
                echo '<option value="'.$maDH.'" data-maTP="'.$maTP_opt.'" data-tenTP="'.$tenTP_opt.'" data-soluong="'.$soLuong_opt.'">'
                     .$maDH.' - '.$tenTP_opt.'</option>';
            }
        }
        ?>
    </select><br><br>

    <label>Mã Thành phẩm:</label>
    <input type="text" id="maTP" name="maTP" value="" readonly style="width:100%; background:#eee;"><br><br>

    <label>Tên Thành phẩm:</label>
    <input type="text" id="tenTP" name="tenTP" value="" readonly style="width:100%; background:#eee;"><br><br>

    <label>Số lượng xuất kho:</label>
    <input type="text" id="soLuong" name="soLuong" value="" readonly style="width:100%; background:#eee;"><br><br>

    <button type="submit" style="background:#4CAF50;color:white;padding:8px 12px;border:none;border-radius:5px;">✅ Xác
        nhận</button>
    <a href="index.php?controller=phieuNhapXuat&action=xuatkhotp" style="margin-left:10px;">⬅ Quay lại</a>
</form>

<script type="text/javascript">
function layThongTinDonHang() {
    var select = document.getElementById('maDonHang');
    var opt = select.options[select.selectedIndex];
    if (opt && opt.value != '') {
        document.getElementById('maTP').value = opt.getAttribute('data-maTP');
        document.getElementById('tenTP').value = opt.getAttribute('data-tenTP');
        document.getElementById('soLuong').value = opt.getAttribute('data-soluong');
    } else {
        document.getElementById('maTP').value = '';
        document.getElementById('tenTP').value = '';
        document.getElementById('soLuong').value = '';
    }
}
</script>

<?php if(!empty($dsPhieu)) { ?>
<h3>📋 Danh sách phiếu xuất kho</h3>
<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse; width:100%;">
    <thead>
        <tr style="background:#eee;">
            <th>Mã phiếu</th>
            <th>Ngày xuất</th>
            <th>Mã kho</th>
            <th>Người lập</th>
            <th>Mã đơn hàng</th>
            <th>Mã TP</th>
            <th>Tên TP</th>
            <th>Số lượng</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($dsPhieu as $p) { ?>
        <tr>
            <td><?php echo $p['maPhieu']; ?></td>
            <td><?php echo $p['ngayXuat']; ?></td>
            <td><?php echo $p['maKho']; ?></td>
            <td><?php echo $p['nguoiLap']; ?></td>
            <td><?php echo $p['maDonHang']; ?></td>
            <td><?php echo $p['maTP']; ?></td>
            <td><?php echo $p['tenTP']; ?></td>
            <td><?php echo $p['soLuong']; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php } ?>