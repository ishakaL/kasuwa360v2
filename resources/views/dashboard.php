<?php $title = 'Vendor dashboard | Kasuwa360'; include APP_ROOT . '/resources/views/partials/header.php'; ?>
<main class="page-shell">
    <div class="container dashboard-layout">
        <aside class="sidebar card">
            <h3>Vendor area</h3>
            <ul>
                <li><a href="<?= APP_URL ?>/dashboard">Overview</a></li>
                <li><a href="<?= APP_URL ?>/vendor/shop">Shop profile</a></li>
                <li><a href="<?= APP_URL ?>/listing/new">Add listing</a></li>
            </ul>
        </aside>

        <section class="dashboard-main">
            <div class="page-head">
                <div>
                    <span class="eyebrow">Welcome</span>
                    <h1><?= e($user['full_name']); ?></h1>
                </div>
                <a href="<?= APP_URL ?>/listing/new" class="button button-primary">New listing</a>
            </div>

            <div class="stats-grid">
                <div class="stat-card card">
                    <span>Total listings</span>
                    <strong><?= count($listings); ?></strong>
                </div>
                <div class="stat-card card">
                    <span>Shop status</span>
                    <strong><?= $shop ? e($shop['approval_status']) : 'No shop'; ?></strong>
                </div>
            </div>

            <?php if (!$shop): ?>
                <div class="empty-state card">
                    <h3>No shop yet</h3>
                    <p>Create your shop profile to start listing products and services.</p>
                    <a href="<?= APP_URL ?>/vendor/shop" class="button button-primary">Create shop</a>
                </div>
            <?php else: ?>
                <div class="card section-block">
                    <h3>Shop overview</h3>
                    <p><strong><?= e($shop['shop_name']); ?></strong> • <?= e($shop['city'] ?: 'Unknown city'); ?>, <?= e($shop['state'] ?: 'Unknown state'); ?></p>
                    <p><?= e($shop['description'] ?: 'No description yet.'); ?></p>
                    <a href="<?= APP_URL ?>/shop/<?= e($shop['slug']); ?>" class="button button-secondary">Preview public shop</a>
                </div>
            <?php endif; ?>

            <div class="card section-block">
                <h3>Listings</h3>
                <?php if (empty($listings)): ?>
                    <p class="muted">You have no listings yet.</p>
                <?php else: ?>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($listings as $item): ?>
                                    <tr>
                                        <td><?= e($item['title']); ?></td>
                                        <td><?= e($item['type']); ?></td>
                                        <td><?= e($item['approval_status']); ?></td>
                                        <td><?= formatPrice($item['price']); ?></td>
                                        <td>
                                            <a href="<?= APP_URL ?>/listing/edit?id=<?= (int) $item['id']; ?>">Edit</a>
                                            <a href="<?= APP_URL ?>/listing/delete?id=<?= (int) $item['id']; ?>" onclick="return confirm('Delete this listing?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>
<?php include APP_ROOT . '/resources/views/partials/footer.php'; ?>
