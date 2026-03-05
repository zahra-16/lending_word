# Model Specification Sections - Installation Checklist

## ✅ Pre-Installation

- [ ] PostgreSQL service running
- [ ] Database `landing_cms` exists
- [ ] Admin login credentials ready (admin/admin123)
- [ ] Laragon/XAMPP running

## ✅ Database Setup

- [ ] Import `database/model_specification_sections.sql`
  ```bash
  psql -U postgres -d landing_cms -f database/model_specification_sections.sql
  ```
- [ ] Verify tables created:
  - [ ] `model_specification_sections`
  - [ ] `model_specification_section_images`
- [ ] Verify sample data inserted (variant_id = 1)
- [ ] Run test query: `test_specification_sections.sql`

## ✅ Files Created

### Database
- [x] `database/model_specification_sections.sql` - Schema + sample data

### Model
- [x] `app/models/ModelSpecificationSection.php` - Model class

### Frontend
- [x] `app/views/frontend/model-detail.php` - Updated with specification section display

### Admin
- [x] `admin/specification.php` - CRUD admin panel
- [x] `admin/manage_models.php` - Updated with "Specification" button

### Documentation
- [x] `SPECIFICATION_SECTION_SETUP.md` - Full setup guide
- [x] `SPECIFICATION_SECTION_SUMMARY.md` - Quick summary
- [x] `README.md` - Updated with new feature
- [x] `test_specification_sections.sql` - Test queries

## ✅ Testing

### 1. Database Test
- [ ] Run test SQL: `psql -U postgres -d landing_cms -f test_specification_sections.sql`
- [ ] Verify output shows tables exist
- [ ] Verify sample data count > 0

### 2. Admin Panel Test
- [ ] Login: http://localhost/lending_word/admin/
- [ ] Navigate to "Manage Model Variants"
- [ ] Click "Specification" button on any model
- [ ] Verify specification.php loads without errors

### 3. CRUD Operations Test
- [ ] **Add Section**: Click "Add New Section", fill form, save
- [ ] **View Section**: Verify section appears in table
- [ ] **Add Images**: Click "Images" button, add carousel images
- [ ] **Update Section**: Click "Update", modify data, save
- [ ] **Update Image**: Click "Update" on image, modify, save
- [ ] **Delete Image**: Click "Delete" on image, confirm
- [ ] **Delete Section**: Click "Delete" on section, confirm

### 4. Frontend Display Test
- [ ] Open: http://localhost/lending_word/app/views/frontend/model-detail.php?id=1
- [ ] Verify specification section appears BEFORE sound section
- [ ] Verify background image displays
- [ ] Verify title and description show
- [ ] Verify carousel cards display (3 cards)
- [ ] Test arrow navigation (left/right)
- [ ] Test responsive (resize browser)

### 5. Integration Test
- [ ] Verify no JavaScript errors in console
- [ ] Verify carousel slides smoothly
- [ ] Verify multiple sections work (if added)
- [ ] Verify section order follows sort_order

## ✅ Troubleshooting

### Database Issues
- **Tables not created**: Check PostgreSQL version (12+)
- **Foreign key error**: Ensure `model_variants` table exists
- **Sample data not inserted**: Check variant_id = 1 exists

### Admin Panel Issues
- **404 Error**: Check file path `admin/specification.php`
- **Session error**: Clear browser cookies, login again
- **Form not submitting**: Check POST method and action values

### Frontend Issues
- **Section not showing**: Check if data exists for that variant_id
- **Carousel not working**: Check JavaScript console for errors
- **Images not loading**: Verify image URLs are valid

### CSS Issues
- **Layout broken**: Clear browser cache (Ctrl+F5)
- **Cards not aligned**: Check browser compatibility
- **Responsive not working**: Test in different screen sizes

## ✅ Post-Installation

- [ ] Add specification sections for other model variants
- [ ] Upload custom images (or use Unsplash URLs)
- [ ] Customize CSS if needed
- [ ] Test on mobile devices
- [ ] Backup database after setup

## 📊 Expected Results

After successful installation:

1. **Database**: 2 new tables with sample data
2. **Admin**: New "Specification" button in manage models
3. **Frontend**: New section with carousel before sound section
4. **Functionality**: Full CRUD operations working

## 🎯 Success Criteria

- ✅ All database tables created
- ✅ Sample data visible in admin panel
- ✅ Specification section displays on model-detail.php
- ✅ Carousel navigation works
- ✅ CRUD operations successful
- ✅ No errors in browser console
- ✅ Responsive design works

## 📞 Need Help?

Check documentation:
- `SPECIFICATION_SECTION_SETUP.md` - Detailed setup guide
- `SPECIFICATION_SECTION_SUMMARY.md` - Quick reference
- `README.md` - General project info

---

**Installation Date**: _____________
**Installed By**: _____________
**Status**: [ ] Success [ ] Issues (describe below)

**Notes**:
_____________________________________________
_____________________________________________
_____________________________________________
