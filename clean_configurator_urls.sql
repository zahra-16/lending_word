-- Clean HTML entities from configurator_url
UPDATE model_variants 
SET configurator_url = REPLACE(REPLACE(configurator_url, '&quot;', ''), '"', '')
WHERE configurator_url LIKE '%&quot;%' OR configurator_url LIKE '%"%';
