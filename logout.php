<?php
// =====================================================
// STEP 7: LOGOUT
// =====================================================
require "functions.php";

// Only accept a POST request with a valid CSRF token
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !verify_csrf()) {
    redirect("dashboard.php");
}

logout_user();        // clear data + delete cookie + destroy session (see functions.php)

session_start();      // start a fresh, empty session just to carry the goodbye message
flash("👋 You have been logged out.");
redirect("login.php");
