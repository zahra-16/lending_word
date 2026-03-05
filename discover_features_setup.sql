-- Tabel untuk Discover Features Section

CREATE TABLE IF NOT EXISTS discover_features (
    id SERIAL PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    image VARCHAR(500),
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert data Porsche Discover features
INSERT INTO discover_features (title, description, image, sort_order) VALUES
('Porsche E-Performance', 'A white Porsche parked outside and charging next to a building.', '/lending_word/public/assets/images/e-performance.jpg', 1),
('Porsche Tequipment', 'A man is walking on the beach next to a parked Porsche Macan 4 with a roof black box.', '/lending_word/public/assets/images/tequipment.jpg', 2),
('Porsche Exclusive Manufaktur', 'A person is making a printing of the Porsche crest on leather.', '/lending_word/public/assets/images/manufaktur.jpg', 3);

-- Content untuk Discover section
INSERT INTO content (section, key_name, value, type) VALUES
('discover', 'title', 'Discover', 'text')
ON CONFLICT (section, key_name) DO UPDATE SET value = EXCLUDED.value;
