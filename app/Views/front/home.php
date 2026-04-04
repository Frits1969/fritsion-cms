<?php
$layout = $homepageLayout;
$selectedLang = $_SESSION['lang'] ?? 'nl';
$pageDataRaw = json_decode($page['content'] ?? '{}', true);
$pageData = $pageDataRaw[$selectedLang] ?? $pageDataRaw['nl'] ?? $pageDataRaw['en'] ?? $pageDataRaw;

function getDeepValue($obj, $path)
{
    if (!$path) return null;
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
            $text  = $data['text']  ?? '';
            return "<div>" . ($title ? "<h1>" . htmlspecialchars($title) . "</h1>" : "") . "<div>" . $text . "</div></div>";

        case 'image':
            $url = $data['url'] ?? '';
            $alt = $data['alt'] ?? '';
            return $url
                ? '<img src="' . htmlspecialchars($url) . '" alt="' . htmlspecialchars($alt) . '">'
                : '<div class="placeholder-image">' . ($lang['msg_image_not_uploaded'] ?? 'Afbeelding niet geüpload') . '</div>';

        case 'cta':
            $title   = $data['title']       ?? ($lang['msg_cta_title'] ?? 'Klaar om te starten?');
            $btnText = $data['button_text'] ?? ($lang['btn_register']  ?? 'Registeer nu');
            $url     = $data['url']    ?? '#';
            $target  = $data['target'] ?? '_self';
            return '<div><h3>' . htmlspecialchars($title) . '</h3><a href="' . htmlspecialchars($url) . '" target="' . htmlspecialchars($target) . '" class="type-cta">' . htmlspecialchars($btnText) . '</a></div>';

        case 'logo':
            if (($settings['hide_logo'] ?? '0') === '1') return '';
            $url = $settings['site_logo'] ?? '/assets/logo/logo_fritsion_cms.png';
            if (empty($url)) $url = '/assets/logo/logo_fritsion_cms.png';
            return '<img src="' . htmlspecialchars($url) . '" alt="Logo" class="logo">';

        case 'menu':
            $items = explode(',', $data['items'] ?? 'Home, Over ons, Contact');
            $html  = '<nav style="display:flex; gap:20px;">';
            foreach ($items as $item) {
                $html .= '<a href="#" style="text-decoration:none; color:inherit; font-weight:600;">' . htmlspecialchars(trim($item)) . '</a>';
            }
            return $html . '</nav>';

        case 'language':
            $currentLang = $_SESSION['lang'] ?? 'nl';
            $html  = '<div class="lang-select-front" style="display:flex; gap:10px; align-items:center;">';
            $html .= '<a href="?lang=nl" style="opacity:' . ($currentLang === 'nl' ? '1' : '0.5') . '; transition:opacity 0.2s;"><img src="/assets/flags/nl.svg" alt="NL" style="width:24px; height:auto; display:block; border-radius:3px;"></a>';
            $html .= '<a href="?lang=en" style="opacity:' . ($currentLang === 'en' ? '1' : '0.5') . '; transition:opacity 0.2s;"><img src="/assets/flags/en.svg" alt="EN" style="width:24px; height:auto; display:block; border-radius:3px;"></a>';
            $html .= '</div>';
            return $html;

        case 'usps':
            $usps = [
                $data['usp_1'] ?? ($lang['usp_1_default'] ?? 'Snelheid'),
                $data['usp_2'] ?? ($lang['usp_2_default'] ?? 'Veiligheid'),
                $data['usp_3'] ?? ($lang['usp_3_default'] ?? 'Kwaliteit'),
            ];
            $html = '<div class="type-usp-grid">';
            foreach ($usps as $usp) {
                $html .= '<div class="usp-card">' . htmlspecialchars($usp) . '</div>';
            }
            return $html . '</div>';

        case 'socials':
            $fb   = $data['facebook']  ?? '';
            $ig   = $data['instagram'] ?? '';
            $html = '<div style="display:flex; gap:15px;">';
            if ($fb) $html .= '<a href="' . htmlspecialchars($fb) . '" style="text-decoration:none;">FB</a>';
            if ($ig) $html .= '<a href="' . htmlspecialchars($ig) . '" style="text-decoration:none;">IG</a>';
            return $html . '</div>';

        case 'video':
            $url = $data['url'] ?? '';
            return '<div style="background:linear-gradient(135deg,#1A1336,#3B2A8C);padding:80px;text-align:center;color:white;border-radius:24px;font-weight:700;">▶ Video Placeholder'
                 . ($url ? '<br><small style="font-weight:400;opacity:0.7;">' . htmlspecialchars($url) . '</small>' : '') . '</div>';

        case 'html':
            return $data['code'] ?? '';

        case 'map':
            $addr = $data['address'] ?? 'Locatie';
            return '<div style="background:#e0f2fe;height:300px;display:flex;align-items:center;justify-content:center;border-radius:24px;color:#0369a1;font-weight:600;">📍 Kaart: ' . htmlspecialchars($addr) . '</div>';

        case 'empty':
            return '';

        default:
            return ($lang['label_block'] ?? 'Block') . ": $type";
    }
}

/**
 * Device breakpoints — identical to those in the JS configurator.
 *   key   = maxCols value stored in deviceWidths
 *   value = [CSS media condition string,  total grid columns for that device]
 */
$deviceBreakpoints = [
    12 => ['min-width: 1536px',                            12],
    4  => ['max-width: 1535px) and (min-width: 1280px',    4],
    3  => ['max-width: 1279px) and (min-width: 1024px',    3],
    2  => ['max-width: 1023px) and (min-width: 481px',     2],
    1  => ['max-width: 480px',                              1],
];

/**
 * Mirror of JS calcDeviceSpan(): compute visual span for one item at a breakpoint.
 */
function calcItemSpan(array $item, int $deviceMaxCols): int
{
    $dw = $item['deviceWidths'] ?? [];
    // Explicit per-device override (0 = hidden)
    if (array_key_exists($deviceMaxCols, $dw)) {
        return (int) $dw[$deviceMaxCols];
    }
    $width12 = (int) ($item['width'] ?? 12);
    if ($width12 === 0)          return 0;             // hidden on base → hidden everywhere
    if ($deviceMaxCols === 1)    return 1;             // smartphone: always 1
    if ($width12 >= 12)          return $deviceMaxCols; // full width → full width
    $span = (int) round(($width12 / 12) * $deviceMaxCols);
    return max(1, min($deviceMaxCols, $span));
}

/**
 * Generate @media rules for all breakpoints and return the CSS class name.
 */
function makeResponsiveClass(array $item, int $rowSpan, string $prefix, array $deviceBreakpoints, array &$cssRules): string
{
    static $counter = 0;
    $cls = 'rc-' . $prefix . '-' . (++$counter);

    foreach ($deviceBreakpoints as $maxCols => [$media, $gridCols]) {
        $span = calcItemSpan($item, $maxCols);
        if ($span === 0) {
            // display:none haalt het item volledig uit de CSS Grid-flow.
            $cssRules[] = "@media ($media) { .$cls { display: none !important; } }";
        } else {
            $safeSpan  = min($span, $gridCols);
            // Geen display override — laat het grid-item zijn standaard display behouden.
            $cssRules[] = "@media ($media) { .$cls { grid-column: span $safeSpan; grid-row: span $rowSpan; } }";
        }
    }
    return $cls;
}

// ── Generate all classes ──────────────────────────────────────────────────────
$cssRules = [];

$headerClasses = [];
foreach ($layout['header']['sections'] ?? [] as $i => $sec) {
    $headerClasses[$i] = makeResponsiveClass($sec, (int)($sec['rowSpan'] ?? 1), 'h', $deviceBreakpoints, $cssRules);
}

$mainClasses = [];
foreach ($layout['main']['rows'] ?? [] as $ri => $row) {
    foreach ($row['columns'] ?? [] as $ci => $col) {
        $mainClasses[$ri][$ci] = makeResponsiveClass($col, (int)($col['rowSpan'] ?? 1), 'm', $deviceBreakpoints, $cssRules);
    }
}

$footerClasses = [];
foreach ($layout['footer']['sections'] ?? [] as $i => $sec) {
    $footerClasses[$i] = makeResponsiveClass($sec, (int)($sec['rowSpan'] ?? 1), 'f', $deviceBreakpoints, $cssRules);
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Outfit:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/grid.css">
    <style>
        :root {
            --primary: #3B2A8C;
            --accent:  #E8186A;
            --text:    #1A1336;
            --muted:   #64748b;
            --bg:      #f8fafc;
            --accent-gradient: linear-gradient(135deg, #E8186A 0%, #C41257 40%, #F0961B 100%);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); line-height: 1.6; }

        .container { max-width: 1200px; margin: 0 auto; }
        @media (max-width: 1240px) { .container { padding: 0 16px; } }

        /* ── Header ──────────────────────────────────────────── */
        header { background: #fff; border-bottom: 1px solid #e2e8f0; }
        .header-inner { display: grid; gap: 20px 40px; align-items: center; }
        @media (min-width: 1536px)                         { .header-inner { grid-template-columns: repeat(12, 1fr); } }
        @media (max-width: 1535px) and (min-width: 1280px) { .header-inner { grid-template-columns: repeat(4,  1fr); } }
        @media (max-width: 1279px) and (min-width: 1024px) { .header-inner { grid-template-columns: repeat(3,  1fr); } }
        @media (max-width: 1023px) and (min-width: 481px)  { .header-inner { grid-template-columns: repeat(2,  1fr); } }
        @media (max-width: 480px)                          { .header-inner { grid-template-columns: repeat(1,  1fr); } }
        .h-section { display: flex; align-items: center; gap: 20px; }

        /* ── Main ────────────────────────────────────────────── */
        main.grid-main { display: grid; gap: 80px 40px; margin-bottom: 80px; padding: 60px 0; }
        @media (min-width: 1536px)                         { main.grid-main { grid-template-columns: repeat(12, 1fr); } }
        @media (max-width: 1535px) and (min-width: 1280px) { main.grid-main { grid-template-columns: repeat(4,  1fr); } }
        @media (max-width: 1279px) and (min-width: 1024px) { main.grid-main { grid-template-columns: repeat(3,  1fr); } }
        @media (max-width: 1023px) and (min-width: 481px)  { main.grid-main { grid-template-columns: repeat(2,  1fr); } }
        @media (max-width: 480px)                          { main.grid-main { grid-template-columns: repeat(1,  1fr); } }

        /* ── Footer ──────────────────────────────────────────── */
        footer { background: #1A1336; color: white; padding: 80px 0; margin-top: 80px; }
        .footer-inner { display: grid; gap: 20px 40px; }
        @media (min-width: 1536px)                         { .footer-inner { grid-template-columns: repeat(12, 1fr); } }
        @media (max-width: 1535px) and (min-width: 1280px) { .footer-inner { grid-template-columns: repeat(4,  1fr); } }
        @media (max-width: 1279px) and (min-width: 1024px) { .footer-inner { grid-template-columns: repeat(3,  1fr); } }
        @media (max-width: 1023px) and (min-width: 481px)  { .footer-inner { grid-template-columns: repeat(2,  1fr); } }
        @media (max-width: 480px)                          { .footer-inner { grid-template-columns: repeat(1,  1fr); } }

        /* ── Shared ──────────────────────────────────────────── */
        .logo { height: 40px; width: auto; object-fit: contain; }
        .col  { min-height: 100px; }
        .col img { max-width: 100%; height: auto; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,.05); display: block; }
        .col img:not(.logo) { width: 100%; }
        .placeholder-image { background:#f1f5f9; height:300px; display:flex; align-items:center; justify-content:center; border-radius:24px; color:#cbd5e1; font-weight:600; }
        .type-cta { background:var(--accent-gradient); color:white!important; padding:15px 35px; border-radius:50px; text-decoration:none; display:inline-block; font-weight:700; box-shadow:0 10px 20px rgba(232,24,106,.2); transition:transform .3s; }
        .type-cta:hover { transform:translateY(-3px); box-shadow:0 15px 30px rgba(232,24,106,.3); }
        .block-cta h3 { font-family:'Outfit',sans-serif; font-size:1.8rem; margin-bottom:20px; }
        .block-text h1 { font-family:'Outfit',sans-serif; font-size:3.5rem; line-height:1.1; margin-bottom:20px; background:var(--accent-gradient); -webkit-background-clip:text; background-clip:text; -webkit-text-fill-color:transparent; }
        .block-text h2 { font-family:'Outfit',sans-serif; font-size:2.5rem; margin-bottom:15px; color:var(--primary); }
        .block-text p  { font-size:1.25rem; color:var(--muted); }
        .type-usp-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:20px; }
        .usp-card { background:#fff; padding:30px; border-radius:20px; box-shadow:0 10px 20px rgba(0,0,0,.02); font-weight:600; text-align:center; }

        /* ── Per-item responsive classes (auto-generated) ────── */
        <?= implode("\n        ", $cssRules) ?>
    </style>
</head>

<body>

<?php if ($layout): ?>

    <header>
        <?php $headerHeight = $layout['header']['height'] ?? '90px'; ?>
        <div class="container header-inner" style="grid-auto-rows:min-content; min-height:<?= $headerHeight ?>; align-items:center;">
            <?php foreach ($layout['header']['sections'] ?? [] as $i => $sec): ?>
                <div class="h-section <?= $headerClasses[$i] ?>"
                    style="display:flex; align-items:center; justify-content:<?= ($sec['width'] ?? 12) >= 12 ? 'center' : ($i % 3 === 0 ? 'flex-start' : ($i % 3 === 1 ? 'center' : 'flex-end')) ?>;">
                    <?= renderBlock($sec['type'], "header.sections.$i", $pageData, $settings) ?>
                </div>
            <?php endforeach; ?>
        </div>
    </header>

    <main class="container grid-main" style="grid-auto-rows:min-content;">
        <?php foreach ($layout['main']['rows'] ?? [] as $ri => $row): ?>
            <?php foreach ($row['columns'] ?? [] as $ci => $col): ?>
                <div class="col block-<?= $col['type'] ?> <?= $mainClasses[$ri][$ci] ?>">
                    <?= renderBlock($col['type'], "main.rows.$ri.columns.$ci", $pageData, $settings) ?>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </main>

    <footer>
        <?php $footerHeight = $layout['footer']['height'] ?? '120px'; ?>
        <div class="container footer-inner" style="grid-auto-rows:min-content; min-height:<?= $footerHeight ?>;">
            <?php foreach ($layout['footer']['sections'] ?? [] as $i => $sec): ?>
                <div class="f-section <?= $footerClasses[$i] ?>" style="display:flex; flex-direction:column; justify-content:flex-start;">
                    <?= renderBlock($sec['type'], "footer.sections.$i", $pageData, $settings) ?>
                </div>
            <?php endforeach; ?>
        </div>
    </footer>

<?php else: ?>
    <div class="container" style="padding:100px; text-align:center;">
        <h1><?= $lang['error_no_layout'] ?? 'Geen layout geconfigureerd.' ?></h1>
        <p><?= $lang['msg_go_to_backoffice'] ?? 'Ga naar de backoffice om uw homepage in te richten.' ?></p>
    </div>
<?php endif; ?>

</body>
</html>