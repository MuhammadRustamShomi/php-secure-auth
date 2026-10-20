<?php
// =====================================================
// STEP 4: REGISTER
// =====================================================
require "functions.php";
require "db.php";

// Already logged in? No need to register again.
if (!empty($_SESSION["user_id"])) {
    redirect("dashboard.php");
}

$errors = [];
$name   = "";
$email  = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // SECURITY CHECK 1 (CSRF): was this form really sent from our page?
    if (!verify_csrf()) {
        $errors[] = "Your form expired. Please try again.";
    } else {
        // Read the inputs. ?? "" avoids "undefined index" warnings.
        // trim() removes spaces around the text.
        $name     = trim($_POST["name"] ?? "");
        $email    = strtolower(trim($_POST["email"] ?? "")); // Ali@Mail.com and ali@mail.com = same user
        $password = $_POST["password"] ?? "";                // never trim passwords: spaces are allowed
        $confirm  = $_POST["confirm_password"] ?? "";

        // SECURITY CHECK 2 (validation): never trust user input.
        // The HTML "required" attribute can be removed by anyone in the browser,
        // so the SERVER must check everything again.
        if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
            $errors[] = "Name must be 2 to 100 characters.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
            $errors[] = "Please enter a valid email address.";
        }
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters.";
        }
        if (strlen($password) > 72) {
            // bcrypt (PHP's default hash) only uses the first 72 bytes
            $errors[] = "Password must be 72 characters or fewer.";
        }
        if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one letter and one number.";
        }
        if ($password !== $confirm) {   // strict !== , remember Lesson 1.2 😉
            $errors[] = "Passwords do not match.";
        }

        // Friendly duplicate check (gives a nice message before inserting).
        // SECURITY CHECK 3 (SQL injection): prepared statement with a placeholder (?).
        // The email is sent separately from the SQL, so it can never change the query.
        if (!$errors) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = "This email is already registered. Try logging in.";
            }
        }

        // Everything OK → save the user
        if (!$errors) {
            // SECURITY CHECK 4 (password hashing):
            // password_hash() adds a random "salt" and scrambles the password one-way.
            // Even if the database is stolen, the real passwords stay unknown.
            $hash = password_hash($password, PASSWORD_DEFAULT);

            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)"
                );
                $stmt->execute([$name, $email, $hash]);

                flash("🎉 Account created! You can log in now.");
                redirect("login.php");
            } catch (PDOException $e) {
                // Error code 23000 = the UNIQUE rule in MySQL blocked a duplicate email.
                // This catches the rare case where two people register the same email
                // at the exact same moment — the DATABASE is the final guard.
                if ($e->getCode() === "23000") {
                    $errors[] = "This email is already registered. Try logging in.";
                } else {
                    error_log("Register failed: " . $e->getMessage());
                    $errors[] = "Something went wrong. Please try again.";
                }
            }
        }
    }
}

$title = "Register";
require "header.php";
?>
<h1>Create account</h1>

<?php foreach ($errors as $error): ?>
    <p class="error"><?= e($error) ?></p>
<?php endforeach; ?>

<form method="post" novalidate>
    <?= csrf_field() ?>

    <label>Name
        <input type="text" name="name" value="<?= e($name) ?>" maxlength="100" required>
    </label>

    <label>Email
        <input type="email" name="email" value="<?= e($email) ?>" maxlength="255" required>
    </label>

    <label>Password <small>(8+ characters, a letter and a number)</small>
        <input type="password" name="password" minlength="8" maxlength="72" required>
    </label>

    <label>Confirm password
        <input type="password" name="confirm_password" required>
    </label>

    <button type="submit">Register</button>
</form>

<p>Already have an account? <a href="login.php">Log in</a></p>
<?php require "footer.php"; ?>
