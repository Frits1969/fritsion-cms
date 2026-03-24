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
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>

<body>
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <div class="main-wrapper">
        <header class="topbar">
            <div style="font-weight: 600; color: var(--text-muted);"><?= $backoffice_title ?> / <?= $pages_title ?> /
                <?= ($mode === 'edit' ? $btn_edit : $btn_add_page) ?>
            </div>

            <div class="topbar-actions">
                <a href="/backoffice/pages"
                    style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;"><?= $nav_back_to_dashboard ?></a>

                <div class="user-widget" id="user-widget">
                    <div class="user-avatar"
                        style="width: 32px; height: 32px; background: var(--accent-gradient); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.85rem; color: white;">
                        <?php
                        $name = $_SESSION['username'] ?? 'Admin';
                        echo strtoupper(substr($name, 0, 1) . (strlen($name) > 1 ? substr($name, 1, 1) : ''));
                        ?>
                    </div>
                    <div class="user-info" style="display: flex; flex-direction: column; margin-left: 10px;">
                        <span class="user-name"
                            style="font-size: 0.9rem; font-weight: 600;"><?= $_SESSION['username'] ?? 'Admin' ?></span>
                        <span class="user-role"
                            style="font-size: 0.75rem; color: var(--text-muted);"><?= $role_super_admin ?></span>
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
            <?php if (isset($error) && $error): ?>
                <div class="alert alert-error"><span>⚠️</span> <?= $error ?></div>
            <?php endif; ?>

            <?php if (isset($success) && $success): ?>
                <div class="alert alert-success"><span>✅</span> <?= $success ?></div>
            <?php endif; ?>

            <div class="header-section">
                <div>
                    <h1><?= ($mode === 'edit' ? $btn_edit : $btn_add_page) ?></h1>
                    <p><?= ($mode === 'edit' ? $success_page_updated : $pages_desc) ?></p>
                </div>
            </div>

            <div class="editor-container">
                <div class="form-card">
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
                            <div style="display:flex; gap:8px; margin-top:8px; align-items:center;">
                                <button type="button" id="btn-refresh-template" onclick="refreshTemplate()" style="display:none; background:var(--glass-bg); border:1px solid var(--glass-border); color:var(--text-muted); padding:5px 12px; border-radius:8px; font-size:0.8rem; cursor:pointer; transition:0.2s;" title="<?= $lang['tooltip_refresh_template'] ?? 'Herlaad laatste template-indeling' ?>">
                                    🔄 <?= $lang['btn_refresh_template'] ?? 'Template vernieuwen' ?>
                                </button>
                                <small id="template-refresh-hint" style="display:none; color:var(--text-muted); font-size:0.75rem;">💡 <?= $lang['msg_template_live'] ?? 'Wijzigingen in de template zijn direct zichtbaar op alle pagina\'s.' ?></small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="title"><?= $label_title ?></label>
                            <input type="text" id="title" name="title" class="form-control"
                                value="<?= htmlspecialchars($page['title'] ?? '') ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="slug"><?= $label_slug ?></label>
                            <input type="text" id="slug" name="slug" class="form-control"
                                value="<?= htmlspecialchars($page['slug'] ?? '') ?>" required>
                            <small style="display: block; margin-top: 5px; color: var(--text-muted); font-size: 0.8rem;">
                                 <?= $slug_tip ?>
                            </small>
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

                        <!-- Visual Inline Builder -->
                        <div id="visual-editor-canvas" class="visual-canvas" style="display: none;">
                            <div class="empty-template"><?= $msg_select_template ?></div>
                        </div>

                        <input type="hidden" name="content" id="content-json">

                        <div
                            style="display: flex; gap: 20px; align-items: center; justify-content: flex-end; margin-top: 30px;">
                            <button type="submit" class="btn-save"><?= $btn_save ?></button>
                            <a href="/backoffice/pages" class="btn-secondary"><?= $btn_cancel ?></a>
                        </div>
                    </form>
                </div>

            </div>
        </main>
    </div>

    <!-- Editor Modal -->
    <div class="editor-modal-backdrop" id="editor-modal">
        <div class="editor-modal">
            <div class="editor-modal-header">
                <div class="editor-modal-title"><span>📝</span> <?= $btn_edit ?></div>
                <div style="display:flex; gap:10px; align-items:center;">
                    <button type="button" id="editor-mode-toggle" onclick="toggleEditorMode()"
                        style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-color); padding: 6px 14px; border-radius: 8px; cursor:pointer; font-size:0.85rem; font-weight:600;">
                        &lt;/&gt; HTML weergave
                    </button>
                    <button type="button" class="editor-modal-close" onclick="closePopupEditor()">&times;</button>
                </div>
            </div>
            <div class="editor-modal-body" style="display:flex; flex-direction:column; gap:0; padding:0;">
                <textarea id="editor-plain"
                    style="flex:1; width:100%; min-height:340px; padding:20px; font-family:'Inter',sans-serif; font-size:1rem; line-height:1.7; border:none; resize:vertical; outline:none; background:#fff; color:#1e293b;"
                    placeholder="<?= $lang['placeholder_text'] ?? 'Voer hier uw tekst in...' ?>"></textarea>
                <textarea id="editor-html"
                    style="display:none; flex:1; width:100%; min-height:340px; padding:20px; font-family:monospace; font-size:0.9rem; line-height:1.6; border:none; resize:vertical; outline:none; background:#f8fafc; color:#1e293b;"
                    placeholder="&lt;p&gt;HTML code hier...&lt;/p&gt;"></textarea>
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

        /** Custom CSS injected purely for the visual canvas inside the CMS admin view */
        const canvasStyles = `
            .visual-canvas {
                --primary: #3B2A8C;
                --accent: #E8186A;
                --text: #1A1336;
                --muted: #64748b;
                --bg: #f8fafc;
                --accent-gradient: linear-gradient(135deg, #E8186A 0%, #C41257 40%, #F0961B 100%);
                border: 2px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-top: 20px;
                background: var(--bg); color: var(--text); line-height: 1.6; font-family: 'Inter', sans-serif;
            }
            .vc-header { background: #fff; border-bottom: 1px solid #e2e8f0; }
            .vc-header-inner { height: 80px; display: flex; align-items: center; justify-content: space-between; gap: 40px; }
            .vc-footer { background: var(--text); color: white; padding: 80px 0; margin-top: 80px; }
            .vc-footer-inner { display: grid; gap: 40px; }
            .vc-container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
            .vc-main { padding: 60px 0; }
            .vc-row { display: grid; gap: 40px; margin-bottom: 80px; align-items: center; }
            .vc-col { position: relative; border: 1px dashed transparent; min-height: 100px; transition: 0.3s; }
            .vc-col:hover { border-color: #cbd5e1; }
            .vc-h-section { display: flex; align-items: center; gap: 20px; flex: 1; }
            
            .vc-input-transparent {
                width: 100%; background: transparent; border: 1px dashed #cbd5e1; padding: 8px; font-family: inherit; font-size: inherit; color: inherit; border-radius: 4px; transition: 0.2s;
            }
            .vc-input-transparent:focus { border-color: var(--accent); outline: none; background: #fff; color: #1e293b; }
            
            .vc-cta-button { display: inline-block; background: var(--accent-gradient); color: white; border-radius: 50px; text-decoration: none; font-weight: 700; border: none; padding: 15px 35px; box-shadow: 0 10px 20px rgba(232, 24, 106, 0.2); }
            .vc-img-preview { max-width: 100%; height: auto; border-radius: 24px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05); display: block; }
            .vc-usp-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
            .vc-usp-card { padding: 30px; background: #fff; border-radius: 20px; font-weight: 600; text-align: center; color: var(--text); box-shadow: 0 10px 20px rgba(0, 0, 0, 0.02); }
            .vc-logo { height: 40px; width: auto; }
            .vc-nav { display: flex; gap: 20px; font-weight: 600; }
            
            .vc-h1-input { font-family: 'Outfit', sans-serif; font-size: 3.5rem; line-height: 1.1; margin-bottom: 20px; width: 100%; background: var(--accent-gradient); -webkit-background-clip: text; color: transparent; border: none; font-weight: 700; padding: 0; }
            .vc-h1-input:focus { color: var(--text); background: transparent; -webkit-background-clip: border-box; }
            .vc-p-input { font-size: 1.25rem; color: var(--muted); width: 100%; min-height: 100px; resize: vertical; border: 1px dashed transparent; }
            .vc-p-input:focus { border-color: #cbd5e1; background: #fff; color: #1a1336; }
            .vc-h3-input { font-family: 'Outfit', sans-serif; font-size: 1.8rem; margin-bottom: 20px; border: none; }
        `;
        
        // Inject styles
        const styleTag = document.createElement('style');
        styleTag.innerHTML = canvasStyles;
        document.head.appendChild(styleTag);

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

                if (template.type === 'homepage') {
                    slugInput.value = '/';
                    slugInput.readOnly = true;
                } else {
                    slugInput.readOnly = false;
                }

                // Show the refresh button and hint
                document.getElementById('btn-refresh-template').style.display = 'inline-block';
                document.getElementById('template-refresh-hint').style.display = 'inline';

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

        function renderVisualCanvas() {
            if (!currentTemplate) return;
            visualCanvas.style.display = 'block';
            const layout = JSON.parse(currentTemplate.layout_json);

            let html = `
                <div class="vc-header">
                    <div class="vc-container vc-header-inner">
                        ${renderVisualSection(layout.header, 'header')}
                    </div>
                </div>
                <div class="vc-main vc-container">
                    ${renderVisualSection(layout.main, 'main')}
                </div>
                <div class="vc-footer">
                    <div class="vc-container vc-footer-inner" style="grid-template-columns: repeat(${layout.footer?.sections?.length || 1}, 1fr);">
                        ${renderVisualSection(layout.footer, 'footer')}
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
        }

        function renderVisualSection(section, type) {
            if (!section) return '';
            let html = '';

            if (type === 'header') {
                section.sections.forEach((s, i) => {
                    const align = i === 0 ? 'flex-start' : (i === 1 ? 'center' : 'flex-end');
                    html += `<div class="vc-h-section" style="justify-content: ${align};">${renderVisualBlock(s.type, `${type}.sections.${i}`)}</div>`;
                });
            } else if (type === 'footer') {
                section.sections.forEach((s, i) => {
                    html += `<div>${renderVisualBlock(s.type, `${type}.sections.${i}`)}</div>`;
                });
            } else if (type === 'main') {
                section.rows.forEach((row, ri) => {
                    const count = row.columns.length;
                    html += `<div class="vc-row" style="grid-template-columns: repeat(${count}, 1fr);">`;
                    row.columns.forEach((col, ci) => {
                        html += `<div class="vc-col">${renderVisualBlock(col.type, `main.rows.${ri}.columns.${ci}`)}</div>`;
                    });
                    html += `</div>`;
                });
            }
            return html;
        }

        function renderVisualBlock(type, path) {
            const data = getDeepValue(getLangData(), path) || {};
            
            switch (type) {
                case 'text':
                    return `
                        <div>
                            <input type="text" class="vc-input-transparent vc-h1-input" placeholder="<?= $lang['placeholder_text'] ?? 'Voer hier uw titel in...' ?>" value="${data.title || ''}" oninput="updateData('${path}.title', this.value)">
                            <div style="position:relative;">
                                <button type="button" class="btn-edit-popup" onclick="openPopupEditor('${path}.text')" style="position:absolute; right:5px; top:-25px; z-index:10; font-size:12px; padding:2px 8px;">✎ Editor</button>
                                <textarea id="textarea_${path.replace(/\./g, '_')}_text" class="vc-input-transparent vc-p-input" placeholder="<?= $lang['placeholder_text'] ?? 'Voer hier uw tekst in of gebruik de uitgebreide editor...' ?>" oninput="updateData('${path}.text', this.value)">${data.text || ''}</textarea>
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
                            <input type="text" class="vc-input-transparent vc-h3-input" placeholder="Actie Titel" value="${data.title || ''}" oninput="updateData('${path}.title', this.value)">
                            <div style="margin-bottom:10px;">
                                <input type="text" class="vc-input-transparent vc-cta-button" placeholder="Knop Tekst (bijv. Registreer Nu)" value="${data.button_text || ''}" oninput="updateData('${path}.button_text', this.value)" style="text-align:center; display:inline-block; max-width:250px;">
                            </div>
                            <input type="text" class="vc-input-transparent" placeholder="Link (bijv. /contact)" value="${data.url || ''}" oninput="updateData('${path}.url', this.value)">
                        </div>
                    `;
                case 'logo':
                    if (siteSettings.hide_logo === '1') return '<div style="color:#cbd5e1; font-size:0.8rem; border:1px dashed #cbd5e1; padding:5px; text-align:center; border-radius:5px;">Logo (Verbergen ingeschakeld in instellingen)</div>';
                    const logoUrl = siteSettings.site_logo || '/assets/logo/logo_fritsion_cms.png';
                    return `
                        <div style="display:flex; align-items:center; gap:10px;">
                            <img src="${logoUrl}" class="vc-logo">
                        </div>`;
                case 'menu': 
                    return `
                        <div class="vc-nav">
                            <input type="text" class="vc-input-transparent" style="text-align:right;" placeholder="Home, Over ons, Contact (komma gescheiden)" value="${data.items || ''}" oninput="updateData('${path}.items', this.value)">
                        </div>`;
                case 'usps': 
                    return `
                        <div class="vc-usp-grid">
                            <input type="text" class="vc-input-transparent vc-usp-card" placeholder="USP 1 (bijv 🚀 Snelle Levering)" value="${data.usp_1 || ''}" oninput="updateData('${path}.usp_1', this.value)">
                            <input type="text" class="vc-input-transparent vc-usp-card" placeholder="USP 2 (bijv 🛡️ Veilig Betalen)" value="${data.usp_2 || ''}" oninput="updateData('${path}.usp_2', this.value)">
                            <input type="text" class="vc-input-transparent vc-usp-card" placeholder="USP 3 (bijv 💎 Top Kwaliteit)" value="${data.usp_3 || ''}" oninput="updateData('${path}.usp_3', this.value)">
                        </div>`;
                case 'socials': 
                    return `
                        <div style="display:flex; gap:10px;">
                            <input type="text" class="vc-input-transparent" placeholder="Facebook Link URL" value="${data.facebook || ''}" oninput="updateData('${path}.facebook', this.value)">
                            <input type="text" class="vc-input-transparent" placeholder="Instagram Link URL" value="${data.instagram || ''}" oninput="updateData('${path}.instagram', this.value)">
                        </div>`;
                case 'video': 
                    return `
                        <div style="background:linear-gradient(135deg, var(--text) 0%, var(--primary) 100%); height:200px; border-radius:24px; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:20px;">
                            <span style="color:white; font-size:2rem; margin-bottom:10px;">▶</span>
                            <input type="text" class="vc-input-transparent" style="color:white; border-color:rgba(255,255,255,0.3); text-align:center; max-width:80%;" placeholder="YouTube/Video Embed URL" value="${data.url || ''}" oninput="updateData('${path}.url', this.value)">
                        </div>`;
                case 'html': 
                    return `
                        <div style="border:1px dashed #cbd5e1; border-radius:8px; padding:10px;">
                            <div style="font-size:0.8rem; font-weight:600; color:#475569; margin-bottom:5px;">&lt;/&gt; HTML Embed Code</div>
                            <textarea class="vc-input-transparent" style="font-family:monospace; min-height:80px; border:none; resize:vertical; background:#f8fafc;" placeholder="Voer hier je eigen HTML..." oninput="updateData('${path}.code', this.value)">${data.code || ''}</textarea>
                        </div>`;
                case 'map': 
                    return `
                        <div style="background:#e0f2fe; height:300px; border-radius:24px; display:flex; flex-direction:column; justify-content:center; align-items:center; padding:20px; text-align:center;">
                            <span style="font-size:2rem;">📍</span>
                            <div style="color:#0369a1; font-weight:600; margin:10px 0;">Kaart</div>
                            <input type="text" class="vc-input-transparent" style="background:#fff; border-color:#bae6fd; color:#0369a1; text-align:center; max-width:300px;" placeholder="Adres (bijv. Straat 1, Stad)" value="${data.address || ''}" oninput="updateData('${path}.address', this.value)">
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
            progress.style.width = '0%';

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/backoffice/media/upload', true);

            xhr.upload.onprogress = (e) => {
                if (e.lengthComputable) {
                    const percent = (e.loaded / e.total) * 100;
                    progress.style.width = percent + '%';
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

        function openPopupEditor(path) {
            activeEditorPath = path;
            const rawContent = getDeepValue(getLangData(), path) || '';
            
            // Always open in plain-text view
            editorMode = 'plain';
            document.getElementById('editor-plain').value = stripHtml(rawContent);
            document.getElementById('editor-html').value = rawContent;
            syncModeUI();
            
            document.getElementById('editor-modal').classList.add('active');
        }

        function closePopupEditor() {
            document.getElementById('editor-modal').classList.remove('active');
            activeEditorPath = null;
        }

        function savePopupEditorContent() {
            if (!activeEditorPath) return;

            // Always save as plain text (strip any HTML)
            let finalContent;
            if (editorMode === 'html') {
                // They were editing HTML — strip it to plain text
                finalContent = stripHtml(document.getElementById('editor-html').value);
            } else {
                finalContent = document.getElementById('editor-plain').value;
            }

            updateData(activeEditorPath, finalContent);
            renderVisualCanvas();
            closePopupEditor();
        }

        // Editor mode toggle: 'plain' or 'html'
        let editorMode = 'plain';

        function toggleEditorMode() {
            if (editorMode === 'plain') {
                // Switch to HTML: take plain text, don't add HTML yet (show raw stored value)
                const stored = getDeepValue(getLangData(), activeEditorPath) || '';
                document.getElementById('editor-html').value = stored;
                editorMode = 'html';
            } else {
                // Switch to plain: strip HTML from the current html textarea
                const htmlVal = document.getElementById('editor-html').value;
                document.getElementById('editor-plain').value = stripHtml(htmlVal);
                editorMode = 'plain';
            }
            syncModeUI();
        }

        function syncModeUI() {
            const plainEl = document.getElementById('editor-plain');
            const htmlEl  = document.getElementById('editor-html');
            const toggleBtn = document.getElementById('editor-mode-toggle');

            if (editorMode === 'plain') {
                plainEl.style.display = 'block';
                htmlEl.style.display  = 'none';
                toggleBtn.textContent = '&lt;/&gt; HTML weergave';
            } else {
                plainEl.style.display = 'none';
                htmlEl.style.display  = 'block';
                toggleBtn.textContent = '📝 Tekst weergave';
            }
        }

        function stripHtml(html) {
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