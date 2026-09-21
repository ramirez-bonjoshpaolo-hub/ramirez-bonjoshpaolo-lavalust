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
        <div class="nav-links"><a href="<?= $home_url ?>">Overview</a><a class="active" href="<?= $profile_url ?>">Profile</a></div>
    </nav>

    <section class="profile-layout table-card">
        <aside class="profile-rail">
            <div class="portrait" aria-label="Bon Ramirez initials">BR</div>
            <p class="rail-note">Access confirmed by StudentMiddleware.<br><br>Route: /student/profile</p>
        </aside>
        <article class="profile-body">
            <p class="kicker" style="color: var(--indigo)">Verified student profile</p>
            <h1 class="display-title"><?= $escape($student['name']) ?></h1>
            <p class="lead">This page becomes available only after the student session has passed the middleware access check.</p>
            <dl class="detail-grid details">
                <div><dt>Student ID</dt><dd><?= $escape($student['student_id']) ?></dd></div>
                <div><dt>Course</dt><dd><?= $escape($student['course']) ?></dd></div>
                <div><dt>Year level</dt><dd><?= $escape($student['year_level']) ?></dd></div>
                <div><dt>Section</dt><dd><?= $escape($student['section']) ?></dd></div>
                <div><dt>Email</dt><dd><?= $escape($student['email']) ?></dd></div>
                <div><dt>Built with</dt><dd>LavaLust MVC</dd></div>
            </dl>
        </article>
    </section>
</main>
</body>
</html>
