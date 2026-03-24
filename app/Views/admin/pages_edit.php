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
                <button type="button" class="editor-modal-close" onclick="closePopupEditor()">&times;</button>
            </div>
            <div class="editor-modal-body">
                <textarea id="popup-tinymce"></textarea>
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
                border: 2px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-top: 20px;
                background: #f8fafc;
            }
            .vc-header { padding: 20px 40px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #fff; }
            .vc-footer { padding: 40px; border-top: 1px solid #e2e8f0; background: #1a1336; color: white; margin-top: 40px; }
            .vc-container { max-width: 1000px; margin: 0 auto; padding: 40px 20px; }
            .vc-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px; margin-bottom: 60px; align-items: center; }
            .vc-col { position: relative; border: 1px dashed transparent; padding: 10px; border-radius: 8px; transition: 0.3s; }
            .vc-col:hover { border-color: #cbd5e1; background: #fff; }
            
            .vc-input-transparent {
                width: 100%; background: transparent; border: 1px dashed #cbd5e1; padding: 8px; font-family: inherit; font-size: inherit; color: inherit; border-radius: 4px; transition: 0.2s;
            }
            .vc-input-transparent:focus { border-color: #e8186a; outline: none; background: #fff; color: #1e293b; }
            
            .vc-cta-button { display: inline-block; background: linear-gradient(135deg, #E8186A 0%, #F0961B 100%); color: white; border-radius: 50px; text-decoration: none; font-weight: 700; border: none; padding: 4px 0; }
            .vc-img-preview { max-width: 100%; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); }
            .vc-usp-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
            .vc-usp-card { padding: 10px; background: #f8fafc; border-radius: 12px; font-weight: 600; text-align: center; color: #1e293b; border: 1px solid #e2e8f0; }
            .vc-logo { max-height: 40px; }
            .vc-nav { color: #64748b; font-weight: 500; display: flex; gap: 20px; }
            
            .vc-h1-input { font-family: 'Outfit', sans-serif; font-size: 2.5rem; color: #E8186A; border: none; font-weight: 700; margin-bottom: 20px; width: 100%; }
            .vc-p-input { font-size: 1.1rem; line-height: 1.6; color: #64748b; width: 100%; height: auto; min-height: 100px; resize: vertical; }
        `;
        
        // Inject styles
        const styleTag = document.createElement('style');
        styleTag.innerHTML = canvasStyles;
        document.head.appendChild(styleTag);

        async function handleTemplateChange(templateId) {
            if (!templateId) {
                visualCanvas.style.display = 'none';
                return;
            }

            try {
                const response = await fetch(`/backoffice/templates/get/${templateId}`);
                const template = await response.json();
                currentTemplate = template;

                if (template.type === 'homepage') {
                    slugInput.value = '/';
                    slugInput.readOnly = true;
                } else {
                    slugInput.readOnly = false;
                }

                renderVisualCanvas();
            } catch (error) {
                console.error('Error fetching template:', error);
            }
        }

        function renderVisualCanvas() {
            if (!currentTemplate) return;
            visualCanvas.style.display = 'block';
            const layout = JSON.parse(currentTemplate.layout_json);

            let html = `
                <div class="vc-header">
                    ${renderVisualSection(layout.header, 'header')}
                </div>
                <div class="vc-container">
                    ${renderVisualSection(layout.main, 'main')}
                </div>
                <div class="vc-footer">
                    ${renderVisualSection(layout.footer, 'footer')}
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

            if (type === 'header' || type === 'footer') {
                html += '<div style="display:flex; gap:30px; align-items:center; width:100%;">';
                section.sections.forEach((s, i) => {
                    html += `<div style="flex:1;">${renderVisualBlock(s.type, `${type}.sections.${i}`)}</div>`;
                });
                html += '</div>';
            } else if (type === 'main') {
                section.rows.forEach((row, ri) => {
                    html += `<div class="vc-row">`;
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
                                <button type="button" class="btn-edit-popup" onclick="openPopupEditor('${path}.text')" style="position:absolute; right:5px; top:5px; z-index:10; font-size:12px; padding:2px 8px;">✎ Editor</button>
                                <textarea id="textarea_${path.replace(/\./g, '_')}_text" class="vc-input-transparent vc-p-input" placeholder="<?= $lang['placeholder_text'] ?? 'Voer hier uw tekst in of gebruik de uitgebreide editor...' ?>" oninput="updateData('${path}.text', this.value)">${data.text || ''}</textarea>
                            </div>
                        </div>
                    `;
                case 'image':
                    return `
                        <div class="dropzone" onclick="document.getElementById('file_${path.replace(/\./g, '_')}').click()" style="min-height:200px; display:flex; flex-direction:column; justify-content:center; align-items:center; border:2px dashed #cbd5e1; border-radius:20px; text-align:center; padding:20px; cursor:pointer;" id="dropzone_${path.replace(/\./g, '_')}">
                            <div class="dropzone-text" style="color:#64748b; margin-bottom:10px;">☁️ Klik of sleep een afbeelding</div>
                            ${data.url ? `<img src="${data.url}" alt="Preview" class="vc-img-preview" style="max-height:200px;">` : ''}
                            <input type="file" id="file_${path.replace(/\./g, '_')}" accept="image/*" style="display:none;" data-path="${path}.url" onchange="handleFileUpload(this, '${path}.url', this.parentNode)">
                            <div class="upload-progress" style="height:4px; background:#10b981; width:0%; transition:0.3s; margin-top:10px; border-radius:2px;"></div>
                        </div>
                        <input type="text" class="vc-input-transparent" placeholder="Afbeelding URL..." value="${data.url || ''}" oninput="updateData('${path}.url', this.value)" style="margin-top:10px;">
                    `;
                case 'cta':
                    return `
                        <div style="text-align:center; background:#f8fafc; padding:30px; border-radius:20px; border:1px solid #e2e8f0;">
                            <input type="text" class="vc-input-transparent" placeholder="Actie Titel" value="${data.title || ''}" oninput="updateData('${path}.title', this.value)" style="font-size:1.5rem; font-weight:700; text-align:center; margin-bottom:15px; color:#1e293b;">
                            <input type="text" class="vc-input-transparent vc-cta-button" placeholder="Knop Tekst (bijv. Registreer Nu)" value="${data.button_text || ''}" oninput="updateData('${path}.button_text', this.value)" style="text-align:center; padding:12px 25px;">
                            <input type="text" class="vc-input-transparent" placeholder="Link (bijv. /contact)" value="${data.url || ''}" oninput="updateData('${path}.url', this.value)" style="margin-top:10px; text-align:center;">
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
                        <div style="background:#000; height:250px; border-radius:20px; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:20px;">
                            <span style="color:white; font-size:2rem; margin-bottom:10px;">▶</span>
                            <input type="text" class="vc-input-transparent" style="color:white; border-color:#333; text-align:center; max-width:80%;" placeholder="YouTube/Video Embed URL" value="${data.url || ''}" oninput="updateData('${path}.url', this.value)">
                        </div>`;
                case 'html': 
                    return `
                        <div style="border:1px solid #cbd5e1; border-radius:10px; overflow:hidden;">
                            <div style="background:#e2e8f0; padding:5px 10px; font-size:0.8rem; font-weight:600; color:#475569;">&lt;/&gt; HTML Embed Code</div>
                            <textarea class="vc-input-transparent" style="font-family:monospace; min-height:100px; border:none; resize:vertical; background:#f8fafc;" placeholder="Voer hier je eigen HTML of Embed iframe in..." oninput="updateData('${path}.code', this.value)">${data.code || ''}</textarea>
                        </div>`;
                case 'map': 
                    return `
                        <div style="background:#e0f2fe; border-radius:20px; padding:20px; text-align:center;">
                            <span style="font-size:2rem;">📍</span>
                            <div style="color:#0369a1; font-weight:600; margin:10px 0;">Google Maps Locatie / Adres</div>
                            <input type="text" class="vc-input-transparent" style="background:#fff; border-color:#bae6fd; color:#0369a1; text-align:center;" placeholder="Adres (bijv. Straat 1, Stad)" value="${data.address || ''}" oninput="updateData('${path}.address', this.value)">
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
            const content = getDeepValue(getLangData(), path) || '';
            
            document.getElementById('editor-modal').classList.add('active');
            
            if (tinymceInstance) {
                tinymceInstance.setContent(content);
            } else {
                tinymce.init({
                    selector: '#popup-tinymce',
                    height: '100%',
                    menubar: false,
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                        'insertdatetime', 'media', 'table', 'help', 'wordcount'
                    ],
                    toolbar: 'undo redo | blocks | ' +
                        'bold italic backcolor | alignleft aligncenter ' +
                        'alignright alignjustify | bullist numlist outdent indent | ' +
                        'removeformat | code help',
                    content_style: 'body { font-family:Inter,Helvetica,Arial,sans-serif; font-size:16px }',
                    setup: function(editor) {
                        tinymceInstance = editor;
                        editor.on('init', function() {
                            editor.setContent(content);
                        });
                    }
                });
            }
        }

        function closePopupEditor() {
            document.getElementById('editor-modal').classList.remove('active');
            activeEditorPath = null;
        }

        function savePopupEditorContent() {
            if (tinymceInstance && activeEditorPath) {
                const content = tinymceInstance.getContent();
                updateData(activeEditorPath, content);
                
                // Also update the hidden textarea if it exists to keep UI in sync
                const textareaId = `textarea_${activeEditorPath.replace(/\./g, '_')}`;
                const textarea = document.getElementById(textareaId);
                if (textarea) {
                    textarea.value = content;
                }
                
                closePopupEditor();
            }
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