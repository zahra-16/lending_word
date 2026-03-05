-- Sound Section Setup

-- Add sound section content
INSERT INTO content (section, key_name, value, type) VALUES
('sound', 'title', 'Set the pace: 9,000 revolutions per minute.', 'text'),
('sound', 'caption', 'The naturally aspirated engine and sport exhaust system ensure an unfiltered sound experience.', 'text'),
('sound', 'image', 'https://files.porsche.com/filestore/image/multimedia/none/992-gt3-rs-modelimage-sideshot/normal/d3e8e4e5-3e3e-11ed-80f6-005056bbdc38;sK;twebp/porsche-normal.webp', 'image'),
('sound', 'button_text', 'Hold for sound', 'text')
ON CONFLICT (section, key_name) DO UPDATE SET value = EXCLUDED.value;

-- Verify installation
SELECT 'Sound section installed successfully!' as status;
SELECT * FROM content WHERE section = 'sound';
