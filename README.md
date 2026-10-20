# PHP Secure Authentication System 🔐

A complete, secure login system in plain PHP + MySQL: **register, login, protected dashboard, logout**.
Every security check is explained with comments inside the code, so it doubles as learning material.

🌐 **Live demo:** _add your InfinityFree link here_
> ⚠️ This is a public demo. Please register with a **test email** and a password you don't use anywhere else.

![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-PDO-4479A1?logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-green)

## 🛡️ Security features

| # | Attack | Protection | Where |
|---|---|---|---|
| 1 | Duplicate email / user id | `UNIQUE` email + `AUTO_INCREMENT PRIMARY KEY` in MySQL, plus a PHP check | `database.sql`, `register.php` |
| 2 | Stolen passwords | `password_hash()` / `password_verify()` (bcrypt + salt) | `register.php`, `login.php` |
| 3 | SQL injection | PDO **prepared statements** with `?` placeholders, real prepares | all queries, `db.php` |
| 4 | XSS (injected JavaScript) | `htmlspecialchars()` via `e()` on every output + `httponly` cookie | `functions.php` |
| 5 | CSRF (fake forms from other sites) | Secret token in every form + `SameSite=Strict` cookie | `functions.php`, all forms |
| 6 | Session fixation | `session_regenerate_id(true)` after login + strict mode | `login.php` |
| 7 | Brute-force password guessing | Account locked 15 min after 5 wrong passwords | `login.php` |
| 8 | User enumeration | Same "Wrong email or password" message + dummy hash timing | `login.php` |
| 9 | Forgotten open sessions | Auto-logout after 30 minutes idle | `functions.php` |
| 10 | Leaking error details | Real errors go to the server log, users see a friendly message | `db.php`, `register.php` |
| 11 | Bad input | Server-side validation of name, email, password strength | `register.php` |
| 12 | Leaked credentials on GitHub | Real DB password lives in `config.php`, which is git-ignored | `.gitignore` |

## 📁 Files

| File | What it does |
|---|---|
| `database.sql` | Creates the `users` table |
| `config.example.php` | Template for your database settings (copy → `config.php`) |
| `db.php` | Connects to MySQL with PDO |
| `functions.php` | Secure session setup + helpers: escaping, CSRF, login guard, logout |
| `register.php` | Sign-up form with validation and password hashing |
| `login.php` | Login with brute-force lock |
| `dashboard.php` | Protected page, only for logged-in users |
| `logout.php` | Safely ends the session |
| `header.php`, `footer.php`, `style.css` | Shared page layout and design |
| `index.php` | Sends visitors to login or dashboard |

## ⚙️ Run it locally (Windows + XAMPP)

1. Open the **XAMPP Control Panel** and **Start** Apache and MySQL.
2. Copy the `auth-system` folder into `C:\xampp\htdocs\`.
3. In the folder, copy `config.example.php` and rename the copy to **`config.php`**. The default values already work with XAMPP.
4. Open `http://localhost/phpmyadmin` → **New** → create a database named `auth_system`.
5. Select `auth_system` → **SQL** tab → paste everything from `database.sql` → **Go**.
6. Open `http://localhost/auth-system/` 🎉

## 🚀 Deploy free on InfinityFree

1. Sign up at [infinityfree.com](https://www.infinityfree.com) and create a hosting account (pick a free subdomain).
2. Control panel → **MySQL Databases** → create a database. Note the **host, database name, username, and password** shown there.
3. Open **phpMyAdmin** from the control panel → select your database → **SQL** tab → paste `database.sql` → **Go**.
4. Create `config.php` from `config.example.php` with your InfinityFree database details, and set `DEMO_MODE` to `true`.
5. Open the **File Manager** (or use FileZilla) → go into `htdocs` → upload all project files, including `config.php`.
6. Control panel → **Free SSL Certificates** → enable HTTPS for your domain.
7. Visit your site and run through the test checklist below. ✅

## ✅ Test checklist

- [ ] Register a new user → "Account created!" message
- [ ] Register again with the **same email** → "already registered" error
- [ ] Try `ALI@mail.com` after `ali@mail.com` → also blocked (emails are lower-cased)
- [ ] Weak password (`abc`) → validation errors
- [ ] Open `dashboard.php` while logged out → sent back to login
- [ ] Register with the name `<script>alert(1)</script>` → it shows as plain text, no popup
- [ ] Log out → can't go back to the dashboard with the browser's Back button + refresh
- [ ] In phpMyAdmin, check the `password_hash` column → no real passwords visible
- [ ] Wrong password 5 times → account locked for 15 minutes _(do this last)_

## 🗺️ Roadmap

- [ ] Email verification
- [ ] Forgot / reset password
- [ ] "Remember me" option
- [ ] User roles (admin / user)

## 👤 Author

**Muhammad Rustam**, AI Developer & Instructor at [AIBYTEC](https://aibytec.com)
GitHub: [@MuhammadRustamShomi](https://github.com/MuhammadRustamShomi)

Licensed under the [MIT License](LICENSE). Feel free to use it for learning.
