<?php

namespace Fritsion;

class Installer
{
    private $db;
    private $errors = [];
    private $prefix;

    public function __construct($host, $user, $pass, $dbname, $prefix = 'fcms_')
    {
        $this->prefix = strtolower($prefix);
        try {
            $this->db = new \mysqli($host, $user, $pass, $dbname);

            if ($this->db->connect_error) {
                throw new \Exception("Database connection failed: " . $this->db->connect_error);
            }

            $this->db->set_charset("utf8mb4");
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    /**
     * Run the complete installation process
     */
    public function run($data)
    {
        try {
            // Step 1: Create tables
            if (!$this->createTables()) {
                throw new \Exception("Failed to create database tables");
            }

            // Step 2: Save settings
            if (!$this->saveSettings($data['site_name'], $data['site_desc'], $data['domain'])) {
                throw new \Exception("Failed to save site settings");
            }

            // Step 3: Create admin user
            if (!$this->createAdminUser($data['username'], $data['email'], $data['password'])) {
                throw new \Exception("Failed to create admin user");
            }

            // Step 4: Generate .env file
            if (!$this->generateEnvFile($data)) {
                throw new \Exception("Failed to generate .env file");
            }

            // Step 5: Lock installer
            if (!$this->lockInstaller()) {
                throw new \Exception("Failed to lock installer");
            }

            return true;
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }
    }

    /**
     * Create database tables from schema
     */
    private function createTables()
    {
        $schemaFile = __DIR__ . '/../database/schema.sql';

        if (!file_exists($schemaFile)) {
            $this->errors[] = "Schema file not found";
            return false;
        }

        $sql = file_get_contents($schemaFile);

        // Remove comments
        $sql = preg_replace('/--.*$/m', '', $sql);

        // Replace Prefix
        $sql = str_replace('fcms_', $this->prefix, $sql);

        // Split by semicolon and execute each statement
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($statements as $statement) {
            // Skip empty statements
            if (empty($statement)) {
                continue;
            }

            if (!$this->db->query($statement)) {
                $this->errors[] = "SQL Error: " . $this->db->error;
                return false;
            }
        }

        return true;
    }

    /**
     * Save site settings to database
     */
    private function saveSettings($siteName, $siteDesc, $domain)
    {
        $settings = [
            'site_name' => $siteName,
            'site_description' => $siteDesc,
            'site_domain' => $domain,
            'installed_at' => date('Y-m-d H:i:s'),
            'version' => App::VERSION
        ];

        $table = $this->prefix . 'settings';
        $stmt = $this->db->prepare("REPLACE INTO $table (setting_key, setting_value) VALUES (?, ?)");

        foreach ($settings as $key => $value) {
            $stmt->bind_param("ss", $key, $value);
            if (!$stmt->execute()) {
                $this->errors[] = "Failed to save setting: $key";
                return false;
            }
        }

        $stmt->close();
        return true;
    }

    /**
     * Create admin user account
     */
    private function createAdminUser($username, $email, $password)
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $table = $this->prefix . 'users';

        $stmt = $this->db->prepare(
            "REPLACE INTO $table (username, email, password_hash, role, status) VALUES (?, ?, ?, 'admin', 'active')"
        );

        $stmt->bind_param("sss", $username, $email, $passwordHash);

        if (!$stmt->execute()) {
            $this->errors[] = "Failed to create admin user: " . $stmt->error;
            $stmt->close();
            return false;
        }

        $stmt->close();
        return true;
    }

    /**
     * Generate .env configuration file
     */
    private function generateEnvFile($data)
    {
        $envPath = __DIR__ . '/../.env';

        $envContent = "# Fritsion CMS Configuration\n";
        $envContent .= "# Generated: " . date('Y-m-d H:i:s') . "\n\n";
        $envContent .= "APP_NAME=\"{$data['site_name']}\"\n";
        $envContent .= "APP_ENV=production\n";
        $envContent .= "APP_DEBUG=false\n";
        $envContent .= "APP_URL=http://{$data['domain']}\n\n";
        $envContent .= "DB_CONNECTION=mysql\n";
        $envContent .= "DB_HOST={$data['db_host']}\n";
        $envContent .= "DB_PORT=3306\n";
        $envContent .= "DB_DATABASE={$data['db_name']}\n";
        $envContent .= "DB_USERNAME={$data['db_user']}\n";
        $envContent .= "DB_PASSWORD={$data['db_pass']}\n";
        $envContent .= "DB_PREFIX={$this->prefix}\n\n"; // Added prefix
        $lang = $_SESSION['lang'] ?? 'nl';
        $envContent .= "DEFAULT_LANGUAGE=$lang\n";

        if (file_put_contents($envPath, $envContent) === false) {
            $this->errors[] = "Failed to write .env file";
            return false;
        }

        return true;
    }

    /**
     * Create install.lock file to prevent reinstallation
     */
    private function lockInstaller()
    {
        $lockPath = __DIR__ . '/../install.lock';

        $lockContent = "Fritsion CMS Installation Lock\n";
        $lockContent .= "Installed: " . date('Y-m-d H:i:s') . "\n";
        $lockContent .= "Version: " . App::VERSION . "\n";
        $lockContent .= "\nDO NOT DELETE THIS FILE\n";
        $lockContent .= "Deleting this file will allow the installer to run again.\n";

        if (file_put_contents($lockPath, $lockContent) === false) {
            $this->errors[] = "Failed to create install.lock file";
            return false;
        }

        return true;
    }

    /**
     * Get all errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Close database connection
     */
    public function __destruct()
    {
        if ($this->db) {
            $this->db->close();
        }
    }
}
