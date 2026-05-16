<?php
// === UPDATE THESE VALUES ===
define('DB_HOST', 'marinadb');           // Change to your MariaDB service name (e.g. 'mariadb', 'db', or host IP)
define('DB_NAME', 'login_test');
define('DB_USER', 'master_admin');      // Your DB user
define('DB_PASS', 'CHANGE_THIS_STRONG_ROOT_PASSWORD');
define('DB_CHARSET', 'utf8mb4');

function getDBConnection() {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . htmlspecialchars($e->getMessage()));
    }
}
