<?php
$lang = $GLOBALS['lang'] ?? [];
extract($lang);

$backoffice_title = $backoffice_title ?? 'Backoffice';
$profile_title = $profile_title ?? 'Profiel';
$nav_dashboard = $nav_dashboard ?? 'Dashboard';
$role_super_admin = $role_super_admin ?? 'Super Admin';
$nav_profile = $nav_profile ?? 'Profiel';
$nav_logout = $nav_logout ?? 'Uitloggen';
$nav_back_to_dashboard = $nav_back_to_dashboard ?? 'Terug naar Dashboard';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'nl' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/assets/logo/logo_fritsion_cms_favicon.png">
    <link rel="shortcut icon" href="/assets/logo/logo_fritsion_cms_favicon.ico">
    <title><?= $profile_title ?> | Fritsion CMS</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/admin_shared.css">
</head>
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="/assets/logo/logo_fritsion_cms.png" alt="Logo">
        </div>
        <nav class="sidebar-nav">
            <a href="/backoffice" class="nav-item <?= $uri === '/backoffice' ? 'active' : '' ?>">
                <span><?= $nav_dashboard ?></span>
            </a>
            <a href="/backoffice/pages" class="nav-item <?= $uri === '/backoffice/pages' ? 'active' : '' ?>">
                <span><?= $nav_pages ?></span>
            </a>
            <a href="/backoffice/media" class="nav-item <?= $uri === '/backoffice/media' ? 'active' : '' ?>">
                <span><?= $nav_media ?></span>
            </a>
            <a href="/backoffice/templates" class="nav-item <?= $uri === '/backoffice/templates' ? 'active' : '' ?>">
                <span><?= $nav_templates ?></span>
            </a>
            <a href="/backoffice/themes" class="nav-item <?= $uri === '/backoffice/themes' ? 'active' : '' ?>">
                <span><?= $nav_themes ?></span>
            </a>
            <a href="/backoffice/settings" class="nav-item <?= $uri === '/backoffice/settings' ? 'active' : '' ?>">
                <span><?= $nav_settings ?></span>
            </a>
            <a href="/" target="_blank" class="nav-item">
                <span><?= $nav_visit_site ?></span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <p style="font-size: 0.8rem; color: #64748b;">v<?= \Fritsion\App::VERSION ?></p>
        </div>
    </aside>

    <div class="main-wrapper">
        <header class="topbar">
            <div style="font-weight: 600; color: var(--text-muted);"><?= $backoffice_title ?> / <?= $nav_profile ?>
            </div>
            <div style="display: flex; align-items: center; gap: 20px;">
                <a href="/backoffice"
                    style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;"><?= $nav_back_to_dashboard ?></a>

                <div class="user-widget" id="user-widget"
                    style="position: relative; display: flex; align-items: center; gap: 12px; cursor: pointer;">
                    <div class="user-avatar"
                        style="width: 32px; height: 32px; background: var(--accent-gradient); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.85rem; color: white;">
                        <?= strtoupper(substr($user['username'], 0, 1)) ?>
                    </div>
                    <div class="user-info">
                        <span class="user-name"><?= $user['username'] ?? 'Admin' ?></span>
                        <span class="user-role"><?= $role_super_admin ?></span>
                    </div>
                    <!-- User Menu Dropdown -->
                    <div class="user-menu" id="user-menu">
                        <a href="/backoffice/profile" class="menu-item active"><?= $nav_profile ?></a>
                        <hr style="margin: 5px 0; border: none; border-top: 1px solid var(--glass-border);">
                        <a href="/backoffice/logout" class="menu-item logout"><?= $nav_logout ?></a>
                    </div>
                </div>

                <div class="lang-select" id="lang-switcher">
                    <?php $selectedLang = $_SESSION['lang'] ?? 'nl'; ?>
                    <a href="?lang=nl" class="<?= $selectedLang === 'nl' ? 'active' : '' ?>"
                        onclick="handleFlagClick(event, 'nl')">
                        <img src="/assets/flags/nl.svg" alt="Nederlands" class="flag-icon">
                    </a>
                    <a href="?lang=en" class="<?= $selectedLang === 'en' ? 'active' : '' ?>"
                        onclick="handleFlagClick(event, 'en')">
                        <img src="/assets/flags/en.svg" alt="English" class="flag-icon">
                    </a>
                </div>
            </div>
        </header>

        <main class="content">
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <?= strtoupper(substr($user['username'], 0, 1)) ?>
                    </div>
                    <div class="profile-title">
                        <h1><?= $profile_title ?></h1>
                        <p><?= $profile_desc ?></p>
                    </div>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <span>⚠️</span> <?= $error ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <span>✅</span> <?= $success ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="username"><?= $username_label ?></label>
                        <input type="text" id="username" name="username"
                            value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email"><?= $email_label ?></label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"
                            required>
                    </div>

                    <div class="section-divider">
                        <h3 class="section-title"><span>🔒</span> <?= $password_change_title ?></h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 20px;">
                            <?= $password_change_tip ?>
                        </p>
                    </div>

                    <div class="form-group">
                        <label for="new_password"><?= $label_new_password ?></label>
                        <input type="password" id="new_password" name="new_password" autocomplete="new-password">
                    </div>

                    <div class="form-group">
                        <label for="confirm_password"><?= $label_confirm_password ?></label>
                        <input type="password" id="confirm_password" name="confirm_password"
                            autocomplete="new-password">
                    </div>

                    <div class="section-divider">
                        <h3 class="section-title"><span>🛡️</span> <?= $confirm_changes_title ?></h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 20px;">
                            <?= $confirm_changes_tip ?>
                        </p>
                    </div>

                    <div class="form-group">
                        <label for="current_password"><?= $label_current_password ?></label>
                        <input type="password" id="current_password" name="current_password" required
                            autocomplete="current-password">
                    </div>

                    <div style="margin-top: 30px; display: flex; gap: 15px; align-items: center;">
                        <button type="submit" class="btn-save"
                            style="width: auto; padding: 12px 30px; margin-top: 0;"><?= $btn_save_profile ?></button>
                        <a href="/backoffice" class="btn-secondary"><?= $btn_cancel ?></a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        const userWidget = document.querySelector('.user-avatar');
        const userMenu = document.getElementById('user-menu');
        const langSwitcher = document.getElementById('lang-switcher');

        function handleFlagClick(event, lang) {
            const currentParams = new URLSearchParams(window.location.search);
            const currentLang = '<?= $_SESSION['lang'] ?? 'nl' ?>';

            if (currentLang === lang) {
                event.preventDefault();
                langSwitcher.classList.toggle('expanded');
                return;
            }
            // Allow default navigation to switch language
        }

        userWidget.addEventListener('click', (e) => {
            e.stopPropagation();
            userMenu.classList.toggle('active');
        });

        document.addEventListener('click', (e) => {
            if (userMenu && !userWidget.contains(e.target)) {
                userMenu.classList.remove('active');
            }
            if (langSwitcher && !langSwitcher.contains(e.target)) {
                langSwitcher.classList.remove('expanded');
            }
        });
    </script>
</body>

</html>