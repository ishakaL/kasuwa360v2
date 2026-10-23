<?php $title = 'User management | Kasuwa360'; include APP_ROOT . '/resources/views/partials/header.php'; ?>
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
            <div class="page-head"><h1>User management</h1></div>
            <div class="card section-block">
                <table>
                    <thead>
                        <tr><th>Name</th><th>Role</th><th>Status</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= e($user['full_name']); ?></td>
                                <td><?= e($user['role']); ?></td>
                                <td><?= e($user['status']); ?></td>
                                <td>
                                    <form method="post" action="<?= APP_URL ?>/admin/user-status">
                                        <?= csrfField(); ?>
                                        <input type="hidden" name="user_id" value="<?= (int) $user['id']; ?>">
                                        <select name="status">
                                            <option value="active" <?= (($user['status'] ?? '') === 'active') ? 'selected' : ''; ?>>Active</option>
                                            <option value="suspended" <?= (($user['status'] ?? '') === 'suspended') ? 'selected' : ''; ?>>Suspended</option>
                                        </select>
                                        <button type="submit" class="button button-secondary">Update</button>
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
