<?php
$uri = strtok($_SERVER['REQUEST_URI'] ?? '/backoffice', '?');
$lang = $GLOBALS['lang'] ?? [];
$nav_dashboard = $lang['nav_dashboard'] ?? 'Dashboard';
$nav_pages = $lang['nav_pages'] ?? 'Pagina\'s';
$nav_media = $lang['nav_media'] ?? 'Media';
$nav_templates = $lang['nav_templates'] ?? 'Templates';
$nav_themes = $lang['nav_themes'] ?? 'Thema\'s';
$nav_settings = $lang['nav_settings'] ?? 'Instellingen';
$nav_visit_site = $lang['nav_visit_site'] ?? 'Website bekijken';
$backoffice_title = $lang['backoffice_title'] ?? 'Fritsion Backoffice';
$role_super_admin = $lang['role_super_admin'] ?? 'Super Admin';
$nav_profile = $lang['nav_profile'] ?? 'Profiel';
$nav_logout = $lang['nav_logout'] ?? 'Uitloggen';
$nav_back_to_dashboard = $lang['nav_back_to_dashboard'] ?? 'Terug naar Dashboard';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'nl' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage Templates | Fritsion CMS</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/png" href="/assets/logo/logo_fritsion_cms_favicon.png">
    <link rel="shortcut icon" href="/assets/logo/logo_fritsion_cms_favicon.ico">
    <link rel="stylesheet" href="/assets/css/admin_shared.css">
</head>

<body>
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <?php
    function renderPreview($type)
    {
        $html = '<div class="preview-container">';
        $html .= '<div class="wf-header"></div>';

        switch ($type) {
            case 'usps':
                $html .= '<div class="wf-hero"></div>';
                $html .= '<div class="wf-grid" style="grid-template-columns: repeat(3, 1fr);">';
                $html .= '<div class="wf-rect" style="height: 20px;"></div><div class="wf-rect" style="height: 20px;"></div><div class="wf-rect" style="height: 20px;"></div>';
                $html .= '</div>';
                break;
            case 'cta_image':
                $html .= '<div class="wf-hero" style="display: flex; gap: 4px; padding: 4px;">';
                $html .= '<div style="flex: 1; background: rgba(255,255,255,0.2); height: 100%;"></div>';
                $html .= '<div style="flex: 1; background: var(--wf-block); height: 100%; border-radius: 4px;"></div>';
                $html .= '</div>';
                break;
            case 'services':
                $html .= '<div class="wf-hero" style="height: 30px; flex: none;"></div>';
                $html .= '<div class="wf-grid" style="grid-template-columns: repeat(3, 1fr); flex: 1;">';
                $html .= '<div class="wf-rect"></div><div class="wf-rect"></div><div class="wf-rect"></div>';
                $html .= '</div>';
                break;
            case 'blog':
                $html .= '<div class="wf-hero" style="height: 30px; flex: none;"></div>';
                $html .= '<div class="wf-grid" style="grid-template-columns: 1fr 1fr; flex: 1;">';
                $html .= '<div class="wf-rect"></div><div class="wf-rect"></div>';
                $html .= '</div>';
                break;
            case 'video':
                $html .= '<div class="wf-hero"><div style="width: 20px; height: 20px; border-radius: 50%; border: 2px solid #fff; display: flex; align-items: center; justify-content: center; font-size: 8px;">▶</div></div>';
                $html .= '<div class="wf-grid" style="grid-template-columns: 1fr 1fr;">';
                $html .= '<div class="wf-rect" style="height: 15px; opacity: 0.3;"></div><div class="wf-rect" style="height: 15px; opacity: 0.3;"></div>';
                $html .= '</div>';
                break;
            case 'split':
                $html .= '<div style="display: flex; flex: 1; gap: 0; margin: -8px; margin-top: 0;">';
                $html .= '<div style="flex: 1; padding: 10px; display: flex; flex-direction: column; gap: 4px;"><div class="wf-rect" style="height: 6px; width: 80%;"></div><div class="wf-rect" style="height: 6px; width: 60%;"></div></div>';
                $html .= '<div style="flex: 1; background: var(--wf-hero);"></div>';
                $html .= '</div>';
                break;
        }

        $html .= '<div class="wf-footer"></div>';
        $html .= '</div>';
        return $html;
    }
    ?>

    <div class="main-wrapper">
        <header class="topbar">
            <div style="font-weight: 600; color: var(--text-muted);">
                <?= $nav_templates ?> / Homepage
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
            <?php if (isset($_GET['saved'])): ?>
                <div class="alert">✅ Homepage structuur succesvol opgeslagen!</div>
            <?php endif; ?>

            <div class="header-section">
                <h1>Kies uw Homepage Structuur</h1>
                <p>Selecteer de gewenste layout voor uw startpagina. Elke structuur is geoptimaliseerd voor een
                    specifiek doel.</p>
            </div>

            <form action="/backoffice/templates/homepage/save" method="POST" id="templateForm">
                <input type="hidden" name="template" id="selectedTemplateInput" value="<?= $currentTemplate ?>">
                <div class="template-grid">
                    <?php foreach ($templates as $tpl): ?>
                        <div class="template-card <?= $currentTemplate === $tpl['id'] ? 'selected' : '' ?>"
                            onclick="selectTemplate('<?= $tpl['id'] ?>', this)">
                            <span class="selected-badge">Actief</span>

                            <?= renderPreview($tpl['preview_type'] ?? 'usps') ?>

                            <div class="template-info">
                                <h3><span>
                                        <?= $tpl['icon'] ?>
                                    </span>
                                    <?= $tpl['name'] ?>
                                </h3>
                                <p>
                                    <?= $tpl['description'] ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </form>
        </main>

        <div class="save-bar">
            <button type="button" class="btn-save" onclick="document.getElementById('templateForm').submit()">Layout
                Opslaan</button>
        </div>
    </div>

    <script>
        function selectTemplate(id, element) {
            document.querySelectorAll('.template-card').forEach(card => card.classList.remove('selected'));
            element.classList.add('selected');
            document.getElementById('selectedTemplateInput').value = id;
        }

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