-- Add hero background image column to model_variants table
ALTER TABLE model_variants ADD COLUMN IF NOT EXISTS hero_bg_image TEXT;
