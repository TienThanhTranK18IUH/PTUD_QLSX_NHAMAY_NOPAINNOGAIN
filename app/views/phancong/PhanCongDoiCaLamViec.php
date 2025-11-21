<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Phân công / Đổi ca</title>
    <style>
    body {
        font-family: Arial;
        margin: 20px;
    }

    .tabs {
        margin-bottom: 10px;
    }

    .tabs button {
        background: #007BFF;
        color: white;
        border: none;
        padding: 10px 20px;
        cursor: pointer;
        margin-right: 5px;
    }

    .tabs button:hover {
        background: #0056b3;
    }

    .tabcontent {
        border: 1px solid #ccc;
        padding: 15px;
        border-radius: 5px;
        background: #f9f9f9;
    }

    form label {
        display: block;
        margin: 10px 0 5px;
    }

    form select,
    form input {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        border-radius: 3px;
        border: 1px solid #ccc;
    }

    form button {
        background: #28a745;
        color: white;
        border: none;
        padding: 10px 20px;
        cursor: pointer;
        border-radius: 3px;
    }

    form button:hover {
        background: #218838;
    }

    .message {
        padding: 10px;
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        margin-bottom: 15px;
        border-radius: 3px;
    }
    </style>
</head>

<body>

    <div class="tabs">
        <button onclick="showTab('phancong')">Phân công ca</button>
        <button onclick="showTab('doica')">Đổi ca</button>
    </div>

    <?php if(!empty($message)): ?>
    <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>


    <!-- Phân công ca -->
    <div id="phancong" class="tabcontent" style="display:block;">
        <h3>Phân công ca làm việc</h3>
        <form method="POST" action="index.php?controller=PhanCongDoiCa&action=capNhat">
            <label>Công nhân (chưa phân ca):</label>
            <select name="maNguoiDung" id="cnChuaPC" onchange="fillXuong('cnChuaPC','xuongPC')" required>
                <option value="">-- Chọn công nhân --</option>
                <?php foreach($congNhanChuaPhanCa as $cn): ?>
                <option value="<?php echo $cn['maNguoiDung']; ?>" data-xuong="<?php echo $cn['maXuong']; ?>">
                    <?php echo $cn['maNguoiDung'] . ' - ' . $cn['tenCongNhan']; ?>
                </option>
                <?php endforeach; ?>
            </select>

            <label>Xưởng:</label>
            <input type="text" name="maXuong" id="xuongPC" readonly required>

            <label>Chọn ca:</label>
            <select name="maCa" required>
                <option value="">-- Chọn ca --</option>
                <?php foreach($danhSachCa as $ca): ?>
                <option value="<?php echo $ca['maCa']; ?>">
                    <?php echo $ca['maCa'] . ' (' . $ca['thoiGianBatDau'].'-'.$ca['thoiGianKetThuc'].')'; ?>
                </option>
                <?php endforeach; ?>
            </select>

            <label>Ngày bắt đầu:</label>
            <input type="date" name="ngayBatDau" required>

            <label>Ngày kết thúc:</label>
            <input type="date" name="ngayKetThuc" required>

            <button type="submit">Lưu phân công</button>
        </form>
    </div>

    <!-- Đổi ca -->
    <div id="doica" class="tabcontent" style="display:none;">
        <h3>Đổi ca làm việc</h3>
        <form method="POST" action="index.php?controller=PhanCongDoiCa&action=capNhat">
            <label>Công nhân (đã phân ca):</label>
            <select name="maNguoiDung" id="cnDaPC" onchange="fillXuong('cnDaPC','xuongDC'); fillCaMoi()" required>
                <option value="">-- Chọn công nhân --</option>
                <?php foreach($congNhanDaPhanCa as $cn): ?>
                <option value="<?php echo $cn['maNguoiDung']; ?>" data-xuong="<?php echo $cn['maXuong']; ?>"
                    data-cahientai="<?php echo $cn['maCa']; ?>">
                    <?php echo $cn['maNguoiDung'] . ' - ' . $cn['tenCongNhan'] . ' (Ca hiện tại: '.$cn['maCa'].')'; ?>
                </option>
                <?php endforeach; ?>
            </select>

            <label>Xưởng:</label>
            <input type="text" name="maXuong" id="xuongDC" readonly required>

            <label>Chọn ca mới:</label>
            <select name="maCa" id="caMoi" required>
                <option value="">-- Chọn ca mới --</option>
                <?php foreach($danhSachCa as $ca): ?>
                <option value="<?php echo $ca['maCa']; ?>">
                    <?php echo $ca['maCa'].' ('.$ca['thoiGianBatDau'].'-'.$ca['thoiGianKetThuc'].')'; ?></option>
                <?php endforeach; ?>
            </select>

            <label>Ngày bắt đầu:</label>
            <input type="date" name="ngayBatDau" required>

            <label>Ngày kết thúc:</label>
            <input type="date" name="ngayKetThuc" required>

            <button type="submit">Cập nhật / Đổi ca</button>
        </form>
    </div>

    <script>
    function showTab(tab) {
        document.getElementById('phancong').style.display = 'none';
        document.getElementById('doica').style.display = 'none';
        document.getElementById(tab).style.display = 'block';
    }

    function fillXuong(selectId, inputId) {
        var sel = document.getElementById(selectId);
        var xuong = sel.options[sel.selectedIndex].getAttribute('data-xuong');
        document.getElementById(inputId).value = xuong;
    }

    function fillCaMoi() {
        var sel = document.getElementById('cnDaPC');
        var caHienTai = sel.options[sel.selectedIndex].getAttribute('data-cahientai');
        var caMoi = document.getElementById('caMoi');
        for (var i = 0; i < caMoi.options.length; i++) {
            caMoi.options[i].style.display = (caMoi.options[i].value == caHienTai) ? 'none' : 'block';
        }
        caMoi.value = ""; // reset chọn
    }
    </script>

</body>

</html>