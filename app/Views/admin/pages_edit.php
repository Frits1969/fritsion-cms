<?php
$lang = $GLOBALS['lang'] ?? [];
extract($lang);

$nav_back_to_dashboard = $nav_back_to_dashboard ?? 'Terug naar Dashboard';
$role_super_admin = $role_super_admin ?? 'Super Admin';
$nav_profile = $nav_profile ?? 'Profiel';
$nav_logout = $nav_logout ?? 'Uitloggen';
$slug_tip = $slug_tip ?? "De 'slug' is het deel van de URL dat na de domeinnaam komt (bijv. /over-ons).";
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'nl' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ($mode === 'edit' ? $btn_edit : $btn_add_page) ?> | Fritsion CMS</title>
    <link rel="icon" type="image/png" href="/assets/logo/logo_fritsion_cms_favicon.png">
    <link rel="shortcut icon" href="/assets/logo/logo_fritsion_cms_favicon.ico">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/admin_shared.css">
    <link rel="stylesheet" href="/assets/css/admin_pages.css">
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>

<body>
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <div class="main-wrapper">
        <header class="topbar">
            <div class="topbar-title"><?= $backoffice_title ?> / <?= $pages_title ?> /
                <?= ($mode === 'edit' ? $btn_edit : $btn_add_page) ?>
            </div>

            <div class="topbar-actions">
                <a href="/backoffice/pages"
                    class="topbar-back-link"><?= $nav_back_to_dashboard ?></a>

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
                        <hr>
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

        <main class="content main-content-header">
            <?php if (isset($error) && $error): ?>
                <div class="alert alert-error"><span>⚠️</span> <?= $error ?></div>
            <?php endif; ?>

            <?php if (isset($success) && $success): ?>
                <div class="alert alert-success"><span>✅</span> <?= $success ?></div>
            <?php endif; ?>

            <div class="header-section header-section-inner">
                <div>
                    <h1><?= ($mode === 'edit' ? $btn_edit : $btn_add_page) ?></h1>
                    <p><?= ($mode === 'edit' ? $success_page_updated : $pages_desc) ?></p>
                </div>
            </div>

            <div class="editor-container editor-container-outer">
                <div class="form-card editor-form-card">
                    <form method="POST" id="pageForm">
                        <div class="form-group">
                            <label for="template_id"><?= $label_template ?? 'Template' ?></label>
                            <?php $templateId = $page['template_id'] ?? ''; ?>
                            <select id="template_id" name="template_id" class="form-control"
                                onchange="handleTemplateChange(this.value)">
                                <option value=""><?= $label_select_template ?? '-- Selecteer Template --' ?></option>
                                <?php foreach ($templates as $tpl): ?>
                                    <?php
                                        $tplDisplayName = htmlspecialchars($tpl['name']);
                                        if ($tplDisplayName === 'Homepage') $tplDisplayName = $lang['nav_homepage'] ?? 'Homepage';
                                        if ($tplDisplayName === 'Contentpagina') $tplDisplayName = $lang['nav_content_page'] ?? 'Content Page';
                                        
                                        $tplTypeDisplay = ucfirst($tpl['type']);
                                        if ($tpl['type'] === 'content') $tplTypeDisplay = $lang['nav_content_page'] ?? 'Content Page';
                                        if ($tpl['type'] === 'homepage') $tplTypeDisplay = $lang['nav_homepage'] ?? 'Homepage';
                                    ?>
                                    <option value="<?= $tpl['id'] ?>" <?= ($templateId == $tpl['id'] ? 'selected' : '') ?>>
                                        <?= $tplDisplayName ?> (<?= $tplTypeDisplay ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="template-control-group">
                                <button type="button" id="btn-refresh-template" onclick="refreshTemplate()" class="btn-refresh-template-ui" title="<?= $lang['tooltip_refresh_template'] ?? 'Herlaad laatste template-indeling' ?>">
                                    🔄 <?= $lang['btn_refresh_template'] ?? 'Template vernieuwen' ?>
                                </button>
                                <small id="template-refresh-hint" class="template-refresh-hint-ui">💡 <?= $lang['msg_template_live'] ?? 'Wijzigingen in de template zijn direct zichtbaar op alle pagina\'s.' ?></small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="title"><?= $label_title ?></label>
                            <textarea id="title" name="title" class="form-control title-textarea-ui"
                                required><?= htmlspecialchars($page['title'] ?? '') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="slug"><?= $label_slug ?></label>
                            <input type="text" id="slug" name="slug" class="form-control"
                                value="<?= htmlspecialchars($page['slug'] ?? '') ?>" required>
                            <small class="slug-tip-ui">
                                 <?= $slug_tip ?>
                            </small>
                        </div>

                        <div class="form-group device-preview-section" id="device-preview-group">
                            <label class="form-label device-preview-label-ui">
                                📱 <?= $lang['label_device_preview'] ?? 'Apparaat Weergave' ?>
                            </label>
                            <?php include __DIR__ . '/partials/device_switcher.php'; ?>
                        </div>

                        <div class="form-group">
                            <label for="status"><?= $label_status ?></label>
                            <select id="status" name="status" class="form-control">
                                <option value="draft" <?= ($page['status'] ?? '') === 'draft' ? 'selected' : '' ?>>
                                    <?= $status_draft ?>
                                </option>
                                <option value="published" <?= ($page['status'] ?? '') === 'published' ? 'selected' : '' ?>>
                                    <?= $status_published ?>
                                </option>
                            </select>
                        </div>
                    </div>

                    <div id="visual-editor-canvas" class="visual-canvas" style="display: <?= $templateId ? 'block' : 'none' ?>;">
                        <div class="empty-template"><?= $msg_select_template ?></div>
                    </div>

                    <div class="form-card editor-form-card">
                        <input type="hidden" name="content" id="content-json">

                        <div class="form-card-footer">
                            <button type="submit" class="btn-save"><?= $btn_save ?></button>
                            <a href="/backoffice/pages" class="btn-secondary"><?= $btn_cancel ?></a>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- Editor Modal -->
    <div class="editor-modal-backdrop" id="editor-modal">
        <div class="editor-modal">
            <div class="editor-modal-header">
                <div class="editor-modal-title"><span>📝</span> <?= $btn_edit ?></div>
                <div class="editor-modal-header-actions">
                    <button type="button" id="editor-mode-toggle" onclick="toggleEditorMode()"
                        class="btn-editor-toggle-ui">
                        &lt;/&gt; HTML weergave
                    </button>
                    <button type="button" class="editor-modal-close" onclick="closePopupEditor()">&times;</button>
                </div>
            </div>
            <div class="editor-modal-body editor-modal-body-ui">
                <textarea id="editor-plain"
                    class="editor-textarea-plain-ui"
                    placeholder=""></textarea>
                <textarea id="editor-html"
                    class="editor-textarea-html-ui"
                    placeholder=""></textarea>
            </div>
            <div class="editor-modal-footer">
                <button type="button" class="btn-modal-cancel" onclick="closePopupEditor()"><?= $btn_cancel ?></button>
                <button type="button" class="btn-modal-save" onclick="savePopupEditorContent()"><?= $btn_save ?></button>
            </div>
        </div>
    </div>

    <script>
        const titleInput = document.getElementById('title');
        const slugInput = document.getElementById('slug');
        let isDirty = false;
        let pageDataObj = <?= $page['content'] ?: '{}' ?>;
        // Data structure mapping: if pageDataObj doesn't have lang keys, wrap it in current language
        if (Object.keys(pageDataObj).length > 0 && !pageDataObj.nl && !pageDataObj.en) {
            let migrated = { "nl": pageDataObj, "en": JSON.parse(JSON.stringify(pageDataObj)) };
            pageDataObj = migrated;
        }
        let editorLang = '<?= $_SESSION['lang'] ?? 'nl' ?>';

        function getLangData() {
            if (!pageDataObj[editorLang]) pageDataObj[editorLang] = {};
            return pageDataObj[editorLang];
        }

        const visualCanvas = document.getElementById('visual-editor-canvas');
        const contentJsonInput = document.getElementById('content-json');
        
        const userWidget = document.getElementById('user-widget');
        const userMenu = document.getElementById('user-menu');
        const langSwitcher = document.getElementById('lang-switcher');

        const siteSettings = <?= json_encode($siteSettings ?? []) ?>;
        
        let currentTemplate = null;
        let currentMaxCols = 12;

        function setDevicePreview(btn, maxCols = 12) {
            document.querySelectorAll('.btn-device').forEach(el => el.classList.remove('active'));
            if (btn) btn.classList.add('active');
            
            if (visualCanvas) {
                let w = '100%';
                if (maxCols === 1) w = '640px';
                else if (maxCols === 2) w = '768px';
                else if (maxCols === 3) w = '1024px';
                else if (maxCols === 4) w = '1280px';
                else if (maxCols === 6) w = '1440px';
                else w = '100%';
                
                visualCanvas.style.width = w;
                visualCanvas.style.margin = (maxCols === 12) ? '40px 0' : '20px auto';
                visualCanvas.style.transition = 'width 0.4s ease';
            }
            
            if (currentMaxCols !== maxCols) {
                currentMaxCols = maxCols;
                if (currentTemplate) {
                    renderVisualCanvas();
                }
            }
        }

        function calcDeviceSpan(item, deviceMaxCols) {
            const dw = item.deviceWidths;
            if (dw && dw[deviceMaxCols] !== undefined) {
                return parseInt(dw[deviceMaxCols]); 
            }
            const width12 = parseInt(item.width ?? 12);
            if (width12 === 0) return 0;
            if (deviceMaxCols === 1) return 1;
            if (width12 >= 12) return deviceMaxCols;
            const span = Math.round((width12 / 12) * deviceMaxCols);
            return Math.max(1, Math.min(deviceMaxCols, span));
        }

        async function handleTemplateChange(templateId) {
            if (!templateId) {
                visualCanvas.style.display = 'none';
                document.getElementById('btn-refresh-template').style.display = 'none';
                document.getElementById('template-refresh-hint').style.display = 'none';
                return;
            }

            try {
                const response = await fetch(`/backoffice/templates/get/${templateId}?nocache=${Date.now()}`);
                const template = await response.json();
                currentTemplate = template;

                // No longer forcing slug to '/' for homepage templates. 
                // Any page can use any template; only the slug '/' determines if it's the home.
                slugInput.readOnly = false;

                // Show the refresh button and hint
                document.getElementById('btn-refresh-template').style.display = 'inline-block';
                document.getElementById('template-refresh-hint').style.display = 'inline';
                
                // Show the device switcher
                document.getElementById('device-preview-group').style.display = 'block';

                renderVisualCanvas();
            } catch (error) {
                console.error('Error fetching template:', error);
            }
        }

        async function refreshTemplate() {
            const templateId = document.getElementById('template_id').value;
            if (!templateId) return;

            const btn = document.getElementById('btn-refresh-template');
            btn.textContent = '⏳ Laden...';
            btn.disabled = true;

            try {
                const response = await fetch(`/backoffice/templates/get/${templateId}?nocache=${Date.now()}`);
                const template = await response.json();
                currentTemplate = template;
                renderVisualCanvas();

                btn.textContent = '✅ Vernieuwd!';
                setTimeout(() => {
                    btn.textContent = '🔄 <?= $lang['btn_refresh_template'] ?? 'Template vernieuwen' ?>';
                    btn.disabled = false;
                }, 1500);
            } catch (error) {
                btn.textContent = '🔄 <?= $lang['btn_refresh_template'] ?? 'Template vernieuwen' ?>';
                btn.disabled = false;
                console.error('Error refreshing template:', error);
            }
        }

        function moveRow(ri, direction) {
            if (!currentTemplate) return;
            const layout = JSON.parse(currentTemplate.layout_json);
            const rows = layout.main.rows;
            const newIndex = ri + direction;
            if (newIndex < 0 || newIndex >= rows.length) return;

            // Swap rows
            const temp = rows[ri];
            rows[ri] = rows[newIndex];
            rows[newIndex] = temp;

            // Update template and re-render
            currentTemplate.layout_json = JSON.stringify(layout);
            isDirty = true;
            renderVisualCanvas();
        }

        function renderVisualCanvas() {
            if (!currentTemplate) return;
            visualCanvas.style.display = 'block';
            const layout = JSON.parse(currentTemplate.layout_json);
            const mc = currentMaxCols;

            const gridGuide = mc > 1 
                ? `<div class="grid-guide" style="grid-template-columns: repeat(${mc}, 1fr);">` + Array(mc).fill('<div class="grid-guide-col"></div>').join('') + `</div>`
                : '';

            let html = `
                <div class="vc-header">
                    <div class="vc-container vc-header-inner" style="position:relative;">
                        ${gridGuide}
                        ${renderVisualSection(layout.header, 'header')}
                    </div>
                </div>
                <div class="vc-main vc-container" style="position:relative;">
                    ${gridGuide}
                    ${renderVisualSection(layout.main, 'main')}
                </div>
                <div class="vc-footer">
                    <div class="vc-container vc-footer-inner" style="position:relative;">
                        ${gridGuide}
                        <div style="display: grid; gap: 40px; grid-template-columns: repeat(${mc}, 1fr);">
                            ${renderVisualSection(layout.footer, 'footer')}
                        </div>
                    </div>
                </div>
            `;
            visualCanvas.innerHTML = html;
            
            // Re-bind dropzones dynamically
            visualCanvas.querySelectorAll('.dropzone').forEach(dz => {
                dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('dragover'); });
                dz.addEventListener('dragleave', () => dz.classList.remove('dragover'));
                dz.addEventListener('drop', e => {
                    e.preventDefault();
                    dz.classList.remove('dragover');
                    const file = e.dataTransfer.files[0];
                    if (file && file.type.startsWith('image/')) {
                        const input = dz.querySelector('input[type="file"]');
                        if(input) {
                            handleFileUpload({files:[file]}, input.dataset.path, dz);
                        }
                    }
                });
            });

            // Auto-resize all textareas after rendering
            setTimeout(() => {
                visualCanvas.querySelectorAll('textarea').forEach(autoResizeTextarea);
            }, 100);
        }

        function autoResizeTextarea(el) {
            if (!el) return;
            el.style.height = 'auto';
            el.style.height = (el.scrollHeight) + 'px';
        }

        function renderVisualSection(section, type) {
            if (!section) return '';
            let html = '';

            if (type === 'main') {
                html += `<div class="vc-main-grid" style="display: grid; grid-template-columns: repeat(${currentMaxCols}, 1fr); grid-auto-rows: min-content; gap: 80px 40px; position: relative; z-index: 1;">`;
                section.rows.forEach((row, ri) => {
                    const isFirst = ri === 0;
                    const isLast = ri === section.rows.length - 1;
                    
                    row.columns.forEach((col, ci) => {
                        const span = calcDeviceSpan(col, currentMaxCols);
                        const rowSpan = col.rowSpan || 1;
                        
                        if (span === 0) return; // Hidden on this device

                        html += `<div class="vc-col" style="grid-column: span ${span}; grid-row: span ${rowSpan}; position: relative; z-index: 1;">`;
                        
                        // Row Actions (only on first column of each row)
                        if (ci === 0) {
                            html += `
                                <div style="position:absolute; left:-60px; top:0; display:flex; flex-direction:column; gap:5px; z-index:100;">
                                    <button type="button" onclick="moveRow(${ri}, -1)" ${isFirst ? 'disabled' : ''} style="width:30px; height:30px; border-radius:50%; border:1px solid #e2e8f0; background:#fff; cursor:pointer; opacity:${isFirst ? 0.3 : 1}; color:#64748b;">↑</button>
                                    <button type="button" onclick="moveRow(${ri}, 1)" ${isLast ? 'disabled' : ''} style="width:30px; height:30px; border-radius:50%; border:1px solid #e2e8f0; background:#fff; cursor:pointer; opacity:${isLast ? 0.3 : 1}; color:#64748b;">↓</button>
                                </div>
                            `;
                        }
                        
                        html += `${renderVisualBlock(col.type, `main.rows.${ri}.columns.${ci}`)}<span class="vc-dimensions">${span} × ${rowSpan}</span></div>`;
                    });
                });
                html += `</div>`;
            } else {
                // Header or Footer
                const mc = currentMaxCols;
                const areaHeight = section.height || (type === 'header' ? '90px' : '120px');
                html += `<div class="vc-row" style="display: grid; grid-template-columns: repeat(${mc}, 1fr); grid-auto-rows: min-content; gap: 40px; align-items: center; position: relative; z-index: 1; min-height: ${areaHeight === 'auto' ? 'initial' : areaHeight};">`;
                
                const sections = section.sections || [];
                sections.forEach((sec, si) => {
                    const span = calcDeviceSpan(sec, mc);
                    const rowSpan = sec.rowSpan || 1;
                    if (span === 0) return; // Hidden

                    html += `<div class="vc-col" style="grid-column: span ${span}; grid-row: span ${rowSpan}; position: relative; z-index: 1;">${renderVisualBlock(sec.type, `${type}.sections.${si}`)}<span class="vc-dimensions">${span} × ${rowSpan}</span></div>`;
                });
                html += `</div>`;
            }
            return html;
        }

        function renderVisualBlock(type, path) {
            const data = getDeepValue(getLangData(), path) || {};
            
            switch (type) {
                case 'empty':
                    return `<div style="border:1px dashed #cbd5e1; height:60px; display:flex; align-items:center; justify-content:center; color:#cbd5e1; font-weight:600; font-size:0.75rem; border-radius:12px;">LEEG</div>`;
                case 'text':
                    const displayHtmlContent = data.text || '';
                    const strippedTextContent = typeof stripHtml === 'function' ? stripHtml(displayHtmlContent) : displayHtmlContent;
                    return `
                        <div>
                            <textarea class="vc-input-transparent vc-h1-input" placeholder="" oninput="updateData('${path}.title', this.value); autoResizeTextarea(this);">${data.title || ''}</textarea>
                            <div style="position:relative;">
                                <button type="button" class="btn-edit-popup" onclick="openPopupEditor('${path}.text')" style="position:absolute; right:5px; top:-25px; z-index:10; font-size:12px; padding:2px 8px;">✎ Editor</button>
                                <textarea id="textarea_${path.replace(/\./g, '_')}_text" class="vc-input-transparent vc-p-input" placeholder="" oninput="updateData('${path}.text', this.value); autoResizeTextarea(this);">${strippedTextContent}</textarea>
                            </div>
                        </div>
                    `;
                case 'image':
                    return `
                        <div class="dropzone" onclick="document.getElementById('file_${path.replace(/\./g, '_')}').click()" style="min-height:300px; display:flex; flex-direction:column; justify-content:center; align-items:center; background:#f1f5f9; border:2px dashed #cbd5e1; border-radius:24px; text-align:center; padding:20px; cursor:pointer; overflow:hidden;" id="dropzone_${path.replace(/\./g, '_')}">
                            ${data.url ? `<img src="${data.url}" alt="Preview" class="vc-img-preview" style="max-height:300px; border-radius:20px;">` : `<div style="color:#cbd5e1; font-weight:600;">☁️ Klik of sleep een afbeelding</div>`}
                            <input type="file" id="file_${path.replace(/\./g, '_')}" accept="image/*" style="display:none;" data-path="${path}.url" onchange="handleFileUpload(this, '${path}.url', this.parentNode)">
                            <div class="upload-progress" style="height:4px; background:#10b981; width:0%; transition:0.3s; margin-top:10px; border-radius:2px;"></div>
                        </div>
                    `;
                case 'cta':
                    return `
                        <div>
                            <textarea class="vc-input-transparent vc-h3-input" placeholder="" oninput="updateData('${path}.title', this.value)">${data.title || ''}</textarea>
                            <div style="margin-bottom:10px;">
                                <textarea class="vc-input-transparent vc-cta-button" placeholder="" oninput="updateData('${path}.button_text', this.value)" style="text-align:center; display:inline-block; max-width:250px; min-height:60px; padding-top:15px; border-radius:30px;">${data.button_text || ''}</textarea>
                            </div>
                            <div style="display:flex; gap:10px; margin-top:5px;">
                                <textarea class="vc-input-transparent" style="flex:1; min-height:45px;" placeholder="" oninput="updateData('${path}.url', this.value)">${data.url || ''}</textarea>
                                <select class="form-select" style="width:auto; padding:4px 8px; font-size:0.8rem;" onchange="updateData('${path}.target', this.value)">
                                    <option value="_self" ${data.target === '_self' ? 'selected' : ''}>Zelfde venster (_self)</option>
                                    <option value="_blank" ${data.target === '_blank' ? 'selected' : ''}>Nieuw tabblad (_blank)</option>
                                    <option value="_top" ${data.target === '_top' ? 'selected' : ''}>Bovenste frame (_top)</option>
                                </select>
                            </div>
                        </div>
                    `;
                case 'logo':
                    if (siteSettings.hide_logo === '1') return '<div style="color:#cbd5e1; font-size:0.8rem; border:1px dashed #cbd5e1; padding:5px; text-align:center; border-radius:5px;">Logo (Verbergen ingeschakeld in instellingen)</div>';
                    const logoUrl = siteSettings.site_logo || '/assets/logo/logo_fritsion_cms.png';
                    return `
                        <div style="display:flex; align-items:center; gap:10px; height:100%; width:100%;">
                            <img src="${logoUrl}" class="vc-logo" style="max-height:100%; width:auto; object-fit:contain;">
                        </div>`;
                case 'menu': 
                    return `
                        <div class="vc-nav">
                            <textarea class="vc-input-transparent" style="text-align:right; min-height:45px;" placeholder="" oninput="updateData('${path}.items', this.value)">${data.items || ''}</textarea>
                        </div>`;
                case 'language':
                    return `
                        <div style="display:flex; gap:10px; align-items:center; padding:10px; border:1px dashed #eee; border-radius:8px; opacity:0.8;">
                            <img src="/assets/flags/nl.svg" style="width:20px; border-radius:2px;">
                            <img src="/assets/flags/en.svg" style="width:20px; border-radius:2px;">
                            <span style="font-size:0.75rem; font-weight:600; color:#64748b; margin-left:5px;">Taal Selectie</span>
                        </div>`;
                case 'usps': 
                    return `
                        <div class="vc-usp-grid">
                            <textarea class="vc-input-transparent vc-usp-card" placeholder="USP 1" oninput="updateData('${path}.usp_1', this.value)">${data.usp_1 || ''}</textarea>
                            <textarea class="vc-input-transparent vc-usp-card" placeholder="USP 2" oninput="updateData('${path}.usp_2', this.value)">${data.usp_2 || ''}</textarea>
                            <textarea class="vc-input-transparent vc-usp-card" placeholder="USP 3" oninput="updateData('${path}.usp_3', this.value)">${data.usp_3 || ''}</textarea>
                        </div>`;
                case 'usp_card':
                    return `
                        <div style="background:white; border-radius:20px; padding:20px; box-shadow:0 10px 30px rgba(0,0,0,0.05); border:1px solid #f1f5f9;">
                            <div class="dropzone" onclick="document.getElementById('file_${path.replace(/\./g, '_')}').click()" style="height:120px; margin-bottom:15px; display:flex; flex-direction:column; justify-content:center; align-items:center; background:#f8fafc; border:2px dashed #e2e8f0; border-radius:12px; cursor:pointer;" id="dropzone_${path.replace(/\./g, '_')}">
                                ${data.url ? `<img src="${data.url}" style="max-height:100px; border-radius:8px;">` : `<div style="color:#94a3b8; font-size:0.75rem;">☁️ USP Icoon</div>`}
                                <input type="file" id="file_${path.replace(/\./g, '_')}" accept="image/*" style="display:none;" data-path="${path}.url" onchange="handleFileUpload(this, '${path}.url', this.parentNode)">
                                <div class="upload-progress" style="height:4px; background:#10b981; width:0%; transition:0.3s; margin-top:10px; border-radius:2px;"></div>
                            </div>
                            <textarea class="vc-input-transparent" style="font-weight:700; font-size:1.1rem; margin-bottom:8px; display:block;" placeholder="USP Kop" oninput="updateData('${path}.title', this.value)">${data.title || ''}</textarea>
                            <textarea class="vc-input-transparent" style="font-size:0.9rem; color:#64748b; font-weight:400;" placeholder="USP Uitleg (max 200 tekens)" maxlength="200" oninput="updateData('${path}.text', this.value); autoResizeTextarea(this);">${data.text || ''}</textarea>
                        </div>
                    `;
                case 'socials': 
                    return `
                        <div style="display:flex; gap:10px;">
                            <textarea class="vc-input-transparent" placeholder="" oninput="updateData('${path}.facebook', this.value)" style="min-height:45px;">${data.facebook || ''}</textarea>
                            <textarea class="vc-input-transparent" placeholder="" oninput="updateData('${path}.instagram', this.value)" style="min-height:45px;">${data.instagram || ''}</textarea>
                        </div>`;
                case 'video': 
                    return `
                        <div style="background:linear-gradient(135deg, var(--text) 0%, var(--primary) 100%); height:200px; border-radius:24px; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:20px;">
                            <span style="color:white; font-size:2rem; margin-bottom:10px;">▶</span>
                            <textarea class="vc-input-transparent" style="color:white; border-color:rgba(255,255,255,0.3); text-align:center; max-width:80%; min-height:45px;" placeholder="" oninput="updateData('${path}.url', this.value)">${data.url || ''}</textarea>
                        </div>`;
                case 'html': 
                    return `
                        <div style="border:1px dashed #cbd5e1; border-radius:8px; padding:10px;">
                            <div style="font-size:0.8rem; font-weight:600; color:#475569; margin-bottom:5px;">&lt;/&gt; HTML Embed Code</div>
                            <textarea class="vc-input-transparent" style="font-family:monospace; min-height:120px; border:none; resize:vertical; background:#f8fafc;" placeholder="" oninput="updateData('${path}.code', this.value)">${data.code || ''}</textarea>
                        </div>`;
                case 'map': 
                    return `
                        <div style="background:#e0f2fe; height:300px; border-radius:24px; display:flex; flex-direction:column; justify-content:center; align-items:center; padding:20px; text-align:center;">
                            <span style="font-size:2rem;">📍</span>
                            <div style="color:#0369a1; font-weight:600; margin:10px 0;">Kaart</div>
                            <textarea class="vc-input-transparent" style="background:#fff; border-color:#bae6fd; color:#0369a1; text-align:center; max-width:300px; min-height:60px;" placeholder="" oninput="updateData('${path}.address', this.value)">${data.address || ''}</textarea>
                        </div>`;
                default: return `<div style="border:1px dashed #eee; padding:10px;">Blok: ${type}</div>`;
            }
        }

        function handleFileUpload(input, path, dz) {
            const file = input.files[0];
            if (file) {
                performUpload(file, path, dz);
            }
        }

        function performUpload(file, path, dz) {
            const formData = new FormData();
            formData.append('file', file);

            const progress = dz.querySelector('.upload-progress');
            if (progress) progress.style.width = '0%';

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/backoffice/media/upload', true);

            xhr.upload.onprogress = (e) => {
                if (e.lengthComputable && progress) {
                    const percent = (e.loaded / e.total) * 100;
                    if (progress) progress.style.width = percent + '%';
                }
            };

            xhr.onload = () => {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        updateData(path, response.url);
                        renderVisualCanvas(); // re-render to show image
                    } else {
                        alert('Fout bij uploaden: ' + response.message);
                    }
                } else {
                    alert('Server fout bij uploaden.');
                }
            };

            xhr.send(formData);
        }

        function updateData(path, value) {
            isDirty = true;
            let langData = getLangData();
            setDeepValue(langData, path, value);
        }

        // Helper functions
        function getDeepValue(obj, path) {
            return path.split('.').reduce((p, c) => p && p[c], obj);
        }

        function setDeepValue(obj, path, value) {
            const parts = path.split('.');
            const last = parts.pop();
            const target = parts.reduce((p, c) => {
                if (!p[c]) p[c] = {};
                return p[c];
            }, obj);
            target[last] = value;
        }

        // Form Submission
        document.getElementById('pageForm').addEventListener('submit', (e) => {
            // Force sync if popup editor is currently active
            if (tinymceInstance && activeEditorPath) {
                const content = tinymceInstance.getContent();
                updateData(activeEditorPath, content);
            }
            contentJsonInput.value = JSON.stringify(pageDataObj);
            
            // Allow submission to continue naturally so AdminController processes it
        });

        // Slug generation
        titleInput.addEventListener('input', () => {
            if ("<?= $mode ?>" === 'add' && !slugInput.readOnly) {
                const slug = titleInput.value
                    .toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                slugInput.value = slug;
            }
        });

        // Initialize state
        document.addEventListener('DOMContentLoaded', () => {
            const initialTplId = document.getElementById('template_id').value;
            if (initialTplId) {
                handleTemplateChange(initialTplId);
            }
        });

        // Popup Editor Logic
        let activeEditorPath = null;
        let tinymceInstance = null;

        function hasHtmlTags(text) {
            return /<[a-z][\s\S]*>/i.test(text);
        }

        function openPopupEditor(path) {
            activeEditorPath = path;
            const rawContent = getDeepValue(getLangData(), path) || '';
            
            // Set initial content
            const plainEl = document.getElementById('editor-plain');
            const htmlEl = document.getElementById('editor-html');
            
            plainEl.value = stripHtml(rawContent);
            htmlEl.value = rawContent;
            
            // Add real-time sync listeners (once)
            if (!plainEl.dataset.syncBound) {
                plainEl.dataset.syncBound = "true";
                // Realtime listeners zijn verwijderd om conflicten te voorkomen.
                // Synchronisatie wordt nu specifiek geregeld in toggleEditorMode() en savePopupEditorContent().
            }

            editorMode = hasHtmlTags(rawContent) ? 'html' : 'plain';
            syncModeUI();
            
            document.getElementById('editor-modal').classList.add('active');
        }

        function closePopupEditor() {
            document.getElementById('editor-modal').classList.remove('active');
            activeEditorPath = null;
        }

        function savePopupEditorContent() {
            if (!activeEditorPath) return;

            const plainEl = document.getElementById('editor-plain');
            const htmlEl  = document.getElementById('editor-html');

            let finalContent;
            if (editorMode === 'html') {
                finalContent = htmlEl.value;
            } else {
                // Controleer of de gebruiker de plain text actief gemuteerd heeft óf er helemaal geen HTML was
                if (plainEl.value.trim() !== stripHtml(htmlEl.value).trim() || !hasHtmlTags(htmlEl.value)) {
                    finalContent = textToHtml(plainEl.value);
                } else {
                    finalContent = htmlEl.value; // Behoud de oorspronkelijke HTML bron
                }
            }

            updateData(activeEditorPath, finalContent);
            renderVisualCanvas();
            closePopupEditor();
        }

        // Editor mode toggle: 'plain' or 'html'
        let editorMode = 'plain';

        function toggleEditorMode() {
            const plainEl = document.getElementById('editor-plain');
            const htmlEl  = document.getElementById('editor-html');

            if (editorMode === 'plain') {
                // plain -> html
                // Zet de aangepaste (gemuteerde) tekst om in HTML.
                // Of converteer direct als er nog geen HTML codes aanwezig waren.
                if (plainEl.value.trim() !== stripHtml(htmlEl.value).trim() || !hasHtmlTags(htmlEl.value)) {
                    htmlEl.value = textToHtml(plainEl.value);
                }
                editorMode = 'html';
            } else {
                // html -> plain
                plainEl.value = stripHtml(htmlEl.value);
                editorMode = 'plain';
            }
            syncModeUI();
        }

        function textToHtml(text) {
            if (!text || !text.trim()) return '';
            // Splits op dubbele regeleindes (alinea's), wikkel elk in <p>
            return text
                .split(/\n{2,}/)
                .map(p => p.trim())
                .filter(p => p.length > 0)
                .map(p => '<p>' + p.replace(/\n/g, '<br>') + '</p>')
                .join('\n');
        }

        function syncModeUI() {
            const plainEl = document.getElementById('editor-plain');
            const htmlEl  = document.getElementById('editor-html');
            const toggleBtn = document.getElementById('editor-mode-toggle');

            if (editorMode === 'plain') {
                plainEl.style.display = 'block';
                htmlEl.style.display = 'none';
                toggleBtn.textContent = ' </> HTML weergave';
            } else {
                plainEl.style.display = 'none';
                htmlEl.style.display = 'block';
                toggleBtn.textContent = ' 📝 Tekst weergave';
            }
        }

        function stripHtml(html) {
            if (!html) return '';
            const tmp = document.createElement('div');
            tmp.innerHTML = html;
            // Preserve newlines from block elements
            tmp.querySelectorAll('p, br, div').forEach(el => {
                el.insertAdjacentText('afterend', '\n');
            });
            return (tmp.textContent || tmp.innerText || '').replace(/\n{3,}/g, '\n\n').trim();
        }

        // UI Handlers (Event listeners only, variables defined above)

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


        function handleFlagClick(event, lang) {
            event.stopPropagation();
            if (!langSwitcher.classList.contains('expanded')) {
                event.preventDefault();
                langSwitcher.classList.add('expanded');
                return;
            }

            event.preventDefault(); // Prevent standard page reload entirely
            langSwitcher.classList.remove('expanded');

            if (editorLang !== lang) {
                // Determine new flag icon
                const oldFlagIcon = document.querySelector(`.lang-select a[href="?lang=${editorLang}"] img`);
                const newFlagIcon = document.querySelector(`.lang-select a[href="?lang=${lang}"] img`);
                
                // Visual update for flag (if they want to keep form active instead of full page reload)
                document.querySelectorAll('.lang-select a').forEach(a => a.classList.remove('active'));
                const newActiveFlag = document.querySelector(`.lang-select a[href="?lang=${lang}"]`);
                if(newActiveFlag) newActiveFlag.classList.add('active');

                // Switch language and Re-render canvas!
                editorLang = lang;
                
                // Also update session lang in background so if they save, it's correct context
                fetch(`?lang=${lang}`); 

                if (currentTemplate) {
                    renderVisualCanvas();
                }
            }
        }
    </script>
</body>

</html>