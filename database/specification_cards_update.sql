-- Rename existing table to be more specific
ALTER TABLE model_specification_section_images RENAME TO model_specification_carousel_images;

-- Create new table for hero cards
CREATE TABLE IF NOT EXISTS model_specification_hero_cards (
    id SERIAL PRIMARY KEY,
    section_id INTEGER NOT NULL REFERENCES model_specification_sections(id) ON DELETE CASCADE,
    image_url TEXT NOT NULL,
    title TEXT NOT NULL,
    description TEXT,
    sort_order INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_specification_hero_cards_section ON model_specification_hero_cards(section_id);

-- Sample data for hero cards
INSERT INTO model_specification_hero_cards (section_id, image_url, title, description, sort_order) VALUES
(1, 'https://images.unsplash.com/photo-1614200187524-dc4b892acf16?w=600', 'Engine Power', 'Twin-turbocharged 3.0L flat-six engine delivering exceptional performance', 1),
(1, 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=600', 'Aerodynamics', 'Active aerodynamics with adjustable rear wing for maximum downforce', 2),
(1, 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?w=600', 'Lightweight', 'Carbon fiber components reduce weight while maintaining structural integrity', 3);
