<?php $title = 'Admin dashboard | Kasuwa360'; include APP_ROOT . '/resources/views/partials/header.php'; ?>
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
            <div class="page-head">
                <div>
                    <span class="eyebrow">Platform overview</span>
                    <h1>Admin dashboard</h1>
                </div>
            </div>
            <div class="stats-grid">
                <div class="stat-card card"><span>Total users</span><strong><?= e($stats['total_users']); ?></strong></div>
                <div class="stat-card card"><span>Total vendors</span><strong><?= e($stats['total_vendors']); ?></strong></div>
                <div class="stat-card card"><span>Total shops</span><strong><?= e($stats['total_shops']); ?></strong></div>
                <div class="stat-card card"><span>Total listings</span><strong><?= e($stats['total_listings']); ?></strong></div>
                <div class="stat-card card"><span>Published listings</span><strong><?= e($stats['published_listings']); ?></strong></div>
                <div class="stat-card card"><span>Pending approvals</span><strong><?= e($stats['pending_listings']); ?></strong></div>
            </div>
            <div class="card section-block">
                <h3>Recent admin activity</h3>
                <ul class="mini-list">
                    <?php foreach ($recent as $item): ?>
                        <li><?= e($item['action']) ?> • <?= e($item['created_at']); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
    </div>
</main>
<?php include APP_ROOT . '/resources/views/partials/footer.php'; ?>
