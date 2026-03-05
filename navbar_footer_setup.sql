-- Navbar and Footer Management Setup

-- Table for Navbar Links
CREATE TABLE IF NOT EXISTS navbar_links (
    id SERIAL PRIMARY KEY,
    label VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for Footer Sections
CREATE TABLE IF NOT EXISTS footer_sections (
    id SERIAL PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for Footer Links (belongs to footer_sections)
CREATE TABLE IF NOT EXISTS footer_links (
    id SERIAL PRIMARY KEY,
    section_id INT REFERENCES footer_sections(id) ON DELETE CASCADE,
    label VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for Social Media Links
CREATE TABLE IF NOT EXISTS social_links (
    id SERIAL PRIMARY KEY,
    platform VARCHAR(50) NOT NULL,
    url VARCHAR(255) NOT NULL,
    icon VARCHAR(100),
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default navbar links
INSERT INTO navbar_links (label, url, sort_order) VALUES
('Home', '#hero', 1),
('About', '#about', 2),
('Models', '#models', 3),
('Discover', '#features', 4);

-- Insert footer sections
INSERT INTO footer_sections (title, sort_order) VALUES
('Company', 1),
('Legal', 2);

-- Insert footer links
INSERT INTO footer_links (section_id, label, url, sort_order) VALUES
(1, 'Porsche Asia Pacific Pte Ltd', '#', 1),
(1, 'Career', '#', 2),
(1, 'Global Partnership Council', '#', 3),
(1, 'Compliance', '#', 4),
(1, 'Sustainability', '#', 5),
(1, 'Newsroom & Press', '#', 6),
(2, 'Legal Notice', '#', 1),
(2, 'Privacy Policy', '#', 2),
(2, 'Cookie Policy', '#', 3),
(2, 'Open Source Software Notice', '#', 4),
(2, 'Whistleblower System', '#', 5);

-- Insert social media links
INSERT INTO social_links (platform, url, icon, sort_order) VALUES
('Facebook', 'https://facebook.com', 'fab fa-facebook', 1),
('Instagram', 'https://instagram.com', 'fab fa-instagram', 2),
('Pinterest', 'https://pinterest.com', 'fab fa-pinterest', 3),
('Youtube', 'https://youtube.com', 'fab fa-youtube', 4),
('Twitter', 'https://twitter.com', 'fab fa-twitter', 5),
('LinkedIn', 'https://linkedin.com', 'fab fa-linkedin', 6);

-- Footer content
INSERT INTO content (section, key_name, value, type) VALUES
('footer', 'newsletter_title', 'Newsletter', 'text'),
('footer', 'newsletter_desc', 'Latest news directly in your inbox', 'text'),
('footer', 'newsletter_button', 'Subscribe', 'text'),
('footer', 'contact_title', 'Locations & Contacts', 'text'),
('footer', 'contact_desc', 'Do you have any questions?', 'text'),
('footer', 'contact_button', 'Get in touch', 'text'),
('footer', 'social_title', 'Social Media', 'text'),
('footer', 'social_desc', 'Get in touch with us via social media.', 'text'),
('footer', 'copyright', '© 2026 Porsche Asia Pacific Pte Ltd.', 'text'),
('footer', 'bottom_text', 'Porsche Connect is available in Singapore, Malaysia and New Zealand.', 'text')
ON CONFLICT (section, key_name) DO UPDATE SET value = EXCLUDED.value;
