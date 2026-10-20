-- =====================================================
-- STEP 2: DATABASE
-- 1. Create an empty database first:
--    XAMPP: phpMyAdmin → New → name it "auth_system"
--    InfinityFree: control panel → MySQL Databases
-- 2. Select that database in phpMyAdmin, open the SQL tab,
--    paste this file and click Go. It creates the users table.
-- =====================================================


CREATE TABLE IF NOT EXISTS users (
    -- PRIMARY KEY + AUTO_INCREMENT:
    -- MySQL gives every user a unique id (1, 2, 3...) automatically.
    -- Two users can NEVER have the same id.
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(100) NOT NULL,

    -- UNIQUE: the database itself refuses a duplicate email.
    -- Even if our PHP check is skipped, MySQL still blocks it.
    email VARCHAR(255) NOT NULL UNIQUE,

    -- We store the HASH, never the real password.
    -- 255 chars leaves room for stronger hashing algorithms in future.
    password_hash VARCHAR(255) NOT NULL,

    -- Brute-force protection: count wrong passwords and lock the account.
    failed_attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    locked_until DATETIME NULL DEFAULT NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
