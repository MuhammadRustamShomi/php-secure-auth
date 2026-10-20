<?php
// =====================================================
// HELPER FUNCTIONS + SECURE SESSION SETUP
// Included at the top of every page.
// =====================================================

// ---------- Secure session cookie settings ----------
// Must run BEFORE session_start().
session_set_cookie_params([
    "lifetime" => 0,          // cookie is deleted when the browser closes
    "path"     => "/",
    "httponly" => true,       // SECURITY: JavaScript can't read the cookie (helps against XSS)
    "samesite" => "Strict",   // SECURITY: cookie isn't sent from other sites (helps against CSRF)
    "secure"   => !empty($_SERVER["HTTPS"]), // send only over HTTPS when the site uses it
]);
ini_set("session.use_strict_mode", "1"); // SECURITY: reject made-up session IDs
session_start();

// How long a logged-in user can be idle before being logged out (seconds)
const SESSION_TIMEOUT = 30 * 60;   // 30 minutes

// Brute-force protection settings
const MAX_LOGIN_ATTEMPTS = 5;      // wrong passwords allowed
const LOCK_MINUTES       = 15;     // how long the account stays locked

/**
 * Escape text before printing it in HTML.
 * SECURITY: stops XSS — if someone registers with the name
 * <script>alert('hack')</script>, it is shown as text, not run as code.
 */
function e(?string $text): string
{
    return htmlspecialchars($text ?? "", ENT_QUOTES, "UTF-8");
}

/**
 * Send the browser to another page and stop the script.
 * exit is important: without it, the rest of the page would still run!
 */
function redirect(string $page): never
{
    header("Location: $page");
    exit;
}

/**
 * Create (once per session) a secret random CSRF token.
 * We put it in a hidden field in every form.
 */
function csrf_token(): string
{
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32)); // 64 random hex characters
    }
    return $_SESSION["csrf_token"];
}

/**
 * Hidden input that carries the CSRF token inside a form.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * SECURITY (CSRF): check that a submitted form really came from OUR page.
 * A fake form on another website can't know the secret token.
 * hash_equals() compares safely (no timing leaks).
 */
function verify_csrf(): bool
{
    $sent = $_POST["csrf_token"] ?? "";
    return is_string($sent) && hash_equals(csrf_token(), $sent);
}

/**
 * Save a one-time message to show on the next page (e.g. "Account created!").
 */
function flash(string $message): void
{
    $_SESSION["flash"] = $message;
}

/**
 * Get the one-time message and delete it, so it shows only once.
 */
function get_flash(): string
{
    $message = $_SESSION["flash"] ?? "";
    unset($_SESSION["flash"]);
    return $message;
}

/**
 * Guard for protected pages: call this at the top of any page
 * that only logged-in users may see.
 */
function require_login(): void
{
    if (empty($_SESSION["user_id"])) {
        flash("Please log in first.");
        redirect("login.php");
    }

    // SECURITY: auto-logout after SESSION_TIMEOUT of inactivity
    if (time() - ($_SESSION["last_activity"] ?? 0) > SESSION_TIMEOUT) {
        logout_user();
        session_start();               // fresh session just to carry the message
        flash("You were logged out after 30 minutes of inactivity.");
        redirect("login.php");
    }
    $_SESSION["last_activity"] = time(); // user is active, reset the timer
}

/**
 * Fully log the user out: clear data, delete the cookie, destroy the session.
 */
function logout_user(): void
{
    $_SESSION = [];                                   // 1. empty the session data
    if (ini_get("session.use_cookies")) {             // 2. delete the session cookie
        $p = session_get_cookie_params();
        setcookie(session_name(), "", time() - 3600, $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
    }
    session_destroy();                                // 3. destroy it on the server
}
