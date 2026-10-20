<?php
// =====================================================
// STEP 3: DATABASE CONNECTION (PDO)
// Every page that needs the database includes this file.
// The real database details live in config.php (never uploaded to GitHub).
// =====================================================

if (!file_exists(__DIR__ . "/config.php")) {
    exit("Setup needed: copy config.example.php, rename the copy to config.php, and add your database details.");
}
require_once __DIR__ . "/config.php";

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            // Throw an exception when a query fails (instead of failing silently)
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // Return rows as associative arrays: $row["email"]
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // SECURITY: use REAL prepared statements in MySQL,
            // so user input is never mixed into the SQL text.
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    // SECURITY: never show the real error to visitors
    // (it can reveal your database name, user, or file paths).
    error_log("DB connection failed: " . $e->getMessage()); // goes to the server log
    exit("Sorry, we can't connect to the database right now. Please check config.php and that MySQL is running.");
}
