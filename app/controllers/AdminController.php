<?php
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/Content.php';
require_once __DIR__ . '/../models/Model.php';
require_once __DIR__ . '/../models/ExploreModel.php';
require_once __DIR__ . '/../models/DiscoverFeature.php';
require_once __DIR__ . '/../models/NavbarLink.php';
require_once __DIR__ . '/../models/FooterSection.php';
require_once __DIR__ . '/../models/ModelVariant.php';
require_once __DIR__ . '/../models/VehicleInquiry.php';
require_once __DIR__ . '/../models/FeaturedVehicle.php';
require_once __DIR__ . '/../models/GpcModel.php'; // ← TAMBAHAN GPC

class AdminController {
    private $adminModel;
    private $contentModel;
    private $modelModel;
    private $exploreModel;
    private $discoverModel;
    private $navbarModel;
    private $footerSectionModel;
    private $footerLinkModel;
    private $socialLinkModel;
    private $modelVariantModel;
    private $vehicleInquiryModel;
    private $featuredVehicleModel;
    private $gpcModel; // ← TAMBAHAN GPC

    public function __construct() {
        $this->adminModel           = new Admin();
        $this->contentModel         = new Content();
        $this->modelModel           = new Model();
        $this->exploreModel         = new ExploreModel();
        $this->discoverModel        = new DiscoverFeature();
        $this->navbarModel          = new NavbarLink();
        $this->footerSectionModel   = new FooterSection();
        $this->footerLinkModel      = new FooterLink();
        $this->socialLinkModel      = new SocialLink();
        $this->modelVariantModel    = new ModelVariant();
        $this->vehicleInquiryModel  = new VehicleInquiry();
        $this->featuredVehicleModel = new FeaturedVehicle();
        $this->gpcModel             = new GpcModel(); // ← TAMBAHAN GPC
    }

    // Show login page
    public function login() {
        if (Admin::isLoggedIn()) {
            header('Location: /lending_word/admin/');
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->adminModel->login($username, $password);
            if ($user) {
                Admin::setSession($user['id']);
                header('Location: /lending_word/admin/');
                exit;
            } else {
                $error = 'Username atau password salah!';
            }
        }

        require_once __DIR__ . '/../views/admin/login.php';
    }

    // Show admin dashboard
    public function dashboard() {
        if (!Admin::isLoggedIn()) {
            header('Location: /lending_word/admin/login.php');
            exit;
        }

        $success = '';
        $tab = $_GET['tab'] ?? 'content';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
            $this->contentModel->bulkUpdate($_POST['content']);
            $success = "Konten berhasil diupdate!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_sound'])) {
            $this->contentModel->bulkUpdate($_POST['content']);
            $success = "Sound content berhasil diupdate!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_model'])) {
            $this->modelModel->create($_POST['name'], $_POST['fuel_types'], $_POST['image'], $_POST['sort_order']);
            $success = "Model berhasil ditambahkan!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_model'])) {
            $this->modelModel->update($_POST['id'], $_POST['name'], $_POST['fuel_types'], $_POST['image'], $_POST['sort_order']);
            $success = "Model berhasil diupdate!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_model'])) {
            $this->modelModel->delete($_POST['id']);
            $success = "Model berhasil dihapus!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_explore'])) {
            $this->exploreModel->update($_POST['id'], $_POST);
            $success = "Explore model berhasil diupdate!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_explore'])) {
            $this->exploreModel->create($_POST);
            $success = "Explore model berhasil ditambahkan!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_explore'])) {
            $this->exploreModel->delete($_POST['id']);
            $success = "Explore model berhasil dihapus!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_discover'])) {
            $this->discoverModel->create($_POST);
            $success = "Discover feature berhasil ditambahkan!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_discover'])) {
            $this->discoverModel->update($_POST['id'], $_POST);
            $success = "Discover feature berhasil diupdate!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_discover'])) {
            $this->discoverModel->delete($_POST['id']);
            $success = "Discover feature berhasil dihapus!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_navbar'])) {
            $this->navbarModel->create($_POST);
            $success = "Navbar link berhasil ditambahkan!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_navbar'])) {
            $this->navbarModel->update($_POST['id'], $_POST);
            $success = "Navbar link berhasil diupdate!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_navbar'])) {
            $this->navbarModel->delete($_POST['id']);
            $success = "Navbar link berhasil dihapus!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_footer_section'])) {
            $this->footerSectionModel->create([
                'title'      => $_POST['section_title'] ?? '',
                'sort_order' => $_POST['section_sort_order'] ?? 0,
            ]);
            $success = "Footer section berhasil ditambahkan!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_footer_section'])) {
            $this->footerSectionModel->update($_POST['id'], [
                'title'      => $_POST['section_title'] ?? '',
                'sort_order' => $_POST['section_sort_order'] ?? 0,
            ]);
            $success = "Footer section berhasil diupdate!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_footer_section'])) {
            $this->footerLinkModel->deleteBySection($_POST['id']);
            $this->footerSectionModel->delete($_POST['id']);
            $success = "Footer section berhasil dihapus!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_footer_link'])) {
            $this->footerLinkModel->create($_POST);
            $success = "Footer link berhasil ditambahkan!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_footer_link'])) {
            $this->footerLinkModel->update($_POST['id'], $_POST);
            $success = "Footer link berhasil diupdate!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_footer_link'])) {
            $this->footerLinkModel->delete($_POST['id']);
            $success = "Footer link berhasil dihapus!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_social'])) {
            $this->socialLinkModel->create($_POST);
            $success = "Social link berhasil ditambahkan!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_social'])) {
            $this->socialLinkModel->update($_POST['id'], $_POST);
            $success = "Social link berhasil diupdate!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_social'])) {
            $this->socialLinkModel->delete($_POST['id']);
            $success = "Social link berhasil dihapus!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_variant'])) {
            $this->modelVariantModel->create($_POST);
            header('Location: ?tab=variants&success=added');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_variant'])) {
            $this->modelVariantModel->update($_POST['id'], $_POST);
            header('Location: ?tab=variants&success=updated');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_variant'])) {
            $this->modelVariantModel->delete($_POST['id']);
            header('Location: ?tab=variants&success=deleted');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_featured'])) {
            $this->featuredVehicleModel->create($_POST);
            $success = "Featured vehicle berhasil ditambahkan!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_featured'])) {
            $this->featuredVehicleModel->update($_POST['id'], $_POST);
            $success = "Featured vehicle berhasil diupdate!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_featured'])) {
            $this->featuredVehicleModel->delete($_POST['id']);
            $success = "Featured vehicle berhasil dihapus!";
        }

        $grouped          = $this->contentModel->getAllGrouped();
        $models           = $this->modelModel->getAll();
        $exploreModels    = $this->exploreModel->getAll();
        $discoverFeatures = $this->discoverModel->getAll();
        $navbarLinks      = $this->navbarModel->getAll();
        $footerSections   = $this->footerSectionModel->getAllWithLinks();
        $socialLinks      = $this->socialLinkModel->getAll();
        $featuredVehicles = $this->featuredVehicleModel->getAll();

        if ($tab === 'variants') {
            $modelVariants = $this->modelVariantModel->getVariantsByCategory('all');
            $categories    = $this->modelVariantModel->getCategories();
        } else {
            $modelVariants = [];
            $categories    = [];
        }

        $unreadInquiries = $this->vehicleInquiryModel->countUnread();

        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    // ← TAMBAHAN GPC
    public function gpc() {
        if (!Admin::isLoggedIn()) {
            header('Location: /lending_word/admin/login.php');
            exit;
        }

        $m       = $this->gpcModel;
        $tab     = $_GET['tab'] ?? 'contents';
        $success = '';
        $error   = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_contents'])) {
            foreach ($_POST['cc'] as $key => $value) $m->upsertContent($key, trim($value));
            $success = "Contents disimpan!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_partner'])) {
            try { $m->createPartner($_POST); $success = "Partner ditambahkan!"; } catch(Exception $e){ $error = $e->getMessage(); }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_partner'])) {
            $m->updatePartner((int)$_POST['id'], $_POST); $success = "Partner diupdate!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_partner'])) {
            $m->deletePartner((int)$_POST['id']); $success = "Partner dihapus!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_coop'])) {
            try { $m->createCooperation($_POST); $success = "Cooperation ditambahkan!"; } catch(Exception $e){ $error = $e->getMessage(); }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_coop'])) {
            $m->updateCooperation((int)$_POST['id'], $_POST); $success = "Cooperation diupdate!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_coop'])) {
            $m->deleteCooperation((int)$_POST['id']); $success = "Cooperation dihapus!";
        }

        $contents        = $m->getAllContents();
        $partners        = $m->getAllPartners();
        $cooperations    = $m->getAllCooperations();
        $unreadInquiries = $this->vehicleInquiryModel->countUnread();

        require_once __DIR__ . '/../views/admin/gpc.php';
    }

    // Logout
    public function logout() {
        Admin::logout();
        header('Location: /lending_word/admin/login.php');
        exit;
    }
}