-- Fix: Create missing hero cards table and rename carousel table

-- Step 1: Rename carousel images table if it exists with old name
DO $$ 
BEGIN
    IF EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'model_specification_section_images') THEN
        ALTER TABLE model_specification_section_images RENAME TO model_specification_carousel_images;
    END IF;
END $$;

-- Step 2: Create hero cards table if not exists
CREATE TABLE IF NOT EXISTS model_specification_hero_cards (
    id SERIAL PRIMARY KEY,
    section_id INTEGER NOT NULL REFERENCES model_specification_sections(id) ON DELETE CASCADE,
    image_url TEXT NOT NULL,
    title TEXT NOT NULL,
    description TEXT,
    sort_order INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Step 3: Create index
CREATE INDEX IF NOT EXISTS idx_specification_hero_cards_section ON model_specification_hero_cards(section_id);

-- Step 4: Add sample data if table is empty
INSERT INTO model_specification_hero_cards (section_id, image_url, title, description, sort_order)
SELECT 1, 'https://images.unsplash.com/photo-1614200187524-dc4b892acf16?w=600', 'Engine Power', 'Twin-turbocharged 3.0L flat-six engine delivering exceptional performance', 1
WHERE EXISTS (SELECT 1 FROM model_specification_sections WHERE id = 1)
AND NOT EXISTS (SELECT 1 FROM model_specification_hero_cards WHERE section_id = 1);

INSERT INTO model_specification_hero_cards (section_id, image_url, title, description, sort_order)
SELECT 1, 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=600', 'Aerodynamics', 'Active aerodynamics with adjustable rear wing for maximum downforce', 2
WHERE EXISTS (SELECT 1 FROM model_specification_sections WHERE id = 1)
AND NOT EXISTS (SELECT 1 FROM model_specification_hero_cards WHERE section_id = 1 AND sort_order = 2);

INSERT INTO model_specification_hero_cards (section_id, image_url, title, description, sort_order)
SELECT 1, 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?w=600', 'Lightweight', 'Carbon fiber components reduce weight while maintaining structural integrity', 3
WHERE EXISTS (SELECT 1 FROM model_specification_sections WHERE id = 1)
AND NOT EXISTS (SELECT 1 FROM model_specification_hero_cards WHERE section_id = 1 AND sort_order = 3);
