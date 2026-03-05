-- Add hero video URL column to model_variants table
ALTER TABLE model_variants ADD COLUMN IF NOT EXISTS model_video TEXT;
