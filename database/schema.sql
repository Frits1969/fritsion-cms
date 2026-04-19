-- Fritsion CMS Database Schema
-- Version: 0.1.8
-- Prefix: fcms_

-- Settings Table: Site Configuration
DROP TABLE IF EXISTS fcms_settings;
CREATE TABLE IF NOT EXISTS fcms_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Users Table: Admin and User Accounts
DROP TABLE IF EXISTS fcms_users;
CREATE TABLE IF NOT EXISTS fcms_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'editor', 'author', 'subscriber') DEFAULT 'subscriber',
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sessions Table: Session Management (Optional - for future use)
DROP TABLE IF EXISTS fcms_sessions;
CREATE TABLE IF NOT EXISTS fcms_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    payload TEXT,
    last_activity INT,
    CONSTRAINT fk_fcms_user_sessions FOREIGN KEY (user_id) REFERENCES fcms_users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pages Table: Content Management
DROP TABLE IF EXISTS fcms_pages;
CREATE TABLE IF NOT EXISTS fcms_pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_homepage TINYINT(1) DEFAULT 0,
    template_id INT,
    INDEX idx_slug (slug),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Templates Table: Layout definitions
DROP TABLE IF EXISTS fcms_templates;
CREATE TABLE IF NOT EXISTS fcms_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    type ENUM('homepage', 'content') NOT NULL,
    layout_json LONGTEXT NOT NULL,
    preview_type VARCHAR(50) DEFAULT 'usps',
    icon VARCHAR(50) DEFAULT '📄',
    description TEXT,
    is_active TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default Templates
REPLACE INTO fcms_templates (name, type, layout_json, is_active, icon, preview_type) VALUES 
('Homepage', 'homepage', '{"header":{"sections":[{"type":"logo"},{"type":"menu"},{"type":"cta"}]},"main":{"rows":[{"columns":[{"type":"text","width":6},{"type":"image","width":6}]}]},"footer":{"sections":[{"type":"text"},{"type":"socials"}]}}', 1, '🏠', 'usps');

-- Themes Table: Visual Themes
DROP TABLE IF EXISTS fcms_themes;
CREATE TABLE IF NOT EXISTS fcms_themes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    is_default TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 0,
    settings_json LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_is_active (is_active),
    INDEX idx_is_default (is_default)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default Theme
REPLACE INTO fcms_themes (name, slug, is_default, is_active, settings_json) VALUES 
('Standaard', 'default', 1, 1, '{"colors":{"primary":"#1e40af","secondary":"#6b7280","accent":"#3b82f6","text":"#111827","link":"#2563eb","header_bg":"#ffffff","header_text":"#111827","footer_bg":"#1f2937","footer_text":"#f9fafb"},"background":{"type":"color","value":"#f3f4f6","image":""},"typography":{"body_font":"Inter, sans-serif","heading_font":"Inter, sans-serif","h1":{"size":"2.5rem","line_height":"1.2"},"h2":{"size":"2rem","line_height":"1.2"},"h3":{"size":"1.75rem","line_height":"1.2"},"p":{"size":"1rem","line_height":"1.5"}},"spacing":{"section_padding":"4rem 0","container_width":"1200px","border_radius":"0.5rem"}}');
