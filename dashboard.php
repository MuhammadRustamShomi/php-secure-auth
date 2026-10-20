<?php
// =====================================================
// STEP 6: PROTECTED PAGE
// Only logged-in users can see this page.
// =====================================================
require "functions.php";
require "db.php";

require_login();   // 🛡️ the guard: not logged in → sent to login.php

// Load fresh user data from the database (the session only stores the id)
$stmt = $pdo->prepare("SELECT name, email, created_at FROM users WHERE id = ?");
$stmt->execute([$_SESSION["user_id"]]);
$user = $stmt->fetch();

// If the user was deleted from the database meanwhile, log them out
if (!$user) {
    logout_user();
    redirect("login.php");
}

$title = "Dashboard";
require "header.php";
?>
<h1>Welcome, <?= e($user["name"]) ?>! 🎉</h1>

<p>You are logged in as <strong><?= e($user["email"]) ?></strong>.</p>
<p>Member since: <?= e(date("d M Y", strtotime($user["created_at"]))) ?></p>

<!--
    Logout is a POST form with a CSRF token (not a simple link).
    SECURITY: a plain link could be triggered by another website
    (e.g. hidden in an <img> tag) to log users out without asking.
-->
<form method="post" action="logout.php">
    <?= csrf_field() ?>
    <button type="submit" class="secondary">Log out</button>
</form>
<?php require "footer.php"; ?>
