<style>
  body {
    font-family: "Segoe UI", sans-serif;
    background-color: #f5f7fa;
    margin: 0;
    padding: 0;
  }

  .dashboard-container {
    text-align: center;
    padding: 0;
    margin: 0;
  }

  .dashboard-image {
    width: 100%;
    height: auto;
    border-radius: 0; /* bỏ bo góc để tràn tự nhiên */
    display: block;
    margin: 0;
    box-shadow: none;
    animation: fadeUp 0.8s ease-out forwards;
    opacity: 0;
    transform: translateY(20px);
  }

  @keyframes fadeUp {
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  footer {
    text-align: center;
    margin-top: 20px;
    color: #666;
    font-size: 14px;
  }
</style>
<div class="dashboard-container">
<img src="/PTUD_QLSX_NHAMAY_NOPAINNOGAIN/public/img/logo.jpg" alt="Logo Nhà máy" class="dashboard-image">

</div>
