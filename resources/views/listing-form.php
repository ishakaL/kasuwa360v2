<?php $title = 'Create listing | Kasuwa360'; include APP_ROOT . '/resources/views/partials/header.php'; ?>
<main class="page-shell">
    <div class="container form-shell">
        <div class="card form-card">
            <div class="page-head">
                <div>
                    <span class="eyebrow">Listings</span>
                    <h1><?= $listing ? 'Edit listing' : 'Add a new listing'; ?></h1>
                </div>
            </div>
            <form method="post" action="<?= APP_URL ?><?= $listing ? '/listing/edit?id=' . (int) $listing['id'] : '/listing/new'; ?>" enctype="multipart/form-data">
                <?= csrfField(); ?>
                <div class="field-grid">
                    <label>
                        Listing title
                        <input type="text" name="title" value="<?= e($listing['title'] ?? ''); ?>" required>
                    </label>
                    <label>
                        Category
                        <select name="category_id" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int) $category['id']; ?>" <?= ($listing['category_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>><?= e($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>
                        Type
                        <select name="type">
                            <option value="product" <?= (($listing['type'] ?? 'product') === 'product') ? 'selected' : ''; ?>>Product</option>
                            <option value="service" <?= (($listing['type'] ?? 'product') === 'service') ? 'selected' : ''; ?>>Service</option>
                        </select>
                    </label>
                    <label>
                        Price
                        <input type="number" step="0.01" name="price" value="<?= e($listing['price'] ?? '0'); ?>" required>
                    </label>
                    <label>
                        Location
                        <input type="text" name="location" value="<?= e($listing['location'] ?? ''); ?>">
                    </label>
                    <label>
                        Availability
                        <select name="availability_status">
                            <option value="in_stock" <?= (($listing['availability_status'] ?? 'in_stock') === 'in_stock') ? 'selected' : ''; ?>>In stock</option>
                            <option value="limited" <?= (($listing['availability_status'] ?? 'in_stock') === 'limited') ? 'selected' : ''; ?>>Limited</option>
                            <option value="sold_out" <?= (($listing['availability_status'] ?? 'in_stock') === 'sold_out') ? 'selected' : ''; ?>>Sold out</option>
                        </select>
                    </label>
                    <label>
                        Stock quantity
                        <input type="number" name="stock_quantity" value="<?= e($listing['stock_quantity'] ?? '0'); ?>">
                    </label>
                    <label>
                        Service pricing model
                        <input type="text" name="pricing_model" value="<?= e($listing['pricing_model'] ?? 'fixed'); ?>">
                    </label>
                    <label class="full-span">
                        Description
                        <textarea name="description" rows="5" required><?= e($listing['description'] ?? ''); ?></textarea>
                    </label>
                    <label class="full-span">
                        Service area (optional)
                        <input type="text" name="service_area" value="<?= e($listing['service_area'] ?? ''); ?>">
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_negotiable" value="1" <?= (!empty($listing['is_negotiable']) ? 'checked' : ''); ?>> Price negotiable
                    </label>
                    <label class="full-span">
                        Listing images
                        <input type="file" name="images[]" accept="image/*" multiple>
                    </label>
                </div>
                <button type="submit" class="button button-primary">Save listing</button>
            </form>
        </div>
    </div>
</main>
<?php include APP_ROOT . '/resources/views/partials/footer.php'; ?>
