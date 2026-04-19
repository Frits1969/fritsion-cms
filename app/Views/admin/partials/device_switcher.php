<?php
/**
 * Partial: Device Preview Switcher
 * ---------------------------------
 * Gedeeld component voor de device-preview knoppen.
 * Wordt geïncludeerd in zowel layout_configurator.php als pages_edit.php.
 *
 * De onclick-handler 'setDevicePreview(this, N)' wordt in elke parent-view
 * apart gedefinieerd — de layout en het uiterlijk zijn hier gecentraliseerd.
 *
 * Gebruik:
 *   <?php include __DIR__ . '/partials/device_switcher.php'; ?>
 *
 * Optionele variabele (uit parent scope):
 *   $activeDevice  — maxCols-waarde van de standaard actieve knop (default: 12)
 */
$activeDevice = $activeDevice ?? 12;
?>
<div class="device-switcher">

    <button type="button"
            class="btn-device <?= $activeDevice === 1 ? 'active' : '' ?>"
            onclick="setDevicePreview(this, 1)"
            title="Mobile (≤ 640px)">
        <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
            <rect x="20" y="12" width="24" height="40" rx="3" ry="3" fill="#FFFFFF"/>
            <rect x="30" y="14" width="4"  height="2"  rx="1" ry="1" fill="#D0C4B5"/>
        </svg>
        <span class="device-label">Mobile</span>
        <span class="device-width">640px</span>
    </button>

    <button type="button"
            class="btn-device <?= $activeDevice === 2 ? 'active' : '' ?>"
            onclick="setDevicePreview(this, 2)"
            title="Tablet (≥ 768px)">
        <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
            <rect x="18" y="16" width="28" height="32" rx="4" ry="4" fill="#FFFFFF"/>
            <circle cx="32" cy="45" r="1.5" fill="#D0C4B5"/>
        </svg>
        <span class="device-label">Tablet</span>
        <span class="device-width">768px</span>
    </button>

    <button type="button"
            class="btn-device <?= $activeDevice === 3 ? 'active' : '' ?>"
            onclick="setDevicePreview(this, 3)"
            title="Laptop S (≥ 1024px)">
        <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
            <rect x="16" y="18" width="32" height="24" rx="4" ry="4" fill="#FFFFFF"/>
            <rect x="16" y="44" width="32" height="4"  rx="2" ry="2" fill="#D0C4B5"/>
        </svg>
        <span class="device-label">Laptop S</span>
        <span class="device-width">1024px</span>
    </button>

    <button type="button"
            class="btn-device <?= $activeDevice === 4 ? 'active' : '' ?>"
            onclick="setDevicePreview(this, 4)"
            title="Laptop M (≥ 1280px)">
        <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
            <rect x="14" y="18" width="36" height="22" rx="3" ry="3" fill="#FFFFFF"/>
            <rect x="22" y="42" width="20" height="3"  rx="1.5" ry="1.5" fill="#D0C4B5"/>
            <rect x="26" y="45" width="12" height="3"  rx="1.5" ry="1.5" fill="#D0C4B5"/>
        </svg>
        <span class="device-label">Laptop M</span>
        <span class="device-width">1280px</span>
    </button>

    <button type="button"
            class="btn-device <?= $activeDevice === 6 ? 'active' : '' ?>"
            onclick="setDevicePreview(this, 6)"
            title="Desktop (≥ 1440px)">
        <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
            <rect x="13" y="18" width="38" height="23" rx="3" ry="3" fill="#FFFFFF"/>
            <rect x="24" y="43" width="16" height="3"  rx="1.5" ry="1.5" fill="#D0C4B5"/>
            <rect x="20" y="46" width="24" height="3"  rx="1.5" ry="1.5" fill="#D0C4B5"/>
        </svg>
        <span class="device-label">Desktop</span>
        <span class="device-width">1440px</span>
    </button>

    <button type="button"
            class="btn-device <?= $activeDevice === 12 ? 'active' : '' ?>"
            onclick="setDevicePreview(this, 12)"
            title="Retina / Volledig (≥ 1536px)">
        <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
            <rect x="12" y="18" width="40" height="24" rx="3" ry="3" fill="#FFFFFF"/>
            <rect x="26" y="44" width="12" height="3"  rx="1.5" ry="1.5" fill="#D0C4B5"/>
            <rect x="22" y="47" width="20" height="3"  rx="1.5" ry="1.5" fill="#D0C4B5"/>
        </svg>
        <span class="device-label">Retina</span>
        <span class="device-width">100%</span>
    </button>

</div>
