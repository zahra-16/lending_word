# Sound Section - Installation Instructions

## 🚀 Quick Install (Recommended)

### Step 1: Import Database
```bash
# Windows (Command Prompt)
cd C:\laragon\www\lending_word
psql -U postgres -d landing_cms -f sound_section_setup.sql

# Windows (PowerShell)
cd C:\laragon\www\lending_word
& "C:\Program Files\PostgreSQL\15\bin\psql.exe" -U postgres -d landing_cms -f sound_section_setup.sql
```

### Step 2: Verify Installation
```bash
# Check if data inserted
psql -U postgres -d landing_cms -c "SELECT * FROM content WHERE section = 'sound';"
```

Expected output:
```
 id | section | key_name    | value                                    | type  
----+---------+-------------+------------------------------------------+-------
 XX | sound   | title       | Set the pace: 9,000 revolutions per...  | text
 XX | sound   | caption     | The naturally aspirated engine and...   | text
 XX | sound   | image       | https://files.porsche.com/filestore...  | image
 XX | sound   | button_text | Hold for sound                          | text
```

### Step 3: Test Frontend
1. Open browser: http://localhost/lending_word/
2. Scroll down to sound section (after Explore Models)
3. Verify:
   - ✅ Background image loads
   - ✅ Title displays
   - ✅ Caption displays
   - ✅ Button shows with play icon

### Step 4: Test Admin Panel
1. Open: http://localhost/lending_word/admin/
2. Login with admin credentials
3. Click "Sound" tab
4. Verify:
   - ✅ Form fields populated
   - ✅ Image preview shows
   - ✅ Can edit and save

## 🔧 Manual Install (Alternative)

### If SQL file doesn't work:

#### 1. Open pgAdmin
- Connect to PostgreSQL server
- Select database: `landing_cms`
- Open Query Tool

#### 2. Copy-Paste SQL
```sql
-- Copy this entire block and run in Query Tool

INSERT INTO content (section, key_name, value, type) VALUES
('sound', 'title', 'Set the pace: 9,000 revolutions per minute.', 'text'),
('sound', 'caption', 'The naturally aspirated engine and sport exhaust system ensure an unfiltered sound experience.', 'text'),
('sound', 'image', 'https://files.porsche.com/filestore/image/multimedia/none/992-gt3-rs-modelimage-sideshot/normal/d3e8e4e5-3e3e-11ed-80f6-005056bbdc38;sK;twebp/porsche-normal.webp', 'image'),
('sound', 'button_text', 'Hold for sound', 'text')
ON CONFLICT (section, key_name) DO UPDATE SET value = EXCLUDED.value;
```

#### 3. Execute Query
- Click "Execute" (F5)
- Check for success message
- Verify 4 rows affected

## 🧪 Testing Checklist

### Frontend Test
```
□ Visit: http://localhost/lending_word/
□ Scroll to sound section
□ Check background image loads
□ Check title visible and centered
□ Check caption visible
□ Check button visible with icon
□ Test button hover effect
□ Test on mobile (resize browser)
□ Check no console errors (F12)
```

### Admin Test
```
□ Visit: http://localhost/lending_word/admin/
□ Login successfully
□ Click "Sound" tab
□ Check form loads
□ Edit title field
□ Click "Save Changes"
□ Check success message
□ Visit frontend to verify change
□ Click "Preview Section" link
```

## 🐛 Troubleshooting

### Problem: "psql: command not found"

**Solution 1:** Add PostgreSQL to PATH
```bash
# Windows
set PATH=%PATH%;C:\Program Files\PostgreSQL\15\bin
```

**Solution 2:** Use full path
```bash
"C:\Program Files\PostgreSQL\15\bin\psql.exe" -U postgres -d landing_cms -f sound_section_setup.sql
```

**Solution 3:** Use pgAdmin (see Manual Install above)

---

### Problem: "database landing_cms does not exist"

**Solution:** Create database first
```bash
psql -U postgres -c "CREATE DATABASE landing_cms;"
```

Then run main setup:
```bash
psql -U postgres -d landing_cms -f setup.sql
```

Then run sound setup:
```bash
psql -U postgres -d landing_cms -f sound_section_setup.sql
```

---

### Problem: Sound section not showing on frontend

**Solution 1:** Clear browser cache
- Press Ctrl+Shift+R (hard reload)
- Or Ctrl+F5

**Solution 2:** Check database
```sql
SELECT * FROM content WHERE section = 'sound';
```
If empty, re-run SQL setup.

**Solution 3:** Check file changes
- Verify `app/views/frontend/index.php` has sound section
- Verify `public/assets/css/style.css` has sound styles

---

### Problem: Admin "Sound" tab not showing

**Solution 1:** Clear PHP cache
- Restart Laragon/XAMPP
- Refresh admin page

**Solution 2:** Check file
- Open `app/views/admin/dashboard.php`
- Search for "Sound" tab
- Verify it exists in tabs section

**Solution 3:** Check session
- Logout and login again
- Clear browser cookies

---

### Problem: Image not loading

**Solution 1:** Check URL
- Copy image URL
- Paste in browser address bar
- Verify image loads

**Solution 2:** Use different image
- Try Unsplash: https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1920
- Or local image: /lending_word/public/assets/images/your-image.jpg

**Solution 3:** Check CORS
- If using external image, check CORS policy
- Use images from same domain if possible

---

### Problem: Changes not saving in admin

**Solution 1:** Check database connection
```php
// In config.php, verify:
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'landing_cms');
define('DB_USER', 'postgres');
define('DB_PASS', 'your_password');
```

**Solution 2:** Check PHP errors
- Enable error reporting
- Check Laragon/XAMPP error logs
- Check browser Network tab for failed requests

**Solution 3:** Check permissions
- Verify database user has UPDATE permission
- Check file write permissions

## 📞 Support Resources

### Documentation
- Full Setup: `SOUND_SECTION_SETUP.md`
- Quick Start: `SOUND_SECTION_QUICKSTART.md`
- Visual Guide: `SOUND_SECTION_VISUAL.md`
- Checklist: `SOUND_SECTION_CHECKLIST.md`
- Summary: `SOUND_SECTION_SUMMARY.md`

### Database Files
- Setup SQL: `sound_section_setup.sql`
- Main Setup: `setup.sql`

### Code Files
- Frontend: `app/views/frontend/index.php`
- Admin: `app/views/admin/dashboard.php`
- CSS: `public/assets/css/style.css`

## ✅ Success Confirmation

If you see this, installation is successful:

### Frontend
```
✓ Sound section visible after Explore Models
✓ Background image showing
✓ Title: "Set the pace: 9,000 revolutions per minute."
✓ Caption visible below title
✓ Button: "Hold for sound" with play icon
✓ No errors in console
```

### Admin
```
✓ "Sound" tab in navigation
✓ Form with 4 fields
✓ Image preview showing
✓ Save button working
✓ Preview link working
```

### Database
```
✓ 4 rows in content table with section = 'sound'
✓ All values populated
✓ No errors in query
```

## 🎉 Next Steps

1. **Customize Content**
   - Login to admin panel
   - Edit title, caption, image, button text
   - Save and preview

2. **Add Your Images**
   - Upload images to `public/assets/images/`
   - Use relative path in admin: `/lending_word/public/assets/images/your-image.jpg`

3. **Test Responsive**
   - Resize browser window
   - Test on mobile device
   - Verify text readable on all sizes

4. **Optional Enhancements**
   - Add audio player functionality
   - Add video background option
   - Add scroll animations
   - Add multiple sound sections

---

**Installation Complete! 🎊**

Your sound section is now live and fully editable via admin panel.
