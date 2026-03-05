-- Inventory Section Setup

-- Content for Inventory section
INSERT INTO content (section, key_name, value, type) VALUES
('inventory', 'title', 'Find your new or pre-owned Porsche.', 'text'),
('inventory', 'description', 'A Porsche is as individual as its owner. It is always an expression of one''s own personality. We help you find your personal dream vehicle from authorised Porsche Centres.', 'textarea'),
('inventory', 'button_text', 'Find your Porsche', 'text'),
('inventory', 'image', 'https://files.porsche.com/filestore/image/multimedia/none/rd-2023-homepage-ww-banner-02/normal/f7c4c909-e5f4-11ed-8101-005056bbdc38;sK;twebp/porsche-normal.webp', 'image')
ON CONFLICT (section, key_name) DO UPDATE SET value = EXCLUDED.value;
