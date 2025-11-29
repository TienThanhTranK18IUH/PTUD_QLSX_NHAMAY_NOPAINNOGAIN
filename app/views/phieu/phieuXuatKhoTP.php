<?php if(empty($dsPhieu)) { ?>
<p class="alert info">⚠️ Chưa có phiếu xuất kho nào.</p>
<?php } else { ?>
<?php
if (isset($_GET['ok']) && $_GET['ok']==1) {
    echo '<div id="popup-success" class="popup">
            <div class="popup-content">
                ✅ Lập phiếu xuất kho thành phẩm thành công!
                <span class="popup-close" onclick="closePopup()">&times;</span>
            </div>
          </div>';
}
?>

<style>
.popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    animation: fadeIn 0.3s;
}

.popup-content {
    background: #d1e7dd;
    color: #0f5132;
    padding: 20px 30px;
    border-radius: 8px;
    font-weight: bold;
    font-size: 16px;
    position: relative;
    text-align: center;
}

.popup-close {
    position: absolute;
    top: 5px;
    right: 10px;
    font-size: 20px;
    cursor: pointer;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}
</style>

<script>
function closePopup() {
    var popup = document.getElementById('popup-success');
    if (popup) popup.style.display = 'none';
}

// Tự động ẩn popup sau 3 giây
window.onload = function() {
    var popup = document.getElementById('popup-success');
    if (popup) {
        setTimeout(function() {
            popup.style.display = 'none';
        }, 3000);
    }
}
</script>

<h2
    style="text-align:center; font-weight:bold; border-bottom:2px solid #007bff; padding-bottom:10px; margin-bottom:20px;">
    📦 DANH SÁCH PHIẾU XUẤT KHO THÀNH PHẨM
</h2>

<div class="table-container">
    <table class="phieu-table">
        <thead>
            <tr>
                <th>Mã Phiếu</th>
                <th>Mã Kho</th>
                <th>Ngày Xuất</th>
                <th>Người Lập</th>
                <th>Mã Đơn Hàng</th>
                <th>Mã Thành Phẩm</th>
                <th>Tên Thành Phẩm</th>
                <th>Số Lượng</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($dsPhieu as $row) { ?>
            <tr>
                <td><?php echo $row['maPhieu']; ?></td>
                <td><?php echo $row['maKho']; ?></td>
                <td><?php echo date('d-m-Y', strtotime($row['ngayXuat'])); ?></td>
                <td><?php echo $row['nguoiLapName']; ?></td>
                <td><?php echo $row['maDonHang']; ?></td>
                <td><?php echo $row['maTP']; ?></td>
                <td><?php echo $row['tenTP']; ?></td>
                <td><?php echo $row['soLuong']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<p style="text-align:center; margin-top:20px;">
    <a href="index.php?controller=phieuNhapXuat&action=taophieu" class="btn-add">➕ Thêm phiếu xuất kho mới</a>
</p>

<style>
.table-container {
    overflow-x: auto;
}

.phieu-table {
    width: 100%;
    border-collapse: collapse;
    font-family: 'Segoe UI', Tahoma, Verdana;
}

.phieu-table th,
.phieu-table td {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: center;
    font-size: 14px;
}

.phieu-table th {
    background: #f0f0f0;
    font-weight: 600;
}

.phieu-table tr:nth-child(even) {
    background: #fafafa;
}

.phieu-table tr:hover {
    background: #e6f2ff;
}

.btn-add {
    background: #198754;
    color: #fff;
    padding: 10px 18px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
}

.btn-add:hover {
    background: #157347;
}

@media(max-width:700px) {

    .phieu-table th,
    .phieu-table td {
        font-size: 13px;
        padding: 8px;
    }
}
</style>

<?php } ?>