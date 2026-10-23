<?php $title = 'Categories | Kasuwa360'; include APP_ROOT . '/resources/views/partials/header.php'; ?>
<main class="page-shell">
    <div class="container dashboard-layout">
        <aside class="sidebar card">
            <h3>Admin menu</h3>
            <ul>
                <li><a href="<?= APP_URL ?>/admin/dashboard">Overview</a></li>
                <li><a href="<?= APP_URL ?>/admin/users">Users</a></li>
                <li><a href="<?= APP_URL ?>/admin/shops">Shops</a></li>
                <li><a href="<?= APP_URL ?>/admin/listings">Listings</a></li>
                <li><a href="<?= APP_URL ?>/admin/categories">Categories</a></li>
            </ul>
        </aside>
        <section class="dashboard-main">
            <div class="page-head"><h1>Categories</h1></div>
            <div class="card section-block">
                <form method="post" action="<?= APP_URL ?>/admin/category/create">
                    <?= csrfField(); ?>
                    <label>
                        New category name
                        <input type="text" name="name" required>
                    </label>
                    <button type="submit" class="button button-primary">Create category</button>
                </form>
                <ul class="mini-list">
                    <?php foreach ($categories as $category): ?>
                        <li><?= e($category['name']); ?> • <?= e($category['slug']); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
    </div>
</main>
<?php include APP_ROOT . '/resources/views/partials/footer.php'; ?>
