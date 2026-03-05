-- Add tag column to model_specification_section_images
ALTER TABLE model_specification_section_images 
ADD COLUMN IF NOT EXISTS tag VARCHAR(100) DEFAULT 'General';

-- Update existing data with default tag
UPDATE model_specification_section_images SET tag = 'General' WHERE tag IS NULL;
