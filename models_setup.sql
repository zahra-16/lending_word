-- Model Variants Setup for Model Overview Page

-- Table for model categories (911, 718, Taycan, etc)
CREATE TABLE IF NOT EXISTS model_categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    count INT DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for model variants (911 Carrera, 911 Carrera S, etc)
CREATE TABLE IF NOT EXISTS model_variants (
    id SERIAL PRIMARY KEY,
    category_id INT REFERENCES model_categories(id) ON DELETE CASCADE,
    name VARCHAR(200) NOT NULL,
    variant_group VARCHAR(200),
    image VARCHAR(500),
    fuel_type VARCHAR(50),
    drive_type VARCHAR(50),
    transmission VARCHAR(50),
    acceleration VARCHAR(50),
    power_kw INT,
    power_ps INT,
    top_speed VARCHAR(50),
    body_design VARCHAR(50),
    seats INT,
    is_new BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert model categories
INSERT INTO model_categories (name, slug, count, sort_order) VALUES
('All', 'all', 83, 0),
('718', '718', 10, 1),
('911', '911', 22, 2),
('Taycan', 'taycan', 14, 3),
('Panamera', 'panamera', 7, 4),
('Macan', 'macan', 9, 5),
('Cayenne', 'cayenne', 21, 6);

-- Insert 911 model variants
INSERT INTO model_variants (category_id, name, variant_group, image, fuel_type, drive_type, transmission, acceleration, power_kw, power_ps, top_speed, body_design, seats, is_new, sort_order) VALUES
-- 911 Carrera Model variants
(3, '911 Carrera', '911 Carrera Model variants', 'https://files.porsche.com/filestore/image/multimedia/none/992-carrera-modelimage-sideshot/model/765dfc51-51bc-11ed-80f5-005056bbdc38/porsche-model.png', 'Gasoline', 'Rear-Wheel Drive', 'Automatic', '4.1 s', 290, 394, '294 km/h', 'Coupe', 4, FALSE, 1),
(3, '911 Carrera T', '911 Carrera Model variants', 'https://files.porsche.com/filestore/image/multimedia/none/992-carrera-t-modelimage-sideshot/model/cfbb8ed6-a3e5-11ec-80f3-005056bbdc38/porsche-model.png', 'Gasoline', 'Rear-Wheel Drive', 'Manual', '4.5 s', 290, 394, '295 km/h', 'Coupe', 4, FALSE, 2),
(3, '911 Carrera S', '911 Carrera Model variants', 'https://files.porsche.com/filestore/image/multimedia/none/992-carrera-s-modelimage-sideshot/model/765dfc51-51bc-11ed-80f5-005056bbdc38/porsche-model.png', 'Gasoline', 'Rear-Wheel Drive', 'Automatic', '3.5 s', 353, 480, '308 km/h', 'Coupe', 4, FALSE, 3),
(3, '911 Carrera 4S', '911 Carrera Model variants', 'https://files.porsche.com/filestore/image/multimedia/none/992-carrera-4s-modelimage-sideshot/model/765dfc51-51bc-11ed-80f5-005056bbdc38/porsche-model.png', 'Gasoline', 'All-Wheel Drive', 'Automatic', '3.5 s', 353, 480, '308 km/h', 'Coupe', 4, FALSE, 4),
(3, '911 Carrera GTS', '911 Carrera Model variants', 'https://files.porsche.com/filestore/image/multimedia/none/992-carrera-gts-modelimage-sideshot/model/765dfc51-51bc-11ed-80f5-005056bbdc38/porsche-model.png', 'Gasoline', 'Rear-Wheel Drive', 'Automatic', '3.0 s', 398, 541, '312 km/h', 'Coupe', 4, FALSE, 5),
(3, '911 Carrera 4 GTS', '911 Carrera Model variants', 'https://files.porsche.com/filestore/image/multimedia/none/992-carrera-4-gts-modelimage-sideshot/model/765dfc51-51bc-11ed-80f5-005056bbdc38/porsche-model.png', 'Gasoline', 'All-Wheel Drive', 'Automatic', '3.0 s', 398, 541, '312 km/h', 'Coupe', 4, FALSE, 6),

-- 911 Carrera Cabriolet Model variants
(3, '911 Carrera Cabriolet', '911 Carrera Cabriolet Model variants', 'https://files.porsche.com/filestore/image/multimedia/none/992-carrera-cabriolet-modelimage-sideshot/model/765dfc51-51bc-11ed-80f5-005056bbdc38/porsche-model.png', 'Gasoline', 'Rear-Wheel Drive', 'Automatic', '4.3 s', 290, 394, '291 km/h', 'Cabriolet', 4, FALSE, 7),
(3, '911 Carrera T Cabriolet', '911 Carrera Cabriolet Model variants', 'https://files.porsche.com/filestore/image/multimedia/none/992-carrera-t-cabriolet-modelimage-sideshot/model/cfbb8ed6-a3e5-11ec-80f3-005056bbdc38/porsche-model.png', 'Gasoline', 'Rear-Wheel Drive', 'Manual', '4.7 s', 290, 394, '293 km/h', 'Cabriolet', 4, FALSE, 8),
(3, '911 Carrera S Cabriolet', '911 Carrera Cabriolet Model variants', 'https://files.porsche.com/filestore/image/multimedia/none/992-carrera-s-cabriolet-modelimage-sideshot/model/765dfc51-51bc-11ed-80f5-005056bbdc38/porsche-model.png', 'Gasoline', 'Rear-Wheel Drive', 'Automatic', '3.7 s', 353, 480, '308 km/h', 'Cabriolet', 4, FALSE, 9),
(3, '911 Carrera 4S Cabriolet', '911 Carrera Cabriolet Model variants', 'https://files.porsche.com/filestore/image/multimedia/none/992-carrera-4s-cabriolet-modelimage-sideshot/model/765dfc51-51bc-11ed-80f5-005056bbdc38/porsche-model.png', 'Gasoline', 'All-Wheel Drive', 'Automatic', '3.7 s', 353, 480, '308 km/h', 'Cabriolet', 4, FALSE, 10),
(3, '911 Carrera GTS Cabriolet', '911 Carrera Cabriolet Model variants', 'https://files.porsche.com/filestore/image/multimedia/none/992-carrera-gts-cabriolet-modelimage-sideshot/model/765dfc51-51bc-11ed-80f5-005056bbdc38/porsche-model.png', 'Gasoline', 'Rear-Wheel Drive', 'Automatic', '3.1 s', 398, 541, '312 km/h', 'Cabriolet', 4, FALSE, 11),
(3, '911 Carrera 4 GTS Cabriolet', '911 Carrera Cabriolet Model variants', 'https://files.porsche.com/filestore/image/multimedia/none/992-carrera-4-gts-cabriolet-modelimage-sideshot/model/765dfc51-51bc-11ed-80f5-005056bbdc38/porsche-model.png', 'Gasoline', 'All-Wheel Drive', 'Automatic', '3.1 s', 398, 541, '312 km/h', 'Cabriolet', 4, FALSE, 12),

-- 911 Targa Model variants
(3, '911 Targa 4S', '911 Targa Model variants', 'https://files.porsche.com/filestore/image/multimedia/none/992-targa-4s-modelimage-sideshot/model/765dfc51-51bc-11ed-80f5-005056bbdc38/porsche-model.png', 'Gasoline', 'All-Wheel Drive', 'Automatic', '3.7 s', 353, 480, '308 km/h', 'Targa', 4, FALSE, 13),
(3, '911 Targa 4 GTS', '911 Targa Model variants', 'https://files.porsche.com/filestore/image/multimedia/none/992-targa-4-gts-modelimage-sideshot/model/765dfc51-51bc-11ed-80f5-005056bbdc38/porsche-model.png', 'Gasoline', 'All-Wheel Drive', 'Automatic', '3.1 s', 398, 541, '312 km/h', 'Targa', 4, FALSE, 14),

-- 911 GT3 Model variants
(3, '911 GT3', '911 GT3 Model variants', 'https://files.porsche.com/filestore/image/multimedia/none/992-gt3-modelimage-sideshot/model/765dfc51-51bc-11ed-80f5-005056bbdc38/porsche-model.png', 'Gasoline', 'Rear-Wheel Drive', 'Automatic/Manual', '3.4 s', 375, 510, '311 km/h', 'Coupe', 2, FALSE, 15),
(3, '911 GT3 with Touring Package', '911 GT3 Model variants', 'https://files.porsche.com/filestore/image/multimedia/none/992-gt3-touring-modelimage-sideshot/model/765dfc51-51bc-11ed-80f5-005056bbdc38/porsche-model.png', 'Gasoline', 'Rear-Wheel Drive', 'Automatic/Manual', '3.9 s', 375, 510, '313 km/h', 'Coupe', 2, FALSE, 16),

-- 911 GT3 RS
(3, '911 GT3 RS', '911 GT3 RS', 'https://files.porsche.com/filestore/image/multimedia/none/992-gt3-rs-modelimage-sideshot/model/765dfc51-51bc-11ed-80f5-005056bbdc38/porsche-model.png', 'Gasoline', 'Rear-Wheel Drive', 'Automatic', '3.2 s', 386, 525, '296 km/h', 'Coupe', 2, FALSE, 17),

-- 911 Spirit 70
(3, '911 Spirit 70', '911 Spirit 70', 'https://files.porsche.com/filestore/image/multimedia/none/992-spirit-70-modelimage-sideshot/model/765dfc51-51bc-11ed-80f5-005056bbdc38/porsche-model.png', 'Gasoline', 'Rear-Wheel Drive', 'Automatic', '3.1 s', 398, 541, '312 km/h', 'Coupe', 4, FALSE, 18),

-- 911 Turbo
(3, '911 Turbo S', '911 Turbo', 'https://files.porsche.com/filestore/image/multimedia/none/992-turbo-s-modelimage-sideshot/model/765dfc51-51bc-11ed-80f5-005056bbdc38/porsche-model.png', 'Gasoline', 'All-Wheel Drive', 'Automatic', '2.5 s', 523, 711, '322 km/h', 'Coupe', 4, TRUE, 19),
(3, '911 Turbo S Cabriolet', '911 Turbo', 'https://files.porsche.com/filestore/image/multimedia/none/992-turbo-s-cabriolet-modelimage-sideshot/model/765dfc51-51bc-11ed-80f5-005056bbdc38/porsche-model.png', 'Gasoline', 'All-Wheel Drive', 'Automatic', '2.6 s', 523, 711, '322 km/h', 'Cabriolet', 4, TRUE, 20),

-- 911 GT3 90 F. A. Porsche
(3, '911 GT3 90 F. A. Porsche', '911 GT3 90 F. A. Porsche', 'https://files.porsche.com/filestore/image/multimedia/none/992-gt3-90-modelimage-sideshot/model/765dfc51-51bc-11ed-80f5-005056bbdc38/porsche-model.png', 'Gasoline', 'Rear-Wheel Drive', 'Manual', '3.9 s', 375, 510, '313 km/h', 'Coupe', 2, TRUE, 21),

-- 911 Turbo 50 Years
(3, '911 Turbo 50 Years', '911 Turbo 50 Years', 'https://files.porsche.com/filestore/image/multimedia/none/992-turbo-50-years-modelimage-sideshot/model/765dfc51-51bc-11ed-80f5-005056bbdc38/porsche-model.png', 'Gasoline', 'All-Wheel Drive', 'Automatic', '2.7 s', 478, 650, '330 km/h', 'Coupe', 4, FALSE, 22);
