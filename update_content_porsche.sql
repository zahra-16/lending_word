-- Copy-paste semua query ini ke pgAdmin Query Tool
-- Lalu klik Execute (F5)

UPDATE content SET value = 'The new Cayenne' WHERE section = 'hero' AND key_name = 'title';
UPDATE content SET value = 'Sporty. Elegant. Versatile. The Cayenne combines the performance of a sports car with the versatility of an SUV.' WHERE section = 'hero' AND key_name = 'subtitle';
UPDATE content SET value = 'Discover Now' WHERE section = 'hero' AND key_name = 'button_text';
UPDATE content SET value = 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1920' WHERE section = 'hero' AND key_name = 'image';

UPDATE content SET value = 'Performance Meets Luxury' WHERE section = 'about' AND key_name = 'title';
UPDATE content SET value = 'Experience the perfect fusion of sportiness and elegance. Every detail is crafted to deliver an unparalleled driving experience that combines power, precision, and comfort in perfect harmony.' WHERE section = 'about' AND key_name = 'description';
UPDATE content SET value = 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=1200' WHERE section = 'about' AND key_name = 'image';

UPDATE content SET value = 'Engineered Excellence' WHERE section = 'features' AND key_name = 'title';
UPDATE content SET value = 'Power & Performance' WHERE section = 'features' AND key_name = 'feature1_title';
UPDATE content SET value = 'Advanced engineering delivers exceptional power and dynamic handling' WHERE section = 'features' AND key_name = 'feature1_desc';
UPDATE content SET value = 'Luxury Interior' WHERE section = 'features' AND key_name = 'feature2_title';
UPDATE content SET value = 'Premium materials and cutting-edge technology for ultimate comfort' WHERE section = 'features' AND key_name = 'feature2_desc';
UPDATE content SET value = 'Innovation' WHERE section = 'features' AND key_name = 'feature3_title';
UPDATE content SET value = 'State-of-the-art features and intelligent systems' WHERE section = 'features' AND key_name = 'feature3_desc';

UPDATE content SET value = 'Experience Excellence' WHERE section = 'cta' AND key_name = 'title';
UPDATE content SET value = 'Schedule your test drive today and discover what sets us apart' WHERE section = 'cta' AND key_name = 'description';
UPDATE content SET value = 'Book Test Drive' WHERE section = 'cta' AND key_name = 'button_text';
