<?php
$uri = strtok($_SERVER['REQUEST_URI'] ?? '/backoffice', '?');
$lang = $GLOBALS['lang'] ?? [];
$backoffice_title = $lang['backoffice_title'] ?? 'Backoffice';
$nav_templates = $lang['nav_templates'] ?? 'Templates';
$templates_title = $lang['templates_title'] ?? 'Templates';
$templates_desc = $lang['templates_desc'] ?? 'Beheer hier alle layouts en structuren van je website.';
$nav_dashboard = $lang['nav_dashboard'] ?? 'Dashboard';
$nav_profile = $lang['nav_profile'] ?? 'Profiel';
$nav_logout = $lang['nav_logout'] ?? 'Uitloggen';
$btn_cancel = $lang['btn_cancel'] ?? 'Annuleren';
$badge_homepage = $lang['badge_homepage'] ?? 'HOME';
$nav_back_to_dashboard = $lang['nav_back_to_dashboard'] ?? 'Terug naar Dashboard';
$role_super_admin = $lang['role_super_admin'] ?? 'Super Admin';
$btn_add_template = $lang['btn_add_template'] ?? 'Template toevoegen';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'nl' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $templates_title ?> | Fritsion CMS</title>
    <link rel="icon" type="image/png" href="/assets/logo/logo_fritsion_cms_favicon.png">
    <link rel="shortcut icon" href="/assets/logo/logo_fritsion_cms_favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/admin_shared.css">
</head>

<body>
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <div class="main-wrapper">
        <header class="topbar">
            <div style="font-weight: 600; color: var(--text-muted);"><?= $backoffice_title ?> / <?= $templates_title ?></div>
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
                        <span class="user-name"><?= $name ?></span>
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
            <div class="header-section">
                <div>
                    <h1><?= $templates_title ?></h1>
                    <p><?= $templates_desc ?></p>
                </div>
                <button class="btn-primary" onclick="openAddModal()">
                    <span>➕</span> <?= $btn_add_template ?>
                </button>
            </div>

            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th><?= $lang['label_title_caps'] ?? 'TITEL' ?></th>
                            <th><?= $lang['label_type_caps'] ?? 'TYPE' ?></th>
                            <th><?= $lang['label_usage_caps'] ?? 'GEBRUIK' ?></th>
                            <th style="text-align:right;"><?= $lang['label_actions_caps'] ?? 'ACTIES' ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($templates as $tpl): ?>
                            <?php 
                                $url = "/backoffice/templates/edit/" . $tpl['id'];
                                if ($tpl['type'] === 'homepage' && $tpl['name'] === 'Homepage') {
                                    $url = "/backoffice/templates/homepage";
                                }
                                $isHomepageTemplate = ($tpl['name'] === 'Homepage');
                            ?>
                            <tr>
                                <td style="font-weight: 600;">
                                    <?php 
                                        $tplDisplayName = htmlspecialchars($tpl['name']);
                                        if ($tplDisplayName === 'Homepage') $tplDisplayName = $lang['nav_homepage'] ?? 'Homepage';
                                        if ($tplDisplayName === 'Contentpagina') $tplDisplayName = $lang['nav_content_page'] ?? 'Content Page';
                                    ?>
                                    <a href="<?= $url ?>" style="text-decoration:none; color:inherit;"><?= $tplDisplayName ?></a>
                                    <?php if ($isHomepageTemplate): ?>
                                        <span class="badge-orange"><?= $badge_homepage ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #64748b;">
                                        <?= $tpl['type'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($isHomepageTemplate): ?>
                                        <span style="color:#d97706; font-weight:700; font-size:0.8rem;"><?= $lang['badge_main'] ?? '⭐ HOOFD' ?></span>
                                    <?php else: ?>
                                        <span style="color:var(--text-muted); font-size:0.85rem;"><?= $lang['label_extra'] ?? 'Extra' ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:right;">
                                    <div style="display:flex; gap:10px; justify-content:flex-end;">
                                        <a href="<?= $url ?>" class="btn-secondary" title="<?= $lang['btn_edit_title'] ?? 'Muteren' ?>">
                                            <span>✏️</span>
                                        </a>
                                        <?php if (!$isHomepageTemplate): ?>
                                            <a href="/backoffice/templates/delete/<?= $tpl['id'] ?>" class="btn-secondary btn-danger" 
                                               onclick="return confirm('<?= $lang['confirm_delete_template'] ?? 'Weet u zeker dat u dit template wilt verwijderen?' ?>')">
                                                <span>🗑️</span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div id="addModal" class="modal-backdrop">
        <div class="modal-card">
            <div class="modal-header">
                <h2><?= $lang['modal_new_template'] ?? 'Nieuw Template' ?></h2>
            </div>
            <form action="/backoffice/templates/add" method="POST">
                <div class="form-group">
                    <label><?= $lang['label_template_name'] ?? 'BENAMING' ?></label>
                    <input type="text" name="name" class="form-control" placeholder="Bijv. Projectoverzicht" required>
                </div>
                <div class="form-group">
                    <label><?= $lang['label_type_caps'] ?? 'TYPE' ?></label>
                    <select name="type" class="form-control">
                        <option value="content"><?= $lang['option_content_page'] ?? 'Inhoudspagina' ?></option>
                        <option value="homepage"><?= $lang['option_homepage_variant'] ?? 'Homepagina Variant' ?></option>
                    </select>
                </div>
                <div style="display:flex; justify-content:flex-end; gap:15px; margin-top: 30px;">
                    <button type="button" onclick="closeAddModal()" style="padding:12px 25px; border:none; background:#f1f5f9; color:#64748b; font-weight:700; border-radius:12px; cursor:pointer;"><?= $btn_cancel ?></button>
                    <button type="submit" class="btn-primary"><?= $lang['btn_create'] ?? 'Aanmaken' ?></button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() { document.getElementById('addModal').classList.add('active'); }
        function closeAddModal() { document.getElementById('addModal').classList.remove('active'); }
        const userWidget = document.getElementById('user-widget');
        const userMenu = document.getElementById('user-menu');
        userWidget.addEventListener('click', (e) => { e.stopPropagation(); userMenu.classList.toggle('active'); });

        const langSwitcher = document.getElementById('lang-switcher');
        function handleFlagClick(e, lang) {
            if (!langSwitcher.classList.contains('expanded')) {
                e.preventDefault();
                langSwitcher.classList.add('expanded');
            }
        }

        document.addEventListener('click', (e) => { 
            if (userMenu && !userWidget.contains(e.target)) userMenu.classList.remove('active'); 
            if (langSwitcher && !langSwitcher.contains(e.target)) langSwitcher.classList.remove('expanded');
            if (e.target.classList.contains('modal-backdrop')) closeAddModal();
        });
    </script>
</body>
</html>
