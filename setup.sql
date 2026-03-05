

-- Connect to landing_cms database before running below commands

CREATE TABLE IF NOT EXISTS content (
    id SERIAL PRIMARY KEY,
    section VARCHAR(50) NOT NULL,
    key_name VARCHAR(100) NOT NULL,
    value TEXT,
    type VARCHAR(20) DEFAULT 'text',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (section, key_name)
);

CREATE TABLE IF NOT EXISTS admin_users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Default admin: username=admin, password=admin123
-- Hash dibuat dengan: password_hash('admin123', PASSWORD_BCRYPT)
INSERT INTO admin_users (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON CONFLICT (username) DO UPDATE SET password = EXCLUDED.password;

-- Default content tema olahraga
INSERT INTO content (section, key_name, value, type) VALUES
('hero', 'title', 'The new Cayenne', 'text'),
('hero', 'subtitle', 'Sporty. Elegant. Versatile. The Cayenne combines the performance of a sports car with the versatility of an SUV.', 'text'),
('hero', 'button_text', 'Discover Now', 'text'),
('hero', 'image', 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1920', 'image'),

('about', 'title', 'Performance Meets Luxury', 'text'),
('about', 'description', 'Experience the perfect fusion of sportiness and elegance. Every detail is crafted to deliver an unparalleled driving experience that combines power, precision, and comfort in perfect harmony.', 'textarea'),
('about', 'image', 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=1200', 'image'),

('features', 'title', 'Engineered Excellence', 'text'),
('features', 'feature1_title', 'Power & Performance', 'text'),
('features', 'feature1_desc', 'Advanced engineering delivers exceptional power and dynamic handling', 'text'),
('features', 'feature2_title', 'Luxury Interior', 'text'),
('features', 'feature2_desc', 'Premium materials and cutting-edge technology for ultimate comfort', 'text'),
('features', 'feature3_title', 'Innovation', 'text'),
('features', 'feature3_desc', 'State-of-the-art features and intelligent systems', 'text'),

('cta', 'title', 'Experience Excellence', 'text'),
('cta', 'description', 'Schedule your test drive today and discover what sets us apart', 'text'),
('cta', 'button_text', 'Book Test Drive', 'text'),

('footer', 'text', '© 2024 SportsPro. All rights reserved.', 'text'),
('footer', 'email', 'info@sportspro.com', 'text'),
('footer', 'phone', '+62 812-3456-7890', 'text');

-- Table for Explore Models
CREATE TABLE IF NOT EXISTS explore_models (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    fuel_types VARCHAR(255),
    doors VARCHAR(50),
    seats VARCHAR(50),
    image VARCHAR(500),
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Porsche models data
INSERT INTO explore_models (name, description, fuel_types, doors, seats, image, sort_order) VALUES
('911', 'Sports car ikonik dengan rear-engine yang legendaris', 'Gasoline', '2 pintu', '2+2 kursi', 'https://files.porsche.com/filestore/image/multimedia/none/992-carrera-modelimage-sideshot/model/765dfc51-51bc-11ed-80f5-005056bbdc38/porsche-model.png', 1),
('718 Spyder RS', 'Mid-engine sports car dengan fokus presisi & performa', 'Gasoline', '2 pintu', '2 kursi', 'https://files.porsche.com/filestore/image/multimedia/none/982-718-spyder-rs-modelimage-sideshot/model/cfbb8ed6-a3e5-11ec-80f3-005056bbdc38/porsche-model.png', 2),
('Taycan', 'Sports car listrik dengan performa tinggi', 'Electric', '4 pintu', '4/5 kursi', 'https://files.porsche.com/filestore/image/multimedia/none/j1-taycan-modelimage-sideshot/model/930894d1-6214-11ea-80c4-005056bbdc38/porsche-model.png', 3),
('Panamera', 'Sedan mewah dengan performa tinggi', 'Hybrid, Gasoline', '4 pintu', '4/5 kursi', 'https://files.porsche.com/filestore/image/multimedia/none/g2-panamera-modelimage-sideshot/model/a6f11901-2c6e-11eb-80d1-005056bbdc38/porsche-model.png', 4),
('Macan', 'Compact SUV sporty dengan desain dinamis', 'Electric, Gasoline', '4 pintu', '5 kursi', 'https://files.porsche.com/filestore/image/multimedia/none/95b-macan-modelimage-sideshot/model/a9e5d5e1-2c6e-11eb-80d1-005056bbdc38/porsche-model.png', 5),
('Cayenne', 'SUV serbaguna dengan kemampuan luar biasa', 'Electric, Hybrid, Gasoline', '4 pintu', '5 kursi', 'https://files.porsche.com/filestore/image/multimedia/none/9ya-cayenne-modelimage-sideshot/model/a0c0e5e1-2c6e-11eb-80d1-005056bbdc38/porsche-model.png', 6);

-- Content for Explore Models section
INSERT INTO content (section, key_name, value, type) VALUES
('explore_models', 'title', 'Explore Our Models', 'text'),
('explore_models', 'subtitle', 'Discover the perfect Porsche for your lifestyle', 'text')
ON CONFLICT (section, key_name) DO UPDATE SET value = EXCLUDED.value;
