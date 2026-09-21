<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$product = $product ?? [];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $escape($title) ?> | Ramirez Inventory</title>
    <link rel="stylesheet" href="/public/css/ramirez-labs.css">
</head>
<body>
<main class="page-shell">
    <nav class="site-nav">
        <a class="brand" href="<?= $escape(site_url('products')) ?>"><span class="brand-mark">BR</span>Ramirez / Inventory</a>
        <div class="nav-links"><a href="<?= $escape(site_url('products')) ?>">Back to products</a></div>
    </nav>

    <section class="auth-page form-card product-form-layout">
        <div class="auth-story product-form-story">
            <p class="kicker"><?= $editing ? 'Update inventory record' : 'Create inventory record' ?></p>
            <h1 class="display-title product-form-title"><?= $editing ? 'Refine the details.' : 'Add something useful.' ?></h1>
            <p class="lead">Keep names clear, quantities current, and pricing accurate for the product table.</p>
        </div>
        <div class="auth-form-wrap product-form-editor">
            <div class="product-form-fields">
                <p class="kicker" style="color: var(--indigo)">Product editor</p>
                <h2><?= $escape($title) ?></h2>
                <?php if (!empty($errors)): ?><div class="error-box"><ul><?php foreach ($errors as $error): ?><li><?= $escape($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
                <form method="post" action="<?= $escape($form_action) ?>">
                    <div class="field"><label for="product_name">Product name</label><input id="product_name" name="product_name" maxlength="100" value="<?= $escape($product['product_name'] ?? '') ?>" required></div>
                    <div class="field"><label for="description">Description</label><textarea id="description" name="description" required><?= $escape($product['description'] ?? '') ?></textarea></div>
                    <div class="detail-grid" style="margin-top: 0">
                        <div class="field"><label for="price">Price (PHP)</label><input id="price" name="price" type="number" min="0" step="0.01" value="<?= $escape($product['price'] ?? '') ?>" required></div>
                        <div class="field"><label for="quantity">Quantity</label><input id="quantity" name="quantity" type="number" min="0" step="1" value="<?= $escape($product['quantity'] ?? '') ?>" required></div>
                    </div>
                    <div class="form-actions"><button class="btn btn-primary" type="submit"><?= $editing ? 'Update product' : 'Save product' ?></button><a class="btn btn-ghost" href="<?= $escape(site_url('products')) ?>">Cancel</a></div>
                </form>
            </div>
        </div>
    </section>
</main>
</body>
</html>
