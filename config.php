<?php
/**
 * Configuration & Database Connection
 * Supports both SQLite (default, zero-setup for local & cPanel) and MySQL.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -------------------------------------------------------------
// Database Configuration
// -------------------------------------------------------------
// Options: 'sqlite' or 'mysql'
define('DB_DRIVER', 'sqlite'); 

// SQLite Settings
define('SQLITE_PATH', __DIR__ . '/database/shop.sqlite');

// MySQL Settings (used only when DB_DRIVER === 'mysql')
define('DB_HOST', 'localhost');
define('DB_NAME', 'lipoma_shop');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Get Database Connection (PDO)
 */
function get_db(): PDO {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    try {
        if (DB_DRIVER === 'sqlite') {
            $db_dir = dirname(SQLITE_PATH);
            if (!is_dir($db_dir)) {
                mkdir($db_dir, 0755, true);
            }
            $is_new = !file_exists(SQLITE_PATH);
            $pdo = new PDO('sqlite:' . SQLITE_PATH);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            if ($is_new || filesize(SQLITE_PATH) === 0) {
                init_sqlite_tables($pdo);
            }
        } else {
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
        }
    } catch (PDOException $e) {
        die('Database Connection Error: ' . htmlspecialchars($e->getMessage()));
    }

    return $pdo;
}

/**
 * Initialize SQLite Database Tables & Seed Defaults
 */
function init_sqlite_tables(PDO $pdo): void {
    // Orders Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_number TEXT UNIQUE,
        customer_name TEXT NOT NULL,
        phone TEXT NOT NULL,
        address TEXT NOT NULL,
        package_name TEXT NOT NULL,
        package_price REAL NOT NULL,
        quantity INTEGER NOT NULL DEFAULT 1,
        shipping_area TEXT NOT NULL,
        shipping_cost REAL NOT NULL,
        total_amount REAL NOT NULL,
        note TEXT,
        status TEXT NOT NULL DEFAULT 'pending',
        ip_address TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Settings Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        setting_key TEXT PRIMARY KEY,
        setting_value TEXT
    )");

    // Admins Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password_hash TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Seed Default Settings
    $default_settings = [
        'site_title'           => 'সাউথ মুন লাইপোমা রিমুভাল স্প্রে | 100% অরিজিনাল',
        'product_name'         => 'South Moon Lipoma Removal Spray',
        'hotline'              => '01872307272',
        'whatsapp'             => '01872307272',
        'package_1_title'      => 'Buy 2 Get 1 Free (৩টি স্প্রে)',
        'package_1_price'      => '1580',
        'package_1_regular'    => '2370',
        'package_1_badge'      => 'সেরা অফার (Buy 2 Get 1)',
        'package_2_title'      => '১টি স্প্রে',
        'package_2_price'      => '790',
        'package_2_regular'    => '990',
        'package_2_badge'      => 'স্ট্যান্ডার্ড প্যাক',
        'delivery_dhaka'       => '70',
        'delivery_outside'     => '130',
        'meta_pixel_id'        => '',
        'meta_pixel_active'    => '0',
        'custom_header_script' => '',
        'custom_footer_script' => ''
    ];

    $stmt = $pdo->prepare("INSERT OR IGNORE INTO settings (setting_key, setting_value) VALUES (:key, :value)");
    foreach ($default_settings as $k => $v) {
        $stmt->execute([':key' => $k, ':value' => $v]);
    }

    // Seed Default Admin: admin / admin123456
    $default_hash = password_hash('admin123456', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO admins (username, password_hash) VALUES ('admin', :hash)");
    $stmt->execute([':hash' => $default_hash]);
}

/**
 * Get setting value with fallback
 */
function get_setting(string $key, string $default = ''): string {
    static $cached_settings = null;
    if ($cached_settings === null) {
        $db = get_db();
        try {
            $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
            $cached_settings = [];
            while ($row = $stmt->fetch()) {
                $cached_settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Exception $e) {
            $cached_settings = [];
        }
    }
    return $cached_settings[$key] ?? $default;
}

/**
 * Update or insert a setting value
 */
function set_setting(string $key, string $value): bool {
    $db = get_db();
    if (DB_DRIVER === 'sqlite') {
        $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value) 
                              ON CONFLICT(setting_key) DO UPDATE SET setting_value = :value");
    } else {
        $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value) 
                              ON DUPLICATE KEY UPDATE setting_value = :value");
    }
    return $stmt->execute([':key' => $key, ':value' => $value]);
}

/**
 * Security: Sanitize input strings
 */
function sanitize(string $data): string {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * CSRF Protection Helpers
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}

/**
 * Format BDT Currency
 */
function format_bdt(float|int|string $amount): string {
    return '৳' . number_format((float)$amount, 0);
}
