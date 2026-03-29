<?php
$uri = strtok($_SERVER['REQUEST_URI'] ?? '/backoffice', '?');
$lang = $GLOBALS['lang'] ?? [];
$backoffice_title = $lang['backoffice_title'] ?? 'Backoffice';
$nav_dashboard = $lang['nav_dashboard'] ?? 'Dashboard';
$role_super_admin = $lang['role_super_admin'] ?? 'Super Admin';
$nav_profile = $lang['nav_profile'] ?? 'Profiel';
$nav_logout = $lang['nav_logout'] ?? 'Uitloggen';
$welcome_back = $lang['welcome_back'] ?? 'Welkom terug';
$welcome_desc = $lang['welcome_desc'] ?? 'Beheer je website eenvoudig en snel.';
$pages_label = $lang['pages_label'] ?? "Pagina's";
$media_files_label = $lang['media_files_label'] ?? 'Media Bestanden';
$users_label = $lang['users_label'] ?? 'Gebruikers';
$templates_label = $lang['templates_label'] ?? 'Templates';
$latest_pages_title = $lang['latest_pages_title'] ?? "Laatst bewerkte pagina's";
$no_other_pages_found = $lang['no_other_pages_found'] ?? "Geen andere pagina's gevonden.";
$system_actions_title = $lang['system_actions_title'] ?? 'Systeem Acties';
$system_actions_desc = $lang['system_actions_desc'] ?? 'Kritieke acties voor je CMS installatie.';
$reset_install_btn = $lang['reset_install_btn'] ?? 'Herstart Installatie';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'nl' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $nav_dashboard ?> | Fritsion CMS</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/assets/logo/logo_fritsion_cms_favicon.png">
    <link rel="stylesheet" href="/assets/css/admin_shared.css">
</head>

<body>
    <!-- Sidebar -->
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <div class="main-wrapper">
        <header class="topbar">
            <div style="font-weight: 600; color: var(--text-muted);"><?= $backoffice_title ?> / <?= $nav_dashboard ?>
            </div>

            <div class="topbar-actions">
                <div class="user-widget" id="user-widget">
                    <div class="user-avatar">
                        <?php
                        $name = $_SESSION['username'] ?? 'Admin';
                        echo strtoupper(substr($name, 0, 1) . (strlen($name) > 1 ? substr($name, 1, 1) : ''));
                        ?>
                    </div>
                    <div class="user-info">
                        <span class="user-name"><?= $_SESSION['username'] ?? 'Admin' ?></span>
                        <span class="user-role"><?= $role_super_admin ?></span>
                    </div>
                    <!-- User Menu Dropdown -->
                    <div class="user-menu" id="user-menu">
                        <a href="/backoffice/profile" class="menu-item"><?= $nav_profile ?></a>
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

        <!-- Main Content Area -->
        <main class="content">
            <section class="welcome-card">
                <h1><?= $welcome_back ?> <?= $_SESSION['username'] ?? 'Admin' ?>!</h1>
                <p><?= $welcome_desc ?></p>
            </section>



            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📄</div>
                    <div class="stat-value"><?= $pageCount ?? 0 ?></div>
                    <div class="stat-label"><?= $pages_label ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🎨</div>
                    <div class="stat-value"><?= $templateCount ?? 0 ?></div>
                    <div class="stat-label"><?= $templates_label ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🖼️</div>
                    <div class="stat-value"><?= $mediaCount ?? 0 ?></div>
                    <div class="stat-label"><?= $media_files_label ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-value">1</div>
                    <div class="stat-label"><?= $users_label ?></div>
                </div>
                <div class="stat-card" style="flex-direction: column; align-items: flex-start; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 15px; width: 100%;">
                        <div class="stat-icon"
                            style="margin: 0; color: <?= (($siteStatus ?? 'inactive') === 'active' ? '#22c55e' : '#ef4444') ?>; background: <?= (($siteStatus ?? 'inactive') === 'active' ? 'rgba(34, 197, 94, 0.1)' : 'rgba(239, 68, 68, 0.1)') ?>;">
                            🌐</div>
                        <div style="flex: 1;">
                            <div class="stat-value"
                                style="font-size: 1.25rem; color: <?= (($siteStatus ?? 'inactive') === 'active' ? '#22c55e' : '#ef4444') ?>;">
                                <?= (($siteStatus ?? 'inactive') === 'active' ? ($lang['active_caps'] ?? 'ACTIEF') : ($lang['inactive_caps'] ?? 'INACTIEF')) ?>
                            </div>
                            <div class="stat-label" style="font-size: 0.75rem;">
                                <?= $lang['website_status_label'] ?? 'Website Status' ?>
                            </div>
                        </div>
                    </div>
                    <a href="/backoffice/site-status/toggle" class="btn-secondary"
                        style="width: 100%; padding: 8px; font-size: 0.75rem; text-align: center; border-radius: 8px; font-weight: 700;">
                        <?= (($siteStatus ?? 'inactive') === 'active' ? ($lang['deactivate'] ?? 'Deactiveren') : ($lang['activate'] ?? 'Activeren')) ?>
                    </a>
                </div>
            </section>

            <section class="dashboard-grid"
                style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px; margin-top: 40px;">
                <!-- Content Overview -->
                <div class="stat-card" style="min-height: 300px;">
                    <h3 style="margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                        <span style="color: var(--accent-green);">📄</span> <?= $latest_pages_title ?>
                    </h3>
                    <div style="background: rgba(255, 255, 255, 0.03); border-radius: 12px; padding: 15px;">
                        <?php if (empty($latestPages)): ?>
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; padding: 10px; border-bottom: 1px solid var(--glass-border);">
                                <div>
                                    <a href="/backoffice/pages" style="text-decoration: none; color: inherit;">
                                        <span style="font-weight: 500;">Tijdelijke Home</span>
                                        <br><small style="color: var(--text-muted);">/ (Root)</small>
                                    </a>
                                </div>
                            </div>
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; padding: 10px; border-bottom: 1px solid var(--glass-border);">
                                <div>
                                    <a href="/backoffice/pages" style="text-decoration: none; color: inherit;">
                                        <span style="font-weight: 500;">Demo Pagina </span> <span
                                            style="font-size: 0.7rem; background: var(--blue); color: white; padding: 2px 6px; border-radius: 4px; margin-left: 5px;">DEMO</span>
                                        <br><small style="color: var(--text-muted);">/demo</small>
                                    </a>
                                </div>
                            </div>
                            <div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 0.9rem;">
                                <?= $no_other_pages_found ?>
                            </div>
                        <?php else: ?>
                            <?php foreach ($latestPages as $page): ?>
                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; padding: 10px; border-bottom: 1px solid var(--glass-border);">
                                    <div>
                                        <a href="/backoffice/pages/edit/<?= $page['id'] ?>"
                                            style="text-decoration: none; color: inherit;">
                                            <span style="font-weight: 500;"><?= htmlspecialchars($page['title']) ?></span>
                                            <br><small
                                                style="color: var(--text-muted);">/<?= htmlspecialchars($page['slug']) ?></small>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- System Actions -->
                <div class="stat-card">
                    <h3 style="margin-bottom: 20px; font-weight: 600;"><?= $system_actions_title ?></h3>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 25px;">
                        <?= $system_actions_desc ?>
                    </p>

                    <a href="/reset_install.php" class="btn-reset"
                        style="display: inline-block; width: 100%; padding: 15px; background: rgba(239, 68, 68, 0.1); color: #ef4444; text-decoration: none; border-radius: 12px; font-weight: 600; text-align: center; border: 1px solid rgba(239, 68, 68, 0.2); transition: all 0.3s;">
                        ⚠️ <?= $reset_install_btn ?>
                    </a>

                    <style>
                        .btn-reset:hover {
                            background: #ef4444 !important;
                            color: white !important;
                            box-shadow: 0 10px 20px rgba(239, 68, 68, 0.2);
                        }
                    </style>
                </div>
            </section>
        </main>
    </div>

    <script>
        const userWidget = document.getElementById('user-widget');
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