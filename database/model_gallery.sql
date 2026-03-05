-- Model Gallery Table
CREATE TABLE IF NOT EXISTS model_gallery (
    id SERIAL PRIMARY KEY,
    variant_id INTEGER NOT NULL REFERENCES model_variants(id) ON DELETE CASCADE,
    image_url TEXT NOT NULL,
    title TEXT,
    section TEXT,
    caption TEXT,
    sort_order INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_model_gallery_variant ON model_gallery(variant_id);
