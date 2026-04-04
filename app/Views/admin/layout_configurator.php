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
        <style>
            .preview-canvas { max-width: none !important; }
            .pv-empty { background: rgba(0,0,0,0.02) !important; border: 1px dashed rgba(0,0,0,0.1) !important; color: rgba(0,0,0,0.3) !important; }
            .pv-empty .pv-col-icon { opacity: 0.2; filter: grayscale(1); }
            .pv-hidden { opacity: 0.3; background: repeating-linear-gradient(45deg, rgba(200,0,0,0.06), rgba(200,0,0,0.06) 4px, transparent 4px, transparent 10px) !important; border: 2px dashed rgba(200,0,0,0.35) !important; color: rgba(200,0,0,0.6) !important; }
            
            .device-switcher .btn-device {
                background: #e2e8f0;
                border: 1px solid var(--glass-border);
                padding: 10px;
                cursor: pointer;
                border-radius: 12px;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 65px;
                height: 65px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            }
            .device-switcher .btn-device svg {
                width: 100%;
                height: 100%;
                transition: transform 0.2s ease, fill 0.3s ease;
            }
            
            /* Override the embedded SVG colors to match the site */
            .device-switcher .btn-device svg circle[r="30"] {
                display: none;
            }
            .device-switcher .btn-device svg rect[fill="#FFFFFF"] {
                fill: var(--text-muted);
                transition: fill 0.3s ease;
            }
            .device-switcher .btn-device svg circle[fill="#D0C4B5"],
            .device-switcher .btn-device svg rect[fill="#D0C4B5"] {
                fill: #94a3b8;
                transition: fill 0.3s ease;
            }

            .device-switcher .btn-device:hover {
                transform: translateY(-2px);
                background: #cbd5e1;
                box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            }
            
            .device-switcher .btn-device.active {
                background: rgba(232, 24, 106, 0.1);
                border-color: rgba(232, 24, 106, 0.3);
                box-shadow: 0 4px 15px rgba(232, 24, 106, 0.1);
            }
            
            /* Colors for the active icon shape */
            .device-switcher .btn-device.active svg rect[fill="#FFFFFF"] {
                fill: var(--accent-pink);
            }
            .device-switcher .btn-device.active svg circle[fill="#D0C4B5"],
            .device-switcher .btn-device.active svg rect[fill="#D0C4B5"] {
                fill: rgba(232, 24, 106, 0.6);
            }
            
            .grid-guide {
                position: absolute;
                top: 0; left: 0; right: 0; bottom: 0;
                pointer-events: none;
                display: grid;
                gap: 10px;
                z-index: 0;
            }
            .grid-guide-col {
                background: rgba(0, 0, 0, 0.03);
                border-left: 1px dashed rgba(0, 0, 0, 0.1);
                border-right: 1px dashed rgba(0, 0, 0, 0.1);
                height: 100%;
            }
        </style>
</head>

<body>
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <div class="main-wrapper">
        <header class="topbar">
            <div style="font-weight: 600; color: var(--text-muted);">
                <?= $nav_templates ?> / <?= htmlspecialchars($template['name']) ?>
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
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-error" style="margin-bottom: 20px;">
                        <span>⚠️</span> 
                        <?= $lang['error_' . $_GET['error']] ?? 'Er is een fout opgetreden: ' . htmlspecialchars($_GET['error']) ?>
                    </div>
                <?php endif; ?>

                <div class="config-section" id="section-template">
                    <h2><span>⚙️</span> <?= $lang['title_template_settings'] ?? 'Template Instellingen' ?></h2>
                    <div class="form-group">
                        <label class="form-label"><?= $lang['label_template_name'] ?? 'Benaming' ?></label>
                        <input type="text" class="form-input" id="tplName" value="<?= htmlspecialchars($template['name']) ?>" placeholder="bijv. Homepage Winter">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $lang['label_type_caps'] ?? 'Type' ?></label>
                        <select class="form-select" id="tplType">
                            <option value="content" <?= $template['type'] === 'content' ? 'selected' : '' ?>><?= $lang['option_content_page'] ?? 'Inhoudspagina' ?></option>
                            <option value="homepage" <?= $template['type'] === 'homepage' ? 'selected' : '' ?>><?= $lang['option_homepage_variant'] ?? 'Homepagina Variant' ?></option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-top: 25px;">
                        <label class="form-label"><?= $lang['label_device_preview'] ?? 'Device Preview' ?></label>
                        <div class="device-switcher" style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <button type="button" class="btn-device" onclick="setDevicePreview(this, 1)" title="Smartphones (≤ 480px) - Max 1 kolom">
                                <svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="title_smartphone"><title id="title_smartphone">Smartphones</title><circle cx="32" cy="32" r="30" fill="#F3E7D8"/><rect x="20" y="12" width="24" height="40" rx="3" ry="3" fill="#FFFFFF"/><rect x="30" y="14" width="4" height="2" rx="1" ry="1" fill="#D0C4B5"/></svg>
                            </button>
                            <button type="button" class="btn-device" onclick="setDevicePreview(this, 2)" title="Kleine tablets (≥ 768px) - Max 2 kolommen">
                                <svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="title_tablet_small"><title id="title_tablet_small">Kleine tablets</title><circle cx="32" cy="32" r="30" fill="#F3E7D8"/><rect x="18" y="16" width="28" height="32" rx="4" ry="4" fill="#FFFFFF"/><circle cx="32" cy="45" r="1.5" fill="#D0C4B5"/></svg>
                            </button>
                            <button type="button" class="btn-device" onclick="setDevicePreview(this, 3)" title="Grote tablets / kleine laptops (≥ 1024px) - Max 3 kolommen">
                                <svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="title_tablet_big"><title id="title_tablet_big">Grote tablets / kleine laptops</title><circle cx="32" cy="32" r="30" fill="#F3E7D8"/><rect x="16" y="18" width="32" height="24" rx="4" ry="4" fill="#FFFFFF"/><rect x="16" y="44" width="32" height="4" rx="2" ry="2" fill="#D0C4B5"/></svg>
                            </button>
                            <button type="button" class="btn-device" onclick="setDevicePreview(this, 4)" title="Laptops / desktops (≥ 1280px) - Max 4 kolommen">
                                <svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="title_pc_small"><title id="title_pc_small">Laptops / desktops</title><circle cx="32" cy="32" r="30" fill="#F3E7D8"/><rect x="14" y="18" width="36" height="22" rx="3" ry="3" fill="#FFFFFF"/><rect x="22" y="42" width="20" height="3" rx="1.5" ry="1.5" fill="#D0C4B5"/><rect x="26" y="45" width="12" height="3" rx="1.5" ry="1.5" fill="#D0C4B5"/></svg>
                            </button>
                            <button type="button" class="btn-device active" onclick="setDevicePreview(this, 12)" title="Grote schermen (≥ 1536px) - Max 12 kolommen">
                                <svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="title_pc_big"><title id="title_pc_big">Grote schermen</title><circle cx="32" cy="32" r="30" fill="#F3E7D8"/><rect x="12" y="18" width="40" height="24" rx="3" ry="3" fill="#FFFFFF"/><rect x="26" y="44" width="12" height="3" rx="1.5" ry="1.5" fill="#D0C4B5"/><rect x="22" y="47" width="20" height="3" rx="1.5" ry="1.5" fill="#D0C4B5"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="config-section" id="section-header">
                    <h2><span>🎨</span> <?= $lang['title_header'] ?? 'Header' ?></h2>
                    <div class="form-group">
                        <label class="form-label"><?= $lang['label_amount_sections'] ?? 'Aantal vlakken' ?></label>
                        <input type="number" class="form-input" id="headerCount" min="1" max="12"
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
                        <input type="number" class="form-input" id="footerCount" min="1" max="12"
                            onchange="updateFooterCount(this.value)">
                    </div>
                    <div id="footerSections"></div>
                </div>
            </div>

            <!-- Preview Side -->
            <div class="preview-panel" style="overflow-x: auto; display: block;">
                <div class="preview-canvas" id="previewCanvas" style="width: 100%; transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1); margin: 0 auto;">
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
            <input type="hidden" name="name" id="nameInput">
            <input type="hidden" name="type" id="typeInput">
            <div class="save-bar">
                <button type="button" class="btn-save" onclick="saveLayout()"><?= $lang['btn_save_config'] ?? 'Configuratie Opslaan' ?></button>
            </div>
        </form>
    </div>

    <div class="alert-toast" id="saveToast">
        ✅ <?= $lang['msg_layout_saved'] ?? 'Layout succesvol opgeslagen!' ?>
        &nbsp;|&nbsp; 🔄 <?= $lang['msg_pages_auto_updated'] ?? 'Alle pagina\'s die deze template gebruiken zijn automatisch bijgewerkt.' ?>
    </div>

    <?php if (isset($_GET['saved'])): ?>
    <div id="saved-banner" style="position:fixed; bottom:30px; left:50%; transform:translateX(-50%); background:linear-gradient(135deg,#10b981,#059669); color:white; padding:14px 28px; border-radius:50px; font-weight:600; font-size:0.95rem; z-index:9999; box-shadow:0 10px 30px rgba(16,185,129,0.3); display:flex; gap:12px; align-items:center;">
        <span>✅</span>
        <span><?= $lang['msg_layout_saved'] ?? 'Template opgeslagen!' ?></span>
        <span style="opacity:0.8; font-weight:400;">— <?= $lang['msg_pages_auto_updated'] ?? 'Pagina\'s bijgewerkt.' ?></span>
        <button onclick="document.getElementById('saved-banner').remove()" style="background:rgba(255,255,255,0.2); border:none; color:white; border-radius:50%; width:24px; height:24px; cursor:pointer; font-size:1rem; display:flex; align-items:center; justify-content:center; flex-shrink:0;">&times;</button>
    </div>
    <script>setTimeout(() => { const b = document.getElementById('saved-banner'); if(b) b.remove(); }, 5000);</script>
    <?php endif; ?>

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
            { id: 'language', name: '<?= $lang['type_language'] ?? 'Taal Selectie' ?>', icon: '🌐' },
            { id: 'logo', name: '<?= $lang['type_logo'] ?? 'Logo' ?>', icon: '✨' },
            { id: 'menu', name: '<?= $lang['type_menu'] ?? 'Menu' ?>', icon: '☰' },
            { id: 'empty', name: '<?= $lang['type_empty'] ?? 'Leeg Vlak' ?>', icon: '⬜' }
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

        let currentMaxCols = 12;

        // Calculate the visual span for an item on a specific device.
        // item.deviceWidths[mc] = explicit override (device-native cols, 0 = hidden).
        // Falls back to auto-scaling from item.width (12-col base).
        function calcDeviceSpan(item, deviceMaxCols) {
            const dw = item.deviceWidths;
            if (dw && dw[deviceMaxCols] !== undefined) {
                return dw[deviceMaxCols]; // explicit override; can be 0
            }
            const width12 = item.width ?? 12;
            if (width12 === 0) return 0;          // hidden on base → hidden everywhere
            if (deviceMaxCols === 1) return 1;    // smartphone always 1
            if (width12 >= 12) return deviceMaxCols;
            const span = Math.round((width12 / 12) * deviceMaxCols);
            return Math.max(1, Math.min(deviceMaxCols, span));
        }

        // Auto-span without device override (used in option label "Auto (n)").
        function calcAutoSpan(item, deviceMaxCols) {
            const width12 = item.width ?? 12;
            if (width12 === 0 || deviceMaxCols === 1) return deviceMaxCols === 1 ? (width12 === 0 ? 0 : 1) : 0;
            if (width12 >= 12) return deviceMaxCols;
            const span = Math.round((width12 / 12) * deviceMaxCols);
            return Math.max(1, Math.min(deviceMaxCols, span));
        }

        // Store a device-specific width override on an item.
        // value '' = remove override (revert to auto); 0 = hidden.
        function setItemDeviceWidth(item, deviceMaxCols, value) {
            if (deviceMaxCols === 12) {
                item.width = (value === '' || value === null) ? 12 : parseInt(value);
            } else {
                if (!item.deviceWidths) item.deviceWidths = {};
                if (value === '' || value === null) {
                    delete item.deviceWidths[deviceMaxCols];
                } else {
                    item.deviceWidths[deviceMaxCols] = parseInt(value);
                }
            }
        }

        function setHeaderSectionDeviceWidth(index, value) {
            setItemDeviceWidth(state.header.sections[index], currentMaxCols, value);
            renderPreview();
        }

        function setFooterSectionDeviceWidth(index, value) {
            setItemDeviceWidth(state.footer.sections[index], currentMaxCols, value);
            renderPreview();
        }

        function setColDeviceWidth(rowIndex, colIndex, value) {
            setItemDeviceWidth(state.main.rows[rowIndex].columns[colIndex], currentMaxCols, value);
            renderPreview();
        }

        // Build <option> list for the width selector, device-aware.
        // On groot scherm (12): 0–12 in 12-col units (0 = verborgen).
        // On other devices: Auto(n) + 0–maxCols in device-native units.
        function getDeviceWidthOptions(item, deviceMaxCols) {
            if (deviceMaxCols === 12) {
                const cur = item.width ?? 12;
                let opts = `<option value="0" ${cur === 0 ? 'selected' : ''}>0 — verborgen</option>`;
                for (let i = 1; i <= 12; i++) {
                    opts += `<option value="${i}" ${cur === i ? 'selected' : ''}>${i}</option>`;
                }
                return opts;
            }
            const dw = item.deviceWidths || {};
            const curVal = dw[deviceMaxCols]; // undefined = auto
            const autoN  = calcAutoSpan(item, deviceMaxCols);
            let opts = `<option value="" ${curVal === undefined ? 'selected' : ''}>Auto (${autoN})</option>`;
            opts += `<option value="0" ${curVal === 0 ? 'selected' : ''}>0 — verborgen</option>`;
            for (let i = 1; i <= deviceMaxCols; i++) {
                opts += `<option value="${i}" ${curVal === i ? 'selected' : ''}>${i}</option>`;
            }
            return opts;
        }

        function setDevicePreview(btn, maxCols = 12) {
            document.querySelectorAll('.btn-device').forEach(el => el.classList.remove('active'));
            if (btn) btn.classList.add('active');
            
            const canvas = document.getElementById('previewCanvas');
            if (canvas) {
                let w = '90%';
                if (maxCols === 1) w = '18%';
                else if (maxCols === 2) w = '36%';
                else if (maxCols === 3) w = '54%';
                else if (maxCols === 4) w = '72%';
                else w = '90%';
                canvas.style.width = w;
            }
            
            if (currentMaxCols !== maxCols) {
                currentMaxCols = maxCols;
                // Note: we do NOT call enforceMaxCols() here — the device switcher is
                // purely a preview. calcDeviceSpan() scales widths visually, and CSS Grid
                // auto-wraps items that no longer fit onto the next row.

                const hCount = document.getElementById('headerCount');
                if(hCount) { 
                    hCount.max = maxCols; 
                    hCount.disabled = (maxCols === 1);
                }
                const fCount = document.getElementById('footerCount');
                if(fCount) { 
                    fCount.max = maxCols; 
                    fCount.disabled = (maxCols === 1);
                }

                renderConfig();
                renderPreview();
            }
        }

        function enforceMaxCols(max) {
            if (state.header.sections.length > max) state.header.sections = state.header.sections.slice(0, max);
            if (state.footer.sections.length > max) state.footer.sections = state.footer.sections.slice(0, max);
            
            state.main.rows.forEach(row => {
                if (row.columns.length > max) {
                    row.columns = row.columns.slice(0, max);
                    const defaultWidth = Math.floor(12 / max);
                    row.columns.forEach(col => col.width = defaultWidth);
                    const currentSum = row.columns.reduce((sum, col) => sum + col.width, 0);
                    if (currentSum < 12 && row.columns.length > 0) {
                        row.columns[row.columns.length - 1].width += (12 - currentSum);
                    }
                }
            });
        }

        // --- State Management ---
        function updateHeaderCount(val) {
            val = parseInt(val);
            if (val < 1) val = 1;
            const defaultWidth = 4; // default to a third
            while (state.header.sections.length < val) state.header.sections.push({ type: 'logo', width: defaultWidth });
            while (state.header.sections.length > val) state.header.sections.pop();
            
            renderConfig();
            renderPreview();
        }

        function updateFooterCount(val) {
            val = parseInt(val);
            if (val < 1) val = 1;
            const defaultWidth = 4; // default to a third
            while (state.footer.sections.length < val) state.footer.sections.push({ type: 'text', width: defaultWidth });
            while (state.footer.sections.length > val) state.footer.sections.pop();

            renderConfig();
            renderPreview();
        }

        function updateSectionType(area, index, type) {
            state[area].sections[index].type = type;
            renderPreview();
        }

        function updateAreaRowSpan(area, index, rowSpan) {
            state[area].sections[index].rowSpan = parseInt(rowSpan);
            renderPreview();
        }

        function addRow() {
            state.main.rows.push({ columns: [{ type: 'text', width: 12, rowSpan: 1 }], height: '90px' });
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
            
            while (row.columns.length < count) {
                row.columns.push({ type: 'text', width: 1 });
            }
            while (row.columns.length > count) {
                row.columns.pop();
            }

            const defaultWidth = Math.floor(12 / count);
            row.columns.forEach((col, i) => {
                col.width = defaultWidth;
            });

            const currentSum = row.columns.reduce((sum, col) => sum + col.width, 0);
            if (currentSum < 12 && row.columns.length > 0) {
                row.columns[row.columns.length - 1].width += (12 - currentSum);
            }

            renderConfig();
            renderPreview();
        }

        function updateColWidth(rowIndex, colIndex, width) {
            state.main.rows[rowIndex].columns[colIndex].width = parseInt(width);
            renderPreview();
        }

        function updateColRowSpan(rowIndex, colIndex, rowSpan) {
            state.main.rows[rowIndex].columns[colIndex].rowSpan = parseInt(rowSpan);
            renderPreview();
        }

        function updateColType(rowIndex, colIndex, type) {
            state.main.rows[rowIndex].columns[colIndex].type = type;
            renderPreview();
        }

        function updateAreaHeight(area, val) {
            state[area].height = val;
            renderPreview();
        }

        function updateRowHeight(rowIndex, val) {
            state.main.rows[rowIndex].height = val;
            renderPreview();
        }

        // --- Rendering ---

        function renderConfig() {
            // Header Sections
            let hHtml = `
                <div class="form-group" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 15px; margin-bottom: 15px;">
                    <label class="form-label">${labels.height || 'Hoogte'}</label>
                    <div style="display:flex; gap:10px;">
                        <input type="text" class="form-input" style="flex:1;" placeholder="Bijv. 90px of auto" value="${state.header.height || '90px'}" oninput="updateAreaHeight('header', this.value)">
                        <select class="form-select" style="width:auto;" onchange="this.previousElementSibling.value = this.value; updateAreaHeight('header', this.value);">
                            <option value="">-- Kies --</option>
                            <option value="90px">90px</option>
                            <option value="120px">120px</option>
                            <option value="auto">Auto</option>
                        </select>
                    </div>
                </div>
            `;
            state.header.sections.forEach((sec, i) => {
                hHtml += `<div class="form-group row-item">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <label class="form-label">${labels.vlakContent.replace('%d', i + 1)}</label>
                        <div style="display:flex; align-items:center; gap:5px;">
                            <span style="font-size:1.4rem; line-height:1;">↔️</span>
                            <select class="form-select" style="width:80px; padding:2px 4px; font-size:0.75rem;" onchange="setHeaderSectionDeviceWidth(${i}, this.value)">
                                ${getDeviceWidthOptions(sec, currentMaxCols)}
                            </select>
                            <span style="font-size:1.4rem; line-height:1; color:var(--text-muted); margin-left:5px;">↕️</span>
                            <select class="form-select" style="width:50px; padding:2px 4px; font-size:0.75rem;" onchange="updateAreaRowSpan('header', ${i}, this.value)">
                                ${[1,2,3,4,5,6,7,8,9,10,11,12].map(rs => `<option value="${rs}" ${sec.rowSpan == rs ? 'selected' : ''}>${rs}</option>`).join('')}
                            </select>
                        </div>
                    </div>
                    <div style="display:flex; gap:5px;">
                        <select class="form-select" id="config-header-${i}" onchange="updateSectionType('header', ${i}, this.value)" style="flex:1;">
                            ${renderOptions(sec.type)}
                        </select>
                        <button class="btn-action remove" onclick="updateSectionType('header', ${i}, 'empty'); renderConfig();" title="Item leegmaken" style="padding: 0 8px;">🗑️</button>
                    </div>
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
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">${labels.rowColumns.replace('%d', ri + 1)}</label>
                            <select class="form-select" onchange="updateColCount(${ri}, this.value)" ${currentMaxCols === 1 ? 'disabled title="Maximaal 1 kolom"' : ''}>
                                ${[...Array(currentMaxCols).keys()].map(x => x + 1).map(n => `<option value="${n}" ${row.columns.length == n ? 'selected' : ''}>${n} ${n > 1 ? labels.columns : labels.column}</option>`).join('')}
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">${labels.height || 'Hoogte'}</label>
                            <div style="display:flex; gap:5px;">
                                <input type="text" class="form-input" style="flex:1; padding: 4px 8px; font-size: 0.85rem;" placeholder="90px" value="${row.height || '90px'}" oninput="updateRowHeight(${ri}, this.value)">
                                <select class="form-select" style="width:auto; padding:0 4px;" onchange="this.previousElementSibling.value = this.value; updateRowHeight(${ri}, this.value);">
                                    <option value="90px">90px</option>
                                    <option value="auto">Auto</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="cols-container">
                        ${row.columns.map((col, ci) => {
                            return `
                                <div class="col-item" id="config-container-row-${ri}-col-${ci}">
                                    <div style="display:flex; justify-content:space-between; align-items:center;">
                                        <label class="form-label" style="font-size: 0.75rem;">${labels.columnType.replace('%d', ci + 1)}</label>
                                        <div style="display:flex; align-items:center; gap:5px;">
                                            <span style="font-size:1.4rem; line-height:1; color:var(--text-muted);">↔️</span>
                                            <select class="form-select" style="width:80px; padding:2px 4px; font-size:0.75rem;" onchange="setColDeviceWidth(${ri}, ${ci}, this.value)">
                                                ${getDeviceWidthOptions(col, currentMaxCols)}
                                            </select>
                                            <span style="font-size:1.4rem; line-height:1; color:var(--text-muted); margin-left:5px;">↕️</span>
                                            <select class="form-select" style="width:50px; padding:2px 4px; font-size:0.75rem;" onchange="updateColRowSpan(${ri}, ${ci}, this.value)">
                                                ${([...Array(state.main.rows.length - ri).keys()].map(x => x + 1)).map(rs => `<option value="${rs}" ${col.rowSpan == rs ? 'selected' : ''}>${rs}</option>`).join('')}
                                            </select>
                                        </div>
                                    </div>
                                    <div style="display:flex; gap:5px;">
                                        <select class="form-select" id="config-row-${ri}-col-${ci}" onchange="updateColType(${ri}, ${ci}, this.value)" style="flex:1;">
                                            ${renderOptions(col.type)}
                                        </select>
                                        <button class="btn-action remove" onclick="updateColType(${ri}, ${ci}, 'empty'); renderConfig();" title="Item leegmaken" style="padding: 0 8px;">🗑️</button>
                                    </div>
                                </div>
                            `;
                        }).join('')}
                    </div>
                </div>`;
            });
            document.getElementById('mainRows').innerHTML = mHtml;

            // Footer Sections
            let fHtml = `
                <div class="form-group" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 15px; margin-bottom: 15px;">
                    <label class="form-label">${labels.height || 'Hoogte'}</label>
                    <div style="display:flex; gap:10px;">
                        <input type="text" class="form-input" style="flex:1;" placeholder="Bijv. 120px of auto" value="${state.footer.height || '120px'}" oninput="updateAreaHeight('footer', this.value)">
                        <select class="form-select" style="width:auto;" onchange="this.previousElementSibling.value = this.value; updateAreaHeight('footer', this.value);">
                            <option value="">-- Kies --</option>
                            <option value="90px">90px</option>
                            <option value="120px">120px</option>
                            <option value="auto">Auto</option>
                        </select>
                    </div>
                </div>
            `;
            state.footer.sections.forEach((sec, i) => {
                fHtml += `<div class="form-group row-item">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <label class="form-label">${labels.vlakContent.replace('%d', i + 1)}</label>
                        <div style="display:flex; align-items:center; gap:5px;">
                            <span style="font-size:1.4rem; line-height:1;">↔️</span>
                            <select class="form-select" style="width:80px; padding:2px 4px; font-size:0.75rem;" onchange="setFooterSectionDeviceWidth(${i}, this.value)">
                                ${getDeviceWidthOptions(sec, currentMaxCols)}
                            </select>
                            <span style="font-size:1.4rem; line-height:1; color:var(--text-muted); margin-left:5px;">↕️</span>
                            <select class="form-select" style="width:50px; padding:2px 4px; font-size:0.75rem;" onchange="updateAreaRowSpan('footer', ${i}, this.value)">
                                ${[1,2,3,4,5,6,7,8,9,10,11,12].map(rs => `<option value="${rs}" ${sec.rowSpan == rs ? 'selected' : ''}>${rs}</option>`).join('')}
                            </select>
                        </div>
                    </div>
                    <div style="display:flex; gap:5px;">
                        <select class="form-select" id="config-footer-${i}" onchange="updateSectionType('footer', ${i}, this.value)" style="flex:1;">
                            ${renderOptions(sec.type)}
                        </select>
                        <button class="btn-action remove" onclick="updateSectionType('footer', ${i}, 'empty'); renderConfig();" title="Item leegmaken" style="padding: 0 8px;">🗑️</button>
                    </div>
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
            const mc = currentMaxCols;
            const gridGuide = mc > 1 
                ? `<div class="grid-guide" style="grid-template-columns: repeat(${mc}, 1fr);">` + Array(mc).fill('<div class="grid-guide-col"></div>').join('') + `</div>`
                : '';

            // Header Preview
            let hEl = document.getElementById('pvHeader');
            hEl.style.position = 'relative';
            hEl.style.display = 'grid';
            hEl.style.gridTemplateColumns = `repeat(${mc}, 1fr)`;
            hEl.style.gridAutoRows = 'minmax(60px, auto)';
            hEl.style.gap = '10px';
            hEl.style.minHeight = state.header.height || '90px';
            hEl.innerHTML = gridGuide + state.header.sections.map((sec, i) => {
                let info = contentTypes.find(ct => ct.id === sec.type);
                const span = calcDeviceSpan(sec, mc);
                const rowSpan = sec.rowSpan || 1;
                const isEmpty = sec.type === 'empty';
                const isHidden = span === 0;
                const colStyle = isHidden
                    ? 'grid-column: span 1; grid-row: span 1; position: relative; z-index: 1;'
                    : `grid-column: span ${span}; grid-row: span ${rowSpan}; position: relative; z-index: 1;`;
                return `<div class="pv-col pv-compact ${isEmpty ? 'pv-empty' : ''} ${isHidden ? 'pv-hidden' : ''}" id="pv-header-${i}" style="${colStyle}" onclick="handlePreviewClick('header[${i}]', 'header-${i}')">
                        <span class="pv-col-icon">${info.icon}</span>
                        <span class="pv-col-type">${isHidden ? 'VERBORGEN' : (isEmpty ? 'LEEG' : info.name)}</span>
                        <span class="pv-dimensions">${isHidden ? '✕' : `${span} × ${rowSpan}`}</span>
                    </div>`;
            }).join('');

            // Main Preview
            let mainEl = document.getElementById('pvMain');
            mainEl.style.display = 'grid';
            mainEl.style.gridTemplateColumns = `repeat(${mc}, 1fr)`;
            mainEl.style.gridAutoRows = 'minmax(90px, auto)';
            mainEl.style.gap = '10px';
            mainEl.style.position = 'relative';
            // Remove fixed row template when on smaller devices so items wrap automatically
            mainEl.style.gridTemplateRows = '';
            
            let mHtml = gridGuide;
            state.main.rows.forEach((row, ri) => {
                row.columns.forEach((col, ci) => {
                    let info = contentTypes.find(ct => ct.id === col.type);
                    const span = calcDeviceSpan(col, mc);
                    const rowSpan = col.rowSpan || 1;
                    const isEmpty = col.type === 'empty';
                    const isHidden = span === 0;
                    const style = isHidden
                        ? 'grid-column: span 1; grid-row: span 1; position: relative; z-index: 1; height: 100%;'
                        : `grid-column: span ${span}; grid-row: span ${rowSpan}; position: relative; z-index: 1; height: 100%;`;
                    mHtml += `<div class="pv-col ${isEmpty ? 'pv-empty' : ''} ${isHidden ? 'pv-hidden' : ''}" id="pv-row-${ri}-col-${ci}" style="${style}" onclick="handlePreviewClick('content[${ri}][${ci}]', 'row-${ri}-col-${ci}')">
                            <span class="pv-col-icon">${info.icon}</span>
                            <span class="pv-col-type">${isHidden ? 'VERBORGEN' : (isEmpty ? 'LEEG' : info.name)}</span>
                            <span class="pv-dimensions">${isHidden ? '✕' : `${span} × ${rowSpan}`}</span>
                        </div>`;
                });
            });
            mainEl.innerHTML = mHtml;

            // Footer Preview
            let fEl = document.getElementById('pvFooter');
            fEl.style.position = 'relative';
            fEl.style.display = 'grid';
            fEl.style.gridTemplateColumns = `repeat(${mc}, 1fr)`;
            fEl.style.gridAutoRows = 'minmax(80px, auto)';
            fEl.style.gap = '10px';
            fEl.style.minHeight = state.footer.height || '120px';
            fEl.innerHTML = gridGuide + state.footer.sections.map((sec, i) => {
                let info = contentTypes.find(ct => ct.id === sec.type);
                const span = calcDeviceSpan(sec, mc);
                const rowSpan = sec.rowSpan || 1;
                const isEmpty = sec.type === 'empty';
                const isHidden = span === 0;
                const colStyle = isHidden
                    ? 'grid-column: span 1; grid-row: span 1; position: relative; z-index: 1;'
                    : `grid-column: span ${span}; grid-row: span ${rowSpan}; position: relative; z-index: 1;`;
                return `<div class="pv-col pv-compact ${isEmpty ? 'pv-empty' : ''} ${isHidden ? 'pv-hidden' : ''}" id="pv-footer-${i}" style="${colStyle}" onclick="handlePreviewClick('footer[${i}]', 'footer-${i}')">
                        <span class="pv-col-icon">${info.icon}</span>
                        <span class="pv-col-type">${isHidden ? 'VERBORGEN' : (isEmpty ? 'LEEG' : info.name)}</span>
                        <span class="pv-dimensions">${isHidden ? '✕' : `${span} × ${rowSpan}`}</span>
                    </div>`;
            }).join('');
        }

        function saveLayout() {
            document.getElementById('layoutJsonInput').value = JSON.stringify(state);
            document.getElementById('nameInput').value = document.getElementById('tplName').value;
            document.getElementById('typeInput').value = document.getElementById('tplType').value;
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