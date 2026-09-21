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
<main class="page-shell">
    <nav class="site-nav">
        <a class="brand" href="<?= $escape(site_url('products')) ?>"><span class="brand-mark">BR</span>Ramirez / Inventory</a>
        <div class="nav-links"><a class="active" href="<?= $escape(site_url('products')) ?>">Products</a></div>
    </nav>

    <?php if (!empty($notice)): ?><p class="notice"><?= $escape($notice) ?></p><?php endif; ?>
    <?php if (!empty($error)): ?><p class="error-box"><?= $escape($error) ?></p><?php endif; ?>

    <section class="table-card">
        <header class="table-heading">
            <div><p class="kicker">Laboratory Exercise 05</p><h1><?= $escape($title) ?></h1><p>Signed in as <?= $escape($username) ?> · <?= count($products ?? []) ?> products</p></div>
            <div class="action-row"><a class="btn btn-primary" href="<?= $escape(site_url('products/create')) ?>">+ Add product</a><a class="btn btn-ghost" href="<?= $escape(site_url('logout')) ?>">Log out</a></div>
        </header>
        <div class="table-wrap">
            <table>
                <thead><tr><th>ID</th><th>Product</th><th>Description</th><th>Price</th><th>Qty.</th><th>Created</th><th>Actions</th></tr></thead>
                <tbody>
                <?php if (empty($products)): ?>
                    <tr><td class="empty-state" colspan="7">Your inventory is empty. Add the first product to begin.</td></tr>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= $escape($product['id']) ?></td>
                            <td><strong><?= $escape($product['product_name']) ?></strong></td>
                            <td><?= $escape($product['description']) ?></td>
                            <td class="price">₱<?= $escape(number_format((float) $product['price'], 2)) ?></td>
                            <td><?= $escape($product['quantity']) ?></td>
                            <td class="meta"><?= $escape($product['created_at']) ?></td>
                            <td><div class="action-row">
                                <a class="btn btn-secondary" href="<?= $escape(site_url('products/edit/' . (int) $product['id'])) ?>">Edit</a>
                                <form method="post" action="<?= $escape(site_url('products/delete/' . (int) $product['id'])) ?>" onsubmit="return confirm('Delete this product permanently?');"><button class="btn btn-danger" type="submit">Delete</button></form>
                            </div></td>
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
