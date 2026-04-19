<?php
$lang = $GLOBALS['lang'] ?? [];
extract($lang);

$backoffice_title = $backoffice_title ?? 'Backoffice';
$settings_title = $settings_title ?? 'Instellingen';
$nav_dashboard = $nav_dashboard ?? 'Dashboard';
$role_super_admin = $role_super_admin ?? 'Super Admin';
$nav_profile = $nav_profile ?? 'Profiel';
$nav_logout = $nav_logout ?? 'Uitloggen';
$nav_back_to_dashboard = $nav_back_to_dashboard ?? 'Terug naar Dashboard';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'nl' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/assets/logo/logo_fritsion_cms_favicon.png">
    <link rel="shortcut icon" href="/assets/logo/logo_fritsion_cms_favicon.ico">
    <title><?= $settings_title ?> | Fritsion CMS</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/admin_shared.css">
</head>

<body>
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <div class="main-wrapper">
        <header class="topbar">
            <div style="font-weight: 600; color: var(--text-muted);"><?= $backoffice_title ?> / <?= $nav_settings ?>
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
            <?php if (isset($error) && $error): ?>
                <div class="alert alert-error">
                    <span>⚠️</span> <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success) && $success): ?>
                <div class="alert alert-success">
                    <span>✅</span> <?= $success ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="header-section">
                    <div>
                        <h1><?= $settings_title ?></h1>
                        <p><?= $settings_desc ?></p>
                    </div>
                </div>

                <div class="settings-grid">
                    <!-- Site Configuration -->
                    <div class="settings-card">
                        <h3><span>🏠</span> <?= $site_config_title ?></h3>
                        <div class="setting-row">
                            <span class="setting-label"><?= $label_site_name ?></span>
                            <span class="setting-value">
                                <input type="text" name="site_name" class="setting-input"
                                    value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>" required>
                            </span>
                        </div>
                        <div class="setting-row">
                            <span class="setting-label"><?= $label_site_desc ?></span>
                            <span class="setting-value">
                                <input type="text" name="site_desc" class="setting-input"
                                    value="<?= htmlspecialchars($settings['site_description'] ?? '') ?>" required>
                            </span>
                        </div>
                        <div class="setting-row">
                            <span class="setting-label"><?= $label_domain ?></span>
                            <span class="setting-value">
                                <input type="text" name="site_domain" class="setting-input"
                                    value="<?= htmlspecialchars($settings['site_domain'] ?? '') ?>" required>
                            </span>
                        </div>
                        <div class="setting-row">
                            <span class="setting-label"><?= $label_default_lang ?></span>
                            <span class="setting-value">
                                <select name="default_lang" class="setting-input">
                                    <option value="nl" <?= ($env['language'] ?? 'nl') === 'nl' ? 'selected' : '' ?>>Nederlands</option>
                                    <option value="en" <?= ($env['language'] ?? 'nl') === 'en' ? 'selected' : '' ?>>English</option>
                                </select>
                            </span>
                        </div>
                    </div>

                    <!-- Branding Section -->
                    <div class="settings-card">
                        <h3><span>✨</span> Branding</h3>
                        <div class="setting-row" style="flex-direction: column; align-items: flex-start; gap: 15px;">
                            <span class="setting-label"><?= $label_website_logo ?></span>
                            <div class="logo-preview-container"
                                style="display: flex; align-items: center; gap: 20px; width: 100%;">
                                <div id="branding-logo-preview"
                                    style="width: 100px; height: 100px; background: #f8fafc; border: 1px dashed var(--glass-border); border-radius: 12px; display: flex; align-items: center; justify-content: center; overflow: hidden; cursor: pointer;"
                                    onclick="document.getElementById('branding_logo_file').click()">
                                    <?php if (!empty($settings['site_logo'])): ?>
                                        <img src="<?= $settings['site_logo'] ?>"
                                            style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    <?php else: ?>
                                        <span style="font-size: 2rem;">🖼️</span>
                                    <?php endif; ?>
                                </div>
                                <div style="flex: 1; display: flex; flex-direction: column; gap: 8px;">
                                    <button type="button" class="btn-secondary"
                                        style="width: 100%;"
                                        onclick="document.getElementById('branding_logo_file').click()"><?= $btn_upload_logo ?></button>
                                    <button type="button" class="btn-secondary"
                                        style="width: 100%;"
                                        onclick="openMediaPicker()"><?= $btn_choose_from_media ?></button>
                                    <input type="file" id="branding_logo_file" style="display: none;" accept="image/*"
                                        onchange="uploadBrandingLogo(this)">
                                    <input type="hidden" name="site_logo" id="site_logo_path"
                                        value="<?= htmlspecialchars($settings['site_logo'] ?? '') ?>">
                                    <p style="font-size: 0.75rem; color: var(--text-muted);"><?= $tip_logo_upload ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="setting-row" style="align-items: center;">
                            <span class="setting-label"><?= $label_hide_logo ?></span>
                            <span class="setting-value">
                                <label class="switch"
                                    style="position: relative; display: inline-block; width: 50px; height: 24px;">
                                    <input type="checkbox" name="hide_logo" value="1" <?= ($settings['hide_logo'] ?? '0') === '1' ? 'checked' : '' ?> style="opacity: 0; width: 0; height: 0;">
                                    <span class="slider"
                                        style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: .4s; border-radius: 24px;"></span>
                                </label>
                            </span>
                        </div>
                    </div>

                    <!-- Database & System -->
                    <div class="settings-card">
                        <h3><span>⚙️</span> <?= $system_db_title ?></h3>
                        <div class="setting-row">
                            <span class="setting-label"><?= $label_db_host ?></span>
                            <span class="setting-value"
                                style="opacity: 0.7;"><?= htmlspecialchars($env['db_host'] ?? 'localhost') ?></span>
                        </div>
                        <div class="setting-row">
                            <span class="setting-label"><?= $label_db_name ?></span>
                            <span class="setting-value"
                                style="opacity: 0.7;"><?= htmlspecialchars($env['db_name'] ?? 'Niet ingesteld') ?></span>
                        </div>
                        <div class="setting-row">
                            <span class="setting-label"><?= $label_db_user ?></span>
                            <span class="setting-value">
                                <input type="text" name="db_user" class="setting-input"
                                    value="<?= htmlspecialchars($env['db_user'] ?? '') ?>" required>
                            </span>
                        </div>
                        <div class="setting-row">
                            <span class="setting-label"><?= $label_db_pass ?></span>
                            <span class="setting-value">
                                <input type="password" name="db_pass" class="setting-input"
                                    value=""
                                    placeholder="<?= $label_db_pass_placeholder ?>">
                            </span>
                        </div>
                        <div class="setting-row">
                            <span class="setting-label"><?= $label_db_prefix ?></span>
                            <span
                                class="setting-value"><code><?= htmlspecialchars($env['db_prefix'] ?? 'fcms_') ?></code></span>
                        </div>
                        <div class="setting-row">
                            <span class="setting-label"><?= $label_install_date ?></span>
                            <span class="setting-value"
                                style="opacity: 0.7;"><?= htmlspecialchars($settings['installed_at'] ?? 'Onbekend') ?></span>
                        </div>
                        <div class="setting-row">
                            <span class="setting-label"><?= $label_cms_version ?></span>
                            <span class="setting-value"><span
                                    class="badge badge-success">v<?= htmlspecialchars($env['version'] ?? '0.1.0') ?></span></span>
                        </div>
                    </div>
                </div>

                <div
                    style="margin-top: 30px; display: flex; gap: 20px; align-items: center; justify-content: flex-end;">
                    <button type="submit" class="btn-save"><?= $btn_save_apply ?></button>
                    <a href="/backoffice" class="btn-secondary"><?= $btn_cancel ?></a>
                </div>
            </form>
        </main>
    </div>

    <!-- Media Picker Modal -->
    <div id="mediaPickerModal" class="modal-backdrop">
        <div class="modal-card wide">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0;"><?= $title_media_picker ?></h2>
                <button type="button" onclick="closeMediaPicker()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            <div id="mediaPickerGrid" class="media-picker-grid">
                <!-- Images will be loaded here -->
                <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--text-muted);">
                    Laden van media...
                </div>
            </div>
            <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
                <button type="button" onclick="closeMediaPicker()" class="btn-secondary"><?= $btn_cancel ?></button>
            </div>
        </div>
    </div>

    <script>
        const userWidget = document.getElementById('user-widget');
        const userMenu = document.getElementById('user-menu');
        const langSwitcher = document.getElementById('lang-switcher');

        function handleFlagClick(event, lang) {
            const currentParams = new URLSearchParams(window.location.search);
            const currentLang = '<?= $_SESSION['lang'] ?? 'nl' ?>';

            if (currentLang === lang) {
                event.preventDefault();
                langSwitcher.classList.toggle('expanded');
                return;
            }
            // Allow default navigation to switch language
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

        async function uploadBrandingLogo(input) {
            const file = input.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);

            const preview = document.getElementById('branding-logo-preview');
            const pathInput = document.getElementById('site_logo_path');

            preview.innerHTML = '<span>⏳</span>';

            try {
                const response = await fetch('/backoffice/media/upload', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    preview.innerHTML = `<img src="${result.url}" style="max-width: 100%; max-height: 100%; object-fit: contain;">`;
                    pathInput.value = result.url;
                } else {
                    alert('Upload mislukt: ' + result.message);
                    preview.innerHTML = '<span style="font-size: 2rem;">🖼️</span>';
                }
            } catch (error) {
                console.error('Error uploading logo:', error);
                alert('Er is een fout opgetreden bij het uploaden.');
                preview.innerHTML = '<span style="font-size: 2rem;">🖼️</span>';
            }
        }

        async function openMediaPicker() {
            const modal = document.getElementById('mediaPickerModal');
            const grid = document.getElementById('mediaPickerGrid');
            modal.classList.add('active');

            try {
                const response = await fetch('/backoffice/media/list');
                const images = await response.json();

                if (images.length === 0) {
                    grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--text-muted);">Geen afbeeldingen gevonden in de mediabibliotheek.</div>';
                    return;
                }

                grid.innerHTML = images.map(img => `
                    <div class="media-picker-item" onclick="selectMediaItem('${img.url}')" title="${img.name}">
                        <img src="${img.url}" alt="${img.name}">
                    </div>
                `).join('');
            } catch (error) {
                console.error('Error fetching media:', error);
                grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #ef4444;">Fout bij het laden van media.</div>';
            }
        }

        function closeMediaPicker() {
            document.getElementById('mediaPickerModal').classList.remove('active');
        }

        function selectMediaItem(url) {
            const preview = document.getElementById('branding-logo-preview');
            const pathInput = document.getElementById('site_logo_path');

            preview.innerHTML = `<img src="${url}" style="max-width: 100%; max-height: 100%; object-fit: contain;">`;
            pathInput.value = url;
            closeMediaPicker();
        }

    </script>
</body>

</html>