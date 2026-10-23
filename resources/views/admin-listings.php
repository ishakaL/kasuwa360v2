<?php $title = 'Listing moderation | Kasuwa360'; include APP_ROOT . '/resources/views/partials/header.php'; ?>
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
            <div class="page-head"><h1>Listing moderation</h1></div>
            <div class="card section-block">
                <table>
                    <thead>
                        <tr><th>Title</th><th>Shop</th><th>Approval</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listings as $listing): ?>
                            <tr>
                                <td><?= e($listing['title']); ?></td>
                                <td><?= e($listing['shop_name']); ?></td>
                                <td><?= e($listing['approval_status']); ?></td>
                                <td>
                                    <form method="post" action="<?= APP_URL ?>/admin/approve-listing" style="display:inline;">
                                        <?= csrfField(); ?>
                                        <input type="hidden" name="listing_id" value="<?= (int) $listing['id']; ?>">
                                        <button type="submit" class="button button-secondary">Approve</button>
                                    </form>
                                    <form method="post" action="<?= APP_URL ?>/admin/reject-listing" style="display:inline;">
                                        <?= csrfField(); ?>
                                        <input type="hidden" name="listing_id" value="<?= (int) $listing['id']; ?>">
                                        <button type="submit" class="button button-secondary danger">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</main>
<?php include APP_ROOT . '/resources/views/partials/footer.php'; ?>
