# Model Specification Sections - Visual Guide

## 📐 Layout Structure

```
┌─────────────────────────────────────────────────────────────┐
│                                                               │
│  FULL-SCREEN BACKGROUND IMAGE (Parallax)                     │
│  ┌──────────────────────────────────────────────┐            │
│  │  Dark Overlay (rgba(0,0,0,0.5))              │            │
│  │                                               │            │
│  │  ┌─────────────────────────────┐              │            │
│  │  │  TITLE (White, Large)       │              │            │
│  │  │  Description (White, Small) │              │            │
│  │  └─────────────────────────────┘              │            │
│  │                                               │            │
│  │  ┌──────────────────────────────────────┐    │            │
│  │  │  CAROUSEL CONTAINER                  │    │            │
│  │  │  ┌────────┐ ┌────────┐ ┌────────┐   │    │            │
│  │  │  │ Card 1 │ │ Card 2 │ │ Card 3 │   │    │            │
│  │  │  │ [IMG]  │ │ [IMG]  │ │ [IMG]  │   │    │            │
│  │  │  │ Title  │ │ Title  │ │ Title  │   │    │            │
│  │  │  │ Desc   │ │ Desc   │ │ Desc   │   │    │            │
│  │  │  └────────┘ └────────┘ └────────┘   │    │            │
│  │  │  ←                              →   │    │            │
│  │  └──────────────────────────────────────┘    │            │
│  └───────────────────────────────────────────────┘            │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

## 🎨 Component Breakdown

### 1. Section Container
```
.specification-carousel-section
├── Background Image (full-screen, parallax)
├── Dark Overlay (::before pseudo-element)
└── Content Wrapper
    ├── Text Area (Title + Description)
    └── Carousel Area (Cards + Arrows)
```

### 2. Carousel Cards
```
Card Structure:
┌─────────────────┐
│                 │
│   [IMAGE]       │  ← 300px height
│                 │
├─────────────────┤
│  Title          │  ← 1.5rem, bold
│  Description    │  ← 0.95rem, regular
│                 │
└─────────────────┘
```

### 3. Navigation
```
[←]  Card 1  Card 2  Card 3  [→]
     ↑ Current view (3 cards)
```

## 📊 Database Relationship

```
model_variants (id)
       ↓
       │ (variant_id FK)
       ↓
model_specification_sections
       ├── id
       ├── variant_id
       ├── background_image
       ├── title
       ├── description
       └── sort_order
       ↓
       │ (section_id FK)
       ↓
model_specification_section_images
       ├── id
       ├── section_id
       ├── image_url
       ├── title
       ├── description
       └── sort_order
```

## 🔄 Data Flow

```
Admin Panel (specification.php)
       ↓
       │ POST Request
       ↓
ModelSpecificationSection.php
       ↓
       │ SQL Query
       ↓
PostgreSQL Database
       ↓
       │ Fetch Data
       ↓
model-detail.php (Frontend)
       ↓
       │ Render HTML
       ↓
User Browser
```

## 📱 Responsive Behavior

```
Desktop (>1024px):
┌────────┐ ┌────────┐ ┌────────┐
│ Card 1 │ │ Card 2 │ │ Card 3 │
└────────┘ └────────┘ └────────┘

Tablet (768px - 1024px):
┌────────┐ ┌────────┐
│ Card 1 │ │ Card 2 │
└────────┘ └────────┘

Mobile (<768px):
┌────────┐
│ Card 1 │
└────────┘
```

## 🎯 Admin Panel Flow

```
1. Login
   ↓
2. Manage Model Variants
   ↓
3. Click "Specification" Button
   ↓
4. Specification Management Page
   ├── Tab: Sections
   │   ├── Add New Section
   │   ├── Update Section
   │   └── Delete Section
   └── Tab: Section Images
       ├── Add New Image
       ├── Update Image
       └── Delete Image
```

## 🖼️ Image Requirements

### Background Image
```
Recommended:
- Size: 1920x1080px (Full HD)
- Format: JPG/PNG
- Quality: High (80-90%)
- Aspect: 16:9
- Subject: Car in environment
```

### Carousel Card Images
```
Recommended:
- Size: 800x600px
- Format: JPG/PNG
- Quality: Medium-High (70-80%)
- Aspect: 4:3
- Subject: Detail shots (engine, interior, etc)
```

## 🎨 Color Scheme

```
Background Overlay: rgba(0,0,0,0.5)
Text (Title): #FFFFFF
Text (Description): rgba(255,255,255,0.95)
Card Background: rgba(255,255,255,0.95)
Card Title: #000000
Card Description: #333333
Arrow Background: rgba(255,255,255,0.9)
Arrow Hover: #FFFFFF
```

## 📏 Spacing & Sizing

```
Section:
- Min Height: 100vh
- Padding: 100px 60px
- Margin: 100px 0
- Border Radius: 24px

Text Area:
- Max Width: 600px
- Margin Bottom: 80px

Carousel:
- Padding: 0 60px
- Gap: 30px

Cards:
- Width: calc(33.333% - 20px)
- Border Radius: 12px
- Padding: 30px
- Shadow: 0 20px 60px rgba(0,0,0,0.3)

Arrows:
- Size: 50px × 50px
- Border Radius: 50%
- Font Size: 1.5rem
```

## 🔄 Carousel Logic

```javascript
Current Index: 0

User clicks [→]:
├── Index + 1
├── If Index >= Total Cards: Index = 0
└── Translate: -Index × CardWidth

User clicks [←]:
├── Index - 1
├── If Index < 0: Index = Total Cards - 1
└── Translate: -Index × CardWidth
```

## 📂 File Organization

```
lending_word/
├── database/
│   └── model_specification_sections.sql
│       ├── CREATE TABLE model_specification_sections
│       ├── CREATE TABLE model_specification_section_images
│       ├── CREATE INDEX
│       └── INSERT sample data
│
├── app/
│   ├── models/
│   │   └── ModelSpecificationSection.php
│   │       ├── getByVariantId()
│   │       ├── getSectionImages()
│   │       ├── create()
│   │       ├── update()
│   │       ├── delete()
│   │       ├── addImage()
│   │       ├── updateImage()
│   │       └── deleteImage()
│   │
│   └── views/
│       └── frontend/
│           └── model-detail.php
│               ├── PHP: Fetch data
│               ├── HTML: Render sections
│               ├── CSS: Styling
│               └── JS: Carousel logic
│
└── admin/
    └── specification.php
        ├── Session check
        ├── CRUD handlers
        ├── Forms (modals)
        └── Tables (display)
```

## 🎬 Animation Flow

```
Page Load:
1. Background image loads (parallax)
2. Overlay fades in
3. Text appears
4. Cards slide in from right
5. Arrows fade in

User Interaction:
1. Click arrow
2. Track translates (0.5s ease)
3. Cards slide smoothly
4. New cards visible
```

## ✅ Quality Checklist

```
Visual:
☑ Background image high quality
☑ Text readable on all backgrounds
☑ Cards aligned properly
☑ Arrows visible and clickable
☑ Responsive on all devices

Functional:
☑ Carousel slides smoothly
☑ Arrows work correctly
☑ Loop works (infinite scroll)
☑ No JavaScript errors
☑ Data loads correctly

Performance:
☑ Images optimized
☑ CSS minified (production)
☑ JS efficient
☑ No layout shift
☑ Fast load time
```

## 🎯 Example Use Case

```
Porsche 911 Targa 4S - Drive Section

Background: 
https://images.unsplash.com/photo-1503376780353-7e6692767b70

Title: "Drive"

Description: "Never before have we been able to make a car 
so powerful and so efficient at the same time."

Carousel Images:
1. Engine photo → "3.0-litre flat-6 engine" → Details
2. Turbo photo → "Twin-turbo system" → Details  
3. Exhaust photo → "Sport exhaust system" → Details

Result: Cinematic showcase of engine technology
```

---

## 📐 CSS Grid Breakdown

```css
/* Desktop: 3 columns */
.specification-carousel-track {
    display: flex;
    gap: 30px;
}

.specification-carousel-card {
    min-width: calc(33.333% - 20px);
    /* = (100% / 3) - (gap adjustment) */
}

/* Tablet: 2 columns */
@media (max-width: 1024px) {
    min-width: calc(50% - 15px);
    /* = (100% / 2) - (gap adjustment) */
}

/* Mobile: 1 column */
@media (max-width: 768px) {
    min-width: 100%;
}
```

---

This visual guide helps understand the complete structure and flow of the Model Specification Sections feature! 🎨
