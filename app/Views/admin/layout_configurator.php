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
    <title><?= $pageType === 'content' ? ($lang['option_content_page'] ?? 'Contentpagina') . ' Layout' : ($lang['badge_homepage'] ?? 'Homepage') . ' Layout' ?> | Fritsion CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/png" href="/assets/logo/logo_fritsion_cms_favicon.png">
    <link rel="shortcut icon" href="/assets/logo/logo_fritsion_cms_favicon.ico">
    <link rel="stylesheet" href="/assets/css/admin_shared.css">
</head>

<body>
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <div class="main-wrapper">
        <header class="topbar">
            <div style="font-weight: 600; color: var(--text-muted);">
                <?= $nav_templates ?> / <?= $pageType === 'content' ? ($lang['option_content_page'] ?? 'Contentpagina') : ($lang['badge_homepage'] ?? 'Homepage') ?>
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

        <div class="config-container">
            <!-- Form Side -->
            <div class="config-panel" id="configPanel">
                <div class="config-section" id="section-header">
                    <h2><span>🎨</span> <?= $lang['title_header'] ?? 'Header' ?></h2>
                    <div class="form-group">
                        <label class="form-label"><?= $lang['label_amount_sections'] ?? 'Aantal vlakken' ?></label>
                        <input type="number" class="form-input" id="headerCount" min="1" max="5"
                            onchange="updateHeaderCount(this.value)">
                    </div>
                    <div id="headerSections"></div>
                </div>

                <div class="config-section" id="section-main">
                    <h2><span>📄</span> <?= $lang['title_main_section'] ?? 'Middenstuk' ?></h2>
                    <div id="mainRows"></div>
                    <button class="btn-add" onclick="addRow()"><span>➕</span> <?= $lang['btn_add_row'] ?? 'Rij toevoegen' ?></button>
                </div>

                <div class="config-section" id="section-footer">
                    <h2><span>🏁</span> <?= $lang['title_footer'] ?? 'Footer' ?></h2>
                    <div class="form-group">
                        <label class="form-label"><?= $lang['label_amount_sections'] ?? 'Aantal vlakken' ?></label>
                        <input type="number" class="form-input" id="footerCount" min="1" max="5"
                            onchange="updateFooterCount(this.value)">
                    </div>
                    <div id="footerSections"></div>
                </div>
            </div>

            <!-- Preview Side -->
            <div class="preview-panel">
                <div class="preview-canvas">
                    <div class="pv-header" id="pvHeader"></div>
                    <div class="pv-main" id="pvMain"></div>
                    <div class="pv-footer" id="pvFooter"></div>
                </div>
            </div>
        </div>

        <?php 
            $formAction = "/backoffice/templates/" . ($pageType === 'content' ? 'content' : 'homepage') . "/save";
            if (isset($template['id'])) {
                $formAction = "/backoffice/templates/edit/" . $template['id'] . "/save";
            }
        ?>
        <form action="<?= $formAction ?>" method="POST" id="layoutForm">
            <input type="hidden" name="layout_json" id="layoutJsonInput">
            <div class="save-bar">
                <button type="button" class="btn-save" onclick="saveLayout()"><?= $lang['btn_save_config'] ?? 'Configuratie Opslaan' ?></button>
            </div>
        </form>
    </div>

    <div class="alert-toast" id="saveToast"><?= $lang['msg_layout_saved'] ?? 'Layout succesvol opgeslagen!' ?></div>

    <script>
        let state = <?= $layoutJson ?>;

        const contentTypes = [
            { id: 'text', name: '<?= $lang['type_text'] ?? 'Tekst' ?>', icon: '📝' },
            { id: 'image', name: '<?= $lang['type_image'] ?? 'Afbeelding' ?>', icon: '🖼️' },
            { id: 'video', name: '<?= $lang['type_video'] ?? 'Video' ?>', icon: '🎬' },
            { id: 'form', name: '<?= $lang['type_form'] ?? 'Formulier' ?>', icon: '📩' },
            { id: 'cta', name: '<?= $lang['type_cta'] ?? 'Call to Action' ?>', icon: '🎯' },
            { id: 'usps', name: '<?= $lang['type_usps'] ?? "USP's" ?>', icon: '🚀' },
            { id: 'blog', name: '<?= $lang['type_blog'] ?? 'Blogoverzicht' ?>', icon: '✍️' },
            { id: 'products', name: '<?= $lang['type_products'] ?? 'Productoverzicht' ?>', icon: '🛍️' },
            { id: 'map', name: '<?= $lang['type_map'] ?? 'Kaart' ?>', icon: '📍' },
            { id: 'html', name: '<?= $lang['type_html'] ?? 'Custom HTML' ?>', icon: '💻' },
            { id: 'logo', name: '<?= $lang['type_logo'] ?? 'Logo' ?>', icon: '✨' },
            { id: 'menu', name: '<?= $lang['type_menu'] ?? 'Menu' ?>', icon: '☰' },
            { id: 'socials', name: '<?= $lang['type_socials'] ?? 'Social Icons' ?>', icon: '📱' }
        ];

        const labels = {
            vlakContent: '<?= $lang['label_vlak_content'] ?? 'Vlak %d content' ?>',
            moveUp: '<?= $lang['title_move_up'] ?? 'Omhoog' ?>',
            moveDown: '<?= $lang['title_move_down'] ?? 'Omlaag' ?>',
            remove: '<?= $lang['title_remove'] ?? 'Verwijderen' ?>',
            rowColumns: '<?= $lang['label_row_columns'] ?? 'Rij %d kolommen' ?>',
            columnType: '<?= $lang['label_column_type'] ?? 'Kolom %d type' ?>',
            column: '<?= $lang['label_column'] ?? 'Kolom' ?>',
            columns: '<?= $lang['label_columns'] ?? 'Kolommen' ?>'
        };

        function init() {
            document.getElementById('headerCount').value = state.header.sections.length;
            document.getElementById('footerCount').value = state.footer.sections.length;
            renderConfig();
            renderPreview();
        }

        // --- Interactive Preview Handling ---
        function handlePreviewClick(path, elementId) {
            // 1. Highlight in preview
            document.querySelectorAll('.pv-col').forEach(el => el.classList.remove('highlighted'));
            const pvElement = document.getElementById(`pv-${elementId}`);
            if (pvElement) pvElement.classList.add('highlighted');

            // 2. Find and highlight in config panel
            const configElement = document.getElementById(`config-${elementId}`);
            if (configElement) {
                // Remove existing highlights
                document.querySelectorAll('.form-group, .row-item, .col-item, .config-section').forEach(el => el.classList.remove('highlighted'));

                // Add highlight to the specific input container
                const container = configElement.closest('.form-group') || configElement.closest('.col-item') || configElement.closest('.row-item');
                if (container) {
                    container.classList.add('highlighted');

                    // Scroll into view
                    container.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        }

        // --- State Management ---
        function updateHeaderCount(val) {
            val = parseInt(val);
            while (state.header.sections.length < val) state.header.sections.push({ type: 'text' });
            while (state.header.sections.length > val) state.header.sections.pop();
            renderConfig();
            renderPreview();
        }

        function updateFooterCount(val) {
            val = parseInt(val);
            while (state.footer.sections.length < val) state.footer.sections.push({ type: 'text' });
            while (state.footer.sections.length > val) state.footer.sections.pop();
            renderConfig();
            renderPreview();
        }

        function updateSectionType(area, index, type) {
            state[area].sections[index].type = type;
            renderPreview();
        }

        function addRow() {
            state.main.rows.push({ columns: [{ type: 'text' }] });
            renderConfig();
            renderPreview();
        }

        function removeRow(index) {
            state.main.rows.splice(index, 1);
            renderConfig();
            renderPreview();
        }

        function moveRow(index, direction) {
            const newIndex = index + direction;
            if (newIndex < 0 || newIndex >= state.main.rows.length) return;

            // Swap rows
            const temp = state.main.rows[index];
            state.main.rows[index] = state.main.rows[newIndex];
            state.main.rows[newIndex] = temp;

            renderConfig();
            renderPreview();

            // Re-highlight the moved row
            setTimeout(() => {
                handlePreviewClick(`content[${newIndex}][0]`, `row-${newIndex}-col-0`);
            }, 100);
        }

        function updateColCount(rowIndex, count) {
            count = parseInt(count);
            let row = state.main.rows[rowIndex];
            while (row.columns.length < count) row.columns.push({ type: 'text' });
            while (row.columns.length > count) row.columns.pop();
            renderConfig();
            renderPreview();
        }

        function updateColType(rowIndex, colIndex, type) {
            state.main.rows[rowIndex].columns[colIndex].type = type;
            renderPreview();
        }

        // --- Rendering ---
        function renderConfig() {
            // Header Sections
            let hHtml = '';
            state.header.sections.forEach((sec, i) => {
                hHtml += `<div class="form-group">
                    <label class="form-label">${labels.vlakContent.replace('%d', i + 1)}</label>
                    <select class="form-select" id="config-header-${i}" onchange="updateSectionType('header', ${i}, this.value)">
                        ${renderOptions(sec.type)}
                    </select>
                </div>`;
            });
            document.getElementById('headerSections').innerHTML = hHtml;

            // Main Rows
            let mHtml = '';
            state.main.rows.forEach((row, ri) => {
                const isFirst = ri === 0;
                const isLast = ri === state.main.rows.length - 1;

                mHtml += `<div class="row-item" id="config-row-${ri}">
                    <div class="row-actions">
                        <button class="btn-action" title="${labels.moveUp}" onclick="moveRow(${ri}, -1)" ${isFirst ? 'disabled' : ''}>↑</button>
                        <button class="btn-action" title="${labels.moveDown}" onclick="moveRow(${ri}, 1)" ${isLast ? 'disabled' : ''}>↓</button>
                        <button class="btn-action remove" title="${labels.remove}" onclick="removeRow(${ri})">✖</button>
                    </div>
                    <div class="form-group">
                        <label class="form-label">${labels.rowColumns.replace('%d', ri + 1)}</label>
                        <select class="form-select" onchange="updateColCount(${ri}, this.value)">
                            ${[1, 2, 3, 4].map(n => `<option value="${n}" ${row.columns.length == n ? 'selected' : ''}>${n} ${n > 1 ? labels.columns : labels.column}</option>`).join('')}
                        </select>
                    </div>
                    <div class="cols-container">
                        ${row.columns.map((col, ci) => `
                            <div class="col-item" id="config-container-row-${ri}-col-${ci}">
                                <label class="form-label" style="font-size: 0.75rem;">${labels.columnType.replace('%d', ci + 1)}</label>
                                <select class="form-select" id="config-row-${ri}-col-${ci}" onchange="updateColType(${ri}, ${ci}, this.value)">
                                    ${renderOptions(col.type)}
                                </select>
                            </div>
                        `).join('')}
                    </div>
                </div>`;
            });
            document.getElementById('mainRows').innerHTML = mHtml;

            // Footer Sections
            let fHtml = '';
            state.footer.sections.forEach((sec, i) => {
                fHtml += `<div class="form-group">
                    <label class="form-label">${labels.vlakContent.replace('%d', i + 1)}</label>
                    <select class="form-select" id="config-footer-${i}" onchange="updateSectionType('footer', ${i}, this.value)">
                        ${renderOptions(sec.type)}
                    </select>
                </div>`;
            });
            document.getElementById('footerSections').innerHTML = fHtml;
        }

        function renderOptions(selected) {
            return contentTypes
                .map(ct => `<option value="${ct.id}" ${ct.id === selected ? 'selected' : ''}>${ct.icon} ${ct.name}</option>`)
                .join('');
        }

        function renderPreview() {
            // Header Preview
            let hEl = document.getElementById('pvHeader');
            hEl.style.gridTemplateColumns = `repeat(${state.header.sections.length}, 1fr)`;
            hEl.innerHTML = state.header.sections.map((sec, i) => {
                let info = contentTypes.find(ct => ct.id === sec.type);
                return `<div class="pv-col pv-compact" id="pv-header-${i}" onclick="handlePreviewClick('header[${i}]', 'header-${i}')">
                        <span class="pv-col-icon">${info.icon}</span>
                        <span class="pv-col-type">${info.name}</span>
                    </div>`;
            }).join('');

            // Main Preview
            document.getElementById('pvMain').innerHTML = state.main.rows.map((row, ri) => {
                return `<div class="pv-row" style="grid-template-columns: repeat(${row.columns.length}, 1fr);">
                    ${row.columns.map((col, ci) => {
                    let info = contentTypes.find(ct => ct.id === col.type);
                    return `<div class="pv-col" id="pv-row-${ri}-col-${ci}" onclick="handlePreviewClick('content[${ri}][${ci}]', 'row-${ri}-col-${ci}')">
                            <span class="pv-col-icon">${info.icon}</span>
                            <span class="pv-col-type">${info.name}</span>
                        </div>`;
                }).join('')}
                </div>`;
            }).join('');

            // Footer Preview
            let fEl = document.getElementById('pvFooter');
            fEl.style.gridTemplateColumns = `repeat(${state.footer.sections.length}, 1fr)`;
            fEl.innerHTML = state.footer.sections.map((sec, i) => {
                let info = contentTypes.find(ct => ct.id === sec.type);
                return `<div class="pv-col pv-compact" id="pv-footer-${i}" onclick="handlePreviewClick('footer[${i}]', 'footer-${i}')">
                        <span class="pv-col-icon">${info.icon}</span>
                        <span class="pv-col-type">${info.name}</span>
                    </div>`;
            }).join('');
        }

        function saveLayout() {
            document.getElementById('layoutJsonInput').value = JSON.stringify(state);
            document.getElementById('layoutForm').submit();
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
            if (e.target.closest('.preview-canvas') === null && e.target.closest('.config-panel') === null) {
                // Clear highlights if clicking outside
                document.querySelectorAll('.pv-col, .form-group, .row-item, .col-item').forEach(el => el.classList.remove('highlighted'));
            }
        });

        window.onload = init;
    </script>
</body>

</html>