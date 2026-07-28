<?= $this->extend('themes/modern/register/layout') ?>
<?= $this->section('content') ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    * {
        font-family: 'Poppins', sans-serif;
    }

    body {
        background: linear-gradient(45deg, #1e3c72, #2a5298);
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0;
    }

    /* ===== CARD ===== */
    .card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.35);
        border: 1px solid rgba(0, 0, 0, 0.05);
        animation: fadeIn 0.8s ease-in-out;
        max-width: 400px;
        width: 100%;
        padding: 35px 30px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 14px 40px rgba(0, 0, 0, 0.4);
    }

    .card-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .logo img {
        max-width: 100px;
        animation: bounceIn 2.5s ease-in-out infinite;
    }

    .card-header p {
        font-size: 14px;
        font-weight: 400;
        color: #333;
        letter-spacing: 0.2px;
    }

    /* ===== FORM INPUTS ===== */
    .form-login .input-group {
        margin-bottom: 18px;
        animation: slideUp 0.6s ease backwards;
    }

    .form-login .input-group:nth-child(1) { animation-delay: 0.1s; }
    .form-login .input-group:nth-child(2) { animation-delay: 0.2s; }

    .form-login .input-group .input-group-text {
        border: 1px solid #d9e2ec;
        border-right: none;
        background: #f4f8fb;
        color: #0072ff;
        border-radius: 50px 0 0 50px;
        transition: color 0.3s ease, background 0.3s ease;
    }

    .form-login .form-control {
        border: 1px solid #d9e2ec;
        border-left: none;
        border-radius: 0 50px 50px 0;
        background: #f4f8fb;
        color: #1a1a1a;
        font-size: 14px;
        font-weight: 400;
        letter-spacing: 0.2px;
        padding: 12px 18px;
        transition: all 0.3s ease;
    }

    .form-login .form-control::placeholder {
        color: #9aa5b1;
        font-weight: 300;
    }

    .form-login .form-control:focus {
        box-shadow: 0 0 0 3px rgba(0, 198, 255, 0.25);
        border-color: #00c6ff;
        background: #ffffff;
    }

    .form-login .input-group:focus-within .input-group-text {
        color: #ffffff;
        background: linear-gradient(45deg, #00c6ff, #0072ff);
    }

    .form-check-label {
        color: #333;
        font-weight: 400;
        font-size: 13px;
    }

    .form-check-input:checked {
        background-color: #00c6ff;
        border-color: #00c6ff;
    }

    /* ===== BUTTON ===== */
    .btn {
        border-radius: 50px;
        background: linear-gradient(45deg, #00c6ff, #0072ff);
        color: #fff;
        padding: 12px;
        font-weight: 600;
        font-size: 15px;
        letter-spacing: 0.5px;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 114, 255, 0.35);
        transition: all 0.3s ease;
    }

    .btn:hover {
        background: linear-gradient(45deg, #0072ff, #00c6ff);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 198, 255, 0.45);
    }

    .btn:active {
        transform: translateY(0);
    }

    /* ===== ERROR MESSAGE ===== */
    .alert-danger {
        background: rgba(220, 53, 69, 0.08);
        border: 1px solid rgba(220, 53, 69, 0.35);
        color: #c0293b;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
        animation: shake 0.5s ease;
        margin-bottom: 18px;
    }

    .alert-danger::before {
        content: "\f071";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
    }

    /* ===== ANIMASI ===== */
    @keyframes fadeIn {
        0% { opacity: 0; transform: translateY(-25px) scale(0.98); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    @keyframes slideUp {
        0% { opacity: 0; transform: translateY(15px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    @keyframes bounceIn {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-12px); }
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        20%, 60% { transform: translateX(-6px); }
        40%, 80% { transform: translateX(6px); }
    }
</style>

<div class="container d-flex justify-content-center align-items-center">
    <div class="card">
        <div class="card-header transparent-header">
            <div class="logo">
                <img src="<?= ($config->baseURL ?? '') . '/public/images/' . ($settingAplikasi['logo_login'] ?? '') ?>" alt="Logo">
            </div>
            <?php if (!empty($desc ?? '')) { echo '<p class="mt-3">' . $desc . '</p>'; } ?>
        </div>
        <div class="card-body">
            <?php if (!empty($message ?? '')) { ?>
                <div class="alert alert-danger">
                    <?= $message ?>
                </div>
            <?php } ?>
            <form method="post" action="" class="form-horizontal form-login">
                <div class="input-group mb-3">
                    <span class="input-group-text">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" name="username" value="<?= $_POST['username'] ?? 'alba' ?>" class="form-control" placeholder="Username" required>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" value="alba" class="form-control" placeholder="Password" required>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="remember" id="rememberme" value="1">
                    <label class="form-check-label" for="rememberme">Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">Submit</button>
                <?= function_exists('csrf_formfield') ? csrf_formfield() : '' ?>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>