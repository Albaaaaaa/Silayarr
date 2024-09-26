<?= $this->extend('themes/modern/register/layout') ?>
<?= $this->section('content') ?>

<!-- Tambahkan link ke Bootstrap dan FontAwesome di head layout -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>

<!-- CSS untuk animasi dan tata letak yang lebih rapi -->
<style>
    body {
        background: linear-gradient(45deg, #1e3c72, #2a5298);
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0;
    }

    .card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        border: 1px solid rgba(255, 255, 255, 0.18);
        animation: fadeIn 1.5s ease-in-out;
        max-width: 400px;
        width: 100%;
        padding: 20px;
    }

    .card-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .logo img {
        max-width: 100px;
        animation: bounceIn 2s ease infinite;
    }

    .form-login .input-group {
        margin-bottom: 15px;
    }

    .form-login .input-group .input-group-text {
        border: none;
        background: transparent;
        color: #00c6ff; /* Ubah warna ikon menjadi biru */
    }

    .form-login .form-control {
        border: 1px solid #fff;
        border-radius: 50px;
        background: transparent;
        color: #00c6ff; /* Ubah warna teks menjadi biru */
        padding: 10px 15px;
    }

    .form-login .form-control::placeholder {
        color: #87cefa; /* Ubah warna placeholder menjadi biru muda */
    }

    .form-login .form-control:focus {
        box-shadow: none;
        border-color: #00c6ff;
    }

    .form-check-label {
        color: #fff;
        font-weight: normal;
    }

    .btn {
        border-radius: 50px;
        background: linear-gradient(45deg, #00c6ff, #0072ff);
        color: #fff;
        padding: 10px;
        transition: background 0.3s ease;
    }

    .btn:hover {
        background: linear-gradient(45deg, #0072ff, #00c6ff);
    }

    .card-footer {
        text-align: center;
        color: #fff;
        margin-top: 20px;
    }

    .card-footer a {
        color: #00c6ff;
        text-decoration: none;
    }

    .card-footer a:hover {
        text-decoration: underline;
    }

    @keyframes fadeIn {
        0% { opacity: 0; transform: translateY(-20px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    @keyframes bounceIn {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-15px); }
    }
</style>

<div class="container d-flex justify-content-center align-items-center">
    <div class="card">
        <div class="card-header transparent-header">
            <div class="logo">
                <img src="<?php echo $config->baseURL . '/public/images/' . $settingAplikasi['logo_login']?>" alt="Logo">
            </div>
            <?php if (!empty($desc)) { echo '<p class="text-white mt-3">' . $desc . '</p>'; } ?>
        </div>
        <div class="card-body">
            <?php if (!empty($message)) { ?>
                <div class="alert alert-danger">
                    <?=$message?>
                </div>
            <?php } ?>
            <form method="post" action="" class="form-horizontal form-login">
                <div class="input-group mb-3">
                    <span class="input-group-text">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" name="username" value="<?=@$_POST['username']?>" class="form-control" placeholder="Username" required>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="remember" id="rememberme" value="1">
                    <label class="form-check-label" for="rememberme">Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">Submit</button>
                <?= csrf_formfield() ?>
            </form>
        </div>
        <!-- <div class="card-footer">
            <p>Lupa Password? <a href="<?=$config->baseURL?>recovery">Request reset password</a></p>
            <?php if ($setting_registrasi['enable'] == 'Y') { ?>
                <p>Belum punya akun? <a href="<?=$config->baseURL?>register">Daftar akun</a></p>
            <?php } ?>
            <p>Tidak menerima link aktivasi? <a href="<?=$config->baseURL?>register/resendlink">Kirim ulang</a></p>
        </div> -->
    </div>
</div>

<?= $this->endSection() ?>

<!-- Tambahkan Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
