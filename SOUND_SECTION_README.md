# 🔊 Sound Section - Complete Guide

> Cinematic full-screen section untuk showcase sound experience kendaraan dengan style premium Porsche

## 📸 Preview

```
┌─────────────────────────────────────────────────────────┐
│                                                         │
│         [Full-screen Background Image]                 │
│                                                         │
│     Set the pace: 9,000 revolutions per minute.       │
│                                                         │
│  The naturally aspirated engine and sport exhaust      │
│  system ensure an unfiltered sound experience.         │
│                                                         │
│              [▶ Hold for sound]                        │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

## ⚡ Quick Start (2 Menit)

```bash
# 1. Import database
psql -U postgres -d landing_cms -f sound_section_setup.sql

# 2. Refresh browser
# Buka: http://localhost/lending_word/

# 3. Done! ✅
```

## 📋 Table of Contents

1. [Features](#features)
2. [Installation](#installation)
3. [Usage](#usage)
4. [Customization](#customization)
5. [Documentation](#documentation)
6. [Troubleshooting](#troubleshooting)
7. [FAQ](#faq)

## ✨ Features

### Frontend
- ✅ Full-screen cinematic background
- ✅ Dark overlay untuk readability
- ✅ Responsive typography (clamp)
- ✅ Interactive button dengan icon
- ✅ Smooth hover effects
- ✅ Mobile-optimized
- ✅ Fast loading

### Backend Admin
- ✅ Dedicated "Sound" tab
- ✅ Edit title (text input)
- ✅ Edit caption (textarea)
- ✅ Edit background image URL
- ✅ Edit button text
- ✅ Live image preview
- ✅ One-click save
- ✅ Preview link

### Technical
- ✅ PostgreSQL database
- ✅ PHP backend
- ✅ Clean CSS (no frameworks)
- ✅ Font Awesome icons
- ✅ SEO-friendly
- ✅ Accessibility compliant

## 🚀 Installation

### Prerequisites
- ✅ Laragon/XAMPP running
- ✅ PostgreSQL service active
- ✅ Database `landing_cms` exists
- ✅ Admin login working

### Step 1: Database Setup

**Option A: Command Line (Recommended)**
```bash
cd C:\laragon\www\lending_word
psql -U postgres -d landing_cms -f sound_section_setup.sql
```

**Option B: pgAdmin**
1. Open pgAdmin
2. Connect to PostgreSQL
3. Select database: `landing_cms`
4. Open Query Tool
5. Copy-paste content from `sound_section_setup.sql`
6. Execute (F5)

### Step 2: Verify Installation

```sql
-- Run this query
SELECT * FROM content WHERE section = 'sound';

-- Should return 4 rows:
-- title, caption, image, button_text
```

### Step 3: Test

**Frontend:**
- Visit: http://localhost/lending_word/
- Scroll to sound section
- Verify all elements visible

**Admin:**
- Visit: http://localhost/lending_word/admin/
- Click "Sound" tab
- Verify form loads correctly

## 📖 Usage

### Edit Content via Admin

1. **Login to Admin Panel**
   ```
   URL: http://localhost/lending_word/admin/
   Username: admin
   Password: admin123
   ```

2. **Navigate to Sound Tab**
   - Click "Sound" in top navigation

3. **Edit Fields**
   - **Title**: Main heading (large text)
   - **Caption**: Subtitle/description (medium text)
   - **Background Image URL**: Full URL or relative path
   - **Button Text**: Text on button (default: "Hold for sound")

4. **Save Changes**
   - Click "Save Changes" button
   - Wait for success message

5. **Preview**
   - Click "Preview Section" link
   - Or visit: http://localhost/lending_word/#sound

### Example Content

**Title:**
```
Set the pace: 9,000 revolutions per minute.
```

**Caption:**
```
The naturally aspirated engine and sport exhaust system 
ensure an unfiltered sound experience.
```

**Image URL:**
```
https://files.porsche.com/filestore/image/multimedia/none/992-gt3-rs-modelimage-sideshot/normal/d3e8e4e5-3e3e-11ed-80f6-005056bbdc38;sK;twebp/porsche-normal.webp
```

**Button Text:**
```
Hold for sound
```

## 🎨 Customization

### Change Background Image

**Option 1: External URL**
```
https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1920
```

**Option 2: Local File**
```
/lending_word/public/assets/images/sound-bg.jpg
```

**Option 3: Porsche Official**
```
https://files.porsche.com/filestore/image/...
```

### Modify Styles

Edit: `public/assets/css/style.css`

**Change overlay darkness:**
```css
.sound-bg {
    filter: brightness(0.6); /* 0.0 = black, 1.0 = original */
}
```

**Change button color:**
```css
.sound-btn {
    background: rgba(255,255,255,0.95); /* White */
    color: #000; /* Black text */
}
```

**Change text color:**
```css
.sound-content {
    color: #fff; /* White */
}
```

**Change font sizes:**
```css
.sound-content h2 {
    font-size: clamp(2.5rem, 5vw, 4.5rem); /* Min, Preferred, Max */
}

.sound-content p {
    font-size: clamp(1rem, 1.5vw, 1.3rem);
}
```

### Add Custom Animations

```css
/* Fade in on load */
.sound-section {
    animation: fadeIn 1s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
```

### Change Section Position

Edit: `app/views/frontend/index.php`

Move the sound section block to desired position:
```php
<!-- Sound Section -->
<section class="sound-section" id="sound">
    ...
</section>
```

## 📚 Documentation

### Complete Guides
- **Setup Guide**: `SOUND_SECTION_SETUP.md` - Full documentation
- **Quick Start**: `SOUND_SECTION_QUICKSTART.md` - 2-minute setup
- **Installation**: `SOUND_SECTION_INSTALL.md` - Detailed install steps
- **Visual Guide**: `SOUND_SECTION_VISUAL.md` - Layout & structure
- **Checklist**: `SOUND_SECTION_CHECKLIST.md` - Testing checklist
- **Summary**: `SOUND_SECTION_SUMMARY.md` - Implementation summary

### Code Files
- **Database**: `sound_section_setup.sql`
- **Frontend**: `app/views/frontend/index.php` (line ~XXX)
- **Admin**: `app/views/admin/dashboard.php` (Sound tab)
- **CSS**: `public/assets/css/style.css` (Sound section)

## 🐛 Troubleshooting

### Sound section not showing

**Check 1: Database**
```sql
SELECT * FROM content WHERE section = 'sound';
```
If empty, re-run: `sound_section_setup.sql`

**Check 2: Browser Cache**
- Press Ctrl+Shift+R (hard reload)
- Or clear cache manually

**Check 3: Console Errors**
- Press F12
- Check Console tab for errors

### Image not loading

**Check 1: URL Valid**
- Copy image URL
- Paste in browser
- Verify it loads

**Check 2: CORS**
- Use images from same domain
- Or use Unsplash/Porsche official

**Check 3: File Path**
- For local images, use absolute path
- Example: `/lending_word/public/assets/images/sound.jpg`

### Admin tab not showing

**Check 1: File Saved**
- Verify `app/views/admin/dashboard.php` saved correctly
- Search for "Sound" tab in file

**Check 2: Cache**
- Restart Laragon/XAMPP
- Clear browser cache
- Logout and login again

**Check 3: Session**
- Check if admin session active
- Try logging out and in

### Changes not saving

**Check 1: Database Connection**
- Verify `config.php` settings
- Test database connection

**Check 2: Permissions**
- Check database user permissions
- Verify UPDATE permission granted

**Check 3: PHP Errors**
- Check Laragon/XAMPP error logs
- Enable error reporting in PHP

## ❓ FAQ

### Q: Can I add multiple sound sections?
**A:** Yes, but requires code modification. Duplicate the section in `index.php` and create new database entries with different section names (e.g., 'sound2', 'sound3').

### Q: Can I add audio player functionality?
**A:** Yes! Add an audio URL field to database, then use HTML5 `<audio>` element or JavaScript audio library. Button can trigger play/pause.

### Q: Can I use video instead of image?
**A:** Yes! Replace `<img>` with `<video>` tag in `index.php`. Update admin to accept video URLs.

### Q: Is it mobile responsive?
**A:** Yes! Font sizes automatically adjust using CSS `clamp()`. Tested on all screen sizes.

### Q: Can I change the button icon?
**A:** Yes! Edit `index.php`, find `.sound-btn` and change Font Awesome class:
```html
<i class="fas fa-play"></i>  <!-- Current -->
<i class="fas fa-volume-up"></i>  <!-- Alternative -->
```

### Q: How do I optimize images?
**A:** Use tools like:
- TinyPNG (https://tinypng.com)
- Squoosh (https://squoosh.app)
- ImageOptim (Mac)
- Recommended: WebP format, < 500KB

### Q: Can I add scroll animations?
**A:** Yes! Use libraries like:
- AOS (Animate On Scroll)
- ScrollReveal
- GSAP ScrollTrigger

### Q: Is it SEO-friendly?
**A:** Yes! Uses semantic HTML, proper heading hierarchy, and alt text for images.

### Q: Can I translate the content?
**A:** Yes! All text is editable via admin panel. Just change to your language.

### Q: How do I backup the content?
**A:** Export database:
```bash
pg_dump -U postgres -d landing_cms -t content > backup.sql
```

## 🎯 Best Practices

### Content
- ✅ Keep title concise (< 60 characters)
- ✅ Caption should be 1-2 sentences
- ✅ Use high-quality images (1920x1080+)
- ✅ Ensure text readable on image

### Performance
- ✅ Optimize images (< 500KB)
- ✅ Use WebP format when possible
- ✅ Lazy load if below fold
- ✅ Test on slow connections

### Accessibility
- ✅ Ensure contrast ratio > 4.5:1
- ✅ Add alt text to images
- ✅ Button has clear purpose
- ✅ Keyboard accessible

### Design
- ✅ Maintain brand consistency
- ✅ Use professional images
- ✅ Test on multiple devices
- ✅ Keep it simple and clean

## 🔄 Updates & Maintenance

### Regular Tasks
- [ ] Update images seasonally
- [ ] Refresh content quarterly
- [ ] Test on new devices
- [ ] Monitor loading speed
- [ ] Check for broken images

### Version History
- **v1.0** (Current) - Initial release
  - Full-screen section
  - Admin panel integration
  - Responsive design
  - Documentation complete

## 🤝 Contributing

Found a bug or have a suggestion?
1. Check existing documentation
2. Test on clean installation
3. Document steps to reproduce
4. Contact developer

## 📞 Support

### Resources
- Documentation: See files listed above
- Database: `sound_section_setup.sql`
- Code: Check file structure in docs

### Contact
For issues or questions, contact the developer.

## 📄 License

Part of Landing Page CMS project.

---

**Made with ❤️ for Porsche-style landing pages**

Last Updated: 2024
Version: 1.0
