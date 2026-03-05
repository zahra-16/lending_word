-- Tambah tabel models untuk kategori mobil
CREATE TABLE IF NOT EXISTS models (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    fuel_types TEXT NOT NULL,
    image VARCHAR(500),
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default models
INSERT INTO models (name, fuel_types, image, sort_order) VALUES
('718', 'Gasoline', 'https://files.porsche.com/filestore/image/multimedia/none/982-718-modelimage-sideshot/model/cfbb8ed3-1a15-11ea-80c7-005056bbdc38/porsche-model.png', 1),
('911', 'Gasoline', 'https://files.porsche.com/filestore/image/multimedia/none/992-911-modelimage-sideshot/model/930894d1-6214-11ea-80c4-005056bbdc38/porsche-model.png', 2),
('Taycan', 'Electric', 'https://files.porsche.com/filestore/image/multimedia/none/j1-taycan-modelimage-sideshot/model/930894d1-6214-11ea-80c4-005056bbdc38/porsche-model.png', 3),
('Panamera', 'Hybrid, Gasoline', 'https://files.porsche.com/filestore/image/multimedia/none/g2-panamera-modelimage-sideshot/model/930894d1-6214-11ea-80c4-005056bbdc38/porsche-model.png', 4),
('Macan', 'Electric, Gasoline', 'https://files.porsche.com/filestore/image/multimedia/none/95b-macan-modelimage-sideshot/model/930894d1-6214-11ea-80c4-005056bbdc38/porsche-model.png', 5),
('Cayenne', 'Electric, Hybrid, Gasoline', 'https://files.porsche.com/filestore/image/multimedia/none/9ya-cayenne-modelimage-sideshot/model/930894d1-6214-11ea-80c4-005056bbdc38/porsche-model.png', 6);
