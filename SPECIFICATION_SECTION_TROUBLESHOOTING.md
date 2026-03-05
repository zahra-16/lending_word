# Model Specification Sections - Troubleshooting

## 🔧 Common Issues & Solutions

### 1. Database Issues

#### ❌ Tables Not Created
**Error**: `relation "model_specification_sections" does not exist`

**Solution**:
```bash
# Re-import the SQL file
psql -U postgres -d landing_cms -f database/model_specification_sections.sql

# Or manually create via pgAdmin Query Tool
```

**Check**:
```sql
SELECT table_name FROM information_schema.tables 
WHERE table_name LIKE 'model_specification%';
```

---

#### ❌ Foreign Key Constraint Error
**Error**: `violates foreign key constraint`

**Cause**: variant_id doesn't exist in model_variants table

**Solution**:
```sql
-- Check if variant exists
SELECT id, name FROM model_variants WHERE id = 1;

-- If not, use existing variant_id
SELECT id, name FROM model_variants LIMIT 5;
```

---

#### ❌ Sample Data Not Inserted
**Error**: No data shows in admin panel

**Solution**:
```sql
-- Check data
SELECT COUNT(*) FROM model_specification_sections;

-- If 0, manually insert
INSERT INTO model_specification_sections 
(variant_id, background_image, title, description, sort_order) 
VALUES (1, 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1920', 
'Drive', 'Test description', 1);
```

---

### 2. Admin Panel Issues

#### ❌ 404 Not Found
**Error**: `specification.php not found`

**Check**:
```bash
# Verify file exists
dir admin\specification.php

# Check path in browser
http://localhost/lending_word/admin/specification.php?variant_id=1
```

**Solution**: Ensure file is in correct location: `admin/specification.php`

---

#### ❌ Session Error / Not Logged In
**Error**: Redirects to login page

**Solution**:
```php
// Clear session and re-login
1. Close all browser tabs
2. Clear cookies
3. Login again: admin / admin123
```

---

#### ❌ Form Not Submitting
**Error**: Nothing happens when clicking Save

**Check**:
1. Browser console for JavaScript errors (F12)
2. Network tab for failed requests
3. PHP error log

**Solution**:
```php
// Enable error reporting in specification.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

---

#### ❌ Modal Not Showing
**Error**: Click "Add New Section" but nothing appears

**Solution**:
```javascript
// Check JavaScript console (F12)
// Ensure modal functions are defined

// Test manually in console:
showModal('addSectionModal');
```

---

### 3. Frontend Display Issues

#### ❌ Section Not Showing
**Error**: Specification section doesn't appear on model-detail.php

**Check**:
```php
// Add debug in model-detail.php
<?php 
var_dump($specificationSections); 
// Should show array with data
?>
```

**Solution**:
```sql
-- Verify data exists for that variant
SELECT * FROM model_specification_sections WHERE variant_id = 1;

-- If empty, add data via admin panel
```

---

#### ❌ Carousel Not Working
**Error**: Arrows don't slide carousel

**Check**:
1. Browser console for JavaScript errors
2. Verify `data-section` attribute exists
3. Check if cards have proper class names

**Solution**:
```javascript
// Test in console:
slideSpecificationCarousel(1, 1); // Should slide

// Check if function exists:
typeof slideSpecificationCarousel; // Should return "function"
```

---

#### ❌ Images Not Loading
**Error**: Broken image icons

**Check**:
```html
<!-- View page source, check image URLs -->
<img src="BROKEN_URL_HERE">
```

**Solution**:
1. Verify URL is valid (open in new tab)
2. Check for CORS issues
3. Use absolute URLs: `https://...`
4. Or relative: `/lending_word/public/assets/images/...`

---

#### ❌ Layout Broken / Cards Not Aligned
**Error**: Cards stacked vertically or overlapping

**Solution**:
```css
/* Check CSS is loaded */
/* Clear browser cache: Ctrl + F5 */

/* Verify card width calculation */
.specification-carousel-card {
    min-width: calc(33.333% - 20px) !important;
}
```

---

### 4. CSS Issues

#### ❌ Styles Not Applied
**Error**: Section looks unstyled

**Check**:
```html
<!-- View page source, search for: -->
.specification-carousel-section

<!-- Should find CSS rules -->
```

**Solution**:
```php
// Add cache buster in model-detail.php
<link rel="stylesheet" href="style.css?v=<?= time() ?>">

// Or clear browser cache: Ctrl + Shift + Delete
```

---

#### ❌ Background Image Not Showing
**Error**: White/black background instead of image

**Check**:
```css
/* Inspect element (F12), check computed styles */
background-image: url('...'); /* Should have valid URL */
```

**Solution**:
```css
/* Ensure URL is correct and accessible */
.specification-carousel-section {
    background-image: url('VALID_URL_HERE') !important;
    background-size: cover !important;
}
```

---

#### ❌ Parallax Not Working
**Error**: Background scrolls with content

**Solution**:
```css
.specification-carousel-section {
    background-attachment: fixed; /* Enable parallax */
}

/* Note: May not work on mobile Safari */
```

---

### 5. JavaScript Issues

#### ❌ Carousel Index Error
**Error**: `Cannot read property 'offsetWidth' of undefined`

**Cause**: No cards found or wrong selector

**Solution**:
```javascript
// Check if cards exist
const cards = document.querySelectorAll('.specification-carousel-card');
console.log('Cards found:', cards.length);

// Should be > 0
```

---

#### ❌ Multiple Sections Conflict
**Error**: Clicking arrow on one section affects another

**Cause**: Section ID not unique or selector issue

**Solution**:
```javascript
// Ensure data-section attribute is unique
<div class="specification-carousel-track" data-section="<?= $specSection['id'] ?>">

// Use specific selector
const track = document.querySelector(`.specification-carousel-track[data-section="${sectionId}"]`);
```

---

### 6. Performance Issues

#### ❌ Slow Page Load
**Error**: Page takes long to load

**Solution**:
```
1. Optimize images:
   - Compress JPG/PNG (TinyPNG, ImageOptim)
   - Use WebP format
   - Resize to required dimensions

2. Use CDN for images:
   - Unsplash with ?w=1920&q=80
   - Imgix with auto optimization

3. Lazy load images:
   <img loading="lazy" src="...">
```

---

#### ❌ Carousel Laggy
**Error**: Carousel slides with delay/stutter

**Solution**:
```css
/* Use GPU acceleration */
.specification-carousel-track {
    transform: translateX(0);
    will-change: transform;
    transition: transform 0.5s ease;
}
```

---

### 7. Responsive Issues

#### ❌ Mobile Layout Broken
**Error**: Cards too small or overlapping on mobile

**Check**:
```html
<!-- Ensure viewport meta tag exists -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
```

**Solution**:
```css
@media (max-width: 768px) {
    .specification-carousel-card {
        min-width: 100% !important;
    }
    .specification-carousel-section {
        padding: 60px 30px !important;
    }
}
```

---

#### ❌ Text Too Small on Mobile
**Error**: Title/description hard to read

**Solution**:
```css
/* Use clamp for responsive text */
.specification-carousel-text h2 {
    font-size: clamp(2rem, 4vw, 4rem);
}

.specification-carousel-text p {
    font-size: clamp(0.9rem, 1.2vw, 1.2rem);
}
```

---

### 8. Data Issues

#### ❌ Wrong Section Showing
**Error**: Section shows for wrong model

**Check**:
```sql
-- Verify variant_id is correct
SELECT id, variant_id, title FROM model_specification_sections;
```

**Solution**:
```sql
-- Update variant_id
UPDATE model_specification_sections 
SET variant_id = 1 
WHERE id = 1;
```

---

#### ❌ Images Out of Order
**Error**: Carousel images in wrong sequence

**Solution**:
```sql
-- Update sort_order
UPDATE model_specification_section_images 
SET sort_order = 1 WHERE id = 1;

UPDATE model_specification_section_images 
SET sort_order = 2 WHERE id = 2;

-- Etc...
```

---

## 🔍 Debug Checklist

### Quick Debug Steps:

1. **Check Database**:
   ```sql
   SELECT * FROM model_specification_sections WHERE variant_id = 1;
   SELECT * FROM model_specification_section_images WHERE section_id = 1;
   ```

2. **Check PHP Errors**:
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```

3. **Check JavaScript Console**:
   - Press F12
   - Look for red errors
   - Test functions manually

4. **Check Network Tab**:
   - F12 → Network
   - Look for failed requests (red)
   - Check response codes

5. **Clear Cache**:
   - Browser: Ctrl + Shift + Delete
   - PHP: Restart Apache/Nginx
   - Database: Reconnect

---

## 📞 Still Having Issues?

### Collect Debug Info:

```
1. PHP Version: <?php echo phpversion(); ?>
2. PostgreSQL Version: SELECT version();
3. Browser: Chrome/Firefox/Safari + version
4. Error Message: [Copy exact error]
5. Steps to Reproduce: [List steps]
6. Expected vs Actual: [Describe]
```

### Check Documentation:
- `SPECIFICATION_SECTION_SETUP.md` - Setup guide
- `SPECIFICATION_SECTION_QUICKSTART.md` - Quick start
- `SPECIFICATION_SECTION_VISUAL.md` - Visual guide
- `SPECIFICATION_SECTION_CHECKLIST.md` - Installation checklist

---

## ✅ Prevention Tips

1. **Always backup database before changes**
2. **Test on localhost before production**
3. **Use version control (Git)**
4. **Keep documentation updated**
5. **Test on multiple browsers**
6. **Validate image URLs before saving**
7. **Use consistent naming conventions**
8. **Comment complex code**

---

**Last Updated**: 2024
**Difficulty**: Most issues are easy to fix (⭐⭐☆☆☆)
