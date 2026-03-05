# Model Overview - Visual Structure

## 📊 Database Structure

```
┌─────────────────────────────────────────────────────────────┐
│                    model_categories                          │
├─────────────────────────────────────────────────────────────┤
│ id  │ name      │ slug      │ count │ sort_order           │
├─────┼───────────┼───────────┼───────┼──────────────────────┤
│ 1   │ All       │ all       │ 83    │ 0                    │
│ 2   │ 718       │ 718       │ 10    │ 1                    │
│ 3   │ 911       │ 911       │ 22    │ 2                    │
│ 4   │ Taycan    │ taycan    │ 14    │ 3                    │
│ 5   │ Panamera  │ panamera  │ 7     │ 4                    │
│ 6   │ Macan     │ macan     │ 9     │ 5                    │
│ 7   │ Cayenne   │ cayenne   │ 21    │ 6                    │
└─────┴───────────┴───────────┴───────┴──────────────────────┘
                            │
                            │ Foreign Key
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                    model_variants                            │
├─────────────────────────────────────────────────────────────┤
│ id │ category_id │ name              │ variant_group       │
│ image │ fuel_type │ drive_type │ transmission            │
│ acceleration │ power_kw │ power_ps │ top_speed            │
│ body_design │ seats │ is_new │ sort_order                 │
└─────────────────────────────────────────────────────────────┘
```

## 🗂️ File Structure

```
lending_word/
│
├── 📄 models.php                    ← Frontend: Model Overview Page
│   ├── Sidebar Filters
│   │   ├── Category Filter (Radio)
│   │   ├── Body Design (Checkbox)
│   │   ├── Seats (Checkbox)
│   │   ├── Drive Type (Checkbox)
│   │   └── Fuel Type (Checkbox)
│   │
│   └── Main Content
│       ├── Variant Groups
│       └── Variant Cards
│           ├── Image
│           ├── Name
│           ├── Tags (Fuel, Drive, Transmission)
│           ├── Specs (Acceleration, Power, Speed)
│           └── Actions (Select, Compare)
│
├── 📁 admin/
│   └── 📄 manage_models.php         ← Backend: Admin Management
│       ├── Add Variant Form
│       └── Variants Table (Edit/Delete)
│
├── 📁 app/
│   ├── 📁 models/
│   │   ├── 📄 ModelVariant.php      ← Model Class
│   │   │   ├── getCategories()
│   │   │   ├── getVariantsByCategory()
│   │   │   ├── getVariantsGrouped()
│   │   │   └── getFilters()
│   │   │
│   │   └── 📄 SocialLink.php        ← Social Links Model
│   │
│   ├── 📁 controllers/
│   │   └── 📄 FrontendController.php (Updated)
│   │
│   └── 📁 views/
│       └── 📁 frontend/
│           └── 📄 index.php         (Updated: Explore button)
│
├── 📄 models_setup.sql              ← Database Setup (Required)
├── 📄 models_additional_data.sql    ← Additional Data (Optional)
│
└── 📁 Documentation/
    ├── 📄 MODEL_OVERVIEW_QUICKSTART.md
    ├── 📄 MODEL_OVERVIEW_SETUP.md
    └── 📄 MODEL_OVERVIEW_SUMMARY.md
```

## 🔄 Data Flow

```
┌─────────────────┐
│  Landing Page   │
│   (index.php)   │
└────────┬────────┘
         │
         │ Click "Explore" Button
         │
         ▼
┌─────────────────────────────────────────┐
│         models.php?category=911         │
├─────────────────────────────────────────┤
│                                         │
│  ┌──────────────┐  ┌────────────────┐  │
│  │   Sidebar    │  │  Main Content  │  │
│  │   Filters    │  │   Variants     │  │
│  └──────┬───────┘  └────────┬───────┘  │
│         │                   │          │
│         └───────┬───────────┘          │
│                 │                      │
└─────────────────┼──────────────────────┘
                  │
                  ▼
         ┌────────────────┐
         │ ModelVariant   │
         │    Class       │
         └────────┬───────┘
                  │
                  ▼
         ┌────────────────┐
         │   PostgreSQL   │
         │    Database    │
         └────────────────┘
```

## 🎨 UI Layout

```
┌─────────────────────────────────────────────────────────────┐
│                     Model overview                           │
└─────────────────────────────────────────────────────────────┘

┌──────────────┬──────────────────────────────────────────────┐
│              │                                              │
│   SIDEBAR    │           MAIN CONTENT                       │
│              │                                              │
│ ┌──────────┐ │  ┌────────────────────────────────────────┐ │
│ │ Models   │ │  │  911 Carrera Model variants            │ │
│ │          │ │  │  ↔ Compare model variants              │ │
│ │ ○ All    │ │  └────────────────────────────────────────┘ │
│ │ ○ 718    │ │                                              │
│ │ ● 911    │ │  ┌──────────┐  ┌──────────┐  ┌──────────┐  │
│ │ ○ Taycan │ │  │  Card 1  │  │  Card 2  │  │  Card 3  │  │
│ └──────────┘ │  │          │  │          │  │          │  │
│              │  │  Image   │  │  Image   │  │  Image   │  │
│ ┌──────────┐ │  │  Name    │  │  Name    │  │  Name    │  │
│ │ Body     │ │  │  Tags    │  │  Tags    │  │  Tags    │  │
│ │ Design   │ │  │  Specs   │  │  Specs   │  │  Specs   │  │
│ │ ▼        │ │  │  Buttons │  │  Buttons │  │  Buttons │  │
│ │ □ Coupe  │ │  └──────────┘  └──────────┘  └──────────┘  │
│ │ □ Cabrio │ │                                              │
│ └──────────┘ │  ┌────────────────────────────────────────┐ │
│              │  │  911 Carrera Cabriolet variants        │ │
│ ┌──────────┐ │  └────────────────────────────────────────┘ │
│ │ Seats    │ │                                              │
│ │ ▼        │ │  ┌──────────┐  ┌──────────┐  ┌──────────┐  │
│ │ □ 2      │ │  │  Card 4  │  │  Card 5  │  │  Card 6  │  │
│ │ □ 4      │ │  │   ...    │  │   ...    │  │   ...    │  │
│ └──────────┘ │  └──────────┘  └──────────┘  └──────────┘  │
│              │                                              │
│ [Reset]      │                                              │
│              │                                              │
└──────────────┴──────────────────────────────────────────────┘
```

## 🔀 User Journey

```
1. User di Landing Page
   │
   ├─→ Lihat "Explore Models" Section
   │   │
   │   └─→ Klik "Explore" pada card "911"
   │       │
   │       └─→ Redirect ke models.php?category=911
   │
2. User di Model Overview Page
   │
   ├─→ Lihat semua 911 variants (22 models)
   │   │
   │   ├─→ Filter by Body Design (Coupe/Cabriolet/Targa)
   │   ├─→ Filter by Seats (2/4)
   │   ├─→ Filter by Drive (RWD/AWD)
   │   └─→ Filter by Fuel (Gasoline)
   │
   ├─→ Lihat spesifikasi detail
   │   ├─→ Acceleration
   │   ├─→ Power (kW/PS)
   │   └─→ Top Speed
   │
   └─→ Action
       ├─→ Select Model
       └─→ Compare Models
```

## 🔧 Admin Workflow

```
1. Admin Login
   │
   └─→ Dashboard
       │
       └─→ Tab "Model Variants"
           │
           ├─→ Add New Variant
           │   ├─→ Select Category
           │   ├─→ Fill Form
           │   │   ├─→ Name, Image
           │   │   ├─→ Fuel, Drive, Transmission
           │   │   ├─→ Acceleration, Power, Speed
           │   │   ├─→ Body Design, Seats
           │   │   └─→ Mark as New (optional)
           │   │
           │   └─→ Submit → Saved to Database
           │
           └─→ Manage Existing Variants
               ├─→ View Table
               └─→ Delete Variant
```

## 📈 Performance

```
Database Queries:
├─→ getCategories()           : 1 query  (7 rows)
├─→ getVariantsByCategory()   : 1 query  (22 rows for 911)
├─→ getVariantsGrouped()      : 1 query  (grouped in PHP)
└─→ getFilters()              : 4 queries (distinct values)

Total: ~6 queries per page load
Response Time: < 100ms (typical)
```

## 🎯 Key Features Visualization

```
┌─────────────────────────────────────────────────────────────┐
│                    FILTER SYSTEM                             │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Category Filter (Radio)                                     │
│  ┌──────┬──────┬──────┬──────┬──────┬──────┬──────┐        │
│  │ All  │ 718  │ 911  │Taycan│Panam.│Macan │Cayen.│        │
│  └──────┴──────┴──────┴──────┴──────┴──────┴──────┘        │
│                                                              │
│  Attribute Filters (Checkbox)                                │
│  ┌─────────────┬─────────────┬─────────────┬─────────────┐ │
│  │Body Design  │   Seats     │    Drive    │  Fuel Type  │ │
│  ├─────────────┼─────────────┼─────────────┼─────────────┤ │
│  │□ Coupe      │□ 2 seats    │□ RWD        │□ Gasoline   │ │
│  │□ Cabriolet  │□ 4 seats    │□ AWD        │□ Electric   │ │
│  │□ Targa      │□ 5 seats    │             │□ Hybrid     │ │
│  └─────────────┴─────────────┴─────────────┴─────────────┘ │
│                                                              │
│  [Reset Filter]                                              │
│                                                              │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                    VARIANT CARD                              │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────────────────────────────────────────────┐    │
│  │                                            [New]     │    │
│  │              [Car Image]                             │    │
│  │                                                      │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                              │
│  911 Carrera S                                               │
│                                                              │
│  [Gasoline] [Rear-Wheel Drive] [Automatic]                  │
│                                                              │
│  3.5 s                                                       │
│  Acceleration 0 - 100 km/h                                   │
│                                                              │
│  353 kW / 480 PS                                             │
│  Power (kW) / Power (PS)                                     │
│                                                              │
│  308 km/h                                                    │
│  Top speed                                                   │
│                                                              │
│  [Select model]  [Compare]                                   │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

---

**Visual Guide Version**: 1.0  
**Last Updated**: 2024
