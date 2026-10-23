<?php $title = 'Marketplace | Kasuwa360'; include APP_ROOT . '/resources/views/partials/header.php'; ?>
<main class="page-shell">
    <div class="container marketplace-layout">
        <aside class="sidebar card filter-panel">
            <h3>Filters</h3>
            <form method="get" action="<?= APP_URL ?>/marketplace">
                <label>
                    Keyword
                    <input type="text" name="keyword" value="<?= e($filters['keyword'] ?? ''); ?>">
                </label>
                <label>
                    Category
                    <select name="category">
                        <option value="">All</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= e($category['slug']); ?>" <?= (($filters['category'] ?? '') === $category['slug']) ? 'selected' : ''; ?>><?= e($category['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    Type
                    <select name="type">
                        <option value="">All</option>
                        <option value="product" <?= (($filters['type'] ?? '') === 'product') ? 'selected' : ''; ?>>Product</option>
                        <option value="service" <?= (($filters['type'] ?? '') === 'service') ? 'selected' : ''; ?>>Service</option>
                    </select>
                </label>
                <label>
                    City
                    <input type="text" name="city" value="<?= e($filters['city'] ?? ''); ?>">
                </label>
                <label>
                    State
                    <input type="text" name="state" value="<?= e($filters['state'] ?? ''); ?>">
                </label>
                <label>
                    Min price
                    <input type="number" name="min_price" value="<?= e($filters['min_price'] ?? ''); ?>">
                </label>
                <label>
                    Max price
                    <input type="number" name="max_price" value="<?= e($filters['max_price'] ?? ''); ?>">
                </label>
                <button type="submit" class="button button-primary full-width">Apply filters</button>
            </form>
        </aside>
        <section class="marketplace-main">
            <div class="section-head">
                <h1>Marketplace</h1>
                <span><?= (int) ($total ?? 0); ?> results</span>
            </div>
            <?php if (empty($listings)): ?>
                <div class="empty-state card">
                    <h3>No listings found</h3>
                    <p>Try a different keyword or filter combination.</p>
                </div>
            <?php else: ?>
                <div class="listing-grid">
                    <?php foreach ($listings as $listing): ?>
                        <article class="listing-card card">
                            <div class="listing-image">
                                <img src="<?= APP_URL ?>/assets/images/placeholder.svg" alt="<?= e($listing['title']); ?>">
                            </div>
                            <div class="listing-body">
                                <span class="tag"><?= e($listing['type']); ?></span>
                                <h3><?= e($listing['title']); ?></h3>
                                <div class="price-row">
                                    <strong><?= formatPrice($listing['price']); ?></strong>
                                    <span><?= e($listing['category_name']); ?></span>
                                </div>
                                <p><?= e($listing['shop_name']); ?> • <?= e($listing['location'] ?: 'Nigeria'); ?></p>
                                <a href="<?= APP_URL ?>/listing/<?= (int) $listing['id']; ?>" class="text-link">View details</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                <?php if (($totalPages ?? 1) > 1): ?>
                    <div class="pagination">
                        <?php for ($pageIndex = 1; $pageIndex <= $totalPages; $pageIndex++): ?>
                            <a href="<?= APP_URL ?>/marketplace?page=<?= $pageIndex; ?>" class="page-link <?= ($pageIndex == ($page ?? 1)) ? 'active' : ''; ?>"><?= $pageIndex; ?></a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </div>
</main>
<?php include APP_ROOT . '/resources/views/partials/footer.php'; ?>
