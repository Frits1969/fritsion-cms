<?php
$lang = $GLOBALS['lang'] ?? [];
extract($lang);

$backoffice_title = $backoffice_title ?? 'Backoffice';
$themes_title = $themes_title ?? "Thema's";
$themes_desc = $themes_desc ?? "Beheer de visuele stijlen van uw website.";
$nav_back_to_dashboard = $nav_back_to_dashboard ?? 'Terug naar Dashboard';
$role_super_admin = $role_super_admin ?? 'Super Admin';
$nav_profile = $nav_profile ?? 'Profiel';
$nav_logout = $nav_logout ?? 'Uitloggen';
$btn_add_theme = $btn_add_theme ?? 'Nieuw thema toevoegen';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'nl' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $themes_title ?> | Fritsion CMS</title>
    <link rel="icon" type="image/png" href="/assets/logo/logo_fritsion_cms_favicon.png">
    <link rel="stylesheet" href="/assets/css/admin_shared.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <div class="main-wrapper">
        <header class="topbar">
            <div style="font-weight: 600; color: var(--text-muted);"><?= $backoffice_title ?> / <?= $themes_title ?></div>

            <div class="topbar-actions">
                <a href="/backoffice" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;"><?= $nav_back_to_dashboard ?></a>

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
                    <div class="user-menu" id="user-menu">
                        <a href="/backoffice/profile" class="menu-item"><?= $nav_profile ?></a>
                        <hr style="margin: 5px 0; border: none; border-top: 1px solid var(--glass-border);">
                        <a href="/backoffice/logout" class="menu-item logout"><?= $nav_logout ?></a>
                    </div>
                </div>

                <div class="lang-select" id="lang-switcher">
                    <?php $selectedLang = $_SESSION['lang'] ?? 'nl'; ?>
                    <a href="?lang=nl" class="<?= $selectedLang === 'nl' ? 'active' : '' ?>" onclick="handleFlagClick(event, 'nl')">
                        <img src="/assets/flags/nl.svg" alt="Nederlands" class="flag-icon">
                    </a>
                    <a href="?lang=en" class="<?= $selectedLang === 'en' ? 'active' : '' ?>" onclick="handleFlagClick(event, 'en')">
                        <img src="/assets/flags/en.svg" alt="English" class="flag-icon">
                    </a>
                </div>
            </div>
        </header>

        <main class="content">
            <?php
            $success_param = $_GET['success'] ?? null;
            $error_param = $_GET['error'] ?? null;
            ?>

            <?php if ($success_param === 'created'): ?>
                <div class="alert alert-success"><span>✅</span> Thema succesvol aangemaakt.</div>
            <?php elseif ($success_param === 'updated'): ?>
                <div class="alert alert-success"><span>✅</span> Thema succesvol bijgewerkt.</div>
            <?php elseif ($success_param === 'deleted'): ?>
                <div class="alert alert-success"><span>✅</span> Thema succesvol verwijderd.</div>
            <?php elseif ($success_param === 'activated'): ?>
                <div class="alert alert-success"><span>✅</span> Thema succesvol geactiveerd.</div>
            <?php endif; ?>

            <?php if ($error_param === 'not_found'): ?>
                <div class="alert alert-error"><span>⚠️</span> Thema niet gevonden.</div>
            <?php elseif ($error_param === 'cannot_delete_default'): ?>
                <div class="alert alert-error"><span>⚠️</span> Het standaard thema kan niet worden verwijderd.</div>
            <?php elseif ($error_param === 'cannot_delete_active'): ?>
                <div class="alert alert-error"><span>⚠️</span> Het actieve thema kan niet worden verwijderd. Activeer eerst een ander thema.</div>
            <?php endif; ?>

            <div class="header-section">
                <div>
                    <h1><?= $themes_title ?></h1>
                    <p><?= $themes_desc ?></p>
                </div>
                <a href="/backoffice/themes/add" class="btn-primary">
                    <span>➕</span> <?= $btn_add_theme ?>
                </a>
            </div>

            <div class="pages-card">
                <table>
                    <thead>
                        <tr>
                            <th>Naam</th>
                            <th>Status</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($themes)): ?>
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                    Geen thema's gevonden.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($themes as $theme): ?>
                                <tr>
                                    <td style="font-weight: 600;">
                                        <a href="/backoffice/themes/edit/<?= $theme['id'] ?>" style="text-decoration: none; color: inherit;"><?= htmlspecialchars($theme['name']) ?></a>
                                        <?php if ($theme['is_default'] == 1): ?>
                                            <span style="display: inline-flex; align-items: center; justify-content: center; background: var(--border); color: var(--text); font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; margin-left: 8px; vertical-align: middle;">
                                                STANDAARD
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($theme['is_active'] == 1): ?>
                                            <span class="status-badge" style="background: rgba(34, 197, 94, 0.1); color: var(--success);">Actief</span>
                                        <?php else: ?>
                                            <span class="status-badge status-draft">Inactief</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="actions">
                                        <?php if ($theme['is_active'] != 1): ?>
                                            <a href="/backoffice/themes/activate/<?= $theme['id'] ?>" class="btn-secondary" title="Activeren">
                                                <span>✔️</span>
                                            </a>
                                        <?php endif; ?>
                                        <a href="/backoffice/themes/edit/<?= $theme['id'] ?>" class="btn-secondary" title="Bewerken">
                                            <span>✏️</span>
                                        </a>
                                        <?php if ($theme['is_default'] != 1 && $theme['is_active'] != 1): ?>
                                        <a href="/backoffice/themes/delete/<?= $theme['id'] ?>" class="btn-secondary btn-danger" title="Verwijderen" onclick="return confirm('Weet je zeker dat je dit thema wilt verwijderen?')">
                                            <span>🗑️</span>
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        const userWidget = document.getElementById('user-widget');
        const userMenu = document.getElementById('user-menu');
        const langSwitcher = document.getElementById('lang-switcher');

        function handleFlagClick(event, lang) {
            const currentLang = '<?= $_SESSION['lang'] ?? 'nl' ?>';
            if (currentLang === lang) {
                event.preventDefault();
                langSwitcher.classList.toggle('expanded');
                return;
            }
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
