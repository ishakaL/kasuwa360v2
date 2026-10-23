<?php $title = 'Kasuwa360 | Local marketplace'; include APP_ROOT . '/resources/views/partials/header.php'; ?>

<main>
    <section class="hero">
        <div class="container hero-inner">
            <div class="hero-copy">
                <span class="eyebrow">Made for local trade</span>
                <h1>Buy and sell from trusted vendors across Nigeria.</h1>
                <p>Discover products, services, and shops from communities you know and trust.</p>
                <form class="search-form" action="<?= APP_URL ?>/marketplace" method="get">
                    <input type="text" name="keyword" placeholder="Search for products or services" value="<?= e($_GET['keyword'] ?? ''); ?>">
                    <button class="button button-primary" type="submit">Search</button>
                </form>
                <div class="hero-actions">
                    <a class="button button-primary" href="<?= APP_URL ?>/register">Become a vendor</a>
                    <a class="button button-secondary" href="<?= APP_URL ?>/marketplace">Browse marketplace</a>
                </div>
            </div>
            <div class="hero-card card">
                <h3>Market snapshot</h3>
                <div class="stats-grid">
                    <div>
                        <strong><?= count($featured ?? []); ?></strong>
                        <span>Featured listings</span>
                    </div>
                    <div>
                        <strong><?= count($shops ?? []); ?></strong>
                        <span>Active shops</span>
                    </div>
                </div>
                <ul class="mini-list">
                    <li>Verified vendor listings</li>
                    <li>Search by city and category</li>
                    <li>Simple contact-first selling</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-head">
                <h2>Popular categories</h2>
            </div>
            <div class="category-grid">
                <?php foreach ($categories as $category): ?>
                    <a class="category-card" href="<?= APP_URL ?>/marketplace?category=<?= e($category['slug']); ?>">
                        <span><?= e($category['name']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section soft-bg">
        <div class="container">
            <div class="section-head">
                <h2>Recently published listings</h2>
                <a href="<?= APP_URL ?>/marketplace">View all</a>
            </div>
            <div class="listing-grid">
                <?php foreach ($featured as $listing): ?>
                    <?php $listingImage = '/assets/images/placeholder.svg'; ?>
                    <article class="listing-card card">
                        <div class="listing-image">
                            <img src="<?= APP_URL . $listingImage; ?>" alt="<?= e($listing['title']); ?>">
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
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-head">
                <h2>Popular shops</h2>
            </div>
            <div class="shop-grid">
                <?php foreach ($shops as $shop): ?>
                    <article class="shop-card card">
                        <div class="shop-thumb"><?= e(substr($shop['shop_name'], 0, 1)); ?></div>
                        <h3><?= e($shop['shop_name']); ?></h3>
                        <p><?= e($shop['city'] . ', ' . $shop['state']); ?></p>
                        <a href="<?= APP_URL ?>/shop/<?= e($shop['slug']); ?>" class="button button-secondary">View shop</a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php include APP_ROOT . '/resources/views/partials/footer.php'; ?>
