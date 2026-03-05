-- Fix typo Cayeene to Cayenne
UPDATE explore_models SET name = 'Cayenne' WHERE name LIKE '%cayeene%' OR name LIKE '%Cayeene%';
