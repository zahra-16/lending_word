-- Test Model Specification Sections
-- Run this to verify the tables and data

-- Check if tables exist
SELECT 'model_specification_sections table' as check_name, 
       CASE WHEN EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'model_specification_sections') 
       THEN 'EXISTS' ELSE 'NOT FOUND' END as status;

SELECT 'model_specification_section_images table' as check_name,
       CASE WHEN EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'model_specification_section_images')
       THEN 'EXISTS' ELSE 'NOT FOUND' END as status;

-- Check sample data
SELECT 'Sample sections count' as check_name, COUNT(*) as count FROM model_specification_sections;
SELECT 'Sample images count' as check_name, COUNT(*) as count FROM model_specification_section_images;

-- View sample data
SELECT * FROM model_specification_sections;
SELECT * FROM model_specification_section_images;

-- Check foreign key relationships
SELECT 
    tc.constraint_name, 
    tc.table_name, 
    kcu.column_name, 
    ccu.table_name AS foreign_table_name,
    ccu.column_name AS foreign_column_name 
FROM information_schema.table_constraints AS tc 
JOIN information_schema.key_column_usage AS kcu
    ON tc.constraint_name = kcu.constraint_name
JOIN information_schema.constraint_column_usage AS ccu
    ON ccu.constraint_name = tc.constraint_name
WHERE tc.constraint_type = 'FOREIGN KEY' 
    AND tc.table_name IN ('model_specification_sections', 'model_specification_section_images');
