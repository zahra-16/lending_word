-- Add model audio URL column to model_variants table
ALTER TABLE model_variants ADD COLUMN IF NOT EXISTS model_audio TEXT;
