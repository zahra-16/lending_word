-- Configurator Colors Table
CREATE TABLE IF NOT EXISTS configurator_colors (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    hex_code VARCHAR(7) NOT NULL,
    type VARCHAR(20) NOT NULL, -- standard, metallic, special
    price DECIMAL(10,2) DEFAULT 0,
    image TEXT,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Configurator Wheels Table
CREATE TABLE IF NOT EXISTS configurator_wheels (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    size VARCHAR(20) NOT NULL,
    price DECIMAL(10,2) DEFAULT 0,
    image TEXT,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Sample Colors
INSERT INTO configurator_colors (name, hex_code, type, price, sort_order) VALUES
('White', '#FFFFFF', 'standard', 0, 1),
('Black', '#000000', 'standard', 0, 2),
('Guards Red', '#E2001A', 'standard', 0, 3),
('Jet Black Metallic', '#1C1C1C', 'metallic', 2500000, 4),
('Carrara White Metallic', '#F0F0F0', 'metallic', 2500000, 5),
('GT Silver Metallic', '#C0C0C0', 'metallic', 2500000, 6),
('Racing Yellow', '#FFD700', 'special', 5000000, 7),
('Miami Blue', '#00A3E0', 'special', 5000000, 8);

-- Insert Sample Wheels
INSERT INTO configurator_wheels (name, size, price, sort_order) VALUES
('Carrera S wheels', '20-inch', 0, 1),
('718 Sport wheels painted in Satin Black', '20-inch', 3000000, 2),
('Carrera Sport wheels', '20-inch', 4000000, 3),
('911 Turbo wheels', '20-inch', 5000000, 4),
('RS Spyder Design wheels', '21-inch', 8000000, 5);

-- Configurator Selections Table (untuk save configuration)
CREATE TABLE IF NOT EXISTS configurator_selections (
    id SERIAL PRIMARY KEY,
    variant_id INT NOT NULL,
    color_id INT NOT NULL,
    wheel_id INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (variant_id) REFERENCES model_variants(id),
    FOREIGN KEY (color_id) REFERENCES configurator_colors(id),
    FOREIGN KEY (wheel_id) REFERENCES configurator_wheels(id)
);
