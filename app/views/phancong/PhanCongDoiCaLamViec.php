<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Phân công / Đổi ca công nhân</title>
    <style>
    body {
        font-family: Arial;
    }

    table {
        border-collapse: collapse;
        width: 80%;
        margin-bottom: 20px;
    }

    th,
    td {
        border: 1px solid #ccc;
        padding: 8px;
        text-align: center;
    }

    th {
        background-color: #eee;
    }

    .tab {
        margin-bottom: 10px;
    }

    .tab button {
        padding: 10px;
        cursor: pointer;
    }

    .tabcontent {
        display: none;
    }

    .tabcontent.active {
        display: block;
    }
    </style>
    <script>
    function openTab(tabName) {
        var i, x, tablinks;
        x = document.getElementsByClassName("tabcontent");
        for (i = 0; i < x.length; i++) x[i].className = x[i].className.replace(" active", "");
        document.getElementById(tabName).className += " active";
    }

    function confirmAction(formId, actionText) {
        if (confirm(actionText)) {
            document.getElementById(formId).submit();
        }
    }
    </script>
</head>

<body>
    <h2>Quản lý phân công / đổi ca công nhân</h2>

    <?php if(isset($message) && $message != ''): ?>
    <p style="color:green;"><?php echo $message; ?></p>
    <?php endif; ?>

    <div class="tab">
        <button onclick="openTab('PhanCong')">Phân công</button>
        <button onclick="openTab('DoiCa')">Đổi ca</button>
    </div>

    <div id="PhanCong" class="tabcontent active">
        <h3>Phân công công nhân chưa có ca</h3>
        <?php if(count($congNhanChuaPhanCa) == 0): ?>
        <p>Chưa có công nhân nào cần phân công.</p>
        <?php else: ?>
        <table>
            <tr>
                <th>Mã NV</th>
                <th>Họ tên</th>
                <th>Chọn ca</th>
                <th>Hành động</th>
            </tr>
            <?php foreach($congNhanChuaPhanCa as $cn): ?>
            <tr>
                <td><?php echo $cn['maNguoiDung']; ?></td>
                <td><?php echo $cn['hoTen']; ?></td>
                <form id="phancong_<?php echo $cn['maNguoiDung']; ?>" method="post"
                    action="index.php?controller=PhanCongDoiCa&action=capNhat">
                    <td>
                        <select name="maCa">
                            <?php foreach($danhSachCa as $ca): ?>
                            <option value="<?php echo $ca; ?>"><?php echo $ca; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="maNguoiDung" value="<?php echo $cn['maNguoiDung']; ?>">
                    </td>
                    <td>
                        <button type="button"
                            onclick="confirmAction('phancong_<?php echo $cn['maNguoiDung']; ?>','Xác nhận phân công ca này cho công nhân?')">Phân
                            công</button>
                    </td>
                </form>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>

    <div id="DoiCa" class="tabcontent">
        <h3>Đổi ca cho công nhân đã có ca</h3>
        <?php if(count($congNhanDaPhanCa) == 0): ?>
        <p>Chưa có công nhân nào để đổi ca.</p>
        <?php else: ?>
        <table>
            <tr>
                <th>Mã NV</th>
                <th>Họ tên</th>
                <th>Ca hiện tại</th>
                <th>Chọn ca mới</th>
                <th>Hành động</th>
            </tr>
            <?php foreach($congNhanDaPhanCa as $cn): ?>
            <tr>
                <td><?php echo $cn['maNguoiDung']; ?></td>
                <td><?php echo $cn['hoTen']; ?></td>
                <td><?php echo $cn['maCa']; ?></td>
                <form id="doica_<?php echo $cn['maNguoiDung']; ?>" method="post"
                    action="index.php?controller=PhanCongDoiCa&action=capNhat">
                    <td>
                        <select name="maCa">
                            <?php foreach($danhSachCa as $ca): ?>
                            <option value="<?php echo $ca; ?>" <?php if($ca==$cn['maCa']) echo 'selected'; ?>>
                                <?php echo $ca; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="maNguoiDung" value="<?php echo $cn['maNguoiDung']; ?>">
                    </td>
                    <td>
                        <button type="button"
                            onclick="confirmAction('doica_<?php echo $cn['maNguoiDung']; ?>','Xác nhận đổi ca cho công nhân?')">Đổi
                            ca</button>
                    </td>
                </form>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>

    <script>
    // Mặc định hiển thị tab Phân công
    openTab('PhanCong');
    </script>
</body>

</html>