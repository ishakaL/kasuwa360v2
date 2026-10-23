<?php $title = e($shop['shop_name']) . ' | Kasuwa360'; include APP_ROOT . '/resources/views/partials/header.php'; ?>
<main class="page-shell">
    <div class="container detail-layout">
        <div class="card detail-media shop-hero">
            <div class="shop-badge"><?= e(substr($shop['shop_name'], 0, 1)); ?></div>
        </div>
        <div class="detail-copy">
            <span class="eyebrow">Shop profile</span>
            <h1><?= e($shop['shop_name']); ?></h1>
            <p><?= e($shop['description'] ?: 'This vendor has not added a description yet.'); ?></p>
            <div class="meta-pills">
                <span><?= e($shop['city'] ?: 'Unknown city'); ?></span>
                <span><?= e($shop['state'] ?: 'Unknown state'); ?></span>
                <span><?= e($shop['market_name'] ?: 'Community market'); ?></span>
            </div>
            <div class="vendor-actions">
                <?php if (!empty($shop['phone'])): ?><a class="button button-primary" href="tel:<?= e($shop['phone']); ?>">Call</a><?php endif; ?>
                <?php if (!empty($shop['whatsapp'])): ?><a class="button button-secondary" href="https://wa.me/<?= preg_replace('/[^0-9]/', '', (string) $shop['whatsapp']); ?>" target="_blank" rel="noreferrer">WhatsApp</a><?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="section-head">
            <h2>Published listings</h2>
        </div>
        <?php if (empty($listings)): ?>
            <div class="empty-state card"><p>This shop has no published listings yet.</p></div>
        <?php else: ?>
            <div class="listing-grid">
                <?php foreach ($listings as $listing): ?>
                    <article class="listing-card card">
                        <div class="listing-image"><img src="<?= APP_URL ?>/assets/images/placeholder.svg" alt="<?= e($listing['title']); ?>"></div>
                        <div class="listing-body">
                            <h3><?= e($listing['title']); ?></h3>
                            <div class="price-row"><strong><?= formatPrice($listing['price']); ?></strong><span><?= e($listing['type']); ?></span></div>
                            <p><?= e($listing['location']); ?></p>
                            <a href="<?= APP_URL ?>/listing/<?= (int) $listing['id']; ?>" class="text-link">View listing</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php include APP_ROOT . '/resources/views/partials/footer.php'; ?>
