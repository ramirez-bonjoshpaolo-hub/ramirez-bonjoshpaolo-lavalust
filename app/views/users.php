<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

$escape = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $escape($page_title) ?> | LavaLust</title>
    <style>
        :root {
            color-scheme: dark;
            --background: #070b14;
            --panel: #111827;
            --panel-light: #172033;
            --line: #2a354a;
            --text: #eef2ff;
            --muted: #9aa7bd;
            --accent: #ff5a1f;
            --cyan: #22d3ee;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at top right, rgba(34, 211, 238, .12), transparent 34rem),
                var(--background);
            color: var(--text);
            font-family: Inter, "Segoe UI", sans-serif;
        }

        main {
            width: min(1120px, calc(100% - 32px));
            margin: 0 auto;
            padding: 48px 0 72px;
        }

        .eyebrow {
            margin: 0 0 12px;
            color: var(--cyan);
            font: 700 .78rem ui-monospace, monospace;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0;
            font-size: clamp(2.25rem, 6vw, 4.75rem);
            letter-spacing: -.05em;
        }

        .intro {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 24px;
            margin-bottom: 32px;
        }

        .intro p {
            max-width: 540px;
            margin: 14px 0 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .home-link {
            flex: none;
            padding: 11px 16px;
            border: 1px solid var(--line);
            border-radius: 12px;
            color: var(--text);
            text-decoration: none;
        }

        .home-link:hover { border-color: var(--cyan); color: var(--cyan); }

        .table-card {
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: rgba(17, 24, 39, .92);
            box-shadow: 0 24px 70px rgba(0, 0, 0, .28);
        }

        .table-scroll { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
        }

        th, td {
            padding: 17px 20px;
            text-align: left;
            border-bottom: 1px solid var(--line);
        }

        th {
            background: var(--panel-light);
            color: var(--muted);
            font-size: .76rem;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        tbody tr:last-child td { border-bottom: 0; }
        tbody tr:hover { background: rgba(34, 211, 238, .045); }
        td:first-child { color: var(--accent); font-weight: 800; }
        .username { color: var(--cyan); font-family: ui-monospace, monospace; }
        .empty { padding: 40px 20px; color: var(--muted); text-align: center; }

        @media (max-width: 720px) {
            main { padding-top: 30px; }
            .intro { align-items: flex-start; flex-direction: column; }
        }
    </style>
</head>
<body>
<main>
    <header class="intro">
        <div>
            <p class="eyebrow">Laboratory Exercise No. 4</p>
            <h1>User Management</h1>
            <p>Records retrieved from the Aiven MySQL database through the LavaLust model, controller, and view.</p>
        </div>
        <a class="home-link" href="<?= $escape(site_url()) ?>">Back to home</a>
    </header>

    <section class="table-card" aria-labelledby="users-heading">
        <div class="table-scroll">
            <table>
                <caption id="users-heading" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0)">Users</caption>
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Username</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($users === []): ?>
                    <tr><td class="empty" colspan="5">No users found.</td></tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $escape($user['id']) ?></td>
                            <td><?= $escape($user['firstname']) ?></td>
                            <td><?= $escape($user['lastname']) ?></td>
                            <td><?= $escape($user['email']) ?></td>
                            <td class="username"><?= $escape($user['username']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
</body>
</html>
