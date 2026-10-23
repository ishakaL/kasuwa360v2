INSERT OR IGNORE INTO categories (name, slug, status) VALUES
    ('Fashion', 'fashion', 'active'),
    ('Electronics', 'electronics', 'active'),
    ('Food & Groceries', 'food-groceries', 'active'),
    ('Home & Living', 'home-living', 'active'),
    ('Health & Beauty', 'health-beauty', 'active'),
    ('Services', 'services', 'active'),
    ('Agriculture', 'agriculture', 'active'),
    ('Construction', 'construction', 'active');

INSERT OR IGNORE INTO application_settings (setting_key, setting_value) VALUES
    ('approval_required', '1'),
    ('default_currency', 'NGN');
