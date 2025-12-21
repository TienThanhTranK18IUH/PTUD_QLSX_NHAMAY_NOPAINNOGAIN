<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Quản lý Lịch Làm</title>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* Scope toàn bộ trong class .ql-lich-container để tránh ảnh hưởng đến Sidebar */
    .ql-lich-container {
        max-width: 800px;
        margin: 20px auto;
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        color: #333;
    }

    .ql-lich-container h3 {
        margin-top: 0;
        color: #2c3e50;
        font-size: 1.5rem;
        border-bottom: 2px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    /* Tabs styling */
    .ql-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        background: #f8f9fa;
        padding: 5px;
        border-radius: 8px;
    }

    .ql-tabs button {
        flex: 1;
        padding: 12px;
        border: none;
        background: transparent;
        cursor: pointer;
        font-weight: 600;
        border-radius: 6px;
        transition: all 0.3s;
        color: #6c757d;
    }

    .ql-tabs button.active {
        background: #fff;
        color: #007bff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    /* Form styling */
    .ql-tabcontent {
        display: none;
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .ql-form-group {
        margin-bottom: 15px;
    }

    .ql-form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .ql-form-group select, 
    .ql-form-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 1rem;
        box-sizing: border-box;
        transition: border-color 0.3s;
    }

    .ql-form-group select:focus, 
    .ql-form-group input:focus {
        border-color: #007bff;
        outline: none;
        background: #fff;
    }

    .btn-save {
        background: #007bff;
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        width: 100%;
        font-size: 1rem;
        transition: background 0.3s;
    }

    .btn-save:hover {
        background: #0056b3;
    }

    /* CSS cho option disabled */
    option:disabled {
        color: #ccc;
        background: #f9f9f9;
    }
</style>
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
</head>
<body>

<div class="ql-lich-container">
    <div class="ql-tabs">
        <button id="btn-taoca" class="active" onclick="showTab('taoca')">Tạo ca mới</button>
        <button id="btn-doica" onclick="showTab('doica')">Đổi ca làm việc</button>
    </div>

    <div id="taoca" class="ql-tabcontent" style="display:block;">
        <h3>Tạo ca mới</h3>
        
        <div class="ql-form-group">
            <label>Công nhân:</label>
            <select id="cnTaoCa">
                <option value="">-- Chọn nhân viên --</option>
                <?php foreach($congNhan as $cn): ?>
                    <option value="<?php echo $cn['maNguoiDung']; ?>"><?php echo $cn['maNguoiDung'].' - '.$cn['tenCongNhan']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="ql-form-group">
            <label>Mã xưởng:</label>
            <select id="xuongTaoCa">
                <option value="">-- Chọn xưởng --</option>
                <?php foreach($danhSachXuong as $x): ?>
                    <option value="<?php echo $x['maXuong']; ?>"><?php echo $x['maXuong'].' - '.$x['tenXuong']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="ql-form-group">
            <label>Ngày làm:</label>
            <input type="date" id="ngayLamTao" value="<?php echo date('Y-m-d'); ?>">
        </div>

        <div class="ql-form-group">
            <label>Chọn ca:</label>
            <select id="caTao">
                <option value="">-- Chọn ca --</option>
                <?php foreach($danhSachCa as $ca): ?>
                    <option value="<?php echo $ca['maCa']; ?>"><?php echo $ca['maCa'].' ('.$ca['thoiGianBatDau'].'-'.$ca['thoiGianKetThuc'].')'; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button class="btn-save" onclick="luuTaoCa()">Lưu lịch làm</button>
    </div>

    <div id="doica" class="ql-tabcontent">
        <h3>Đổi ca làm việc</h3>
        
        <div class="ql-form-group">
            <label>Công nhân:</label>
            <select id="cnDoi">
                <option value="">-- Chọn nhân viên --</option>
                <?php foreach($congNhan as $cn): ?>
                    <option value="<?php echo $cn['maNguoiDung']; ?>"><?php echo $cn['maNguoiDung'].' - '.$cn['tenCongNhan']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="ql-form-group">
            <label>Lịch làm hiện tại:</label>
            <select id="lichLamDoi">
                <option value="">-- Chọn lịch --</option>
            </select>
        </div>

        <div class="ql-form-group">
            <label>Chọn ca mới:</label>
            <select id="caMoi">
                <option value="">-- Chọn ca mới --</option>
                <?php foreach($danhSachCa as $ca): ?>
                    <option value="<?php echo $ca['maCa']; ?>"><?php echo $ca['maCa'].' ('.$ca['thoiGianBatDau'].'-'.$ca['thoiGianKetThuc'].')'; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button class="btn-save" onclick="luuDoiCa()" style="background: #28a745;">Xác nhận đổi ca</button>
    </div>
</div>

<script>
function showTab(tab){
    $('.ql-tabcontent').hide();
    $('.ql-tabs button').removeClass('active');
    $('#'+tab).show();
    $('#btn-'+tab).addClass('active');
}

// Tự động điền xưởng khi chọn công nhân
$('#cnTaoCa').change(function(){
    var maNguoiDung = $(this).val();
    if(!maNguoiDung) return;
    $.post('index.php?controller=DoiCa&action=getXuongNV', {maNguoiDung:maNguoiDung}, function(res){
        var r = JSON.parse(res);
        $('#xuongTaoCa').val(r.maXuong ? r.maXuong : '');
    });
});

// Lưu tạo ca mới với SweetAlert2
function luuTaoCa(){
    var maNguoiDung = $('#cnTaoCa').val();
    var maXuong = $('#xuongTaoCa').val();
    var ngayLam = $('#ngayLamTao').val();
    var maCa = $('#caTao').val();

    if(!maNguoiDung || !maCa) {
        Swal.fire('Thông báo', 'Vui lòng điền đủ thông tin', 'warning');
        return;
    }

    $.post('index.php?controller=DoiCa&action=taoCaMoi',
        {maNguoiDung:maNguoiDung, maXuong:maXuong, ngayLam:ngayLam, maCa:maCa},
        function(res){
            var r = JSON.parse(res);
            if(r.success){
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công',
                    text: r.message + (r.maLichLam ? ' - Mã: ' + r.maLichLam : ''),
                }).then(() => location.reload());
            } else {
                Swal.fire('Lỗi', r.message, 'error');
            }
        }
    );
}

// Lấy lịch làm nhân viên khi đổi ca
$('#cnDoi').change(function(){
    var maNguoiDung=$(this).val();
    $('#lichLamDoi').html('<option>Đang tải...</option>');
    $.post('index.php?controller=DoiCa&action=getLichLamNV',{maNguoiDung:maNguoiDung},function(res){
        var r=JSON.parse(res);
        var html='<option value="">-- Chọn lịch --</option>';
        var today='<?php echo date('Y-m-d'); ?>';
        for(var i=0;i<r.length;i++){
            var disabled=(r[i].ngayLam < today)?'disabled':'';
            html+='<option value="'+r[i].maLichLam+'" '+disabled+'>'+r[i].ngayLam+' | '+r[i].maCa+' | Xưởng: '+r[i].maXuong+'</option>';
        }
        $('#lichLamDoi').html(html);
    });
});

// Lưu đổi ca với SweetAlert2
function luuDoiCa(){
    var maLichLam=$('#lichLamDoi').val();
    var maCaMoi=$('#caMoi').val();

    if(!maLichLam || !maCaMoi) {
        Swal.fire('Thông báo', 'Vui lòng chọn lịch cần đổi và ca mới', 'warning');
        return;
    }

    $.post('index.php?controller=DoiCa&action=doiCa',{maLichLam:maLichLam,maCaMoi:maCaMoi},function(res){
        var r=JSON.parse(res);
        if(r.success){
            Swal.fire('Thành công', r.message, 'success').then(() => location.reload());
        } else {
            Swal.fire('Thất bại', r.message, 'error');
        }
    });
}
</script>

</body>
</html>