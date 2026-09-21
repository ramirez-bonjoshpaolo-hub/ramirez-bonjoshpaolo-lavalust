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
    <title><?= $escape($title ?? 'Community Directory') ?></title>
    <link rel="stylesheet" href="/public/css/ramirez-labs.css">
</head>
<body>
<main class="page-shell">
    <nav class="site-nav">
        <a class="brand" href="<?= $escape(site_url('student')) ?>"><span class="brand-mark">BR</span>Ramirez / Data Lab</a>
        <div class="nav-links"><a class="active" href="<?= $escape(site_url('users')) ?>">Users</a></div>
    </nav>

    <section class="table-card">
        <header class="table-heading">
            <div><p class="kicker">LavaLust Laboratory 04</p><h1><?= $escape($title ?? 'Community Directory') ?></h1><p>Live records retrieved through UsersModel::all().</p></div>
            <span class="btn btn-primary"><?= count($users ?? []) ?> records</span>
        </header>
        <?php if (empty($users)): ?>
            <p class="empty-state">No users found. Connect the application to your Aiven MySQL database and add sample records.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>ID</th><th>First name</th><th>Last name</th><th>Email</th><th>Username</th></tr></thead>
                    <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $escape($user['id']) ?></td>
                            <td><?= $escape($user['firstname']) ?></td>
                            <td><?= $escape($user['lastname']) ?></td>
                            <td><?= $escape($user['email']) ?></td>
                            <td><strong><?= $escape($user['username']) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
