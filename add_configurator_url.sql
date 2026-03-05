-- Add configurator_url column to model_variants
ALTER TABLE model_variants ADD COLUMN IF NOT EXISTS configurator_url TEXT;

-- Update with Porsche configurator URLs (sample - you need to update with correct URLs for each model)
UPDATE model_variants SET configurator_url = 'https://configurator.porsche.com/en-ID/mode/model/982851' WHERE name LIKE '%718%';
UPDATE model_variants SET configurator_url = 'https://configurator.porsche.com/en-ID/mode/model/992110' WHERE name LIKE '%911%';
UPDATE model_variants SET configurator_url = 'https://configurator.porsche.com/en-ID/mode/model/J1A110' WHERE name LIKE '%Taycan%';
UPDATE model_variants SET configurator_url = 'https://configurator.porsche.com/en-ID/mode/model/G3A110' WHERE name LIKE '%Panamera%';
UPDATE model_variants SET configurator_url = 'https://configurator.porsche.com/en-ID/mode/model/95BPAA' WHERE name LIKE '%Macan%';
UPDATE model_variants SET configurator_url = 'https://configurator.porsche.com/en-ID/mode/model/E3A110' WHERE name LIKE '%Cayenne%';
