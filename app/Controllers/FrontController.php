<?php

namespace Fritsion\Controllers;

use Fritsion\Database;

class FrontController extends BaseController
{
    public function index($uri = '/')
    {
        // 1. Fetch site settings
        $settings = [];
        $prefix = Database::getPrefix();
        $result = Database::query("SELECT setting_key, setting_value FROM {$prefix}settings");

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        }

        $siteStatus = $settings['site_status'] ?? 'inactive';
        $isAdmin = isset($_SESSION['user_id']);

        // Clear leading/trailing slashes for easier comparison, except for the root itself
        $slug = trim($uri, '/');
        if (empty($slug))
            $slug = '/';

        // 2. Fetch the page based on slug or homepage flag
        $page = null;
        $statusFilter = $isAdmin ? "('published', 'draft')" : "('published')";

        if ($slug !== '/') {
            // Try to find page by slug
            $stmt = Database::connect()->prepare("SELECT p.*, t.layout_json FROM {$prefix}pages p LEFT JOIN {$prefix}templates t ON p.template_id = t.id WHERE p.slug = ? AND p.status IN $statusFilter LIMIT 1");
            $stmt->bind_param("s", $slug);
            $stmt->execute();
            $page = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        }

        // If no page found by slug (or it was root), find the homepage
        if (!$page) {
            $pageRes = Database::query("SELECT p.*, t.layout_json FROM {$prefix}pages p LEFT JOIN {$prefix}templates t ON p.template_id = t.id WHERE p.is_homepage = 1 AND p.status IN $statusFilter LIMIT 1");
            if ($pageRes && $pageRes->num_rows > 0) {
                $page = $pageRes->fetch_assoc();
            }
        }

        // 3. Logic for what to show
        if (!$isAdmin && $siteStatus === 'inactive') {
            $this->view('front/maintenance');
            return;
        }

        if (!$page && !$isAdmin) {
            $this->view('front/maintenance', ['no_content' => true]);
            return;
        }

        $layoutJson = '';
        if ($page && !empty($page['layout_json'])) {
            $layoutJson = $page['layout_json'];
        } else {
            // Fallback: Fetch dynamic layout JSON from active homepage template
            $tplRes = Database::query("SELECT layout_json FROM {$prefix}templates WHERE type = 'homepage' AND is_active = 1 LIMIT 1");
            if ($tplRes && $tplRes->num_rows > 0) {
                $row = $tplRes->fetch_assoc();
                $layoutJson = $row['layout_json'];
            }
        }

        if (empty($layoutJson)) {
            $layoutJson = $settings['homepage_layout_json'] ?? '';
        }

        $homepageLayout = !empty($layoutJson) ? json_decode($layoutJson, true) : null;

        // Fetch all published pages for frontend navigation logic
        $allPages = [];
        $pagesRes = Database::query("SELECT id, title, slug, is_homepage FROM {$prefix}pages WHERE status = 'published' ORDER BY is_homepage DESC");
        if ($pagesRes) {
            while ($p = $pagesRes->fetch_assoc()) {
                $allPages[] = $p;
            }
        }

        $this->view('front/home', [
            'settings' => $settings,
            'homepageLayout' => $homepageLayout,
            'page' => $page,
            'allPages' => $allPages
        ]);
    }

}
