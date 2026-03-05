-- Model Specification Sections Table
CREATE TABLE IF NOT EXISTS model_specification_sections (
    id SERIAL PRIMARY KEY,
    variant_id INTEGER NOT NULL REFERENCES model_variants(id) ON DELETE CASCADE,
    background_image TEXT NOT NULL,
    title TEXT NOT NULL,
    description TEXT,
    sort_order INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Model Specification Section Images Table (carousel images)
CREATE TABLE IF NOT EXISTS model_specification_section_images (
    id SERIAL PRIMARY KEY,
    section_id INTEGER NOT NULL REFERENCES model_specification_sections(id) ON DELETE CASCADE,
    image_url TEXT NOT NULL,
    title TEXT,
    description TEXT,
    sort_order INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_specification_sections_variant ON model_specification_sections(variant_id);
CREATE INDEX idx_specification_section_images_section ON model_specification_section_images(section_id);

-- Sample data for 911 Targa 4S (variant_id = 1)
INSERT INTO model_specification_sections (variant_id, background_image, title, description, sort_order) VALUES
(1, 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1920', 'Drive', 'Never before have we been able to make a car so powerful and so efficient at the same time. The new 911 is the result of our relentless pursuit of perfection.', 1);

-- Sample carousel images for the section
INSERT INTO model_specification_section_images (section_id, image_url, title, description, sort_order) VALUES
(1, 'https://images.unsplash.com/photo-1614200187524-dc4b892acf16?w=800', '3.0-litre flat-6 engine.', 'The engine is a tribute to the most extreme performance and efficiency. It is the heart of the 911 and the soul of every Porsche.', 1),
(1, 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800', 'Performance.', 'High-performance engineering meets cutting-edge technology. Every component is designed for maximum power and efficiency.', 2),
(1, 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?w=800', 'Precision.', 'Engineered to perfection with meticulous attention to detail. Every element works in harmony to deliver an unmatched driving experience.', 3);
