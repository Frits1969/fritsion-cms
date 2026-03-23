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

                        <!-- Dynamic Blocks Section -->
                        <div id="dynamic-blocks" class="blocks-container">
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

                <!-- Preview Panel -->
                <div class="preview-panel">
                    <div class="preview-header">
                        <div style="display: flex; gap: 6px;">
                            <div class="preview-dot"></div>
                            <div class="preview-dot"></div>
                            <div class="preview-dot"></div>
                        </div>
                        <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted);"><?= $label_live_preview ?></div>
                        <div style="width: 40px;"></div>
                    </div>
                    <div class="preview-content">
                        <iframe id="preview-frame" class="preview-frame"></iframe>
                    </div>
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
        const dynamicBlocksContainer = document.getElementById('dynamic-blocks');
        const previewFrame = document.getElementById('preview-frame');
        const contentJsonInput = document.getElementById('content-json');
        
        const userWidget = document.getElementById('user-widget');
        const userMenu = document.getElementById('user-menu');
        const langSwitcher = document.getElementById('lang-switcher');

        const siteSettings = <?= json_encode($siteSettings ?? []) ?>;
        let currentTemplate = null;
        let pageData = <?= json_encode(json_decode($page['content'] ?? '{}', true)) ?>;

        const blockDefinitions = {
            'text': { name: '<?= $block_text ?>', icon: '📝', fields: [{ name: 'title', type: 'text', label: '<?= $label_title ?>' }, { name: 'text', type: 'textarea', label: '<?= $label_text ?>' }] },
            'image': { name: '<?= $block_image ?>', icon: '🖼️', fields: [{ name: 'url', type: 'image', label: '<?= $block_image ?>' }, { name: 'alt', type: 'text', label: '<?= $label_alt_text ?>' }] },
            'cta': { name: '<?= $block_cta ?>', icon: '🎯', fields: [{ name: 'title', type: 'text', label: '<?= $label_title ?>' }, { name: 'button_text', type: 'text', label: '<?= $label_button_text ?>' }, { name: 'url', type: 'text', label: 'Link' }] },
            'logo': { name: '<?= $block_logo ?>', icon: '✨', fields: [] },
            'menu': { name: '<?= $block_menu ?>', icon: '☰', fields: [{ name: 'items', type: 'text', label: '<?= $label_items ?>' }] },
            'socials': { name: '<?= $block_socials ?>', icon: '📱', fields: [{ name: 'facebook', type: 'text', label: '<?= $label_facebook ?>' }, { name: 'instagram', type: 'text', label: '<?= $label_instagram ?>' }] },
            'usps': { name: '<?= $block_usps ?>', icon: '🚀', fields: [{ name: 'usp_1', type: 'text', label: '<?= $label_usp_1 ?>' }, { name: 'usp_2', type: 'text', label: '<?= $label_usp_2 ?>' }, { name: 'usp_3', type: 'text', label: '<?= $label_usp_3 ?>' }] },
            'video': { name: '<?= $block_video ?>', icon: '🎬', fields: [{ name: 'url', type: 'text', label: 'YouTube/Video URL' }] },
            'html': { name: '<?= $block_html ?>', icon: '💻', fields: [{ name: 'code', type: 'textarea', label: 'HTML/Embed Code' }] },
            'map': { name: '<?= $block_map ?>', icon: '📍', fields: [{ name: 'address', type: 'text', label: '<?= $label_address ?>' }] }
        };

        async function handleTemplateChange(templateId) {
            if (!templateId) {
                dynamicBlocksContainer.innerHTML = '<div class="empty-template"><?= $msg_select_template ?></div>';
                updatePreview();
                return;
            }

            try {
                const response = await fetch(`/backoffice/templates/get/${templateId}`);
                const template = await response.json();
                currentTemplate = template;

                const layout = JSON.parse(template.layout_json);
                renderBlockFields(layout);

                // Specific Homepage logic
                if (template.type === 'homepage') {
                    slugInput.value = '/';
                    slugInput.readOnly = true;
                } else {
                    slugInput.readOnly = false;
                }

                updatePreview();
            } catch (error) {
                console.error('Error fetching template:', error);
            }
        }

        function renderBlockFields(layout) {
            dynamicBlocksContainer.innerHTML = '';

            // Render Header Sections
            if (layout.header && layout.header.sections) {
                appendSectionLabel('<?= $lang['section_header'] ?? "Header" ?>');
                layout.header.sections.forEach((section, index) => {
                    appendBlockField(`header.sections.${index}`, section.type);
                });
            }

            // Render Main Rows/Cols
            if (layout.main && layout.main.rows) {
                appendSectionLabel('<?= $lang['label_content'] ?? "Inhoud" ?>');
                layout.main.rows.forEach((row, rowIndex) => {
                    row.columns.forEach((col, colIndex) => {
                        appendBlockField(`main.rows.${rowIndex}.columns.${colIndex}`, col.type);
                    });
                });
            }

            // Render Footer Sections
            if (layout.footer && layout.footer.sections) {
                appendSectionLabel('<?= $lang['section_footer'] ?? "Footer" ?>');
                layout.footer.sections.forEach((section, index) => {
                    appendBlockField(`footer.sections.${index}`, section.type);
                });
            }
        }

        function appendSectionLabel(text) {
            const label = document.createElement('div');
            label.className = 'section-label';
            label.textContent = text;
            dynamicBlocksContainer.appendChild(label);
        }

        function appendBlockField(path, type) {
            const def = blockDefinitions[type] || { name: type, icon: '📦', fields: [{ name: 'value', type: 'text', label: 'Waarde' }] };
            const blockDiv = document.createElement('div');
            blockDiv.className = 'block-item';

            let fieldsHtml = `<div class="block-header"><span class="block-icon">${def.icon}</span> ${def.name}</div>`;

            if (type === 'logo') {
                const logoUrl = siteSettings.site_logo || '/assets/logo/logo_fritsion_cms.png';
                fieldsHtml += `
                    <div style="padding: 15px; background: #fff; border-radius: 10px; border: 1px solid var(--glass-border); text-align: center;">
                        <img src="${logoUrl}" style="max-height: 50px; margin-bottom: 10px; display: block; margin-left: auto; margin-right: auto;">
                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                            <?= $lang['msg_logo_settings'] ?? "Systeembrede instelling. Wijzig dit logo via" ?> <a href="/backoffice/settings" target="_blank" style="color: var(--accent-pink);"><?= $nav_settings ?></a>.
                        </div>
                    </div>
                `;
            }

            def.fields.forEach(field => {
                const value = getDeepValue(pageData, `${path}.${field.name}`) || '';

                if (field.type === 'image') {
                    fieldsHtml += `
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label style="font-size: 0.8rem;">${field.label}</label>
                            <div class="dropzone" id="dropzone_${path.replace(/\./g, '_')}_${field.name}" onclick="document.getElementById('file_${path.replace(/\./g, '_')}_${field.name}').click()">
                                <div class="dropzone-icon">☁️</div>
                                <div class="dropzone-text"><?= $lang['msg_dropzone'] ?? "Sleep afbeelding hiernaartoe of klik om te uploaden" ?></div>
                                ${value ? `<img src="${value}" alt="Preview">` : ''}
                                <div class="upload-progress" id="progress_${path.replace(/\./g, '_')}_${field.name}"></div>
                                <input type="file" id="file_${path.replace(/\./g, '_')}_${field.name}" accept="image/*" onchange="handleFileUpload(this, '${path}.${field.name}')">
                            </div>
                            <input type="text" class="form-control" style="margin-top: 10px; font-size: 0.8rem;" value="${value}" oninput="updateData('${path}.${field.name}', this.value)" placeholder="<?= $lang['placeholder_url'] ?? "Of voer een URL in..." ?>">
                        </div>
                    `;
                } else {
                    fieldsHtml += `
                        <div class="form-group" style="margin-bottom: 15px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <label style="font-size: 0.85rem; font-weight: 600; color: var(--text-color);">${field.label}</label>
                                ${field.type === 'textarea' ? `<button type="button" class="btn-edit-popup" onclick="openPopupEditor('${path}.${field.name}')"><span>✨</span> <?= $lang['btn_editor'] ?? "Editor" ?></button>` : ''}
                            </div>
                            ${field.type === 'textarea'
                            ? `<textarea class="form-control" id="textarea_${path.replace(/\./g, '_')}_${field.name}" style="min-height: 120px;" oninput="updateData('${path}.${field.name}', this.value)">${value}</textarea>`
                            : `<input type="text" class="form-control" value="${value}" oninput="updateData('${path}.${field.name}', this.value)">`
                        }
                        </div>
                    `;
                }
            });

            blockDiv.innerHTML = fieldsHtml;
            dynamicBlocksContainer.appendChild(blockDiv);

            // Setup drag and drop for any dropzones in this block
            blockDiv.querySelectorAll('.dropzone').forEach(dz => {
                dz.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dz.classList.add('dragover');
                });
                dz.addEventListener('dragleave', () => dz.classList.remove('dragover'));
                dz.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dz.classList.remove('dragover');
                    const file = e.dataTransfer.files[0];
                    if (file && file.type.startsWith('image/')) {
                        // Extract path from ID
                        const pathId = dz.id.replace('dropzone_', '');
                        // Map back to dotted path
                        // This is a bit tricky, let's use a data attribute instead next time
                        // For now we assume the ID structure is consistent
                        const dottedPath = dz.querySelector('input[type="file"]').onchange.toString().match(/'([^']+)'/)[1];
                        performUpload(file, dottedPath, dz);
                    }
                });
            });
        }

        function handleFileUpload(input, path) {
            const file = input.files[0];
            const dz = input.closest('.dropzone');
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
                        // Update preview image in dropzone
                        let img = dz.querySelector('img');
                        if (!img) {
                            img = document.createElement('img');
                            dz.appendChild(img);
                        }
                        img.src = response.url;
                        dz.querySelector('.dropzone-text').textContent = '<?= $lang['msg_upload_complete'] ?? "Upload voltooid!" ?>';
                        setTimeout(() => progress.style.width = '0%', 1000);
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
            setDeepValue(pageData, path, value);
            updatePreview();
        }

        function updatePreview() {
            if (!currentTemplate) return;

            const layout = JSON.parse(currentTemplate.layout_json);

            // Build premium preview HTML
            let html = `
                <html>
                <head>
                    <base href="/">
                    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@700&display=swap" rel="stylesheet">
                    <style>
                        body { font-family: 'Inter', sans-serif; margin: 0; color: #1a1336; background: #fff; overflow-x: hidden; }
                        header { padding: 0 40px; height: 80px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #fff; }
                        footer { padding: 60px 40px; border-top: 1px solid #f1f5f9; background: #1a1336; color: white; margin-top: 60px; }
                        .container { max-width: 1000px; margin: 0 auto; padding: 40px 20px; }
                        .row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px; margin-bottom: 60px; align-items: center; }
                        .block { padding: 10px; border-radius: 12px; }
                        h1 { font-family: 'Outfit', sans-serif; font-size: 2.5rem; margin-bottom: 20px; color: #E8186A; }
                        p { font-size: 1.1rem; line-height: 1.6; color: #64748b; }
                        .cta-button { display: inline-block; padding: 14px 30px; background: linear-gradient(135deg, #E8186A 0%, #F0961B 100%); color: white; border-radius: 50px; text-decoration: none; font-weight: 700; margin-top: 20px; box-shadow: 0 10px 20px rgba(232, 24, 106, 0.2); }
                        img { max-width: 100%; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); }
                        .usp-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
                        .usp-card { padding: 20px; background: #f8fafc; border-radius: 16px; font-weight: 600; text-align: center; }
                        .logo { max-height: 40px; }
                        .nav-placeholder { color: #64748b; font-weight: 500; display: flex; gap: 20px; }
                    </style>
                </head>
                <body>
                    <header>
                        ${renderLayoutSection(layout.header, 'header')}
                    </header>
                    <div class="container">
                        ${renderLayoutSection(layout.main, 'main')}
                    </div>
                    <footer>
                        ${renderLayoutSection(layout.footer, 'footer')}
                    </footer>
                </body>
                </html>
            `;

            const doc = previewFrame.contentDocument || previewFrame.contentWindow.document;
            doc.open();
            doc.write(html);
            doc.close();
        }

        function renderLayoutSection(section, type) {
            if (!section) return '';
            let html = '';

            if (type === 'header' || type === 'footer') {
                html += '<div style="display:flex; gap:30px; align-items:center; width:100%;">';
                section.sections.forEach((s, i) => {
                    html += `<div style="flex:1;">${renderBlock(s.type, `${type}.sections.${i}`)}</div>`;
                });
                html += '</div>';
            } else if (type === 'main') {
                section.rows.forEach((row, ri) => {
                    html += `<div class="row">`;
                    row.columns.forEach((col, ci) => {
                        html += `<div class="col">${renderBlock(col.type, `main.rows.${ri}.columns.${ci}`)}</div>`;
                    });
                    html += `</div>`;
                });
            }
            return html;
        }

        function renderBlock(type, path) {
            const data = getDeepValue(pageData, path) || {};
            switch (type) {
                case 'text': return `<div>\${data.title ? \`<h2>\${data.title}</h2>\` : ''}<div>\${data.text || '<?= $lang['placeholder_text'] ?? "Tekstblok..." ?>'}</div></div>`;
                case 'image': return data.url ? `<img src="\${data.url}" alt="\${data.alt || ''}">` : `<div style="background:#f1f5f9; height:200px; display:flex; align-items:center; justify-content:center; border-radius:20px; color:#cbd5e1;"><?= $block_image ?></div>`;
                case 'cta': return `<div><h3>\${data.title || '<?= $lang['msg_cta_title'] ?? "Klaar om te starten?" ?>'}</h3><a href="#" class="cta-button">\${data.button_text || '<?= $lang['btn_register'] ?? "Registeer nu" ?>'}</a></div>`;
                case 'logo':
                    if (siteSettings.hide_logo === '1') return '';
                    const logoUrl = siteSettings.site_logo || '/assets/logo/logo_fritsion_cms.png';
                    return `<img src="${logoUrl}" class="logo">`;
                case 'menu': return `<div class="nav-placeholder">${(data.items || 'Home, Over ons, Producten, Contact').split(',').map(i => `<span>${i.trim()}</span>`).join('')}</div>`;
                case 'usps': return `<div class="usp-grid"><div class="usp-card">🚀 \${data.usp_1 || '<?= $lang['usp_1_default'] ?? "Snelle Levering" ?>'}</div><div class="usp-card">🛡️ \${data.usp_2 || '<?= $lang['usp_2_default'] ?? "Veilig Betalen" ?>'}</div><div class="usp-card">💎 \${data.usp_3 || '<?= $lang['usp_3_default'] ?? "Top Kwaliteit" ?>'}</div></div>`;
                case 'socials': return `<div style="display:flex; gap:15px; font-size:0.9rem;">${data.facebook ? 'FB ' : ''}${data.instagram ? 'IG ' : ''}</div>`;
                case 'video': return `<div style="background:#000; height:250px; border-radius:20px; display:flex; align-items:center; justify-content:center; color:white;">▶ Play Video</div>`;
                case 'html': return data.code || '<pre>&lt;Custom HTML&gt;</pre>';
                case 'map': return `<div style="background:#e0f2fe; height:200px; border-radius:20px; display:flex; align-items:center; justify-content:center; color:#0369a1;">📍 <?= $block_map ?>: \${data.address || '<?= $lang['label_address'] ?? "Locatie" ?>'}</div>`;
                default: return `<div style="border:1px dashed #eee; padding:10px;"><?= $lang['label_block'] ?? "Blok" ?>: \${type}</div>`;
            }
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
            contentJsonInput.value = JSON.stringify(pageData);
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
            updatePreview();
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
            const content = getDeepValue(pageData, path) || '';
            
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

            // If already expanded and clicking the other flag
            const currentLang = '<?= $_SESSION['lang'] ?? 'nl' ?>';
            if (currentLang !== lang && isDirty) {
                if (!confirm('<?= $msg_unsaved_changes ?>')) {
                    event.preventDefault();
                    langSwitcher.classList.remove('expanded');
                }
            }
        }
    </script>
</body>

</html>