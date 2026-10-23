<?php $title = e($listing['title']) . ' | Kasuwa360'; include APP_ROOT . '/resources/views/partials/header.php'; ?>
<main class="page-shell">
    <div class="container detail-layout">
        <div class="card detail-media">
            <img src="<?= APP_URL ?>/assets/images/placeholder.svg" alt="<?= e($listing['title']); ?>">
        </div>
        <div class="detail-copy">
            <span class="eyebrow"><?= e($listing['type']); ?></span>
            <h1><?= e($listing['title']); ?></h1>
            <div class="price-badge"><?= formatPrice($listing['price']); ?></div>
            <p><?= e($listing['description']); ?></p>
            <div class="meta-pills">
                <span>Category: <?= e($listing['category_name']); ?></span>
                <span>Location: <?= e($listing['location'] ?: 'Not specified'); ?></span>
                <span>Availability: <?= e($listing['availability_status']); ?></span>
            </div>
            <div class="vendor-actions">
                <a class="button button-primary" href="tel:<?= e($listing['shop_phone'] ?? ''); ?>">Call seller</a>
                <a class="button button-secondary" href="https://wa.me/<?= preg_replace('/[^0-9]/', '', (string) ($listing['shop_whatsapp'] ?? '')); ?>" target="_blank" rel="noreferrer">WhatsApp</a>
            </div>
        </div>
    </div>
    <div class="container detail-section">
        <div class="card info-panel">
            <h3>Shop details</h3>
            <p><strong><?= e($listing['shop_name']); ?></strong></p>
            <p><?= e($listing['shop_city'] ?: 'Unknown city'); ?>, <?= e($listing['shop_state'] ?: 'Unknown state'); ?></p>
            <a href="<?= APP_URL ?>/shop/<?= e($listing['shop_slug']); ?>" class="text-link">Visit shop</a>
        </div>
    </div>
</main>
<?php include APP_ROOT . '/resources/views/partials/footer.php'; ?>
