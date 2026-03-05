<?php
require_once __DIR__ . '/../models/Content.php';
require_once __DIR__ . '/../models/NavbarLink.php';
require_once __DIR__ . '/../models/FooterSection.php';
require_once __DIR__ . '/../models/Vehicle.php';
require_once __DIR__ . '/../models/ModelSeries.php';
require_once __DIR__ . '/../models/BodyType.php';
require_once __DIR__ . '/../models/PorscheCenter.php';

class FinderController {
    private $contentModel;
    private $navbarModel;
    private $footerSectionModel;
    private $socialLinkModel;
    private $vehicleModel;
    private $modelSeriesModel;
    private $bodyTypeModel;
    private $porscheCenterModel;
    
    public function __construct() {
        $this->contentModel = new Content();
        $this->navbarModel = new NavbarLink();
        $this->footerSectionModel = new FooterSection();
        $this->socialLinkModel = new SocialLink();
        $this->vehicleModel = new Vehicle();
        $this->modelSeriesModel = new ModelSeries();
        $this->bodyTypeModel = new BodyType();
        $this->porscheCenterModel = new PorscheCenter();
    }
    
    // Show finder page
    public function index() {
        // Get filter parameters from URL
        $filters = [
            'condition' => $_GET['condition'] ?? null,
            'series_id' => $_GET['series_id'] ?? null,
            'min_price' => $_GET['min_price'] ?? null,
            'max_price' => $_GET['max_price'] ?? null,
            'fuel_type' => $_GET['fuel_type'] ?? null,
            'transmission' => $_GET['transmission'] ?? null,
            'body_type_id' => $_GET['body_type_id'] ?? null,
            'min_year' => $_GET['min_year'] ?? null,
            'max_year' => $_GET['max_year'] ?? null,
            'color_category' => $_GET['color_category'] ?? null,
            'center_id' => $_GET['center_id'] ?? null,
            'sort_by' => $_GET['sort_by'] ?? 'recommended'
        ];
        
        // Get vehicles based on filters
        $vehicles = $this->vehicleModel->search($filters);
        
        // Get filter options
        $modelSeries = $this->modelSeriesModel->getAll();
        $bodyTypes = $this->bodyTypeModel->getAll();
        $porscheCenters = $this->porscheCenterModel->getAll();
        
        // Get available filter values from database
        $filterOptions = $this->vehicleModel->getFilterOptions();
        
        // Get navbar links
        $navbarLinks = $this->navbarModel->getAll();
        
        // Get footer sections
        $footerSections = $this->footerSectionModel->getAllWithLinks();
        $socialLinks = $this->socialLinkModel->getAll();
        
        // Pass content helper
        $getContent = function($section, $key) {
            return $this->contentModel->get($section, $key);
        };
        
        require_once __DIR__ . '/../views/frontend/finder.php';
    }
    
    // Show vehicle detail page
    public function detail($id) {
        $vehicle = $this->vehicleModel->getById($id);
        
        if (!$vehicle) {
            header('Location: /lending_word/finder.php');
            exit;
        }
        
        // Increment view count
        $this->vehicleModel->incrementViewCount($id);
        
        // Get vehicle images
        $images = $this->vehicleModel->getImages($id);
        
        // Get similar vehicles
        $similarVehicles = $this->vehicleModel->getSimilar($id, 4);
        
        // Get navbar links
        $navbarLinks = $this->navbarModel->getAll();
        
        // Get footer sections
        $footerSections = $this->footerSectionModel->getAllWithLinks();
        $socialLinks = $this->socialLinkModel->getAll();
        
        // Pass content helper
        $getContent = function($section, $key) {
            return $this->contentModel->get($section, $key);
        };
        
        require_once __DIR__ . '/../views/frontend/vehicle_detail.php';
    }
}