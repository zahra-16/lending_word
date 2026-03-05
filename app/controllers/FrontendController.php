<?php
require_once __DIR__ . '/../models/Content.php';
require_once __DIR__ . '/../models/Model.php';
require_once __DIR__ . '/../models/ExploreModel.php';
require_once __DIR__ . '/../models/DiscoverFeature.php';
require_once __DIR__ . '/../models/NavbarLink.php';
require_once __DIR__ . '/../models/FooterSection.php';
require_once __DIR__ . '/../models/FeaturedVehicle.php'; // ← TAMBAHAN

class FrontendController {
    private $contentModel;
    private $modelModel;
    private $exploreModel;
    private $discoverModel;
    private $navbarModel;
    private $footerSectionModel;
    private $socialLinkModel;
    private $featuredVehicleModel; // ← TAMBAHAN

    public function __construct() {
        $this->contentModel = new Content();
        $this->modelModel = new Model();
        $this->exploreModel = new ExploreModel();
        $this->discoverModel = new DiscoverFeature();
        $this->navbarModel = new NavbarLink();
        $this->footerSectionModel = new FooterSection();
        $this->socialLinkModel = new SocialLink();
        $this->featuredVehicleModel = new FeaturedVehicle(); // ← TAMBAHAN
    }

    // Show landing page
    public function index() {
        // Pass content model to view
        $getContent = function($section, $key) {
            return $this->contentModel->get($section, $key);
        };

        // Get all models
        $models = $this->modelModel->getAll();

        // Get explore models
        $exploreModels = $this->exploreModel->getAll();

        // Get discover features
        $discoverFeatures = $this->discoverModel->getAll();

        // Get navbar links
        $navbarLinks = $this->navbarModel->getAll();

        // Get footer sections with links
        $footerSections = $this->footerSectionModel->getAllWithLinks();

        // Get social links
        $socialLinks = $this->socialLinkModel->getAll();

        // Get featured / popular vehicles ← TAMBAHAN
        $featuredVehicles = $this->featuredVehicleModel->getActive();

        require_once __DIR__ . '/../views/frontend/index.php';
    }
}