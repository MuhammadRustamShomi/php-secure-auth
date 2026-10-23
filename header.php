<?php
// Shared top part of every page. Set $title before including this file.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? "Auth System") ?> · PHP Auth Demo (student project)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="card">
<?php if (defined("DEMO_MODE") && DEMO_MODE): ?>
    <p class="demo">🧪 <strong>Public demo.</strong> Please use a test email and a password you don't use anywhere else.</p>
<?php endif; ?>
<?php if ($message = get_flash()): ?>
    <p class="flash"><?= e($message) ?></p>
<?php endif; ?>
