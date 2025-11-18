<?php
// app/views/thongke/index.php
?>

<div class="content" style="margin:20px; font-family: Arial, sans-serif;">
    <h2>📊 Báo cáo phiếu kiểm tra & đơn hàng</h2>

    <form method="get" action="index.php" style="margin-bottom:15px;">
        <input type="hidden" name="controller" value="thongKe">
        <input type="hidden" name="action" value="index">
        From: <input type="date" name="from" value="<?php echo $from; ?>">
        To: <input type="date" name="to" value="<?php echo $to; ?>">
        <button type="submit">Xem báo cáo</button>
    </form>

    <!-- Bảng phiếu QC -->
    <h3>Bảng phiếu kiểm tra</h3>
    <table border="1" cellpadding="6" style="width:100%; border-collapse: collapse;">
        <tr>
            <th>Mã phiếu</th><th>Mã TP</th><th>Tên TP</th>
            <th>Số lượng kiểm tra</th><th>Số lượng đạt chuẩn</th>
            <th>Kết quả</th><th>Ngày lập</th><th>Mã nhân viên QC</th>
        </tr>
        <?php if(!empty($phieuQC)) {
            foreach($phieuQC as $row): ?>
            <tr>
                <td><?php echo $row['maPhieu']; ?></td>
                <td><?php echo $row['maTP']; ?></td>
                <td><?php echo $row['tenTP']; ?></td>
                <td><?php echo $row['SL_KiemTra']; ?></td>
                <td><?php echo $row['SL_DatChuan']; ?></td>
                <td><?php echo $row['ketQua']; ?></td>
                <td><?php echo $row['ngayLap']; ?></td>
                <td><?php echo $row['maNhanVienQC']; ?></td>
            </tr>
        <?php endforeach;
        } else { ?>
            <tr><td colspan="8" style="text-align:center;">Không có dữ liệu</td></tr>
        <?php } ?>
    </table>

    <!-- Bảng đơn hàng -->
    <h3>Bảng đơn hàng</h3>
    <table border="1" cellpadding="6" style="width:100%; border-collapse: collapse;">
        <tr>
            <th>Mã đơn</th><th>Ngày đặt</th><th>Ngày giao</th><th>Số lượng</th>
            <th>Tình trạng</th><th>Mã SP</th><th>Tên SP</th><th>Kích cỡ</th><th>Màu sắc</th>
        </tr>
        <?php if(!empty($donHangTheoNgay)) {
            foreach($donHangTheoNgay as $dh): ?>
            <tr>
                <td><?php echo $dh['maDonHang']; ?></td>
                <td><?php echo $dh['ngayDat']; ?></td>
                <td><?php echo $dh['ngayGiao']; ?></td>
                <td><?php echo $dh['soLuong']; ?></td>
                <td><?php echo $dh['tinhTrang']; ?></td>
                <td><?php echo $dh['maSP']; ?></td>
                <td><?php echo $dh['tenSP']; ?></td>
                <td><?php echo $dh['kichCo']; ?></td>
                <td><?php echo $dh['mauSac']; ?></td>
            </tr>
        <?php endforeach;
        } else { ?>
            <tr><td colspan="9" style="text-align:center;">Không có dữ liệu</td></tr>
        <?php } ?>
    </table>

    <!-- Biểu đồ -->
    <h3>Biểu đồ</h3>
    <div style="display:flex; justify-content:space-between; gap:20px;">
        <!-- Biểu đồ tròn QC -->
        <canvas id="pieChart" width="400" height="300" style="border:1px solid #ddd; border-radius:6px; padding:5px;"></canvas>
        <!-- Biểu đồ đường đơn hàng -->
        <canvas id="lineChart" width="400" height="300" style="border:1px solid #ddd; border-radius:6px; padding:5px;"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Biểu đồ tròn QC
var ctxPie = document.getElementById('pieChart').getContext('2d');
var datat = {
    'Đạt': <?php echo isset($chartPie['Đạt']) ? $chartPie['Đạt'] : 0; ?>,
    'Không đạt': <?php echo isset($chartPie['Không đạt']) ? $chartPie['Không đạt'] : 0; ?>
};
var pieData = {
    labels: ['Đạt', 'Không đạt'],
    datasets: [{
        data: [datat['Đạt'], datat['Không đạt']],
        backgroundColor: ['rgba(75, 192, 192, 0.7)','rgba(255, 99, 132, 0.7)']
    }]
};
new Chart(ctxPie, { type: 'pie', data: pieData, options: {
    responsive: false,
    plugins: { legend:{position:'right',labels:{boxWidth:12,padding:8}},
        tooltip: { callbacks: { label: function(context){
            var total = datat['Đạt'] + datat['Không đạt'];
            var value = context.raw;
            var percent = total ? ((value/total)*100).toFixed(1) : 0;
            return context.label + ': ' + value + ' ('+percent+'%)';
        }}}}}});

// Biểu đồ đường đơn hàng
// Biểu đồ đường đơn hàng
var ctxLine = document.getElementById('lineChart').getContext('2d');

var labels = [
    <?php foreach($chartDonHang as $dh) { echo "'".$dh['ngayDat']."',"; } ?>
];

var tongDH = [
    <?php foreach($chartDonHang as $dh) { echo $dh['tongDH'].','; } ?>
];

var dhHoanThanh = [
    <?php foreach($chartDonHang as $dh) { echo $dh['dhHoanThanh'].','; } ?>
];

var dhChuaHoanThanh = [
    <?php foreach($chartDonHang as $dh) { echo $dh['dhChuaHoanThanh'].','; } ?>
];

new Chart(ctxLine, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Tổng đơn',
                data: tongDH,
                borderColor:'rgba(54,162,235,0.8)',
                fill:false
            },
            {
                label: 'Đã giao',
                data: dhHoanThanh,
                borderColor:'rgba(75,192,192,0.8)',
                fill:false
            },
            {
                label: 'Chưa giao',
                data: dhChuaHoanThanh,
                borderColor:'rgba(255,99,132,0.8)',
                fill:false
            }
        ]
    },
    options: {
        responsive: false,
        plugins: { legend:{position:'top'} }
    }
});
</script>
