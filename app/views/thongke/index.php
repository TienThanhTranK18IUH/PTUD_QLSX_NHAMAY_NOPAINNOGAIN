<h2 style="text-align:center;">📊 THỐNG KÊ SẢN XUẤT</h2>

<form method="get" style="text-align:center;margin-bottom:20px;">
    <input type="hidden" name="controller" value="thongke">
    <input type="hidden" name="action" value="index">

    Từ ngày:
    <input type="date" name="from" value="<?php echo $from; ?>">
    Đến ngày:
    <input type="date" name="to" value="<?php echo $to; ?>">

    <button type="submit">Xem thống kê</button>
</form>

<hr>

<?php
$dat = isset($chartPie['Đạt']) ? (int)$chartPie['Đạt'] : 0;
$khongDat = isset($chartPie['Không đạt']) ? (int)$chartPie['Không đạt'] : 0;
$tongQC = $dat + $khongDat;
?>

<h3 style="text-align:center;">🔍 QC THÀNH PHẨM</h3>

<?php if ($tongQC == 0): ?>
    <p style="text-align:center;color:red;">Không có dữ liệu QC</p>
<?php else: ?>
    <div style="width:260px;margin:10px auto;">
        <canvas id="qcChart"></canvas>
    </div>

    <table border="1" cellpadding="6" cellspacing="0" width="45%" style="margin:auto;">
        <tr style="background:#f2f2f2;text-align:center;">
            <th>Số lượng đơn hàng</th>
            
            <th>Tỷ lệ (%)</th>
        </tr>
        <tr style="text-align:center;">
            <td style="color:#4CAF50;">Đạt</td>
            <td><?php echo $dat; ?></td>
            <td><?php echo round($dat/$tongQC*100,1); ?>%</td>
        </tr>
        <tr style="text-align:center;">
            <td style="color:#F44336;">Không đạt</td>
            <td><?php echo $khongDat; ?></td>
            <td><?php echo round($khongDat/$tongQC*100,1); ?>%</td>
        </tr>
    </table>
<?php endif; ?>

<hr>

<h3>📦 ĐƠN HÀNG THEO THÁNG</h3>

<table border="1" cellpadding="8" cellspacing="0" width="90%">
    <tr style="background:#f2f2f2;text-align:center;">
        <th>Tháng</th>
        <th>Tổng đơn</th>
        <th>Đơn hàng đã hoàn thành</th>
        <th>Đơn chưa hoàn thành</th>
        <th>Chênh lệch so với tháng trước</th>
        <th>Xu hướng</th>
    </tr>

<?php
$prevTong = null;

if (empty($donHangTheoThang)):
?>
    <tr>
        <td colspan="6" style="text-align:center;color:red;">Không có dữ liệu</td>
    </tr>
<?php
else:
foreach ($donHangTheoThang as $row):
    $chenh = ($prevTong === null) ? 0 : $row['tongDon'] - $prevTong;
?>
    <tr style="text-align:center;">
        <td><?php echo date('m/Y', strtotime($row['thang'].'-01')); ?></td>
        <td><?php echo $row['tongDon']; ?></td>
        <td style="color:green;font-weight:bold;"><?php echo $row['donDat']; ?></td>
        <td style="color:red;"><?php echo $row['donChuaDat']; ?></td>
        <td>
            <?php
                if ($chenh > 0) echo '+'.$chenh;
                else echo $chenh;
            ?>
        </td>
        <td>
            <?php
                if ($chenh > 0) echo '<span style="color:green;">▲ Tăng</span>';
                elseif ($chenh < 0) echo '<span style="color:red;">▼ Giảm</span>';
                else echo '—';
            ?>
        </td>
    </tr>
<?php
    $prevTong = $row['tongDon'];
endforeach;
endif;
?>
</table>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
window.onload = function () {
    var dat = <?php echo $dat; ?>;
    var khongDat = <?php echo $khongDat; ?>;
    if (dat === 0 && khongDat === 0) return;

    new Chart(document.getElementById('qcChart'), {
        type: 'doughnut',
        data: {
            labels: ['Đạt', 'Không đạt'],
            datasets: [{
                data: [dat, khongDat],
                backgroundColor: ['#4CAF50', '#F44336']
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
};
</script>
