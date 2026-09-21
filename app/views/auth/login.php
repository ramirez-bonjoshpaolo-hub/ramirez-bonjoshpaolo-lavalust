<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $escape($title) ?></title>
    <link rel="stylesheet" href="/public/css/ramirez-labs.css">
</head>
<body>
<main class="auth-page">
    <section class="auth-story">
        <a class="brand" href="<?= $escape(site_url()) ?>"><span class="brand-mark">BR</span>Ramirez / LavaLust</a>
        <div>
            <p class="kicker">Laboratory Exercise 05</p>
            <h1 class="display-title">Stock, sorted.</h1>
            <p class="lead">Track Nike footwear, apparel, and accessories in one secure inventory workspace.</p>
        </div>
        <p class="meta">Protected inventory workspace</p>
    </section>
    <section class="auth-form-wrap">
        <div class="form-card">
            <p class="kicker" style="color: var(--indigo)">Welcome back</p>
            <h2>Sign in to inventory</h2>
            <p>Use the credentials configured through your Render environment variables.</p>
            <?php if (!empty($error)): ?><p class="error-box"><?= $escape($error) ?></p><?php endif; ?>
            <?php if (!empty($notice)): ?><p class="notice"><?= $escape($notice) ?></p><?php endif; ?>
            <form method="post" action="<?= $escape(site_url('login')) ?>">
                <div class="field"><label for="username">Username</label><input id="username" name="username" value="<?= $escape($username ?? '') ?>" autocomplete="username" required></div>
                <div class="field"><label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required></div>
                <div class="form-actions"><button class="btn btn-primary" type="submit">Open workspace</button></div>
            </form>
        </div>
    </section>
</main>
</body>
</html>
