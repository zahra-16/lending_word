<?php
    session_start();
    if (!isset($_SESSION['admin_logged_in'])) {
        header('Location: login.php');
        exit;
    }

    require_once __DIR__ . '/../app/models/ModelVariant.php';
    require_once __DIR__ . '/../app/models/ModelSpecificationSection.php';

    $modelVariant = new ModelVariant();
    $modelSpec = new ModelSpecificationSection();

    $variantId = $_GET['variant_id'] ?? 0;
    $variant = $modelVariant->getById($variantId);

    if (!$variant) {
        header('Location: manage_models.php');
        exit;
    }

    // Handle actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'add_section') {
            $modelSpec->create(
                $variantId,
                $_POST['background_image'],
                $_POST['title'],
                $_POST['description'],
                $_POST['sort_order'] ?? 0
            );
            header('Location: specification.php?variant_id=' . $variantId);
            exit;
        }
        
        if ($action === 'update_section') {
            $modelSpec->update(
                $_POST['section_id'],
                $_POST['background_image'],
                $_POST['title'],
                $_POST['description'],
                $_POST['sort_order'] ?? 0
            );
            header('Location: specification.php?variant_id=' . $variantId);
            exit;
        }
        
        if ($action === 'delete_section') {
            $modelSpec->delete($_POST['section_id']);
            header('Location: specification.php?variant_id=' . $variantId);
            exit;
        }
        
        // Hero Cards
        if ($action === 'add_hero_card') {
            $modelSpec->addHeroCard(
                $_POST['section_id'],
                $_POST['image_url'],
                $_POST['title'],
                $_POST['description'],
                $_POST['sort_order'] ?? 0
            );
            header('Location: specification.php?variant_id=' . $variantId);
            exit;
        }
        
        if ($action === 'update_hero_card') {
            $modelSpec->updateHeroCard(
                $_POST['card_id'],
                $_POST['image_url'],
                $_POST['title'],
                $_POST['description'],
                $_POST['sort_order'] ?? 0
            );
            header('Location: specification.php?variant_id=' . $variantId);
            exit;
        }
        
        if ($action === 'delete_hero_card') {
            $modelSpec->deleteHeroCard($_POST['card_id']);
            header('Location: specification.php?variant_id=' . $variantId);
            exit;
        }
        
        // Carousel Images
        if ($action === 'add_image') {
            $modelSpec->addImage(
                $_POST['section_id'],
                $_POST['image_url'],
                $_POST['title'],
                $_POST['description'],
                $_POST['sort_order'] ?? 0
            );
            header('Location: specification.php?variant_id=' . $variantId);
            exit;
        }
        
        if ($action === 'update_image') {
            $modelSpec->updateImage(
                $_POST['image_id'],
                $_POST['image_url'],
                $_POST['title'],
                $_POST['description'],
                $_POST['sort_order'] ?? 0
            );
            header('Location: specification.php?variant_id=' . $variantId);
            exit;
        }
        
        if ($action === 'delete_image') {
            $modelSpec->deleteImage($_POST['image_id']);
            header('Location: specification.php?variant_id=' . $variantId);
            exit;
        }
    }

    // Handle AJAX requests
    if (isset($_GET['action'])) {
        header('Content-Type: application/json');
        $sectionId = $_GET['section_id'] ?? 0;
        
        if ($_GET['action'] === 'get_images') {
            $images = $modelSpec->getSectionImages($sectionId);
            echo json_encode($images);
            exit;
        }
        
        if ($_GET['action'] === 'get_hero_cards') {
            $cards = $modelSpec->getHeroCards($sectionId);
            echo json_encode($cards);
            exit;
        }
    }

    $sections = $modelSpec->getByVariantId($variantId);
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Manage Specification - <?= htmlspecialchars($variant['name']) ?></title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: Arial, sans-serif; background: #f5f5f5; }
            .header { background: #000; color: #fff; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; }
            .header h1 { font-size: 1.3rem; font-weight: 400; }
            .header a { color: #fff; text-decoration: none; padding: 10px 20px; background: #333; border-radius: 4px; font-size: 0.9rem; }
            .container { max-width: 1600px; margin: 30px auto; padding: 0 40px; }
            
            .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
            .section-header h2 { font-size: 1.5rem; font-weight: 400; }
            .btn { padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-block; }
            .btn-primary { background: #000; color: #fff; }
            .btn-success { background: #28a745; color: #fff; }
            .btn-danger { background: #dc3545; color: #fff; }
            .btn-secondary { background: #6c757d; color: #fff; }
            .btn-sm { padding: 6px 12px; font-size: 12px; }
            .card-actions { position: absolute; top: 10px; right: 10px; display: flex; gap: 5px; }
            .carousel-card { position: relative; }
            
            .sections-grid { display: grid; gap: 30px; margin-bottom: 60px; }
            .section-card { background: transparent; border-radius: 0; overflow: visible; box-shadow: none; margin-bottom: 40px; }
            .section-preview { position: relative; height: 500px; background-size: cover; background-position: center; border-radius: 12px; overflow: hidden; }
            .section-preview::before { content: ''; position: absolute; inset: 0; background: rgba(0,0,0,0.4); }
            .section-info { position: absolute; bottom: 20px; left: 20px; right: 20px; color: #fff; z-index: 2; }
            .section-info h3 { font-size: 1.8rem; margin-bottom: 10px; font-weight: 400; }
            .section-info p { font-size: 0.95rem; opacity: 0.9; line-height: 1.5; }
            
            .section-actions { padding: 20px 0; display: flex; gap: 10px; justify-content: space-between; align-items: center; }
            .section-meta { color: #666; font-size: 0.9rem; }
            
            .carousel-preview { display: flex; gap: 15px; padding: 0; background: transparent; overflow-x: auto; margin-top: 20px; }
            .carousel-card { min-width: 250px; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
            .carousel-card img { width: 100%; height: 150px; object-fit: cover; }
            .carousel-card-content { padding: 15px; }
            .carousel-card-content h4 { font-size: 1rem; margin-bottom: 8px; font-weight: 500; }
            .carousel-card-content p { font-size: 0.85rem; color: #666; line-height: 1.4; }
            
            .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; overflow-y: auto; }
            .modal.active { display: flex; align-items: center; justify-content: center; padding: 40px; }
            .modal-content { background: #fff; max-width: 700px; width: 100%; padding: 40px; border-radius: 12px; max-height: 90vh; overflow-y: auto; }
            .modal-content h2 { margin-bottom: 30px; font-size: 1.5rem; font-weight: 400; }
            .form-group { margin-bottom: 25px; }
            .form-group label { display: block; margin-bottom: 8px; font-weight: 500; font-size: 0.9rem; }
            .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 0.95rem; font-family: inherit; }
            .form-group textarea { min-height: 100px; resize: vertical; }
            .form-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 30px; }
            
            .empty-state { text-align: center; padding: 80px 20px; color: #999; }
            .empty-state i { font-size: 4rem; margin-bottom: 20px; opacity: 0.3; }
            .empty-state p { font-size: 1.1rem; margin-bottom: 30px; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Specification - <?= htmlspecialchars($variant['name']) ?></h1>
            <a href="/lending_word/admin/?tab=variants"><i class="fas fa-arrow-left"></i> Back to Variants</a>
        </div>
        
        <div class="container">
            <div class="section-header">
                <h2>Specification Sections</h2>
                <button class="btn btn-success" onclick="showModal('addSectionModal')"><i class="fas fa-plus"></i> Add Section</button>
            </div>
            
            <?php if (empty($sections)): ?>
            <div class="empty-state">
                <i class="fas fa-image"></i>
                <p>No specification sections yet</p>
                <button class="btn btn-primary" onclick="showModal('addSectionModal')">Create First Section</button>
            </div>
            <?php else: ?>
            <div class="sections-grid">
                <?php foreach ($sections as $section): ?>
                <?php 
                    $heroCards = $modelSpec->getHeroCards($section['id']); 
                    $carouselImages = $modelSpec->getSectionImages($section['id']); 
                ?>
                <div class="section-card">
                    <div class="section-preview" style="background-image: url('<?= htmlspecialchars($section['background_image']) ?>');">
                        <div class="section-info">
                            <h3><?= htmlspecialchars($section['title']) ?></h3>
                            <p><?= htmlspecialchars($section['description']) ?></p>
                        </div>
                    </div>
                    
                    <?php if (!empty($heroCards)): ?>
                    <div style="margin-top: 20px;">
                        <h4 style="font-size: 1rem; margin-bottom: 15px; color: #666;">
                            Hero Cards (<?= count($heroCards) ?>)
                        </h4>

                        <div class="carousel-preview">
                            <?php foreach ($heroCards as $card): ?>
                            <div class="carousel-card">
                                <img src="<?= htmlspecialchars($card['image_url']) ?>" 
                                    alt="<?= htmlspecialchars($card['title']) ?>" style="width: 100%; height: 150px; object-fit: cover;">

                                <div class="carousel-card-content">
                                    <h4><?= htmlspecialchars($card['title']) ?></h4>
                                    <p><?= htmlspecialchars(substr($card['description'], 0, 80)) ?>...</p>
                                    <small style="color:#999;">Sort: <?= $card['sort_order'] ?></small>
                                </div>

                                <div class="card-actions">
                                    <button type="button" class="btn btn-sm btn-primary"
                                        onclick='editHeroCardDirect(<?= json_encode($card) ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="delete_hero_card">
                                        <input type="hidden" name="card_id" value="<?= $card['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Delete this card?')"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    
                    <?php if (!empty($carouselImages)): ?>
                    <div style="margin-top: 20px;">
                        <h4 style="font-size: 1rem; margin-bottom: 15px; color: #666;">Carousel Images (<?= count($carouselImages) ?>)</h4>
                        <div class="carousel-preview">
                            <?php foreach ($carouselImages as $img): ?>
                            <div class="carousel-card">
                                <img src="<?= htmlspecialchars($img['image_url']) ?>" alt="<?= htmlspecialchars($img['title']) ?>">
                                <div class="carousel-card-content">
                                    <h4><?= htmlspecialchars($img['title']) ?></h4>
                                    <p><?= htmlspecialchars(substr($img['description'], 0, 60)) ?>...</p>
                                    <small style="color:#999;">Sort: <?= $img['sort_order'] ?></small>
                                </div>
                                <div class="card-actions">
                                    <button type="button" class="btn btn-sm btn-primary"
                                        onclick='editImageDirect(<?= json_encode($img) ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="delete_image">
                                        <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Delete this image?')"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="section-actions">
                        <div class="section-meta">
                            <span>Sort: <?= $section['sort_order'] ?></span> • 
                            <span><?= count($heroCards) ?> cards</span> • 
                            <span><?= count($carouselImages) ?> carousel</span>
                        </div>
                        <div>
                            <button class="btn btn-secondary" onclick="manageHeroCards(<?= $section['id'] ?>)"><i class="fas fa-th-large"></i> Cards</button>
                            <button class="btn btn-secondary" onclick="manageImages(<?= $section['id'] ?>)"><i class="fas fa-images"></i> Carousel</button>
                            <button class="btn btn-primary" onclick="editSection(<?= $section['id'] ?>)"><i class="fas fa-edit"></i> Edit</button>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="delete_section">
                                <input type="hidden" name="section_id" value="<?= $section['id'] ?>">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this section?')"><i class="fas fa-trash"></i> Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- ========================= MODALS ========================= -->

        <!-- Add Section Modal -->
        <div id="addSectionModal" class="modal">
            <div class="modal-content">
                <h2>Add New Section</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add_section">
                    <div class="form-group">
                        <label>Background Image URL</label>
                        <input type="url" name="background_image" required>
                    </div>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" value="0">
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="hideModal('addSectionModal')">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Section</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Section Modal -->
        <div id="editSectionModal" class="modal">
            <div class="modal-content">
                <h2>Edit Section</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="update_section">
                    <input type="hidden" name="section_id" id="edit_section_id">
                    <div class="form-group">
                        <label>Background Image URL</label>
                        <input type="url" name="background_image" id="edit_background_image" required>
                    </div>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" id="edit_title" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" id="edit_description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" id="edit_sort_order">
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="hideModal('editSectionModal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Section</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Manage Images Modal -->
        <div id="manageImagesModal" class="modal">
            <div class="modal-content" style="max-width: 1000px;">
                <h2>Manage Carousel Images</h2>
                <div style="margin-bottom: 20px; display: flex; justify-content: space-between;">
                    <button class="btn btn-success" onclick="showModal('addImageModal')"><i class="fas fa-plus"></i> Add Image</button>
                    <button class="btn btn-secondary" onclick="hideModal('manageImagesModal')"><i class="fas fa-times"></i> Close</button>
                </div>
                <div id="imagesList"></div>
            </div>
        </div>

        <!-- Manage Hero Cards Modal -->
        <div id="manageHeroCardsModal" class="modal">
            <div class="modal-content" style="max-width: 1000px;">
                <h2>Manage Hero Cards</h2>
                <div style="margin-bottom: 20px; display: flex; justify-content: space-between;">
                    <button class="btn btn-success" onclick="showModal('addHeroCardModal')"><i class="fas fa-plus"></i> Add Card</button>
                    <button class="btn btn-secondary" onclick="hideModal('manageHeroCardsModal')"><i class="fas fa-times"></i> Close</button>
                </div>
                <div id="heroCardsList"></div>
            </div>
        </div>

        <!-- Add Image Modal -->
        <div id="addImageModal" class="modal">
            <div class="modal-content">
                <h2>Add Carousel Image</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add_image">
                    <input type="hidden" name="section_id" id="add_image_section_id">
                    <div class="form-group">
                        <label>Image URL</label>
                        <input type="url" name="image_url" required placeholder="https://example.com/image.jpg">
                    </div>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" required placeholder="Image Title">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" required placeholder="Image description"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" value="0">
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="hideModal('addImageModal')">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Image</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Image Modal -->
        <div id="editImageModal" class="modal">
            <div class="modal-content">
                <h2>Edit Carousel Image</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="update_image">
                    <input type="hidden" name="image_id" id="edit_image_id">
                    <div class="form-group">
                        <label>Image URL</label>
                        <input type="url" name="image_url" id="edit_image_url" required>
                    </div>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" id="edit_image_title" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" id="edit_image_description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" id="edit_image_sort_order">
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="hideModal('editImageModal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Image</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Hero Card Modal -->
        <div id="addHeroCardModal" class="modal">
            <div class="modal-content">
                <h2>Add Hero Card</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add_hero_card">
                    <input type="hidden" name="section_id" id="add_card_section_id">
                    <div class="form-group">
                        <label>Image URL</label>
                        <input type="url" name="image_url" required placeholder="https://example.com/image.jpg">
                    </div>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" required placeholder="Engine Power">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" required placeholder="Brief description of this feature"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" value="0">
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="hideModal('addHeroCardModal')">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Card</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Hero Card Modal -->
        <div id="editHeroCardModal" class="modal">
            <div class="modal-content">
                <h2>Edit Hero Card</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="update_hero_card">
                    <input type="hidden" name="card_id" id="edit_card_id">

                    <div class="form-group">
                        <label>Image URL</label>
                        <input type="url" name="image_url" id="edit_card_url" required>
                    </div>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" id="edit_card_title" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" id="edit_card_description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" id="edit_card_sort_order">
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="hideModal('editHeroCardModal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Card</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================= JAVASCRIPT ========================= -->

        <script>
            const sections = <?= json_encode($sections) ?>;
            let currentSectionId = null;

            // ========== MODAL FUNCTIONS ==========
            function showModal(modalId) {
                document.getElementById(modalId).classList.add('active');
            }

            function hideModal(modalId) {
                document.getElementById(modalId).classList.remove('active');
            }

            // Close modal when clicking outside
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal')) {
                    e.target.classList.remove('active');
                }
            });

            // ========== SECTION FUNCTIONS ==========
            function editSection(sectionId) {
                const section = sections.find(s => s.id == sectionId);
                if (section) {
                    document.getElementById('edit_section_id').value = section.id;
                    document.getElementById('edit_background_image').value = section.background_image;
                    document.getElementById('edit_title').value = section.title;
                    document.getElementById('edit_description').value = section.description;
                    document.getElementById('edit_sort_order').value = section.sort_order;
                    showModal('editSectionModal');
                }
            }

            // ========== HERO CARDS FUNCTIONS ==========
            function manageHeroCards(sectionId) {
                currentSectionId = sectionId;
                document.getElementById('add_card_section_id').value = sectionId;
                loadHeroCards(sectionId);
                showModal('manageHeroCardsModal');
            }

            function loadHeroCards(sectionId) {
                fetch(`?action=get_hero_cards&section_id=${sectionId}`)
                    .then(res => res.json())
                    .then(cards => {
                        const cardsList = document.getElementById('heroCardsList');
                        if (cards.length === 0) {
                            cardsList.innerHTML = '<p style="text-align: center; color: #999; padding: 40px;">No hero cards yet</p>';
                            return;
                        }

                        cardsList.innerHTML = cards.map(card => `
                            <div style="display:flex; gap:15px; padding:15px; border:1px solid #ddd; border-radius:8px; margin-bottom:15px; align-items:center;">
                                <img src="${card.image_url}" style="width:100px; height:80px; object-fit:cover; border-radius:4px;">
                                <div style="flex:1;">
                                    <h4>${card.title}</h4>
                                    <p style="font-size:0.9rem; color:#666;">${card.description}</p>
                                    <small style="color:#999;">Sort: ${card.sort_order}</small>
                                </div>
                                <div style="display:flex; flex-direction:column; gap:5px;">
                                    <button class="btn btn-primary" onclick='editHeroCard(${JSON.stringify(card)})'>
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form method="POST">
                                        <input type="hidden" name="action" value="delete_hero_card">
                                        <input type="hidden" name="card_id" value="${card.id}">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        `).join('');
                    });
            }

            // Edit Hero Card from main page (direct click)
            function editHeroCardDirect(card) {
                editHeroCard(card);
            }

            // Edit Hero Card (universal function)
            function editHeroCard(card) {
                if (typeof card === 'object' && card !== null) {
                    document.getElementById('edit_card_id').value = card.id;
                    document.getElementById('edit_card_url').value = card.image_url;
                    document.getElementById('edit_card_title').value = card.title;
                    document.getElementById('edit_card_description').value = card.description;
                    document.getElementById('edit_card_sort_order').value = card.sort_order;
                    showModal('editHeroCardModal');
                } else {
                    console.error('Invalid card data');
                }
            }

            // ========== CAROUSEL IMAGES FUNCTIONS ==========
            function manageImages(sectionId) {
                currentSectionId = sectionId;
                document.getElementById('add_image_section_id').value = sectionId;
                loadImages(sectionId);
                showModal('manageImagesModal');
            }

            function loadImages(sectionId) {
                fetch(`?action=get_images&section_id=${sectionId}`)
                    .then(response => response.json())
                    .then(images => {
                        const imagesList = document.getElementById('imagesList');
                        if (images.length === 0) {
                            imagesList.innerHTML = '<p style="text-align: center; color: #999; padding: 40px;">No carousel images yet</p>';
                            return;
                        }
                        
                        imagesList.innerHTML = images.map(img => `
                            <div style="display: flex; gap: 15px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 15px; align-items: center;">
                                <img src="${img.image_url}" style="width: 80px; height: 60px; object-fit: cover; border-radius: 4px;">
                                <div style="flex: 1;">
                                    <h4 style="margin-bottom: 5px;">${img.title}</h4>
                                    <p style="color: #666; font-size: 0.9rem;">${img.description}</p>
                                    <small style="color: #999;">Sort: ${img.sort_order}</small>
                                </div>
                                <div style="display:flex; flex-direction:column; gap:5px;">
                                    <button class="btn btn-primary" onclick='editImage(${JSON.stringify(img)})'>
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form method="POST">
                                        <input type="hidden" name="action" value="delete_image">
                                        <input type="hidden" name="image_id" value="${img.id}">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this image?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        `).join('');
                    });
            }

            // Edit Image from main page (direct click)
            function editImageDirect(image) {
                editImage(image);
            }

            // Edit Image (universal function)
            function editImage(image) {
                if (typeof image === 'object' && image !== null) {
                    document.getElementById('edit_image_id').value = image.id;
                    document.getElementById('edit_image_url').value = image.image_url;
                    document.getElementById('edit_image_title').value = image.title;
                    document.getElementById('edit_image_description').value = image.description;
                    document.getElementById('edit_image_sort_order').value = image.sort_order;
                    showModal('editImageModal');
                } else {
                    console.error('Invalid image data');
                }
            }

        </script>
    </body>
    </html>