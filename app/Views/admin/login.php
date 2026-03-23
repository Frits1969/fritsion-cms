<?php
$lang = $GLOBALS['lang'] ?? [];
extract($lang);

$page_title_login = $page_title_login ?? 'Inloggen';
$login_welcome = $login_welcome ?? 'Welkom';
$login_desc = $login_desc ?? 'Log in op je CMS';
$label_username_email = $label_username_email ?? 'Gebruikersnaam';
$label_password = $label_password ?? 'Wachtwoord';
$btn_login = $btn_login ?? 'Log in';
$btn_back_to_site = $btn_back_to_site ?? 'Terug naar site';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'nl' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title_login ?> | Fritsion CMS</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/assets/logo/logo_fritsion_cms_favicon.png">
    <link rel="shortcut icon" href="/assets/logo/logo_fritsion_cms_favicon.ico">
    <link rel="stylesheet" href="/assets/css/admin_shared.css">
</head>

<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="login-card">
        <div class="logo-container">
            <img src="/assets/logo/logo_fritsion_cms.png" alt="Fritsion Logo">
        </div>
        <h2><?= $login_welcome ?></h2>
        <p><?= $login_desc ?></p>

        <?php if (isset($error) && $error): ?>
            <div class="alert alert-error" style="margin-top: 20px;">
                <span>⚠️</span> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/backoffice/login" style="margin-top: 20px;">
            <div class="form-group">
                <label for="username"><?= $label_username_email ?></label>
                <input type="text" name="username" id="username" class="form-input" placeholder="admin" required>
            </div>

            <div class="form-group">
                <label for="password"><?= $label_password ?></label>
                <input type="password" name="password" id="password" class="form-input" placeholder="••••••••" required>
            </div>

            <button type="submit" class="login-btn"><?= $btn_login ?></button>
        </form>

        <div class="footer-links">
            <a href="/"><?= $btn_back_to_site ?></a>
        </div>
    </div>
</body>

</html>