<div class="content">
    <h2>📦 Ghi nhận thành phẩm</h2>

    <form id="ghinhanForm">
        <div class="form-row">
            <label>Mã TP:</label>
            <input type="text" name="maTP" value="<?php echo $maTP; ?>" readonly>
        </div>
        <div class="form-row">
            <label>Mã kế hoạch:</label>
            <select name="maKeHoach" id="maKeHoach" required>
                <option value="">-- Chọn kế hoạch --</option>
                <?php foreach($keHoachList as $kh){ ?>
                    <option value="<?php echo $kh['maKeHoach']; ?>" data-xuong="<?php echo $kh['maXuong']; ?>">
                        <?php echo $kh['maKeHoach']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="form-row">     
    <label>Tên thành phẩm:</label>
    <input type="text" name="tenTP" id="tenTP" required readonly>
</div>
        </div>
        <div class="form-row">
            <label>Số lượng:</label>
            <input type="number" name="soLuong" required>
        </div>
        <div class="form-row">
            <label>Mã Kho:</label>
            <input type="text" name="maKho" value="K002" readonly>
        </div>
        <div class="form-row">
            <label>Tên Kho:</label>
            <input type="text" name="tenKho" value="Kho Thành Phẩm" readonly>
        </div>
        
        <div class="form-row">
            <label>Mã Xưởng:</label>
            <input type="text" name="maXuong" id="maXuong" readonly>
        </div>
        <div class="form-row">
            <label>Tình trạng:</label>
            <input type="text" name="tinhTrang" value="Chờ kiểm tra" readonly>
        </div>

        <br>
        <button type="submit">Ghi nhận</button>
    </form>

    <div id="message"></div>
    <div id="thanhPhamTable"></div>
</div>

<style>
.content {
    max-width: 500px;
    margin: 20px auto;
    font-family: Arial, sans-serif;
}

.form-row {
    display: flex;
    margin-bottom: 10px;
    align-items: center;
}

.form-row label {
    width: 130px;   /* căn label cố định */
    font-weight: bold;
}

.form-row input, 
.form-row select {
    flex: 1;
    padding: 5px 8px;
    border: 1px solid #ccc;
    border-radius: 3px;
}
button {
    padding: 8px 15px;
    border: none;
    background-color: #2E8B57;
    color: white;
    font-weight: bold;
    border-radius: 3px;
    cursor: pointer;
}
button:hover {
    background-color: #246b45;
}
</style>

<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script>
   $('#maKeHoach').change(function(){
    var maKH = $(this).val();
    var xuong = $(this).find(':selected').data('xuong');
    $('#maXuong').val(xuong);

    if(maKH){
        $.post('index.php?controller=ghinhanthanhpham&action=getTenThanhPham',
            {maKeHoach: maKH}, function(data){
                $('#tenTP').val(data.tenSP);
            }, 'json'
        );
    } else {
        $('#tenTP').val('');
    }
});
$('#maKeHoach').change(function(){
    var xuong = $(this).find(':selected').data('xuong');
    $('#maXuong').val(xuong);
});

$('#ghinhanForm').submit(function(e){
    e.preventDefault();
    $.post('index.php?controller=ghinhanthanhpham&action=save', $(this).serialize(), function(data){
        alert(data.message);
        $('#thanhPhamTable').html(data.htmlTable);
        // Sinh mã TP mới
$('input[name="maTP"]').val(data.newMaTP);
        $('input[name="tenTP"]').val('');
        $('input[name="soLuong"]').val('');
        $('#maKeHoach').val('');
        $('#maXuong').val('');
    }, 'json');
});
</script>