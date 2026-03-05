# Sound Section - Visual Guide

## 📐 Layout Structure

```
┌─────────────────────────────────────────────────────────────┐
│                     SOUND SECTION                           │
│                    (100vh height)                           │
│                                                             │
│  ┌───────────────────────────────────────────────────────┐ │
│  │                                                         │ │
│  │         [Background Image - Full Screen]               │ │
│  │         (Dark overlay: brightness 0.6)                 │ │
│  │                                                         │ │
│  │              ┌─────────────────────┐                   │ │
│  │              │                     │                   │ │
│  │              │   TITLE (Large)     │                   │ │
│  │              │   White, Centered   │                   │ │
│  │              │                     │                   │ │
│  │              └─────────────────────┘                   │ │
│  │                                                         │ │
│  │              ┌─────────────────────┐                   │ │
│  │              │                     │                   │ │
│  │              │  Caption (Medium)   │                   │ │
│  │              │  White, Centered    │                   │ │
│  │              │                     │                   │ │
│  │              └─────────────────────┘                   │ │
│  │                                                         │ │
│  │                  ┌─────────────┐                       │ │
│  │                  │  ▶ Button   │                       │ │
│  │                  └─────────────┘                       │ │
│  │                                                         │ │
│  └───────────────────────────────────────────────────────┘ │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

## 🎨 Component Breakdown

### 1. Container (.sound-section)
```
Position: relative
Height: 100vh (full viewport)
Display: flex
Align: center
Justify: center
Background: #000
Overflow: hidden
```

### 2. Background Image (.sound-bg)
```
Position: absolute
Size: 100% x 100%
Object-fit: cover
Filter: brightness(0.6)
Z-index: 0
```

### 3. Content Wrapper (.sound-content)
```
Position: relative
Z-index: 2
Text-align: center
Color: #fff
Padding: 40px
Max-width: 1200px
```

### 4. Title (h2)
```
Font-size: 2.5rem - 4.5rem (responsive)
Font-weight: 400
Letter-spacing: -0.02em
Margin-bottom: 30px
Line-height: 1.2
```

### 5. Caption (p)
```
Font-size: 1rem - 1.3rem (responsive)
Font-weight: 300
Margin-bottom: 50px
Opacity: 0.9
Max-width: 900px
```

### 6. Button (.sound-btn)
```
Display: inline-flex
Align-items: center
Gap: 12px
Padding: 14px 32px
Background: rgba(255,255,255,0.95)
Color: #000
Border-radius: 4px
Font-size: 0.95rem
```

## 📱 Responsive Behavior

### Desktop (> 768px)
```
┌─────────────────────────────────────┐
│                                     │
│    Set the pace: 9,000 revolutions │
│         per minute.                 │
│                                     │
│  The naturally aspirated engine... │
│                                     │
│        [▶ Hold for sound]          │
│                                     │
└─────────────────────────────────────┘

Title: 4.5rem
Caption: 1.3rem
Button: Full size
```

### Mobile (≤ 768px)
```
┌─────────────────────┐
│                     │
│  Set the pace:      │
│  9,000 revolutions  │
│  per minute.        │
│                     │
│  The naturally...   │
│                     │
│  [▶ Hold for sound] │
│                     │
└─────────────────────┘

Title: 2rem
Caption: 1rem
Button: Compact
```

## 🎯 Page Position

```
Landing Page Flow:
├── Hero Section
├── About Section
├── Models Section
├── Explore Models Section
├── ─────────────────────
├── 🔊 SOUND SECTION ← HERE
├── ─────────────────────
├── Inventory Section
├── Discover Features
├── CTA Section
└── Footer
```

## 🗂️ File Structure

```
lending_word/
│
├── sound_section_setup.sql          ← Database setup
│
├── app/
│   ├── views/
│   │   ├── frontend/
│   │   │   └── index.php            ← Sound section HTML
│   │   └── admin/
│   │       └── dashboard.php        ← Sound tab admin
│   └── controllers/
│       └── AdminController.php      ← (No changes needed)
│
├── public/
│   └── assets/
│       └── css/
│           └── style.css            ← Sound section CSS
│
└── Documentation/
    ├── SOUND_SECTION_SETUP.md       ← Full guide
    ├── SOUND_SECTION_QUICKSTART.md  ← Quick start
    ├── SOUND_SECTION_SUMMARY.md     ← Summary
    ├── SOUND_SECTION_CHECKLIST.md   ← Testing checklist
    └── SOUND_SECTION_VISUAL.md      ← This file
```

## 🎨 Color Palette

```
Background:
├── Image overlay: brightness(0.6)
└── Fallback: #000 (black)

Text:
├── Title: #fff (white)
├── Caption: #fff (white, 90% opacity)
└── Button text: #000 (black)

Button:
├── Background: rgba(255,255,255,0.95)
├── Hover background: #fff
└── Hover shadow: rgba(255,255,255,0.2)
```

## 📊 Database Schema

```
Table: content
┌────┬─────────┬────────────┬──────────────────────┬──────┐
│ id │ section │ key_name   │ value                │ type │
├────┼─────────┼────────────┼──────────────────────┼──────┤
│ XX │ sound   │ title      │ Set the pace: 9,000..│ text │
│ XX │ sound   │ caption    │ The naturally aspir..│ text │
│ XX │ sound   │ image      │ https://files.porsc..│ image│
│ XX │ sound   │ button_text│ Hold for sound       │ text │
└────┴─────────┴────────────┴──────────────────────┴──────┘
```

## 🔄 Data Flow

```
1. User visits landing page
   ↓
2. PHP loads content from database
   ↓
3. Frontend renders sound section
   ↓
4. CSS applies styling
   ↓
5. User sees cinematic section

Admin Edit Flow:
1. Admin opens admin panel
   ↓
2. Clicks "Sound" tab
   ↓
3. Edits fields (title, caption, image, button)
   ↓
4. Clicks "Save Changes"
   ↓
5. PHP updates database
   ↓
6. Changes visible on frontend
```

## 🎬 Animation States

### Initial State
```
Section: Visible
Background: Static
Title: Visible
Caption: Visible
Button: Default state
```

### Hover State (Button)
```
Button:
├── Transform: translateY(-2px)
├── Background: #fff
└── Shadow: 0 10px 30px rgba(255,255,255,0.2)
```

### Mobile State
```
Font sizes: Reduced
Padding: Adjusted
Layout: Same (centered)
```

## 📐 Spacing Guide

```
Section:
├── Padding: 40px
├── Min-height: 100vh
└── Max-width content: 1200px

Title:
├── Margin-bottom: 30px
└── Line-height: 1.2

Caption:
├── Margin-bottom: 50px
├── Max-width: 900px
└── Line-height: 1.6

Button:
├── Padding: 14px 32px
├── Gap (icon-text): 12px
└── Border-radius: 4px
```

## 🖼️ Image Requirements

### Recommended Specs
```
Format: JPG, PNG, WebP
Size: 1920x1080 or higher
Aspect ratio: 16:9
File size: < 500KB (optimized)
Quality: High (but compressed)
```

### Example URLs
```
Porsche Official:
https://files.porsche.com/filestore/image/...

Unsplash:
https://images.unsplash.com/photo-...

Local:
/lending_word/public/assets/images/sound-bg.jpg
```

## ✨ Best Practices

### Content
- ✅ Keep title short and impactful
- ✅ Caption should be 1-2 sentences
- ✅ Use high-quality images
- ✅ Ensure text readable on image

### Performance
- ✅ Optimize images (compress)
- ✅ Use WebP format when possible
- ✅ Lazy load if below fold
- ✅ Test on slow connections

### Accessibility
- ✅ Ensure contrast ratio > 4.5:1
- ✅ Add alt text to images
- ✅ Button has clear purpose
- ✅ Keyboard accessible

## 🎯 Success Metrics

Visual Quality:
- [ ] Image loads < 2 seconds
- [ ] Text clearly readable
- [ ] Button stands out
- [ ] Professional appearance

Functionality:
- [ ] Responsive on all devices
- [ ] No layout shifts
- [ ] Smooth hover effects
- [ ] Admin edits work

User Experience:
- [ ] Engaging visual
- [ ] Clear message
- [ ] Intuitive button
- [ ] Fast loading

---

**Reference Image Style:**
Porsche GT3 RS on track with large title overlay and "Hold for sound" button
