<?php
$lang = $GLOBALS['lang'] ?? [];
extract($lang);

$backoffice_title = $backoffice_title ?? 'Backoffice';
$pages_title = $pages_title ?? "Pagina's";
$nav_back_to_dashboard = $nav_back_to_dashboard ?? 'Terug naar Dashboard';
$role_super_admin = $role_super_admin ?? 'Super Admin';
$nav_profile = $nav_profile ?? 'Profiel';
$nav_logout = $nav_logout ?? 'Uitloggen';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'nl' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pages_title ?> | Fritsion CMS</title>
    <link rel="icon" type="image/png" href="/assets/logo/logo_fritsion_cms_favicon.png">
    <link rel="shortcut icon" href="/assets/logo/logo_fritsion_cms_favicon.ico">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/admin_shared.css">
</head>

<body>
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <div class="main-wrapper">
        <header class="topbar">
            <div style="font-weight: 600; color: var(--text-muted);"><?= $backoffice_title ?> / <?= $pages_title ?>
            </div>

            <div class="topbar-actions">
                <a href="/backoffice"
                    style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;"><?= $nav_back_to_dashboard ?></a>

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
            <?php
            $success_param = $_GET['success'] ?? null;
            $error_param = $_GET['error'] ?? null;
            ?>

            <?php if ($success_param === 'created'): ?>
                <div class="alert alert-success"><span>✅</span> <?= $success_page_created ?></div>
            <?php elseif ($success_param === 'updated'): ?>
                <div class="alert alert-success"><span>✅</span> <?= $success_page_updated ?></div>
            <?php elseif ($success_param === 'deleted'): ?>
                <div class="alert alert-success"><span>✅</span> <?= $success_page_deleted ?></div>
            <?php endif; ?>

            <?php if ($error_param === 'not_found'): ?>
                <div class="alert alert-error"><span>⚠️</span> <?= $error_page_not_found ?></div>
            <?php elseif ($error_param === 'delete_failed'): ?>
                <div class="alert alert-error"><span>⚠️</span> <?= $error_page_delete ?></div>
            <?php endif; ?>

            <div class="header-section">
                <div>
                    <h1><?= $pages_title ?></h1>
                    <p><?= $pages_desc ?></p>
                </div>
                <a href="/backoffice/pages/add" class="btn-primary">
                    <span>➕</span> <?= $btn_add_page ?>
                </a>
            </div>

            <div class="pages-card">
                <table>
                    <thead>
                        <tr>
                            <th><?= $label_title ?></th>
                            <th><?= $label_slug ?></th>
                            <th><?= $label_status ?></th>
                            <th><?= $label_actions ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pages)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                    <?= $no_other_pages_found ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pages as $page): ?>
                                <tr>
                                    <td style="font-weight: 600;">
                                        <?= htmlspecialchars($page['title']) ?>
                                        <?php if (($page['is_homepage'] ?? 0) == 1): ?>
                                            <span
                                                style="display: inline-flex; align-items: center; justify-content: center; background: var(--accent-orange); color: white; font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; margin-left: 8px; vertical-align: middle;">
                                                <?= $badge_homepage ?? 'HOME' ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><code><?= htmlspecialchars($page['slug']) ?></code></td>
                                    <td>
                                        <span class="status-badge status-<?= $page['status'] ?>">
                                            <?= ${'status_' . $page['status']} ?? $page['status'] ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <?php if ($page['status'] === 'published'): ?>
                                            <a href="/backoffice/pages/toggle/<?= $page['id'] ?>" class="btn-secondary"
                                                title="<?= $btn_unpublish ?>">
                                                <span>🔓</span>
                                            </a>
                                        <?php else: ?>
                                            <a href="/backoffice/pages/toggle/<?= $page['id'] ?>" class="btn-secondary"
                                                title="<?= $btn_publish ?>">
                                                <span>🔒</span>
                                            </a>
                                        <?php endif; ?>
                                        <a href="/backoffice/pages/edit/<?= $page['id'] ?>" class="btn-secondary"
                                            title="<?= $btn_edit ?>">
                                            <span>✏️</span>
                                        </a>
                                        <a href="/backoffice/pages/delete/<?= $page['id'] ?>" class="btn-secondary btn-danger"
                                            title="<?= $btn_delete ?>" onclick="return confirm('<?= $confirm_delete_page ?>')">
                                            <span>🗑️</span>
                                        </a>
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