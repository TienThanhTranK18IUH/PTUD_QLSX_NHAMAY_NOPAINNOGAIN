<?php
// Nếu bạn đang dùng PHP 5.x, hãy xóa hoặc comment dòng declare(strict_types=1);
// declare(strict_types=1);
?>

<div class="content" style="margin:20px;">
    <h2>🧾 Lập phiếu yêu cầu sửa chữa</h2>

    <form action="index.php?controller=phieu&action=save_suachua" method="post" style="max-width:650px;">

        <!-- Chọn xưởng và dây chuyền trước khi chọn thiết bị -->
        <div style="margin-bottom:10px; display:flex; gap:10px;">
            <div style="flex:1;">
                <label><b>Xưởng:</b></label><br>
                <select id="maXuongSelect" style="width:100%;padding:8px;">
                    <option value="">-- Tất cả xưởng --</option>
                    <?php
                    // Tạo danh sách xưởng duy nhất từ $thietbis (dùng tenXuong)
                    $xuongs = array();
                    if (!empty($thietbis)) {
                        foreach ($thietbis as $tb) {
                            $mx = isset($tb['maXuong']) ? $tb['maXuong'] : '';
                            $tx = isset($tb['tenXuong']) ? $tb['tenXuong'] : '';
                            if ($mx !== '' && !isset($xuongs[$mx])) {
                                $xuongs[$mx] = $tx;
                            }
                        }
                    }
                    foreach ($xuongs as $mx => $tx) {
                        echo '<option value="' . htmlspecialchars($mx) . '">' . htmlspecialchars($tx) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div style="flex:1;">
                <label><b>Dây chuyền:</b></label><br>
                <select id="dayChuyenSelect" style="width:100%;padding:8px;">
                    <option value="">-- Tất cả dây chuyền --</option>
                    <?php
                    // Danh sách dây chuyền duy nhất
                    $days = array();
                    if (!empty($thietbis)) {
                        foreach ($thietbis as $tb) {
                            $dc = isset($tb['dayChuyen']) ? $tb['dayChuyen'] : '';
                            if ($dc !== '' && !in_array($dc, $days)) $days[] = $dc;
                        }
                    }
                    foreach ($days as $dc) {
                        echo '<option value="' . htmlspecialchars($dc) . '">' . htmlspecialchars($dc) . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <!-- Chọn thiết bị -->
        <div style="margin-bottom:10px;">
            <label><b>Thiết bị cần sửa:</b></label><br>

            <select name="maTB" required style="width:100%;padding:8px;">
                <option value="">-- Chọn thiết bị --</option>

                <?php if (!empty($thietbis)) foreach ($thietbis as $tb): ?>
                    <option value="<?php echo htmlspecialchars($tb['maTB']); ?>"
                        data-tentb="<?php echo htmlspecialchars($tb['tenTB']); ?>"
                        data-daychuyen="<?php echo htmlspecialchars($tb['dayChuyen']); ?>"
                        data-maxuong="<?php echo htmlspecialchars($tb['maXuong']); ?>"
                        data-tenxuong="<?php echo htmlspecialchars(isset($tb['tenXuong']) ? $tb['tenXuong'] : ''); ?>">
                        <?php echo htmlspecialchars($tb['tenTB']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Khung hiển thị thông tin thiết bị -->
        <div id="thongTinTB" 
            style="display:none;margin-bottom:15px;padding:12px;border:1px solid #ddd;border-radius:8px;background:#f8f8f8;">
            
            <h4>📌 Thông tin thiết bị</h4>
            <p><b>Mã thiết bị:</b> <span id="show_maTB"></span></p>
            <p><b>Tên thiết bị:</b> <span id="show_tenTB"></span></p>
            <p><b>Dây chuyền:</b> <span id="show_dayChuyen"></span></p>
            <p><b>Xưởng:</b> <span id="show_maXuong"></span></p>
        </div>

        <!-- Mô tả sự cố -->
        <div style="margin-bottom:10px;">
            <label><b>Mô tả sự cố:</b></label><br>
            <textarea name="moTaSuCo" rows="4" style="width:100%;padding:8px;" required></textarea>
        </div>

        <!-- Ngày lập phiếu -->
        <div style="margin-bottom:10px;">
            <label><b>Ngày lập phiếu:</b></label><br>
            <input type="date" name="ngayLap" value="<?php echo date('Y-m-d'); ?>" style="width:100%;padding:8px;">
        </div>

        <!-- Người lập phiếu -->
        <div style="margin-bottom:10px;">
            <label><b>Người lập phiếu:</b></label><br>

            <?php 
            // SỬA: tránh dùng '??' để tương thích PHP < 7
            $displayName = 'Chưa xác định';
            $maNguoiLap = '';

            if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
                $displayName = isset($_SESSION['user']['hoTen']) ? $_SESSION['user']['hoTen'] : 'Chưa xác định';
                $maNguoiLap  = isset($_SESSION['user']['maNguoiDung']) ? $_SESSION['user']['maNguoiDung'] : '';
            }
            ?>

            <input type="text" value="<?php echo htmlspecialchars($displayName); ?>" 
                style="width:100%;padding:8px;" disabled>

            <input type="hidden" name="maNguoiLap" value="<?php echo htmlspecialchars($maNguoiLap); ?>">
        </div>

        <!-- Trạng thái -->
        <div style="margin-bottom:10px;">
            <label><b>Trạng thái:</b></label><br>
            <select name="trangThai" style="width:100%;padding:8px;">
                <option value="Chờ xử lý">Chờ xử lý</option>
                
            </select>
        </div>

        <!-- Nút -->
        <div>
            <button type="submit" 
                style="background:#27ae60;color:white;padding:8px 16px;border:none;border-radius:6px;">
                💾 Lưu phiếu
            </button>

            <a href="index.php?controller=phieu&action=suachua"
               style="margin-left:10px;padding:8px 16px;background:#ccc;border-radius:6px;text-decoration:none;">
               ⬅ Quay lại
            </a>
        </div>

    </form>
</div>


<!-- SCRIPT HIỂN THỊ THÔNG TIN THIẾT BỊ -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    var deviceSelect = document.querySelector('select[name="maTB"]');
    var xuongSelect = document.getElementById('maXuongSelect');
    var daySelect = document.getElementById('dayChuyenSelect');

    // Cache all device options
    var allDevices = Array.prototype.slice.call(deviceSelect.options).map(function (opt) {
        return {
            value: opt.value,
            text: opt.text,
            tentb: opt.getAttribute('data-tentb') || '',
            daychuyen: opt.getAttribute('data-daychuyen') || '',
            maxuong: opt.getAttribute('data-maxuong') || '',
            tenxuong: opt.getAttribute('data-tenxuong') || ''
        };
    });

    // Build a map of maXuong -> list of unique dayChuyen values in that xuong
    var xuongToDayMap = {};
    allDevices.forEach(function (d) {
        if (d.value === '' || !d.maxuong) return;
        if (!xuongToDayMap[d.maxuong]) xuongToDayMap[d.maxuong] = [];
        if (xuongToDayMap[d.maxuong].indexOf(d.daychuyen) === -1) {
            xuongToDayMap[d.maxuong].push(d.daychuyen);
        }
    });

    function rebuildDayChuyenOptions() {
        var selXu = xuongSelect ? xuongSelect.value : '';
        var html = '<option value="">-- Tất cả dây chuyền --</option>';
        
        if (selXu && xuongToDayMap[selXu]) {
            xuongToDayMap[selXu].forEach(function (dc) {
                html += '<option value="' + htmlspecialchars(dc) + '">' + htmlspecialchars(dc) + '</option>';
            });
        } else if (!selXu) {
            // Show all dây chuyền when no xuong selected
            var allDays = [];
            allDevices.forEach(function (d) {
                if (d.value !== '' && d.daychuyen && allDays.indexOf(d.daychuyen) === -1) {
                    allDays.push(d.daychuyen);
                }
            });
            allDays.forEach(function (dc) {
                html += '<option value="' + htmlspecialchars(dc) + '">' + htmlspecialchars(dc) + '</option>';
            });
        }
        
        daySelect.innerHTML = html;
        daySelect.value = '';
        rebuildDeviceOptions();
    }

    function rebuildDeviceOptions() {
        var selXu = xuongSelect ? xuongSelect.value : '';
        var selDay = daySelect ? daySelect.value : '';
        var html = '<option value="">-- Chọn thiết bị --</option>';
        allDevices.forEach(function (d) {
            if (d.value === '') return;
            if (selXu && d.maxuong !== selXu) return;
            if (selDay && d.daychuyen !== selDay) return;
            html += '<option value="' + htmlspecialchars(d.value) + '" data-tentb="' + htmlspecialchars(d.tentb) + '" data-daychuyen="' + htmlspecialchars(d.daychuyen) + '" data-maxuong="' + htmlspecialchars(d.maxuong) + '" data-tenxuong="' + htmlspecialchars(d.tenxuong) + '">' + htmlspecialchars(d.text) + '</option>';
        });
        deviceSelect.innerHTML = html;
        attachDeviceChange();
    }

    function attachDeviceChange() {
        // remove existing listener by cloning
        var newSelect = deviceSelect.cloneNode(true);
        deviceSelect.parentNode.replaceChild(newSelect, deviceSelect);
        deviceSelect = newSelect;

        deviceSelect.addEventListener('change', function () {
            var opt = this.options[this.selectedIndex];

            if (!this.value) {
                document.getElementById('thongTinTB').style.display = 'none';
                return;
            }

            document.getElementById('show_maTB').innerText = this.value;
            document.getElementById('show_tenTB').innerText = opt.getAttribute('data-tentb') || 'Không có';
            document.getElementById('show_dayChuyen').innerText = opt.getAttribute('data-daychuyen') || 'Không có';
            document.getElementById('show_maXuong').innerText = opt.getAttribute('data-tenxuong') || opt.getAttribute('data-maxuong') || 'Không rõ';

            document.getElementById('thongTinTB').style.display = 'block';
        });
    }

    // helper to escape HTML in JS when building options
    function htmlspecialchars(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#039;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    // initial attach
    attachDeviceChange();

    // update devices when xuong or day changes
    if (xuongSelect) xuongSelect.addEventListener('change', rebuildDayChuyenOptions);
    if (daySelect) daySelect.addEventListener('change', rebuildDeviceOptions);
});
</script>
