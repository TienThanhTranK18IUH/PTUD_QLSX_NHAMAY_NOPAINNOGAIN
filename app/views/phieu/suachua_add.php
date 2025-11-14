<div class="content" style="margin:20px;">
    <h2>🧾 Lập phiếu yêu cầu sửa chữa</h2>

    <form action="index.php?controller=phieu&action=save_suachua" method="post" style="max-width:600px;">

        <div style="margin-bottom:10px;">
            <label>Thiết bị cần sửa:</label><br>
            <select name="maTB" required style="width:100%;padding:6px;">
                <option value="">-- Chọn thiết bị --</option>
                <?php if (!empty($thietbis)) foreach ($thietbis as $tb): ?>
                    <option value="<?php echo htmlspecialchars($tb['maTB']); ?>">
                        <?php echo htmlspecialchars($tb['tenTB']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom:10px;">
            <label>Mô tả sự cố:</label><br>
            <textarea name="moTaSuCo" rows="4" style="width:100%;padding:6px;" required></textarea>
        </div>

        <div style="margin-bottom:10px;">
            <label>Ngày lập phiếu:</label><br>
            <input type="date" name="ngayLap" value="<?php echo date('Y-m-d'); ?>" style="width:100%;padding:6px;">
        </div>

        <div style="margin-bottom:10px;">
            <label>Người lập phiếu:</label><br>
            <select name="maNguoiLap" required style="width:100%;padding:6px;">
                <option value="">-- Chọn nhân viên --</option>
                <?php if (!empty($nhanviens)) foreach ($nhanviens as $nv): ?>
                    <option value="<?php echo htmlspecialchars($nv['maNV']); ?>">
                        <?php echo htmlspecialchars($nv['hoTen']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom:10px;">
            <label>Trạng thái:</label><br>
            <select name="trangThai" style="width:100%;padding:6px;">
                <option value="Chờ xử lý">Chờ xử lý</option>
                <option value="Đang sửa chữa">Đang sửa chữa</option>
                <option value="Hoàn thành">Hoàn thành</option>
            </select>
        </div>

        <div>
            <button type="submit" style="background:#27ae60;color:white;padding:8px 16px;border:none;border-radius:4px;">
                💾 Lưu phiếu
            </button>
            <a href="index.php?controller=phieu&action=suachua"
               style="margin-left:10px;padding:8px 16px;background:#ccc;border-radius:4px;text-decoration:none;">
               ⬅ Quay lại
            </a>
        </div>
    </form>
</div>
