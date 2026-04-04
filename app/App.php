<?php

namespace Fritsion;

require_once __DIR__ . '/Language.php';
require_once __DIR__ . '/Installer.php';

require_once __DIR__ . '/Config.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Controllers/BaseController.php';
require_once __DIR__ . '/Controllers/FrontController.php';
require_once __DIR__ . '/Controllers/AdminController.php';

use Fritsion\Language;
use Fritsion\Config;
use Fritsion\Controllers\FrontController;
use Fritsion\Controllers\AdminController;

class App
{
    const VERSION = '0.1.7';
    protected static $instance;

    public function __construct()
    {
        // Constructor logic
    }

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function run()
    {
        session_start();

        // Load Configuration if installed
        $defaultLang = 'nl';
        if (file_exists(__DIR__ . '/../install.lock')) {
            Config::load(__DIR__ . '/../.env');
            $defaultLang = Config::get('DEFAULT_LANGUAGE', 'nl');
        }

        // taal bepalen
        if (isset($_GET['lang'])) {
            $_SESSION['lang'] = $_GET['lang'];
        }

        $selected = $_SESSION['lang'] ?? $defaultLang;

        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $context = (strpos($uri, '/backoffice') === 0) ? 'admin' : 'front';

        // taalbestand laden
        $lang = Language::load($selected, $context);
        $GLOBALS['lang'] = $lang; // Make it globally available for now

        // Check if already installed
        if (file_exists(__DIR__ . '/../install.lock')) {
            // Load Configuration
            Config::load(__DIR__ . '/../.env');

            $uri = $_SERVER['REQUEST_URI'] ?? '/';
            $uri = strtok($uri, '?');

            // Normalize URI: Remove the subdirectory if present
            $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
            // Remove 'public' from script dir if it's there (since we are rewriting to it)
            $scriptDir = str_replace('/public', '', $scriptDir);

            if ($scriptDir !== '/' && $scriptDir !== '.') {
                if (strpos($uri, $scriptDir) === 0) {
                    $uri = substr($uri, strlen($scriptDir));
                }
            }
            if (empty($uri))
                $uri = '/';

            // Simple Routing

            if (strpos($uri, '/backoffice') === 0) {

                $controller = new AdminController();
                if ($uri === '/backoffice/login') {
                    $controller->login();
                } elseif ($uri === '/backoffice/profile') {
                    $controller->profile();
                } elseif ($uri === '/backoffice/settings') {
                    $controller->settings();
                } elseif ($uri === '/backoffice/pages') {
                    $controller->pages();
                } elseif ($uri === '/backoffice/pages/add') {
                    $controller->addPage();
                } elseif (preg_match('#^/backoffice/pages/edit/(\d+)$#', $uri, $matches)) {
                    $controller->editPage($matches[1]);
                } elseif (preg_match('#^/backoffice/pages/toggle/(\d+)$#', $uri, $matches)) {
                    $controller->togglePageStatus($matches[1]);
                } elseif (preg_match('#^/backoffice/pages/delete/(\d+)$#', $uri, $matches)) {
                    $controller->deletePage($matches[1]);
                } elseif ($uri === '/backoffice/site-status/toggle') {
                    $controller->toggleSiteStatus();
                } elseif ($uri === '/backoffice/templates') {
                    $controller->templates();
                } elseif ($uri === '/backoffice/templates/homepage') {
                    $controller->layoutConfigurator();
                } elseif ($uri === '/backoffice/templates/homepage/save') {
                    $controller->saveLayoutConfig();
                } elseif ($uri === '/backoffice/templates/content') {
                    $controller->contentLayoutConfigurator();
                } elseif ($uri === '/backoffice/templates/content/save') {
                    $controller->saveContentLayoutConfig();
                } elseif ($uri === '/backoffice/templates/add') {
                    $controller->addTemplate();
                } elseif (preg_match('#^/backoffice/templates/edit/(\d+)$#', $uri, $matches)) {
                    $controller->templateConfigurator($matches[1]);
                } elseif (preg_match('#^/backoffice/templates/edit/(\d+)/save$#', $uri, $matches)) {
                    $controller->saveTemplateConfig($matches[1]);
                } elseif (preg_match('#^/backoffice/templates/delete/(\d+)$#', $uri, $matches)) {
                    $controller->deleteTemplate($matches[1]);
                } elseif (preg_match('#^/backoffice/templates/get/(\d+)$#', $uri, $matches)) {
                    $controller->getTemplate($matches[1]);
                } elseif ($uri === '/backoffice/media/upload') {
                    $controller->uploadMedia();
                } elseif ($uri === '/backoffice/media') {
                    $controller->media();
                } elseif ($uri === '/backoffice/media/create-folder') {
                    $controller->createMediaFolder();
                } elseif ($uri === '/backoffice/media/delete') {
                    $controller->deleteMedia();
                } elseif ($uri === '/backoffice/media/rename') {
                    $controller->renameMedia();
                } else {
                    $controller->index();
                }
            } else {
                $controller = new FrontController();
                $controller->index($uri);
            }
            return;
        }

        // DB Test Action
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'test_db') {
            header('Content-Type: application/json');

            $host = $_POST['host'] ?? 'localhost';
            $user = $_POST['user'] ?? '';
            $pass = $_POST['pass'] ?? '';
            $name = $_POST['name'] ?? '';

            try {
                // Suppress warnings for connection errors
                $conn = @new \mysqli($host, $user, $pass, $name);

                if ($conn->connect_error) {
                    throw new \Exception($conn->connect_error);
                }

                echo json_encode(['success' => true]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }

        // Install Action
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'install') {
            header('Content-Type: application/json');

            try {
                // Validate required fields
                $required = ['site_name', 'site_desc', 'domain', 'username', 'email', 'password', 'db_host', 'db_name', 'db_user', 'db_pass', 'db_prefix'];
                foreach ($required as $field) {
                    if (empty($_POST[$field])) {
                        throw new \Exception("Missing required field: $field");
                    }
                }

                // Create installer instance
                $installer = new Installer(
                    $_POST['db_host'],
                    $_POST['db_user'],
                    $_POST['db_pass'],
                    $_POST['db_name'],
                    $_POST['db_prefix'] ?? 'frcms_'
                );

                // Run installation
                if ($installer->run($_POST)) {
                    // Log user in immediately
                    $_SESSION['user_id'] = 1;
                    $_SESSION['username'] = $_POST['username'] ?? 'Admin';

                    echo json_encode(['success' => true, 'message' => $lang['install_success']]);
                } else {
                    $errors = implode(', ', $installer->getErrors());
                    throw new \Exception($errors);
                }
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }

        // view tonen
        $version = self::VERSION;
        include __DIR__ . '/Views/splash.php';
    }

}
