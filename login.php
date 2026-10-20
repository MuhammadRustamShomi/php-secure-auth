<?php
// =====================================================
// STEP 5: LOGIN
// =====================================================
require "functions.php";
require "db.php";

if (!empty($_SESSION["user_id"])) {
    redirect("dashboard.php");
}

$error = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // SECURITY CHECK 1 (CSRF)
    if (!verify_csrf()) {
        $error = "Your form expired. Please try again.";
    } else {
        $email    = strtolower(trim($_POST["email"] ?? ""));
        $password = $_POST["password"] ?? "";

        // SECURITY CHECK 2 (SQL injection): prepared statement.
        // We also ask MySQL whether the account is locked right now,
        // so PHP and MySQL time zones can never disagree.
        $stmt = $pdo->prepare(
            "SELECT id, name, password_hash, failed_attempts,
                    (locked_until IS NOT NULL AND locked_until > NOW()) AS is_locked
             FROM users WHERE email = ?"
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();   // array if found, false if not

        if ($user && $user["is_locked"]) {
            // SECURITY CHECK 3 (brute force): too many wrong passwords → locked.
            // Even the CORRECT password is refused until the lock ends.
            $error = "Too many failed attempts. Please try again in " . LOCK_MINUTES . " minutes.";

        } elseif ($user && password_verify($password, $user["password_hash"])) {
            // ✅ CORRECT PASSWORD

            // Reset the wrong-password counter
            $pdo->prepare("UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?")
                ->execute([$user["id"]]);

            // If PHP gets a stronger default hashing algorithm in future,
            // upgrade this user's stored hash automatically.
            if (password_needs_rehash($user["password_hash"], PASSWORD_DEFAULT)) {
                $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?")
                    ->execute([password_hash($password, PASSWORD_DEFAULT), $user["id"]]);
            }

            // SECURITY CHECK 4 (session fixation): give the user a brand-new session ID.
            // If an attacker planted a session ID before login, it becomes useless.
            session_regenerate_id(true);
            unset($_SESSION["csrf_token"]);        // fresh CSRF token for the new session

            $_SESSION["user_id"]       = $user["id"];
            $_SESSION["user_name"]     = $user["name"];
            $_SESSION["last_activity"] = time();

            redirect("dashboard.php");

        } else {
            // ❌ WRONG EMAIL OR PASSWORD

            if ($user) {
                // Count the failed attempt; lock the account after MAX_LOGIN_ATTEMPTS
                $attempts = $user["failed_attempts"] + 1;

                if ($attempts >= MAX_LOGIN_ATTEMPTS) {
                    $pdo->prepare(
                        "UPDATE users SET failed_attempts = 0,
                                          locked_until = NOW() + INTERVAL ? MINUTE
                         WHERE id = ?"
                    )->execute([LOCK_MINUTES, $user["id"]]);
                } else {
                    $pdo->prepare("UPDATE users SET failed_attempts = ? WHERE id = ?")
                        ->execute([$attempts, $user["id"]]);
                }
            } else {
                // SECURITY CHECK 5 (timing attack): when the email doesn't exist,
                // still run password_verify on a dummy hash, so the response takes
                // the same time. Otherwise attackers could guess which emails exist
                // by measuring how fast the page answers.
                password_verify($password, password_hash("dummy-password", PASSWORD_DEFAULT));
            }

            // SECURITY CHECK 6 (user enumeration): the SAME vague message for
            // "email not found" and "wrong password", so attackers learn nothing.
            $error = "Wrong email or password.";
        }
    }
}

$title = "Login";
require "header.php";
?>
<h1>Log in</h1>

<?php if ($error): ?>
    <p class="error"><?= e($error) ?></p>
<?php endif; ?>

<form method="post">
    <?= csrf_field() ?>

    <label>Email
        <input type="email" name="email" value="<?= e($email) ?>" required autofocus>
    </label>

    <label>Password
        <input type="password" name="password" required>
    </label>

    <button type="submit">Log in</button>
</form>

<p>New here? <a href="register.php">Create an account</a></p>
<?php require "footer.php"; ?>
