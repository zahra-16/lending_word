-- Create model_sound table
CREATE TABLE IF NOT EXISTS model_sound (
    id SERIAL PRIMARY KEY,
    variant_id INTEGER NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    caption TEXT,
    background_image VARCHAR(500),
    button_text VARCHAR(100),
    audio_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (variant_id) REFERENCES model_variants(id) ON DELETE CASCADE
);

-- Add index
CREATE INDEX idx_model_sound_variant ON model_sound(variant_id);
