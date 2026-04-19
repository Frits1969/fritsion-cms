<?php
$lang = $GLOBALS['lang'] ?? [];
extract($lang);

$backoffice_title = $backoffice_title ?? 'Backoffice';
$themes_title = $themes_title ?? "Thema's";
$nav_back_to_dashboard = $nav_back_to_dashboard ?? 'Terug naar Dashboard';
$btn_save = $btn_save ?? 'Opslaan';
$btn_cancel = $btn_cancel ?? 'Annuleren';

$settings = [];
if (!empty($theme['settings_json'])) {
    $settings = json_decode($theme['settings_json'], true) ?? [];
}

// Defaults
$c_primary = $settings['colors']['primary'] ?? '#007bff';
$c_secondary = $settings['colors']['secondary'] ?? '#6c757d';
$c_accent = $settings['colors']['accent'] ?? '#ffc107';
$c_text = $settings['colors']['text'] ?? '#212529';
$c_bg = $settings['colors']['background'] ?? '#ffffff';
$c_bg_url = $settings['background_url'] ?? '';
$c_link = $settings['colors']['link'] ?? '#0d6efd';

$f_body = $settings['typography']['bodyFont'] ?? 'System UI, sans-serif';
$f_heading = $settings['typography']['headingFont'] ?? 'System UI, sans-serif';
$f_base = $settings['typography']['baseSize'] ?? '16px';

$s_pad = $settings['spacing']['sectionPadding'] ?? '4rem';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'nl' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ($mode === 'edit' ? 'Bewerk' : 'Voeg toe') ?> Thema | Fritsion CMS</title>
    <link rel="stylesheet" href="/assets/css/admin_shared.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .theme-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .color-preview { width: 30px; height: 30px; border-radius: 5px; border: 1px solid #ccc; display: inline-block; vertical-align: middle; margin-right: 10px; cursor: pointer; }
        .color-group { display: flex; align-items: center; margin-bottom: 15px; }
        .form-section { background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); }
        .form-section h3 { margin-top: 0; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <div class="main-wrapper">
        <header class="topbar">
            <div style="font-weight: 600; color: var(--text-muted);"><?= $backoffice_title ?> / <?= $themes_title ?> / <?= ($mode === 'edit' ? 'Bewerken' : 'Toevoegen') ?></div>
            <div class="topbar-actions">
                <a href="/backoffice/themes" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;">Terug</a>
            </div>
        </header>

        <main class="content">
            <?php if (isset($error) && $error): ?>
                <div class="alert alert-error"><span>⚠️</span> <?= $error ?></div>
            <?php endif; ?>

            <div class="header-section">
                <h1><?= ($mode === 'edit' ? 'Thema Bewerken' : 'Nieuw Thema') ?></h1>
            </div>

            <form method="POST" id="themeForm">
                <div class="form-section">
                    <h3>Algemene Informatie</h3>
                    <div class="form-group">
                        <label>Themanaam</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($theme['name'] ?? '') ?>" required>
                    </div>
                    <?php if ($mode === 'edit' && ($theme['is_default'] ?? 0) == 1): ?>
                        <div style="color: var(--text-muted); font-size: 0.85rem; margin-top: 5px;">Dit is het standaard thema.</div>
                    <?php endif; ?>
                </div>

                <div class="theme-form-grid">
                    <div class="form-section">
                        <h3>Kleurenpalet</h3>
                        <div class="color-group">
                            <input type="color" id="cp_primary" value="<?= $c_primary ?>" oninput="updateJson()" style="display:none;">
                            <label class="color-preview" id="prev_primary" style="background: <?= $c_primary ?>" onclick="document.getElementById('cp_primary').click()"></label>
                            Primaire kleur
                        </div>
                        <div class="color-group">
                            <input type="color" id="cp_secondary" value="<?= $c_secondary ?>" oninput="updateJson()" style="display:none;">
                            <label class="color-preview" id="prev_secondary" style="background: <?= $c_secondary ?>" onclick="document.getElementById('cp_secondary').click()"></label>
                            Secundaire kleur
                        </div>
                        <div class="color-group">
                            <input type="color" id="cp_accent" value="<?= $c_accent ?>" oninput="updateJson()" style="display:none;">
                            <label class="color-preview" id="prev_accent" style="background: <?= $c_accent ?>" onclick="document.getElementById('cp_accent').click()"></label>
                            Accent kleur
                        </div>
                        <div class="color-group">
                            <input type="color" id="cp_text" value="<?= $c_text ?>" oninput="updateJson()" style="display:none;">
                            <label class="color-preview" id="prev_text" style="background: <?= $c_text ?>" onclick="document.getElementById('cp_text').click()"></label>
                            Tekst kleur
                        </div>
                        <div class="color-group">
                            <input type="color" id="cp_bg" value="<?= $c_bg ?>" oninput="updateJson()" style="display:none;">
                            <label class="color-preview" id="prev_bg" style="background: <?= $c_bg ?>" onclick="document.getElementById('cp_bg').click()"></label>
                            Achtergrond kleur
                        </div>
                        <div class="color-group">
                            <input type="color" id="cp_link" value="<?= $c_link ?>" oninput="updateJson()" style="display:none;">
                            <label class="color-preview" id="prev_link" style="background: <?= $c_link ?>" onclick="document.getElementById('cp_link').click()"></label>
                            Link kleur
                        </div>

                        <label>Achtergrond Afbeelding URL (Optioneel)</label>
                        <input type="text" id="bg_url" class="form-control" placeholder="/uploads/mijn-achtergrond.jpg" value="<?= htmlspecialchars($c_bg_url) ?>" oninput="updateJson()">
                    </div>

                    <div class="form-section">
                        <h3>Typografie & Spacing</h3>
                        <div class="form-group">
                            <label>Basis Lettertype (Body Font Family)</label>
                            <input type="text" id="f_body" class="form-control" value="<?= htmlspecialchars($f_body) ?>" oninput="updateJson()" placeholder="Inter, sans-serif">
                        </div>
                        <div class="form-group">
                            <label>Koppen Lettertype (Heading Font Family)</label>
                            <input type="text" id="f_heading" class="form-control" value="<?= htmlspecialchars($f_heading) ?>" oninput="updateJson()" placeholder="Roboto, sans-serif">
                        </div>
                        <div class="form-group">
                            <label>Basis Lettergrootte</label>
                            <input type="text" id="f_base" class="form-control" value="<?= htmlspecialchars($f_base) ?>" oninput="updateJson()" placeholder="16px">
                        </div>
                        
                        <div class="form-group" style="margin-top: 30px;">
                            <h3 style="margin-bottom: 10px; font-size: 1.1rem; border: none; padding: 0;">Spacing</h3>
                            <label>Sectie Padding (e.g., 4rem 2rem)</label>
                            <input type="text" id="s_pad" class="form-control" value="<?= htmlspecialchars($s_pad) ?>" oninput="updateJson()" placeholder="4rem">
                        </div>
                    </div>
                </div>

                <input type="hidden" name="settings_json" id="settings_json" value="<?= htmlspecialchars(json_encode($settings)) ?>">

                <div class="form-card-footer" style="padding: 20px; background: white; border-radius: 12px; display: flex; gap: 10px;">
                    <button type="submit" class="btn-save btn-primary"><?= $btn_save ?></button>
                    <a href="/backoffice/themes" class="btn-secondary"><?= $btn_cancel ?></a>
                </div>
            </form>
        </main>
    </div>

    <script>
        function updateJson() {
            const data = {
                colors: {
                    primary: document.getElementById('cp_primary').value,
                    secondary: document.getElementById('cp_secondary').value,
                    accent: document.getElementById('cp_accent').value,
                    text: document.getElementById('cp_text').value,
                    background: document.getElementById('cp_bg').value,
                    link: document.getElementById('cp_link').value
                },
                background_url: document.getElementById('bg_url').value,
                typography: {
                    bodyFont: document.getElementById('f_body').value,
                    headingFont: document.getElementById('f_heading').value,
                    baseSize: document.getElementById('f_base').value
                },
                spacing: {
                    sectionPadding: document.getElementById('s_pad').value
                }
            };
            
            // Update previews immediately
            document.getElementById('prev_primary').style.backgroundColor = data.colors.primary;
            document.getElementById('prev_secondary').style.backgroundColor = data.colors.secondary;
            document.getElementById('prev_accent').style.backgroundColor = data.colors.accent;
            document.getElementById('prev_text').style.backgroundColor = data.colors.text;
            document.getElementById('prev_bg').style.backgroundColor = data.colors.background;
            document.getElementById('prev_link').style.backgroundColor = data.colors.link;

            document.getElementById('settings_json').value = JSON.stringify(data);
        }

        // Initialize state to ensure inputs perfectly match json
        updateJson();
    </script>
</body>
</html>
