<?php
$layout = $homepageLayout;
$selectedLang = $_SESSION['lang'] ?? 'nl';
$pageDataRaw = json_decode($page['content'] ?? '{}', true);
$pageData = $pageDataRaw[$selectedLang] ?? $pageDataRaw['nl'] ?? $pageDataRaw['en'] ?? $pageDataRaw;

function getDeepValue($obj, $path)
{
    if (!$path)
        return null;
    $parts = explode('.', $path);
    foreach ($parts as $part) {
        if (isset($obj[$part])) {
            $obj = $obj[$part];
        } else {
            return null;
        }
    }
    return $obj;
}

function renderBlock($type, $path, $pageData, $settings)
{
    global $lang;
    $data = getDeepValue($pageData, $path) ?: [];

    switch ($type) {
        case 'text':
            $title = $data['title'] ?? '';
            $text = $data['text'] ?? '';
            return "<div>" . ($title ? "<h1>" . htmlspecialchars($title) . "</h1>" : "") . "<div>" . $text . "</div></div>";

        case 'image':
            $url = $data['url'] ?? '';
            $alt = $data['alt'] ?? '';
            return $url ? '<img src="' . htmlspecialchars($url) . '" alt="' . htmlspecialchars($alt) . '">' : '<div class="placeholder-image">' . ($lang['msg_image_not_uploaded'] ?? 'Afbeelding niet geüpload') . '</div>';

        case 'cta':
            $title = $data['title'] ?? ($lang['msg_cta_title'] ?? 'Klaar om te starten?');
            $btnText = $data['button_text'] ?? ($lang['btn_register'] ?? 'Registeer nu');
            $url = $data['url'] ?? '#';
            return '<div><h3>' . htmlspecialchars($title) . '</h3><a href="' . htmlspecialchars($url) . '" class="type-cta">' . htmlspecialchars($btnText) . '</a></div>';

        case 'logo':
            if (($settings['hide_logo'] ?? '0') === '1') {
                return '';
            }
            $url = $settings['site_logo'] ?? '/assets/logo/logo_fritsion_cms.png';
            if (empty($url))
                $url = '/assets/logo/logo_fritsion_cms.png';
            return '<img src="' . htmlspecialchars($url) . '" alt="Logo" class="logo">';

        case 'menu':
            $items = explode(',', $data['items'] ?? 'Home, Over ons, Contact');
            $html = '<nav style="display:flex; gap:20px;">';
            foreach ($items as $item) {
                $html .= '<a href="#" style="text-decoration:none; color:inherit; font-weight:600;">' . htmlspecialchars(trim($item)) . '</a>';
            }
            return $html . '</nav>';

        case 'usps':
            $usps = [$data['usp_1'] ?? ($lang['usp_1_default'] ?? 'Snelheid'), $data['usp_2'] ?? ($lang['usp_2_default'] ?? 'Veiligheid'), $data['usp_3'] ?? ($lang['usp_3_default'] ?? 'Kwaliteit')];
            $html = '<div class="type-usp-grid">';
            foreach ($usps as $usp) {
                $html .= '<div class="usp-card">' . htmlspecialchars($usp) . '</div>';
            }
            return $html . '</div>';

        case 'socials':
            $fb = $data['facebook'] ?? '';
            $ig = $data['instagram'] ?? '';
            $html = '<div style="display:flex; gap:15px;">';
            if ($fb)
                $html .= '<a href="' . htmlspecialchars($fb) . '" style="text-decoration:none;">FB</a>';
            if ($ig)
                $html .= '<a href="' . htmlspecialchars($ig) . '" style="text-decoration:none;">IG</a>';
            return $html . '</div>';

        case 'video':
            $url = $data['url'] ?? '';
            return '<div style="background:linear-gradient(135deg, #1A1336 0%, #3B2A8C 100%); padding:80px; text-align:center; color:white; border-radius:24px; font-weight:700;">▶ Video Placeholder' . ($url ? '<br><small style="font-weight:400; opacity:0.7;">' . htmlspecialchars($url) . '</small>' : '') . '</div>';

        case 'html':
            return $data['code'] ?? '';

        case 'map':
            $addr = $data['address'] ?? 'Locatie';
            return '<div style="background:#e0f2fe; height:300px; display:flex; align-items:center; justify-content:center; border-radius:24px; color:#0369a1; font-weight:600;">📍 Kaart: ' . htmlspecialchars($addr) . '</div>';

        default:
            return ($lang['label_block'] ?? "Block") . ": $type";
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'nl' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page['title'] ?? $settings['site_name'] ?? 'Fritsion Website') ?></title>
    <link rel="icon" type="image/png" href="/assets/logo/logo_fritsion_cms_favicon.png">
    <link rel="shortcut icon" href="/assets/logo/logo_fritsion_cms_favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Outfit:wght@700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary: #3B2A8C;
            --accent: #E8186A;
            --text: #1A1336;
            --muted: #64748b;
            --bg: #f8fafc;
            --accent-gradient: linear-gradient(135deg, #E8186A 0%, #C41257 40%, #F0961B 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-inner {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 40px;
        }

        .h-section {
            display: flex;
            align-items: center;
            gap: 20px;
            flex: 1;
        }

        .logo {
            height: 40px;
        }

        main {
            padding: 60px 0;
        }

        .row {
            display: grid;
            gap: 40px;
            margin-bottom: 80px;
            align-items: center;
        }

        .col {
            min-height: 100px;
        }

        .block-text h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 3.5rem;
            line-height: 1.1;
            margin-bottom: 20px;
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .block-text h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--primary);
        }

        .block-text p {
            font-size: 1.25rem;
            color: var(--muted);
        }

        .col img {
            max-width: 100%;
            height: auto;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            display: block;
        }

        .col img:not(.logo) {
            width: 100%;
        }

        .logo {
            height: 40px;
            width: auto;
            object-fit: contain;
        }

        .placeholder-image {
            background: #f1f5f9;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 24px;
            color: #cbd5e1;
            font-weight: 600;
        }

        .type-cta {
            background: var(--accent-gradient);
            color: white !important;
            padding: 15px 35px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            font-weight: 700;
            box-shadow: 0 10px 20px rgba(232, 24, 106, 0.2);
            transition: transform 0.3s;
        }

        .type-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(232, 24, 106, 0.3);
        }

        .block-cta h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.8rem;
            margin-bottom: 20px;
        }

        .type-usp-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .usp-card {
            background: #fff;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.02);
            font-weight: 600;
            text-align: center;
        }

        footer {
            background: #1A1336;
            color: white;
            padding: 80px 0;
            margin-top: 80px;
        }

        .footer-inner {
            display: grid;
            gap: 40px;
        }

        @media (max-width: 768px) {
            .row {
                grid-template-columns: 1fr !important;
            }

            .block-text h1 {
                font-size: 2.5rem;
            }

            .header-inner {
                flex-direction: column;
                height: auto;
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <?php if ($layout): ?>
        <header>
            <div class="container header-inner">
                <?php foreach ($layout['header']['sections'] ?? [] as $i => $sec): ?>
                    <div class="h-section"
                        style="justify-content: <?= $i === 0 ? 'flex-start' : ($i === 1 ? 'center' : 'flex-end') ?>;">
                        <?= renderBlock($sec['type'], "header.sections.$i", $pageData, $settings) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </header>

        <main class="container">
            <?php foreach ($layout['main']['rows'] ?? [] as $ri => $row): ?>
                <div class="row" style="grid-template-columns: repeat(<?= count($row['columns'] ?? []) ?>, 1fr);">
                    <?php foreach ($row['columns'] ?? [] as $ci => $col): ?>
                        <div class="col block-<?= $col['type'] ?>">
                            <?= renderBlock($col['type'], "main.rows.$ri.columns.$ci", $pageData, $settings) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </main>

        <footer>
            <div class="container footer-inner"
                style="grid-template-columns: repeat(<?= count($layout['footer']['sections'] ?? []) ?>, 1fr);">
                <?php foreach ($layout['footer']['sections'] ?? [] as $i => $sec): ?>
                    <div class="f-section">
                        <?= renderBlock($sec['type'], "footer.sections.$i", $pageData, $settings) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </footer>
    <?php else: ?>
        <div class="container" style="padding: 100px; text-align: center;">
            <h1><?= $lang['error_no_layout'] ?? 'Geen layout geconfigureerd.' ?></h1>
            <p><?= $lang['msg_go_to_backoffice'] ?? 'Ga naar de backoffice om uw homepage in te richten.' ?></p>
        </div>
    <?php endif; ?>

</body>

</html>