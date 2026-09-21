<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$home_url = $escape(site_url('student'));
$profile_url = $escape(site_url('student/profile'));
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
<main class="page-shell">
    <nav class="site-nav" aria-label="Student navigation">
        <a class="brand" href="<?= $home_url ?>"><span class="brand-mark">BR</span>Ramirez / Field Notes</a>
        <div class="nav-links">
            <a class="active" href="<?= $home_url ?>">Overview</a>
            <a href="<?= $profile_url ?>">Profile</a>
        </div>
    </nav>

    <?php if (!empty($notice)): ?><p class="notice"><?= $escape($notice) ?></p><?php endif; ?>

    <section class="hero-grid">
        <div class="hero-panel">
            <p class="kicker">LavaLust Laboratory 03</p>
            <h1 class="display-title">Student<br>field notes.</h1>
            <p class="lead">A personalized student workspace exploring routes, controllers, views, and protected middleware in one clean request flow.</p>
        </div>

        <article class="card">
            <p class="kicker" style="color: var(--indigo)">Student information</p>
            <h2><?= $escape($student['name']) ?></h2>
            <dl class="details">
                <div><dt>Student ID</dt><dd><?= $escape($student['student_id']) ?></dd></div>
                <div><dt>Course</dt><dd><?= $escape($student['course']) ?></dd></div>
                <div><dt>Year level</dt><dd><?= $escape($student['year_level']) ?></dd></div>
                <div><dt>Section</dt><dd><?= $escape($student['section']) ?></dd></div>
                <div><dt>Email</dt><dd><?= $escape($student['email']) ?></dd></div>
            </dl>
            <div class="form-actions"><a class="btn btn-primary" href="<?= $profile_url ?>">View protected profile</a></div>
        </article>
    </section>
</main>
</body>
</html>
