-- Tambahan untuk Explore Models Section

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

-- Insert data Porsche models
INSERT INTO explore_models (name, description, fuel_types, doors, seats, image, sort_order) VALUES
('911', 'Sports car ikonik dengan rear-engine yang legendaris', 'Gasoline', '2 pintu', '2+2 kursi', 'https://files.porsche.com/filestore/image/multimedia/none/992-carrera-modelimage-sideshot/model/765dfc51-51bc-11ed-80f5-005056bbdc38/porsche-model.png', 1),
('718 Spyder RS', 'Mid-engine sports car dengan fokus presisi & performa', 'Gasoline', '2 pintu', '2 kursi', 'https://files.porsche.com/filestore/image/multimedia/none/982-718-spyder-rs-modelimage-sideshot/model/cfbb8ed6-a3e5-11ec-80f3-005056bbdc38/porsche-model.png', 2),
('Taycan', 'Sports car listrik dengan performa tinggi', 'Electric', '4 pintu', '4/5 kursi', 'https://files.porsche.com/filestore/image/multimedia/none/j1-taycan-modelimage-sideshot/model/930894d1-6214-11ea-80c4-005056bbdc38/porsche-model.png', 3),
('Panamera', 'Sedan mewah dengan performa tinggi', 'Hybrid, Gasoline', '4 pintu', '4/5 kursi', 'https://files.porsche.com/filestore/image/multimedia/none/g2-panamera-modelimage-sideshot/model/a6f11901-2c6e-11eb-80d1-005056bbdc38/porsche-model.png', 4),
('Macan', 'Compact SUV sporty dengan desain dinamis', 'Electric, Gasoline', '4 pintu', '5 kursi', 'https://files.porsche.com/filestore/image/multimedia/none/95b-macan-modelimage-sideshot/model/a9e5d5e1-2c6e-11eb-80d1-005056bbdc38/porsche-model.png', 5),
('Cayenne', 'SUV serbaguna dengan kemampuan luar biasa', 'Electric, Hybrid, Gasoline', '4 pintu', '5 kursi', 'https://files.porsche.com/filestore/image/multimedia/none/9ya-cayenne-modelimage-sideshot/model/a0c0e5e1-2c6e-11eb-80d1-005056bbdc38/porsche-model.png', 6);

-- Content untuk Explore Models section
INSERT INTO content (section, key_name, value, type) VALUES
('explore_models', 'title', 'Explore Our Models', 'text'),
('explore_models', 'subtitle', 'Discover the perfect Porsche for your lifestyle', 'text')
ON CONFLICT (section, key_name) DO UPDATE SET value = EXCLUDED.value;
