<?php
require_once __DIR__ . '/../models/Content.php';
require_once __DIR__ . '/../models/Model.php';
require_once __DIR__ . '/../models/ExploreModel.php';
require_once __DIR__ . '/../models/DiscoverFeature.php';
require_once __DIR__ . '/../models/NavbarLink.php';
require_once __DIR__ . '/../models/FooterSection.php';
require_once __DIR__ . '/../models/FeaturedVehicle.php';
require_once __DIR__ . '/../models/GpcModel.php'; // ← TAMBAHAN GPC

class FrontendController {
    private $contentModel;
    private $modelModel;
    private $exploreModel;
    private $discoverModel;
    private $navbarModel;
    private $footerSectionModel;
    private $socialLinkModel;
    private $featuredVehicleModel;
    private $gpcModel; // ← TAMBAHAN GPC

    public function __construct() {
        $this->contentModel         = new Content();
        $this->modelModel           = new Model();
        $this->exploreModel         = new ExploreModel();
        $this->discoverModel        = new DiscoverFeature();
        $this->navbarModel          = new NavbarLink();
        $this->footerSectionModel   = new FooterSection();
        $this->socialLinkModel      = new SocialLink();
        $this->featuredVehicleModel = new FeaturedVehicle();
        $this->gpcModel             = new GpcModel(); // ← TAMBAHAN GPC
    }

    // Show landing page
    public function index() {
        $getContent = function($section, $key) {
            return $this->contentModel->get($section, $key);
        };

        $models           = $this->modelModel->getAll();
        $exploreModels    = $this->exploreModel->getAll();
        $discoverFeatures = $this->discoverModel->getAll();
        $navbarLinks      = $this->navbarModel->getAll();
        $footerSections   = $this->footerSectionModel->getAllWithLinks();
        $socialLinks      = $this->socialLinkModel->getAll();
        $featuredVehicles = $this->featuredVehicleModel->getActive();

        require_once __DIR__ . '/../views/frontend/index.php';
    }

    // ← TAMBAHAN GPC
    public function globalpartnershipcouncil() {
        $gpc          = $this->gpcModel;
        $partners     = $this->gpcModel->getActivePartners();
        $cooperations = $this->gpcModel->getActiveCooperations();

        $navbarLinks    = $this->navbarModel->getAll();
        $footerSections = $this->footerSectionModel->getAllWithLinks();
        $socialLinks    = $this->socialLinkModel->getAll();
        $getContent     = fn($section, $key) => $this->contentModel->get($section, $key);

        require_once __DIR__ . '/../views/frontend/globalpartnershipcouncil.php';
    }
}