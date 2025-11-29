<div class="content">
    <h2>🔧 Quản lý phiếu sửa chữa thiết bị</h2>

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
                        <th>Trạng thái YC</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dsYeuCau)) {
            foreach ($dsYeuCau as $r) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['maPhieu']); ?></td>
                        <td><?php echo htmlspecialchars($r['maTB']); ?></td>
                        <td><?php echo htmlspecialchars($r['tenTB']); ?></td>
                        <td><?php echo htmlspecialchars($r['trangThai']); ?></td>
                        <td>
                            <a
                                href="index.php?controller=baotri&action=index&maPhieuYCSC=<?php echo urlencode($r['maPhieu']); ?>">📝
                                Ghi nhận</a>
                        </td>
                    </tr>
                    <?php }
          } else { ?>
                    <tr>
                        <td colspan="5" align="center">Không có phiếu yêu cầu sửa chữa.</td>
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
                        <td><?php echo htmlspecialchars($r['maNguoiDung']); ?></td>
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

        <p>
            <label><b>Ngày hoàn thành:</b></label><br />
            <input type="date" name="ngayHoanThanh" required />
        </p>

        <p>
            <label><b>Trạng thái:</b></label><br />
            <select name="trangThai">
                <option value="Chờ xử lý">Chờ xử lý</option>
                <option value="Đang xử lý" selected>Đang xử lý</option>
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
/* ==================== GLOBAL ==================== */
body {
    font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    background-color: #eef1f6;
    color: #2c3e50;
    margin: 0;
    padding: 0;
}

.content {
    max-width: 1400px;
    margin: 35px auto;
    padding: 28px;
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.08);
}

/* ==================== TITLES ==================== */
h2 {
    color: #2563eb;
    border-bottom: 3px solid #2563eb;
    padding-bottom: 12px;
    margin-bottom: 28px;
    font-size: 1.8em;
    font-weight: 700;
}

h3 {
    font-size: 1.25em;
    color: #1f2937;
    margin-bottom: 15px;
    font-weight: 600;
}

/* ==================== TABLE ==================== */
.tbl {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95em;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
}

.tbl thead th {
    background-color: #2563eb;
    color: white;
    padding: 13px;
    text-align: left;
    font-weight: 600;
}

.tbl tbody tr {
    background: white;
    transition: background 0.2s ease;
}

.tbl tbody tr:nth-child(even) {
    background: #f6f8fc;
}

.tbl tbody tr:hover {
    background: #e8f0fe;
}

.tbl td {
    padding: 12px 14px;
    border-bottom: 1px solid #e5e7eb;
}

.tbl td:last-child {
    text-align: center;
}

/* Link "Ghi nhận" */
.tbl a {
    background: #34d399;
    color: white;
    padding: 6px 10px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 500;
    transition: background 0.2s ease;
}

.tbl a:hover {
    background: #059669;
}

/* ==================== FORM ==================== */
.form-edit {
    max-width: 720px;
    padding: 25px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
    border: 1px solid #e5e7eb;
}

.form-edit p {
    margin-bottom: 20px;
}

.form-edit label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
    display: block;
}

.form-edit input[type=date],
.form-edit select,
.form-edit textarea {
    width: 100%;
    max-width: 480px;
    padding: 12px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #f9fafb;
    font-size: 1em;
    transition: border 0.2s ease, box-shadow 0.2s ease;
}

.form-edit input:focus,
.form-edit select:focus,
.form-edit textarea:focus {
    border-color: #2563eb;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.25);
}

.form-edit textarea {
    min-height: 110px;
}

/* ==================== BUTTONS ==================== */
.form-edit button[name="btnSave"] {
    background-color: #2563eb;
    padding: 10px 22px;
    border: none;
    border-radius: 8px;
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s ease, transform 0.15s ease;
}

.form-edit button[name="btnSave"]:hover {
    background-color: #1d4ed8;
    transform: translateY(-1px);
}

.form-edit a {
    display: inline-block;
    padding: 9px 18px;
    color: #4b5563;
    border: 1px solid #6b7280;
    border-radius: 8px;
    text-decoration: none;
    transition: background 0.2s ease;
}

.form-edit a:hover {
    background: #e5e7eb;
}

/* ==================== DIVIDERS ==================== */
hr {
    border: none;
    height: 1px;
    margin: 30px 0;
    background: linear-gradient(to right, transparent, #ccc, transparent);
}
</style>