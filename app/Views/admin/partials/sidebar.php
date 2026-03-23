<?php
$uri = strtok($_SERVER['REQUEST_URI'] ?? '/backoffice', '?');
// Variables $nav_dashboard etc. are already extracted by BaseController::view
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <img src="/assets/logo/logo_fritsion_cms.png" alt="Logo">
    </div>

    <nav class="sidebar-nav">
        <a href="/backoffice" class="nav-item <?= $uri === '/backoffice' ? 'active' : '' ?>">
            <span><?= $nav_dashboard ?></span>
        </a>
        <a href="/backoffice/pages" class="nav-item <?= strpos($uri, '/backoffice/pages') === 0 ? 'active' : '' ?>">
            <span><?= $nav_pages ?></span>
        </a>
        <a href="/backoffice/media" class="nav-item <?= $uri === '/backoffice/media' ? 'active' : '' ?>">
            <span><?= $nav_media ?></span>
        </a>

        <a href="/backoffice/templates"
            class="nav-item <?= strpos($uri, '/backoffice/templates') === 0 ? 'active' : '' ?>">
            <span><?= $nav_templates ?></span>
        </a>
<?php /* Submenu removed to simplify as per user request */ ?>

        <a href="/backoffice/themes" class="nav-item <?= $uri === '/backoffice/themes' ? 'active' : '' ?>">
            <span><?= $nav_themes ?></span>
        </a>
        <a href="/backoffice/settings" class="nav-item <?= $uri === '/backoffice/settings' ? 'active' : '' ?>">
            <span><?= $nav_settings ?></span>
        </a>
        <a href="/" target="_blank" class="nav-item">
            <span><?= $nav_visit_site ?></span>
        </a>
    </nav>
    <div class="sidebar-footer">
        <p style="font-size: 0.8rem; color: var(--text-muted);">v<?= \Fritsion\App::VERSION ?></p>
    </div>
</aside>
