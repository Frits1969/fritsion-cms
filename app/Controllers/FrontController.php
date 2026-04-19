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

        // Auto-create fcms_themes if it does not exist
        $checkTable = Database::query("SHOW TABLES LIKE '{$prefix}themes'");
        if (!$checkTable || $checkTable->num_rows === 0) {
            $sql = "CREATE TABLE IF NOT EXISTS `{$prefix}themes` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(100) NOT NULL,
                `slug` VARCHAR(100) NOT NULL UNIQUE,
                `is_default` TINYINT(1) DEFAULT 0,
                `is_active` TINYINT(1) DEFAULT 0,
                `settings_json` LONGTEXT NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_is_active (`is_active`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            Database::query($sql);
            
            $seedSql = "REPLACE INTO `{$prefix}themes` (name, slug, is_default, is_active, settings_json) VALUES 
            ('Standaard', 'default', 1, 1, '{\"colors\":{\"primary\":\"#3B2A8C\",\"secondary\":\"#C41257\",\"accent\":\"#E8186A\",\"text\":\"#1A1336\",\"background\":\"#F6F5FF\",\"link\":\"#E8186A\"},\"typography\":{\"bodyFont\":\"\\\'Inter\\\', sans-serif\",\"headingFont\":\"\\\'Outfit\\\', sans-serif\",\"baseSize\":\"16px\"},\"spacing\":{\"sectionPadding\":\"4rem 2rem\"}}')";
            Database::query($seedSql);
        }

        // Fetch active theme settings
        $themeSettings = [];
        $themeRes = Database::query("SELECT settings_json FROM {$prefix}themes WHERE is_active = 1 LIMIT 1");
        if ($themeRes && $themeRes->num_rows > 0) {
            $row = $themeRes->fetch_assoc();
            if (!empty($row['settings_json'])) {
                $themeSettings = json_decode($row['settings_json'], true) ?? [];
            }
        } else {
            // Fallback to default
            $themeResDef = Database::query("SELECT settings_json FROM {$prefix}themes WHERE is_default = 1 LIMIT 1");
            if ($themeResDef && $themeResDef->num_rows > 0) {
                $row = $themeResDef->fetch_assoc();
                if (!empty($row['settings_json'])) {
                    $themeSettings = json_decode($row['settings_json'], true) ?? [];
                }
            }
        }

        $this->view('front/home', [
            'settings' => $settings,
            'homepageLayout' => $homepageLayout,
            'page' => $page,
            'allPages' => $allPages,
            'themeSettings' => $themeSettings
        ]);
    }

}
