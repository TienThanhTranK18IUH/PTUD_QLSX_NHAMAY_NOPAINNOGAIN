<?php declare(strict_types=1); 
// View: ghinhansuachua.php (PHP 5.2 compatible)
if (session_id() === '') session_start();
?>
<div class="content">
    <h2>🔧 Phiếu ghi nhận sửa chữa thiết bị</h2>

    <div style="display:flex; gap:20px;">
        <!-- DANH SÁCH PHIẾU YÊU CẦU -->
        <div style="flex:1;">
<h3>📋 Danh sách phiếu yêu cầu sửa chữa</h3>
<table class="tbl" width="100%">
    <thead>
        <tr>
            <th>Mã phiếu YC</th>
            <th>Mã thiết bị</th>
            <th>Tên thiết bị</th>
            <th>Mô tả sự cố</th>
            <th>Ngày lập</th>
            <th>Người lập</th>
            <th>Trạng thái YC</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $rowsPrinted = 0;
        // Build a list of request IDs (maPhieu of YC) that already have a completed ghi nhận
        $completedRequests = array();
        if (!empty($dsGhiNhan)) {
            foreach ($dsGhiNhan as $g) {
                if (isset($g['maPhieuYCSC']) && isset($g['trangThai']) && trim($g['trangThai']) === 'Hoàn thành') {
                    $completedRequests[] = $g['maPhieuYCSC'];
                }
            }
            // unique for faster in_array checks
            $completedRequests = array_unique($completedRequests);
        }

        if (!empty($dsYeuCau)) {
            foreach ($dsYeuCau as $r) {
                // Ẩn các phiếu đã hoàn thành trực tiếp hoặc đã có ghi nhận hoàn thành
                if ((isset($r['trangThai']) && trim($r['trangThai']) === 'Hoàn thành') || (isset($r['maPhieu']) && in_array($r['maPhieu'], $completedRequests))) continue;
                $rowsPrinted++;
                ?>
        <tr>
            <td><?php echo htmlspecialchars($r['maPhieu']); ?></td>
            <td><?php echo htmlspecialchars($r['maTB']); ?></td>
            <td><?php echo htmlspecialchars($r['tenTB']); ?></td>
            <td><?php echo htmlspecialchars($r['moTaSuCo']); ?></td>
            <td><?php echo htmlspecialchars($r['ngayLap']); ?></td>
            <td>
                <?php 
                    echo htmlspecialchars(
                        isset($r['hoTenNguoiLap']) ? 
                        ($r['maNguoiLap'] . " - " . $r['hoTenNguoiLap']) 
                        : $r['maNguoiLap']
                    );
                ?>
            </td>
            <td><?php echo htmlspecialchars($r['trangThai']); ?></td>
            <td>
                <a href="index.php?controller=baotri&action=index&maPhieuYCSC=<?php echo urlencode($r['maPhieu']); ?>">
                    📝 Ghi nhận
                </a>
            </td>
        </tr>
        <?php }
        }

        if ($rowsPrinted === 0) { ?>
        <tr>
            <td colspan="8" align="center">Không có phiếu yêu cầu sửa chữa.</td>
        </tr>
        <?php } ?>
    </tbody>
</table>

        </div>

        <!-- DANH SÁCH PHIẾU GHI NHẬN -->
        <div style="flex:1;">
            <h3>🧾 Danh sách phiếu ghi nhận sửa chữa</h3>
            <table class="tbl" width="100%">
                <thead>
                    <tr>
                        <th>Mã phiếu GN</th>
                        <th>Mã phiếu YC</th>
                        <th>Nội dung</th>
                        <th>Ngày hoàn thành</th>
                        <th>Mã NV kỹ thuật</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dsGhiNhan)) {
            foreach ($dsGhiNhan as $r) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['maPhieu']); ?></td>
                        <td><?php echo htmlspecialchars($r['maPhieuYCSC']); ?></td>
                        <td><?php echo htmlspecialchars($r['noiDung']); ?></td>
                        <td><?php echo htmlspecialchars($r['ngayHoanThanh']); ?></td>
                        <td><?php echo htmlspecialchars(isset($r['maNguoiDung']) ? $r['maNguoiDung'] . ' - ' . (isset($r['hoTenNguoiDung']) ? $r['hoTenNguoiDung'] : '') : ''); ?></td>
                        <td><?php echo htmlspecialchars($r['trangThai']); ?></td>
                    </tr>
                    <?php }
          } else { ?>
                    <tr>
                        <td colspan="6" align="center">Không có phiếu ghi nhận sửa chữa.</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- FORM GHI NHẬN MỚI -->
    <?php if (!empty($_GET['maPhieuYCSC'])) { ?>
    <hr>
    <h3>📝 Tạo phiếu ghi nhận cho phiếu yêu cầu: <?php echo htmlspecialchars($_GET['maPhieuYCSC']); ?></h3>
    <form method="post" action="index.php?controller=baotri&action=index" class="form-edit" style="max-width:640px;">
        <input type="hidden" name="maPhieu" value="" />
        <input type="hidden" name="maPhieuYCSC" value="<?php echo htmlspecialchars($_GET['maPhieuYCSC']); ?>" />
        <input type="hidden" name="maThietBi" value="<?php echo htmlspecialchars(isset($phieuEdit['maThietBi']) ? $phieuEdit['maThietBi'] : ''); ?>" />
        <input type="hidden" name="tenThietBi" value="<?php echo htmlspecialchars(isset($phieuEdit['tenThietBi']) ? $phieuEdit['tenThietBi'] : ''); ?>" />

        <p>
            <label><b>Ngày hoàn thành:</b></label><br />
            <input type="date" name="ngayHoanThanh" required />
        </p>

        <p>
            <label><b>Người lập phiếu:</b></label><br />
            <?php 
            $displayName = 'Chưa xác định';
            $maNguoiLap = '';
            if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
                $displayName = isset($_SESSION['user']['hoTen']) ? $_SESSION['user']['hoTen'] : 'Chưa xác định';
                $maNguoiLap = isset($_SESSION['user']['maNguoiDung']) ? $_SESSION['user']['maNguoiDung'] : '';
            }
            ?>
            <input type="text" value="<?php echo htmlspecialchars($displayName); ?>" style="width:100%; padding:6px; border:1px solid #ccc; border-radius:4px;" disabled />
            <input type="hidden" name="maNguoiLap" value="<?php echo htmlspecialchars($maNguoiLap); ?>" />
        </p>

        <p>
            <label><b>Trạng thái:</b></label><br />
            <select name="trangThai" style="width:100%; padding:6px; border:1px solid #ccc; border-radius:4px;">
                <option value="Hoàn thành">Hoàn thành</option>
            </select>
        </p>

        <p>
            <label><b>Nội dung:</b></label><br />
            <textarea name="noiDung" rows="4" style="width:100%;" placeholder="Nhập nội dung ghi nhận..."></textarea>
        </p>

        <p>
            <button type="submit" name="btnSave">💾 Lưu phiếu ghi nhận</button>
            <a href="index.php?controller=baotri&action=index">↩ Quay lại</a>
        </p>
    </form>
    <?php } ?>
</div>

<style>
    /* ===== Khung nội dung ===== */
.content {
    background: #fff;
    padding: 20px;
    border-radius: 6px;
    border: 1px solid #ddd;
    font-family: Arial, sans-serif;
}

/* ===== Tiêu đề ===== */
h2, h3 {
    margin-bottom: 12px;
    color: #333;
}

/* ===== Bảng ===== */
.tbl {
    border-collapse: collapse;
    width: 100%;
    margin-bottom: 20px;
    font-size: 14px;
}

.tbl th, .tbl td {
    border: 1px solid #ccc;
    padding: 8px;
    text-align: left;
}

.tbl th {
    background: #f2f2f2;
    font-weight: bold;
}

.tbl tr:nth-child(even) {
    background: #fafafa;
}

/* ===== Link thao tác ===== */
.tbl a {
    text-decoration: none;
    color: #0066cc;
    font-weight: bold;
}

.tbl a:hover {
    text-decoration: underline;
}

/* ===== Form ===== */
.form-edit label {
    font-weight: bold;
    margin-bottom: 4px;
    display: block;
    color: #333;
}

.form-edit input[type="text"],
.form-edit input[type="date"],
.form-edit textarea,
.form-edit select {
    width: 100%;
    padding: 6px;
    border: 1px solid #ccc;
    border-radius: 4px;
    margin-top: 4px;
    margin-bottom: 12px;
    box-sizing: border-box;
    font-size: 14px;
}

/* ===== Nút ===== */
button {
    background: #27ae60;
    color: #fff;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}

button:hover {
    background: #1f8a4c;
}

a.btn-back, .form-edit a {
    display: inline-block;
    padding: 8px 16px;
    background: #ccc;
    border-radius: 4px;
    color: #333;
    text-decoration: none;
    margin-left: 8px;
}

a.btn-back:hover, .form-edit a:hover {
    background: #b5b5b5;
}

</style>