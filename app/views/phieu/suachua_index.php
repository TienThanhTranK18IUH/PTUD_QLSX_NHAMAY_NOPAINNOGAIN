<div class="content">
    <h2>🧰 Danh sách phiếu yêu cầu sửa chữa</h2>
    <a href="index.php?controller=phieu&action=add_suachua"
       style="background:#27ae60;color:white;padding:6px 12px;border-radius:4px;">➕ Lập phiếu mới</a>
    <br><br>

    <table border="1" width="100%" cellpadding="8" cellspacing="0">
        <thead style="background:#f0f0f0;">
            <tr>
                <th>Mã phiếu</th>
                <th>Tên thiết bị</th>
                <th>Mô tả</th>
                <th>Ngày lập</th>
                <th>Người lập</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($phieus)): foreach ($phieus as $p): ?>
            <tr>
                <td><?php echo htmlspecialchars($p['maPhieu']); ?></td>
                <td><?php echo htmlspecialchars($p['tenTB']); ?></td>
                <td><?php echo htmlspecialchars($p['moTaSuCo']); ?></td>
                <td><?php echo htmlspecialchars($p['ngayLap']); ?></td>
                <td><?php echo htmlspecialchars($p['hoTen']); ?></td>
                <td><?php echo htmlspecialchars($p['trangThai']); ?></td>
                <td>
                    <a href="index.php?controller=phieu&action=delete_suachua&id=<?php
                        echo urlencode($p['maPhieu']); ?>" onclick="return confirm('Xóa?');">🗑️ Xóa</a>
                </td>
            </tr>
        <?php endforeach; else: ?>
            <tr><td colspan="7">Không có dữ liệu phiếu sửa chữa</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
