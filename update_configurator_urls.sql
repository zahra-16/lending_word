-- Update all empty configurator_url to default Porsche configurator
UPDATE model_variants 
SET configurator_url = 'https://configurator.porsche.com/en-ID/' 
WHERE configurator_url IS NULL OR configurator_url = '';
