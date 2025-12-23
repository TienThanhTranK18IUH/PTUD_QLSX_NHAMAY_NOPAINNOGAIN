<?php declare(strict_types=1); 
$maTP = isset($maTP) ? $maTP : 'TP00X';
$keHoachList = isset($keHoachList) ? $keHoachList : array();
$success = isset($success) ? $success : false;
$tenTP_popup = isset($tenTP) ? $tenTP : '';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ghi nhận thành phẩm</title>

    <style>
    /* ----------------------------------------
           RESET + BASE 
        -----------------------------------------*/
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: #f2f3f7;
        font-family: Arial, sans-serif;
        color: #333;
    }

    /* ----------------------------------------
           CONTAINER
        -----------------------------------------*/
    .content {
        padding: 30px;
        max-width: 800px;
        margin: 50px auto;
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    h2.page-title {
        font-size: 2rem;
        color: #0d6efd;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        font-weight: 600;
    }

    h2.page-title i {
        margin-right: 0.7rem;
    }

    /* ----------------------------------------
           FORM SECTION
        -----------------------------------------*/
    .form-section {
        margin-bottom: 1.5rem;
        padding: 1.5rem;
        border: 1px solid #dee2e6;
        border-radius: .7rem;
        background: #f8f9fa;
    }

    .section-title {
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        font-weight: 600;
        color: #0d6efd;
        margin-bottom: 1.2rem;
    }

    .section-title i {
        margin-right: .6rem;
    }

    /* ----------------------------------------
           GRID (thay thế Bootstrap)
        -----------------------------------------*/
    .row {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }

    .col-md-6 {
        flex: 0 0 calc(50% - 10px);
    }

    @media(max-width: 576px) {
        .col-md-6 {
            flex: 0 0 100%;
        }
    }

    /* ----------------------------------------
           INPUT STYLE (fake Bootstrap)
        -----------------------------------------*/
    .form-control,
    .form-select,
    textarea {
        width: 100%;
        padding: 10px 12px;
        font-size: 1rem;
        border: 1px solid #ced4da;
        border-radius: .4rem;
        background-color: #fff;
        transition: border-color .2s, box-shadow .2s;
    }

    .form-control:focus,
    .form-select:focus,
    textarea:focus {
        border-color: #0d6efd;
        outline: none;
        box-shadow: 0 0 4px rgba(13, 110, 253, 0.35);
    }

    .readonly-input {
        background-color: #e9ecef !important;
        color: #6c757d !important;
    }

    label {
        font-weight: 500;
        margin-bottom: 6px;
        display: block;
    }

    /* ----------------------------------------
           BUTTONS (đẹp như Bootstrap)
        -----------------------------------------*/
    .btn {
        padding: 10px 18px;
        border-radius: .4rem;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 600;
        border: none;
        transition: 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-success {
        background: #198754;
        color: white;
    }

    .btn-success:hover {
        background: #146c43;
        transform: translateY(-1px);
    }

    .btn-primary {
        background: #0d6efd;
        color: white;
    }

    .btn-primary:hover {
        background: #0a58ca;
        transform: translateY(-1px);
    }

    .text-end {
        text-align: end;
    }

    /* ----------------------------------------
           POPUP
        -----------------------------------------*/
    #popupMessage {
        position: fixed;
        top: 20%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #fff;
        padding: 1.5rem 2rem;
        border-radius: .5rem;
        min-width: 350px;
        text-align: center;
        display: none;
        z-index: 9999;
        border-left: 8px solid #198754;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    #popupMessage.error {
        border-left-color: #dc3545;
    }

    #popupMessage.show {
        display: block;
    }

    #popupMessage span {
        font-size: 1.1rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }
    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>



<body>
    <div class="content">
        <h2 class="page-title"><i class="fas fa-boxes"></i> Ghi nhận thành phẩm</h2>
        <form method="post" action="index.php?controller=ghinhanthanhpham&action=save">
            <!-- Thông tin chung -->
            <div class="form-section">
                <h3 class="section-title"><i class="fas fa-info-circle"></i> Thông tin chung</h3>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Mã TP:</label>
                        <input type="text" name="maTP" value="<?php echo $maTP; ?>" readonly
                            class="form-control readonly-input">
                    </div>
                    <div class="col-md-6">
                        <label>Mã kế hoạch: <span class="text-danger">*</span></label>
                        <?php if(!empty($keHoachList)){ ?>
                        <select name="maKeHoach" id="maKeHoach" class="form-select" required>
                            <option value="">-- Chọn kế hoạch --</option>
                            <?php foreach($keHoachList as $kh){ ?>
                            <option value="<?php echo $kh['maKeHoach']; ?>" data-xuong="<?php echo $kh['maXuong']; ?>">
                                <?php echo $kh['maKeHoach']; ?>
                            </option>
                            <?php } ?>
                        </select>
                        <?php } else { ?>
                        <input type="text" value="Không có mã kế hoạch" readonly
                            class="form-control readonly-input text-danger">
                        <?php } ?>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label>Tên thành phẩm:</label>
                        <input type="text" name="tenTP" id="tenTP" readonly class="form-control readonly-input">
                    </div>
                    <div class="col-md-6">
                        <label>Số lượng: <span class="text-danger">*</span></label>
                        <input type="number" name="soLuong" min="1" required placeholder="Nhập số lượng"
                            class="form-control">
                    </div>
                </div>
            </div>

            <!-- Kho & Xưởng -->
            <div class="form-section">
                <h3 class="section-title"><i class="fas fa-warehouse"></i> Kho & Xưởng</h3>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Mã Kho:</label>
                        <input type="text" name="maKho" value="K002" readonly class="form-control readonly-input">
                    </div>
                    <div class="col-md-6">
                        <label>Tên Kho:</label>
                        <input type="text" name="tenKho" value="Kho Thành Phẩm" readonly
                            class="form-control readonly-input">
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label>Mã Xưởng:</label>
                        <input type="text" name="maXuong" id="maXuong" readonly class="form-control readonly-input">
                    </div>

                </div>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save me-2"></i> Ghi nhận</button>
            </div>
        </form>
    </div>

    <!-- Popup -->
    <div id="popupMessage">
        <span id="popupText"></span>
        <button onclick="document.getElementById('popupMessage').style.display='none'"
            class="btn btn-primary mt-2">Đóng</button>
    </div>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script>
    $(document).ready(function() {
        // Khi chọn mã kế hoạch, tự động lấy tên TP và mã xưởng
        $('#maKeHoach').change(function() {
            var maKH = $(this).val();
            var xuong = $(this).find(':selected').data('xuong');
            $('#maXuong').val(xuong);
            if (maKH) {
                $.post('index.php?controller=ghinhanthanhpham&action=getTenThanhPham', {
                    maKeHoach: maKH
                }, function(data) {
                    $('#tenTP').val(data.tenSP);
                }, 'json');
            } else {
                $('#tenTP').val('');
                $('#maXuong').val('');
            }
        });

        // Hiển thị popup nếu success
        <?php if($success){ ?>
        $('#popupText').text('Ghi nhận thành công <?php echo addslashes($tenTP_popup); ?>!');
        $('#popupMessage').addClass('show');
        <?php } ?>
    });
    </script>
</body>

</html>