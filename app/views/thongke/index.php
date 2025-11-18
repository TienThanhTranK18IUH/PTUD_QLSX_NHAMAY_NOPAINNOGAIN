<?php
// app/views/thongke/index.php
?>

<div class="content" style="margin:20px; font-family: Arial, sans-serif;">
    <h2>📊 Báo cáo phiếu kiểm tra thành phẩm</h2>

    <form method="get" action="index.php" style="margin-bottom:15px;">
        <input type="hidden" name="controller" value="thongKe">
        <input type="hidden" name="action" value="index">
        From: <input type="date" name="from" value="<?php echo $from; ?>">
        To: <input type="date" name="to" value="<?php echo $to; ?>">
        <button type="submit">Xem báo cáo</button>
    </form>

    <h3>Bảng phiếu kiểm tra</h3>
    <table border="1" cellpadding="6">
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

    <h3>Biểu đồ tròn: Tỉ lệ kết quả QC</h3>
    <canvas id="pieChart" width="400" height="300" style="border:1px solid #ddd; border-radius:6px; padding:5px;"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
var ctxPie = document.getElementById('pieChart').getContext('2d');

// đảm bảo luôn có cả 2 nhãn
var datat = {
    'Đạt': <?php echo isset($chartPie['Đạt']) ? $chartPie['Đạt'] : 0; ?>,
    'Không đạt': <?php echo isset($chartPie['Không đạt']) ? $chartPie['Không đạt'] : 0; ?>
};

var pieData = {
    labels: ['Đạt', 'Không đạt'],
    datasets: [{
        data: [datat['Đạt'], datat['Không đạt']],
        backgroundColor: [
            'rgba(75, 192, 192, 0.7)',
            'rgba(255, 99, 132, 0.7)'
        ]
    }]
};

var pieChart = new Chart(ctxPie, { 
    type: 'pie', 
    data: pieData,
    options: {
        responsive: false,
        plugins: {
            legend: {
                position: 'right',
                labels: { boxWidth: 12, padding: 8 }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        var total = datat['Đạt'] + datat['Không đạt'];
                        var value = context.raw;
                        var percent = total ? ((value / total) * 100).toFixed(1) : 0;
                        return context.label + ': ' + value + ' (' + percent + '%)';
                    }
                }
            }
        }
    }
});
</script>
