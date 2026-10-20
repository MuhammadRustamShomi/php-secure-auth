<?php
// =====================================================
// CONFIG TEMPLATE
// 1. Copy this file and rename the copy to: config.php
// 2. Put your real database details in config.php
//
// config.php is listed in .gitignore, so your real
// passwords are NEVER uploaded to GitHub.
// =====================================================

// ---- Local (XAMPP) default values ----
const DB_HOST = "localhost";
const DB_NAME = "auth_system";
const DB_USER = "root";
const DB_PASS = "";

// ---- InfinityFree example (find yours in the control panel → MySQL Databases) ----
// const DB_HOST = "sql123.infinityfree.com";
// const DB_NAME = "if0_12345678_auth_system";
// const DB_USER = "if0_12345678";
// const DB_PASS = "your-vPanel-password";

// true  = show a "public demo" warning on the login and register pages
// false = normal site
const DEMO_MODE = false;
