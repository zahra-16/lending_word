-- Update Sound Section with complete data

INSERT INTO content (section, key_name, value, type) VALUES
('sound', 'title', 'Set the pace: 9,000 revolutions per minute.', 'text'),
('sound', 'caption', 'The naturally aspirated engine and sport exhaust system ensure an unfiltered sound experience.', 'text'),
('sound', 'background_image', 'https://a.storyblok.com/f/322327/3064x1332/c99c36b9a0/cz23v20ox0006-911-gt3-rs-rear-2.jpg/m/2814x1406/smart/filters:format(avif)', 'image'),
('sound', 'button_text', 'Hold for sound', 'text'),
('sound', 'audio_url', '/lending_word/public/assets/audio/911.mp3', 'text')
ON CONFLICT (section, key_name) DO UPDATE SET value = EXCLUDED.value;

SELECT * FROM content WHERE section = 'sound';
