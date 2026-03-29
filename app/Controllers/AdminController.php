<?php

namespace Fritsion\Controllers;

use Fritsion\Controllers\BaseController;
use Fritsion\Database;
use Fritsion\Config;
use Fritsion\App;

class AdminController extends BaseController
{
    public function index()
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/backoffice';

        // Handle sub-routes manually if needed inside index, 
        // but App.php now routes most specific /backoffice/ paths

        if (strpos($uri, '/backoffice/logout') === 0) {
            $this->logout();
            return;
        }

        // Simple authentication check
        if (!isset($_SESSION['user_id'])) {
            header('Location: /backoffice/login');
            exit;
        }

        $db = Database::connect();
        $prefix = Database::getPrefix();

        // Fetch stats
        $pageCount = 0;
        $latestPages = [];

        // Check if pages table exists first to avoid crashes
        $tableExists = false;
        $checkTable = $db->query("SHOW TABLES LIKE '{$prefix}pages'");
        if ($checkTable && $checkTable->num_rows > 0) {
            $tableExists = true;
        }

        if (!$tableExists) {
            // Auto-create table if missing
            $sql = "CREATE TABLE IF NOT EXISTS `{$prefix}pages` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(255) NOT NULL,
                `slug` VARCHAR(255) NOT NULL UNIQUE,
                `content` TEXT,
                `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

            if ($db->query($sql)) {
                $tableExists = true;
                // Add a welcome page if it's the first time
                $db->query("INSERT INTO `{$prefix}pages` (title, slug, content, status) VALUES ('Welkom', 'welkom', 'Dit is uw eerste pagina.', 'published')");
            }
        }

        if ($tableExists) {
            // Fetch stats
            $res = $db->query("SELECT COUNT(*) as count FROM {$prefix}pages");
            if ($res) {
                $row = $res->fetch_assoc();
                $pageCount = $row['count'];
            }

            // Fetch latest pages for dashboard overview
            $res = $db->query("SELECT * FROM {$prefix}pages ORDER BY created_at DESC LIMIT 5");
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $latestPages[] = $row;
                }
            }
        }

        // Fetch template count
        $templateCount = 0;
        $checkTplTable = $db->query("SHOW TABLES LIKE '{$prefix}templates'");
        if ($checkTplTable && $checkTplTable->num_rows > 0) {
            $res = $db->query("SELECT COUNT(*) as count FROM {$prefix}templates");
            if ($res) {
                $row = $res->fetch_assoc();
                $templateCount = $row['count'];
            }
        }

        // Fetch media count
        $mediaCount = 0;
        $uploadBase = __DIR__ . '/../../public/uploads/';
        if (is_dir($uploadBase)) {
            $mediaCount = $this->countMediaFiles($uploadBase);
        }

        // Fetch site status
        $siteStatus = 'inactive';
        $res = $db->query("SELECT setting_value FROM {$prefix}settings WHERE setting_key = 'site_status'");
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $siteStatus = $row['setting_value'];
        }

        $this->view('admin/dashboard', [
            'pageCount' => $pageCount,
            'templateCount' => $templateCount,
            'mediaCount' => $mediaCount,
            'latestPages' => $latestPages,
            'siteStatus' => $siteStatus
        ]);
    }

    private function countMediaFiles($dir) {
        $count = 0;
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            if (is_dir($dir . '/' . $file)) {
                $count += $this->countMediaFiles($dir . '/' . $file);
            } else {
                $count++;
            }
        }
        return $count;
    }

    public function profile()
    {
        // Simple authentication check
        if (!isset($_SESSION['user_id'])) {
            header('Location: /backoffice/login');
            exit;
        }

        $db = Database::connect();
        $prefix = Database::getPrefix();
        $userId = $_SESSION['user_id'];
        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // 1. Verify current password
            $stmt = $db->prepare("SELECT password_hash FROM {$prefix}users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
                $error = $GLOBALS['lang']['error_current_password_incorrect'];
            } else {
                // 2. Perform updates
                $updates = [];
                $params = [];
                $types = "";

                if (!empty($username)) {
                    $updates[] = "username = ?";
                    $params[] = $username;
                    $types .= "s";
                }

                if (!empty($email)) {
                    $updates[] = "email = ?";
                    $params[] = $email;
                    $types .= "s";
                }

                if (!empty($newPassword)) {
                    if ($newPassword !== $confirmPassword) {
                        $error = $GLOBALS['lang']['error_passwords_mismatch'];
                    } else {
                        $updates[] = "password_hash = ?";
                        $params[] = password_hash($newPassword, PASSWORD_DEFAULT);
                        $types .= "s";
                    }
                }

                if (!$error && !empty($updates)) {
                    $sql = "UPDATE {$prefix}users SET " . implode(", ", $updates) . " WHERE id = ?";
                    $params[] = $userId;
                    $types .= "i";

                    $stmt = $db->prepare($sql);
                    $stmt->bind_param($types, ...$params);

                    if ($stmt->execute()) {
                        $success = $GLOBALS['lang']['success_profile_updated'];
                        if (!empty($username)) {
                            $_SESSION['username'] = $username;
                        }
                    } else {
                        $error = $GLOBALS['lang']['error_profile_update'];
                    }
                    $stmt->close();
                } elseif (!$error) {
                    $error = $GLOBALS['lang']['error_no_changes'];
                }
            }
        }

        // Fetch current data
        $stmt = $db->prepare("SELECT username, email FROM {$prefix}users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $userData = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $this->view('admin/profile', [
            'user' => $userData,
            'error' => $error,
            'success' => $success
        ]);
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $db = Database::connect();
            $prefix = Database::getPrefix();

            $stmt = $db->prepare("SELECT id, username, password_hash FROM {$prefix}users WHERE username = ? AND status = 'active'");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: /backoffice');
                exit;
            } else {
                // For now, if it's the very first login and DB is empty or something, 
                // we might want a fallback, but let's stick to real auth now.
                $error = $GLOBALS['lang']['error_invalid_login'];
                $this->view('admin/login', ['error' => $error]);
                return;
            }
        }
        $this->view('admin/login');
    }


    public function settings()
    {
        // Simple authentication check
        if (!isset($_SESSION['user_id'])) {
            header('Location: /backoffice/login');
            exit;
        }

        $db = Database::connect();
        $prefix = Database::getPrefix();
        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $siteName = $_POST['site_name'] ?? '';
            $siteDesc = $_POST['site_desc'] ?? '';
            $siteDomain = $_POST['site_domain'] ?? '';
            $siteLogo = $_POST['site_logo'] ?? '';
            $hideLogo = isset($_POST['hide_logo']) ? '1' : '0';
            $defaultLang = $_POST['default_lang'] ?? 'nl';
            $dbUser = $_POST['db_user'] ?? '';
            $dbPass = $_POST['db_pass'] ?? '';

            try {
                // 1. Update Database settings
                $stmtUpdate = $db->prepare("UPDATE {$prefix}settings SET setting_value = ? WHERE setting_key = ?");
                $stmtInsert = $db->prepare("INSERT INTO {$prefix}settings (setting_key, setting_value) VALUES (?, ?)");

                $dbSettings = [
                    'site_name' => $siteName,
                    'site_description' => $siteDesc,
                    'site_domain' => $siteDomain,
                    'site_logo' => $siteLogo,
                    'hide_logo' => $hideLogo
                ];

                foreach ($dbSettings as $key => $value) {
                    // First check if it exists, if not INSERT
                    $check = $db->query("SELECT id FROM {$prefix}settings WHERE setting_key = '" . $db->real_escape_string($key) . "'");
                    if ($check && $check->num_rows > 0) {
                        $stmtUpdate->bind_param("ss", $value, $key);
                        $stmtUpdate->execute();
                    } else {
                        $stmtInsert->bind_param("ss", $key, $value);
                        $stmtInsert->execute();
                    }
                }
                $stmtUpdate->close();
                $stmtInsert->close();

                // 2. Update .env file
                $envPath = __DIR__ . '/../../.env';
                if (file_exists($envPath)) {
                    $envContent = file_get_contents($envPath);
                    $replacements = [
                        'APP_NAME' => $siteName,
                        'APP_URL' => "http://" . $siteDomain,
                        'DB_USERNAME' => $dbUser,
                        'DEFAULT_LANGUAGE' => $defaultLang
                    ];

                    // Only update password if provided
                    if (!empty($dbPass)) {
                        $replacements['DB_PASSWORD'] = $dbPass;
                    }

                    foreach ($replacements as $key => $value) {
                        // Match key=value (with optional quotes)
                        $pattern = "/^" . preg_quote($key) . "=(.*)$/m";
                        $replacement = $key . "=\"" . str_replace('"', '\"', $value) . "\"";

                        if (preg_match($pattern, $envContent)) {
                            $envContent = preg_replace($pattern, $replacement, $envContent);
                        } else {
                            // If not found, append it
                            $envContent .= "\n" . $replacement;
                        }
                    }

                    if (file_put_contents($envPath, $envContent) === false) {
                        throw new \Exception($GLOBALS['lang']['error_env_write']);
                    }

                    // Reload config to reflect changes immediately
                    Config::load($envPath);
                }

                $success = $GLOBALS['lang']['success_settings_updated'];
            } catch (\Exception $e) {
                $error = $GLOBALS['lang']['error_occurred'] . ": " . $e->getMessage();
            }

        }

        // Fetch DB settings from table
        $stmt = $db->prepare("SELECT setting_key, setting_value FROM {$prefix}settings");
        $stmt->execute();
        $result = $stmt->get_result();
        $settings = [];
        while ($row = $result->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        $stmt->close();

        // Environment settings
        $env = [
            'app_name' => Config::get('APP_NAME'),
            'app_url' => Config::get('APP_URL'),
            'db_host' => Config::get('DB_HOST'),
            'db_name' => Config::get('DB_DATABASE'),
            'db_user' => Config::get('DB_USERNAME'),
            'db_prefix' => Config::get('DB_PREFIX'),
            'language' => Config::get('DEFAULT_LANGUAGE', 'nl'),
            'version' => App::VERSION
        ];

        $this->view('admin/settings', [
            'settings' => $settings,
            'env' => $env,
            'error' => $error,
            'success' => $success
        ]);
    }

    public function logout()
    {
        session_destroy();
        header('Location: /');
        exit;
    }

    public function pages()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /backoffice/login');
            exit;
        }

        $db = Database::connect();
        $prefix = Database::getPrefix();

        $result = $db->query("SELECT * FROM {$prefix}pages ORDER BY created_at DESC");
        $pages = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $pages[] = $row;
            }
        }

        $this->view('admin/pages_list', [
            'pages' => $pages
        ]);
    }

    public function addPage()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /backoffice/login');
            exit;
        }

        $db = Database::connect();
        $prefix = Database::getPrefix();
        $error = null;
        $success = null;

        // Fetch templates
        $templatesRes = $db->query("SELECT id, name, type FROM {$prefix}templates");
        $templates = [];
        if ($templatesRes) {
            while ($row = $templatesRes->fetch_assoc()) {
                $templates[] = $row;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $content = $_POST['content'] ?? '';
            $status = $_POST['status'] ?? 'draft';
            $templateId = !empty($_POST['template_id']) ? (int) $_POST['template_id'] : null;
            $isHomepage = 0;

            // Check if page is intended to be the homepage (slug is '/')
            if ($slug === '/') {
                $isHomepage = 1;
            }

            if (empty($title) || empty($slug)) {
                $error = $GLOBALS['lang']['error_title_slug_required'];
            } else {
                // Check if slug already exists
                $slugCheck = $db->query("SELECT id FROM {$prefix}pages WHERE slug = '" . $db->real_escape_string($slug) . "' LIMIT 1");
                if ($slugCheck && $slugCheck->num_rows > 0) {
                    $error = $GLOBALS['lang']['error_slug_exists'];
                    $page = ['title' => $title, 'slug' => $slug, 'content' => $content, 'status' => $status, 'template_id' => $templateId];
                } else {
                    if ($isHomepage) {
                        // Reset other homepages
                        $db->query("UPDATE {$prefix}pages SET is_homepage = 0 WHERE is_homepage = 1");
                    }

                    $stmt = $db->prepare("INSERT INTO {$prefix}pages (title, slug, content, status, is_homepage, template_id) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssii", $title, $slug, $content, $status, $isHomepage, $templateId);

                    if ($stmt->execute()) {
                        header('Location: /backoffice/pages?success=created');
                        exit;
                    } else {
                        $error = $GLOBALS['lang']['error_occurred'] . ": " . $db->error;
                    }
                    $stmt->close();
                }
            }
        }

        // Fetch site settings for branding
        $settingsRes = $db->query("SELECT setting_key, setting_value FROM {$prefix}settings");
        $siteSettings = [];
        if ($settingsRes) {
            while ($row = $settingsRes->fetch_assoc()) {
                $siteSettings[$row['setting_key']] = $row['setting_value'];
            }
        }

        $this->view('admin/pages_edit', [
            'mode' => 'add',
            'error' => $error,
            'templates' => $templates,
            'page' => $page ?? null,
            'siteSettings' => $siteSettings
        ]);
    }

    public function editPage($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /backoffice/login');
            exit;
        }

        $db = Database::connect();
        $prefix = Database::getPrefix();
        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $content = $_POST['content'] ?? '';
            $status = $_POST['status'] ?? 'draft';
            $templateId = !empty($_POST['template_id']) ? (int) $_POST['template_id'] : null;
            $isHomepage = 0;

            // Check if page is intended to be the homepage (slug is '/')
            if ($slug === '/') {
                $isHomepage = 1;
            }

            if (empty($title) || empty($slug)) {
                $error = $GLOBALS['lang']['error_title_slug_required'];
            } else {
                // Check if slug already exists (excluding current page)
                $slugCheck = $db->query("SELECT id FROM {$prefix}pages WHERE slug = '" . $db->real_escape_string($slug) . "' AND id != $id LIMIT 1");
                if ($slugCheck && $slugCheck->num_rows > 0) {
                    $error = $GLOBALS['lang']['error_slug_exists'];
                    $page = ['id' => $id, 'title' => $title, 'slug' => $slug, 'content' => $content, 'status' => $status, 'template_id' => $templateId, 'is_homepage' => $isHomepage];
                } else {
                    if ($isHomepage) {
                        // Reset other homepages
                        $db->query("UPDATE {$prefix}pages SET is_homepage = 0 WHERE is_homepage = 1");
                    }

                    $stmt = $db->prepare("UPDATE {$prefix}pages SET title = ?, slug = ?, content = ?, status = ?, is_homepage = ?, template_id = ? WHERE id = ?");
                    $stmt->bind_param("ssssiii", $title, $slug, $content, $status, $isHomepage, $templateId, $id);

                    if ($stmt->execute()) {
                        header('Location: /backoffice/pages?success=updated');
                        exit;
                    } else {
                        $error = $GLOBALS['lang']['error_occurred'] . ": " . $db->error;
                    }
                    $stmt->close();
                }
            }
        }

        // Fetch current data if not already set by error handler
        if (!isset($page)) {
            $stmt = $db->prepare("SELECT * FROM {$prefix}pages WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $page = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        }

        // Fetch templates
        $templatesRes = $db->query("SELECT id, name, type FROM {$prefix}templates");
        $templates = [];
        if ($templatesRes) {
            while ($row = $templatesRes->fetch_assoc()) {
                $templates[] = $row;
            }
        }

        if (!$page) {
            header('Location: /backoffice/pages?error=not_found');
            exit;
        }

        // Fetch site settings for branding
        $settingsRes = $db->query("SELECT setting_key, setting_value FROM {$prefix}settings");
        $siteSettings = [];
        if ($settingsRes) {
            while ($row = $settingsRes->fetch_assoc()) {
                $siteSettings[$row['setting_key']] = $row['setting_value'];
            }
        }

        $this->view('admin/pages_edit', [
            'mode' => 'edit',
            'page' => $page,
            'error' => $error,
            'success' => $success,
            'templates' => $templates,
            'siteSettings' => $siteSettings
        ]);
    }

    public function deletePage($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /backoffice/login');
            exit;
        }

        $db = Database::connect();
        $prefix = Database::getPrefix();

        $stmt = $db->prepare("DELETE FROM {$prefix}pages WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header('Location: /backoffice/pages?success=deleted');
        } else {
            header('Location: /backoffice/pages?error=delete_failed');
        }
        $stmt->close();
        exit;
    }

    public function togglePageStatus($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /backoffice/login');
            exit;
        }

        $db = Database::connect();
        $prefix = Database::getPrefix();

        // Get current status
        $stmt = $db->prepare("SELECT status FROM {$prefix}pages WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $page = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($page) {
            $newStatus = ($page['status'] === 'published') ? 'draft' : 'published';
            $stmt = $db->prepare("UPDATE {$prefix}pages SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $newStatus, $id);
            $stmt->execute();
            $stmt->close();
        }

        header('Location: /backoffice/pages');
        exit;
    }

    public function toggleSiteStatus()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /backoffice/login');
            exit;
        }

        $db = Database::connect();
        $prefix = Database::getPrefix();

        // Check current status
        $res = $db->query("SELECT setting_value FROM {$prefix}settings WHERE setting_key = 'site_status'");
        $currentStatus = 'inactive';
        $exists = false;

        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $currentStatus = $row['setting_value'];
            $exists = true;
        }

        $newStatus = ($currentStatus === 'active') ? 'inactive' : 'active';

        if ($exists) {
            $stmt = $db->prepare("UPDATE {$prefix}settings SET setting_value = ? WHERE setting_key = 'site_status'");
            $stmt->bind_param("s", $newStatus);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $db->prepare("INSERT INTO {$prefix}settings (setting_key, setting_value) VALUES ('site_status', ?)");
            $stmt->bind_param("s", $newStatus);
            $stmt->execute();
            $stmt->close();
        }

        header('Location: /backoffice');
        exit;
    }

    public function layoutConfigurator()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /backoffice/login');
            exit;
        }

        $db = Database::connect();
        $prefix = Database::getPrefix();

        // Fetch current active layout from templates table
        $layoutJson = '';
        $res = $db->query("SELECT layout_json FROM {$prefix}templates WHERE type = 'homepage' AND is_active = 1 LIMIT 1");
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $layoutJson = $row['layout_json'];
        }

        // Fallback to settings if table is empty (migration) or if no active template found
        if (empty($layoutJson)) {
            $res = $db->query("SELECT setting_value FROM {$prefix}settings WHERE setting_key = 'homepage_layout_json'");
            if ($res && $res->num_rows > 0) {
                $row = $res->fetch_assoc();
                $layoutJson = $row['setting_value'];
            }
        }

        // Default layout if completely empty
        if (empty($layoutJson)) {
            $defaultLayout = [
                'header' => [
                    'height' => '90px',
                    'sections' => [
                        ['type' => 'logo', 'width' => 4],
                        ['type' => 'menu', 'width' => 4],
                        ['type' => 'cta', 'width' => 4]
                    ]
                ],
                'main' => [
                    'rows' => [
                        [
                            'height' => '90px',
                            'columns' => [
                                ['type' => 'text', 'width' => 6],
                                ['type' => 'image', 'width' => 6]
                            ]
                        ]
                    ]
                ],
                'footer' => [
                    'height' => '120px',
                    'sections' => [
                        ['type' => 'text', 'width' => 6],
                        ['type' => 'socials', 'width' => 6]
                    ]
                ]
            ];
            $layoutJson = json_encode($defaultLayout);
        }

        $this->view('admin/layout_configurator', [
            'layoutJson' => $layoutJson,
            'pageType' => 'homepage'
        ]);
    }

    public function contentLayoutConfigurator()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /backoffice/login');
            exit;
        }

        $db = Database::connect();
        $prefix = Database::getPrefix();

        // Fetch current active layout from templates table
        $layoutJson = '';
        $res = $db->query("SELECT layout_json FROM {$prefix}templates WHERE type = 'content' AND is_active = 1 LIMIT 1");
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $layoutJson = $row['layout_json'];
        }

        // Fallback to settings if table is empty (migration) or if no active template found
        if (empty($layoutJson)) {
            $res = $db->query("SELECT setting_value FROM {$prefix}settings WHERE setting_key = 'content_layout_json'");
            if ($res && $res->num_rows > 0) {
                $row = $res->fetch_assoc();
                $layoutJson = $row['setting_value'];
            }
        }

        // Default layout if empty
        if (empty($layoutJson)) {
            $defaultLayout = [
                'header' => [
                    'height' => '90px',
                    'sections' => [
                        ['type' => 'logo', 'width' => 4],
                        ['type' => 'menu', 'width' => 4],
                        ['type' => 'cta', 'width' => 4]
                    ]
                ],
                'main' => [
                    'rows' => [
                        [
                            'height' => '90px',
                            'columns' => [
                                ['type' => 'text', 'width' => 12]
                            ]
                        ]
                    ]
                ],
                'footer' => [
                    'height' => '120px',
                    'sections' => [
                        ['type' => 'text', 'width' => 6],
                        ['type' => 'socials', 'width' => 6]
                    ]
                ]
            ];
            $layoutJson = json_encode($defaultLayout);
        }

        $this->view('admin/layout_configurator', [
            'layoutJson' => $layoutJson,
            'pageType' => 'content'
        ]);
    }

    public function saveLayoutConfig()
    {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /backoffice/login');
            exit;
        }

        $layoutJson = $_POST['layout_json'] ?? '';

        // Basic validation
        if (!json_decode($layoutJson)) {
            header('Location: /backoffice/templates/homepage?error=invalid_json');
            exit;
        }

        $db = Database::connect();
        $prefix = Database::getPrefix();

        // Update active template in templates table
        $res = $db->query("SELECT id FROM {$prefix}templates WHERE type = 'homepage' AND is_active = 1 LIMIT 1");
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $tplId = $row['id'];
            $stmt = $db->prepare("UPDATE {$prefix}templates SET layout_json = ? WHERE id = ?");
            $stmt->bind_param("si", $layoutJson, $tplId);
            $stmt->execute();
            $stmt->close();
        } else {
            // Create a default if none active
            $stmt = $db->prepare("INSERT INTO {$prefix}templates (name, type, layout_json, is_active) VALUES ('Homepage', 'homepage', ?, 1)");
            $stmt->bind_param("s", $layoutJson);
            $stmt->execute();
            $stmt->close();
        }

        // Also update settings for backward compatibility/quick access
        $res = $db->query("SELECT id FROM {$prefix}settings WHERE setting_key = 'homepage_layout_json'");
        if ($res && $res->num_rows > 0) {
            $stmt = $db->prepare("UPDATE {$prefix}settings SET setting_value = ? WHERE setting_key = 'homepage_layout_json'");
            $stmt->bind_param("s", $layoutJson);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $db->prepare("INSERT INTO {$prefix}settings (setting_key, setting_value) VALUES ('homepage_layout_json', ?)");
            $stmt->bind_param("s", $layoutJson);
            $stmt->execute();
            $stmt->close();
        }

        header('Location: /backoffice/templates/homepage?saved=1');
        exit;
    }

    public function saveContentLayoutConfig()
    {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /backoffice/login');
            exit;
        }

        $layoutJson = $_POST['layout_json'] ?? '';

        // Basic validation
        if (!json_decode($layoutJson)) {
            header('Location: /backoffice/templates/content?error=invalid_json');
            exit;
        }

        $db = Database::connect();
        $prefix = Database::getPrefix();

        // Update active template in templates table
        $res = $db->query("SELECT id FROM {$prefix}templates WHERE type = 'content' AND is_active = 1 LIMIT 1");
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $tplId = $row['id'];
            $stmt = $db->prepare("UPDATE {$prefix}templates SET layout_json = ? WHERE id = ?");
            $stmt->bind_param("si", $layoutJson, $tplId);
            $stmt->execute();
            $stmt->close();
        } else {
            // Create a default if none active
            $stmt = $db->prepare("INSERT INTO {$prefix}templates (name, type, layout_json, is_active) VALUES ('Contentpagina', 'content', ?, 1)");
            $stmt->bind_param("s", $layoutJson);
            $stmt->execute();
            $stmt->close();
        }

        // Also update settings
        $res = $db->query("SELECT id FROM {$prefix}settings WHERE setting_key = 'content_layout_json'");
        if ($res && $res->num_rows > 0) {
            $stmt = $db->prepare("UPDATE {$prefix}settings SET setting_value = ? WHERE setting_key = 'content_layout_json'");
            $stmt->bind_param("s", $layoutJson);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $db->prepare("INSERT INTO {$prefix}settings (setting_key, setting_value) VALUES ('content_layout_json', ?)");
            $stmt->bind_param("s", $layoutJson);
            $stmt->execute();
            $stmt->close();
        }

        header('Location: /backoffice/templates/content?saved=1');
        exit;
    }

    public function media()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /backoffice/login');
            exit;
        }

        $subDir = $_GET['dir'] ?? '';
        // Path safety: remove any leading slashes and prevent ../
        $subDir = str_replace(['..', '\\'], ['', '/'], ltrim($subDir, '/'));
        
        $uploadBase = __DIR__ . '/../../public/uploads/';
        $targetDir = realpath($uploadBase . $subDir);

        // Security check: ensure target is within uploads
        if (!$targetDir || strpos($targetDir, realpath($uploadBase)) !== 0) {
            $targetDir = realpath($uploadBase);
            if (!$targetDir) {
                // Folder doesnt exist yet
                mkdir($uploadBase, 0755, true);
                $targetDir = realpath($uploadBase);
            }
            $subDir = '';
        }

        $items = [];
        if (is_dir($targetDir)) {
            $files = scandir($targetDir);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;
                
                $fullPath = $targetDir . '/' . $file;
                $isDir = is_dir($fullPath);
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                
                $items[] = [
                    'name' => $file,
                    'isDir' => $isDir,
                    'size' => $isDir ? 0 : filesize($fullPath),
                    'ext' => $ext,
                    'url' => '/uploads/' . ($subDir ? $subDir . '/' : '') . $file,
                    'path' => ($subDir ? $subDir . '/' : '') . $file
                ];
            }
        }

        // Sort: naturally, folders first
        usort($items, function($a, $b) {
            if ($a['isDir'] && !$b['isDir']) return -1;
            if (!$a['isDir'] && $b['isDir']) return 1;
            return strcasecmp($a['name'], $b['name']);
        });

        $this->view('admin/media', [
            'items' => $items,
            'currentDir' => $subDir,
            'breadcrumbs' => array_filter(explode('/', trim($subDir, '/')))
        ]);
    }

    public function uploadMedia()
    {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if (isset($_FILES['file'])) {
            $file = $_FILES['file'];
            $targetPath = $_POST['path'] ?? '';
            $targetPath = str_replace(['..', '\\'], ['', '/'], ltrim($targetPath, '/'));
            
            $uploadBase = realpath(__DIR__ . '/../../public/uploads/');
            $uploadDir = $uploadBase . ($targetPath ? DIRECTORY_SEPARATOR . $targetPath : '');

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Allowed: images and specified video formats
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'mp4', 'webm', 'ogv', 'mov'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($extension, $allowedExtensions)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Invalid file type.']);
                exit;
            }

            // Sanitized filename
            $safeName = preg_replace('/[^a-zA-Z0-9.\-_]/', '_', $file['name']);
            // Avoid collisions
            if (file_exists($uploadDir . '/' . $safeName)) {
                $baseName = pathinfo($safeName, PATHINFO_FILENAME);
                $safeName = $baseName . '_' . time() . '.' . $extension;
            }

            $finalTarget = $uploadDir . '/' . $safeName;

            if (move_uploaded_file($file['tmp_name'], $finalTarget)) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true, 
                    'url' => '/uploads/' . ($targetPath ? $targetPath . '/' : '') . $safeName,
                    'name' => $safeName
                ]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Upload failed.']);
            }
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No file provided.']);
        exit;
    }

    public function createMediaFolder()
    {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') exit;

        $parent = $_POST['parent'] ?? '';
        $name = $_POST['name'] ?? '';
        
        $parent = str_replace(['..', '\\'], ['', '/'], ltrim($parent, '/'));
        $name = preg_replace('/[^a-zA-Z0-9.\-_]/', '_', $name);

        if (empty($name)) {
            header('Location: /backoffice/media?dir=' . urlencode($parent) . '&error=invalid_name');
            exit;
        }

        $uploadBase = realpath(__DIR__ . '/../../public/uploads/');
        $targetPath = $uploadBase . ($parent ? DIRECTORY_SEPARATOR . $parent : '') . DIRECTORY_SEPARATOR . $name;

        if (is_dir($targetPath)) {
            header('Location: /backoffice/media?dir=' . urlencode($parent) . '&error=folder_exists');
        } else {
            mkdir($targetPath, 0755, true);
            header('Location: /backoffice/media?dir=' . urlencode($parent) . '&success=folder_created');
        }
        exit;
    }

    public function deleteMedia()
    {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $path = $_POST['path'] ?? '';
        $path = str_replace(['..', '\\'], ['', '/'], ltrim($path, '/'));
        
        $uploadBase = realpath(__DIR__ . '/../../public/uploads/');
        $fullPath = realpath($uploadBase . DIRECTORY_SEPARATOR . $path);

        // Security check
        if ($fullPath && strpos($fullPath, $uploadBase) === 0 && $fullPath !== $uploadBase) {
            if (is_dir($fullPath)) {
                $this->recursiveDelete($fullPath);
            } else {
                unlink($fullPath);
            }
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid path']);
        }
        exit;
    }

    private function recursiveDelete($dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->recursiveDelete("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    public function renameMedia()
    {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $oldPath = $_POST['oldPath'] ?? '';
        $newName = $_POST['newName'] ?? '';

        $oldPath = str_replace(['..', '\\'], ['', '/'], ltrim($oldPath, '/'));
        $newName = preg_replace('/[^a-zA-Z0-9.\-_]/', '_', $newName);

        $uploadBase = realpath(__DIR__ . '/../../public/uploads/');
        $fullOldPath = realpath($uploadBase . DIRECTORY_SEPARATOR . $oldPath);
        
        if ($fullOldPath && strpos($fullOldPath, $uploadBase) === 0 && !empty($newName)) {
            $parentDir = dirname($fullOldPath);
            $ext = pathinfo($fullOldPath, PATHINFO_EXTENSION);
            $targetName = $newName . ($ext ? '.' . $ext : '');
            $fullNewPath = $parentDir . DIRECTORY_SEPARATOR . $targetName;

            if (rename($fullOldPath, $fullNewPath)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'newName' => $targetName]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Rename failed']);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
        }
        exit;
    }

    public function getTemplate($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $db = Database::connect();
        $prefix = Database::getPrefix();
        $id = (int) $id;

        $res = $db->query("SELECT * FROM {$prefix}templates WHERE id = $id");
        if ($res && $res->num_rows > 0) {
            header('Content-Type: application/json');
            echo json_encode($res->fetch_assoc());
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Template not found']);
        }
        exit;
    }

    public function templates()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /backoffice/login');
            exit;
        }

        $db = Database::connect();
        $prefix = Database::getPrefix();

        // Fetch only unique templates for the list
        $res = $db->query("SELECT * FROM {$prefix}templates WHERE id IN (SELECT MIN(id) FROM {$prefix}templates GROUP BY name) ORDER BY FIELD(type, 'homepage', 'content'), name ASC");
        $templates = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $templates[] = $row;
            }
        }

        $this->view('admin/templates_list', [
            'templates' => $templates
        ]);
    }

    public function addTemplate()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /backoffice/login');
            exit;
        }

        $db = Database::connect();
        $prefix = Database::getPrefix();
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $type = $_POST['type'] ?? 'content';

            if (empty($name)) {
                $error = "Naam is verplicht.";
            } else {
                // Default empty layout
                $layout = [
                    'header' => ['sections' => [['type' => 'logo'], ['type' => 'menu']]],
                    'main' => ['rows' => [['columns' => [['type' => 'text', 'width' => 12]]]]],
                    'footer' => ['sections' => [['type' => 'text']]]
                ];
                $layoutJson = json_encode($layout);

                if ($type === 'homepage') {
                    // Exactly one homepage: demote others
                    $db->query("UPDATE {$prefix}templates SET type = 'content' WHERE type = 'homepage'");
                }

                $stmt = $db->prepare("INSERT INTO {$prefix}templates (name, type, layout_json, is_active) VALUES (?, ?, ?, 0)");
                $stmt->bind_param("sss", $name, $type, $layoutJson);

                if ($stmt->execute()) {
                    $newId = $db->insert_id;
                    header("Location: /backoffice/templates/edit/$newId?success=1");
                    exit;
                } else {
                    $error = "Er is een fout opgetreden: " . $db->error;
                }
                $stmt->close();
            }
        }

        $this->view('admin/layout_configurator', [
            'mode' => 'add',
            'error' => $error,
            'pageType' => 'custom'
        ]);
    }

    public function templateConfigurator($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /backoffice/login');
            exit;
        }

        $db = Database::connect();
        $prefix = Database::getPrefix();
        $id = (int) $id;

        $res = $db->query("SELECT * FROM {$prefix}templates WHERE id = $id");
        $template = $res->fetch_assoc();

        if (!$template) {
            header('Location: /backoffice?error=template_not_found');
            exit;
        }

        $this->view('admin/layout_configurator', [
            'template' => $template,
            'layoutJson' => $template['layout_json'],
            'pageType' => $template['type'],
            'mode' => 'edit'
        ]);
    }

    public function saveTemplateConfig($id)
    {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /backoffice/login');
            exit;
        }

        $id = (int) $id;
        $name = $_POST['name'] ?? '';
        $type = $_POST['type'] ?? 'content';
        $layoutJson = $_POST['layout_json'] ?? '';

        if (empty($name) || !json_decode($layoutJson)) {
            header("Location: /backoffice/templates/edit/$id?error=invalid_data");
            exit;
        }

        $db = Database::connect();
        $prefix = Database::getPrefix();

        // 1. Fetch current type
        $res = $db->query("SELECT type FROM {$prefix}templates WHERE id = $id");
        $tpl = $res->fetch_assoc();
        
        // 2. Homepage Validation: if changing from homepage to content, ensure others exist
        if ($tpl['type'] === 'homepage' && $type === 'content') {
            $countRes = $db->query("SELECT COUNT(*) as total FROM {$prefix}templates WHERE type = 'homepage'");
            $countRow = $countRes->fetch_assoc();
            if ($countRow['total'] <= 1) {
                header("Location: /backoffice/templates/edit/$id?error=last_homepage");
                exit;
            }
        }

        // 3. Homepage Validation: if changing TO homepage, demote others
        if ($type === 'homepage') {
            $db->query("UPDATE {$prefix}templates SET type = 'content' WHERE type = 'homepage'");
        }

        $stmt = $db->prepare("UPDATE {$prefix}templates SET name = ?, type = ?, layout_json = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $type, $layoutJson, $id);

        if ($stmt->execute()) {
            header("Location: /backoffice/templates/edit/$id?saved=1");
        } else {
            header("Location: /backoffice/templates/edit/$id?error=db_error");
        }
        $stmt->close();
        exit;
    }

    public function deleteTemplate($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /backoffice/login');
            exit;
        }

        $db = Database::connect();
        $prefix = Database::getPrefix();
        $id = (int) $id;

        // Homepage Validation: Don't delete the last homepage template
        $check = $db->query("SELECT type FROM {$prefix}templates WHERE id = $id");
        if ($check && $row = $check->fetch_assoc()) {
            if ($row['type'] === 'homepage') {
                $countRes = $db->query("SELECT COUNT(*) as total FROM {$prefix}templates WHERE type = 'homepage'");
                $countRow = $countRes->fetch_assoc();
                if ($countRow['total'] <= 1) {
                    header('Location: /backoffice/templates?error=cannot_delete_last_homepage');
                    exit;
                }
            }
        }

        $db->query("DELETE FROM {$prefix}templates WHERE id = $id");
        header('Location: /backoffice/templates?deleted=1');
        exit;
    }
}
