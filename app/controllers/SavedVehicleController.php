<?php
/**
 * Saved Vehicle Controller
 * Place this in: /lending_word/app/controllers/SavedVehicleController.php
 */

require_once __DIR__ . '/../models/SavedVehicle.php';
require_once __DIR__ . '/../models/NavbarLink.php';
require_once __DIR__ . '/../models/FooterSection.php';
require_once __DIR__ . '/../models/SocialLink.php';
require_once __DIR__ . '/../models/Content.php';

class SavedVehicleController {
    private $savedVehicleModel;
    private $navbarModel;
    private $footerSectionModel;
    private $socialLinkModel;
    private $contentModel;
    
    public function __construct() {
        $this->savedVehicleModel = new SavedVehicle();
        $this->navbarModel = new NavbarLink();
        $this->footerSectionModel = new FooterSection();
        $this->socialLinkModel = new SocialLink();
        $this->contentModel = new Content();
    }
    
    /**
     * AJAX API Handler - handles all AJAX requests
     */
    public function api() {
        header('Content-Type: application/json');
        
        $action = $_POST['action'] ?? $_GET['action'] ?? '';
        $response = ['success' => false, 'message' => ''];
        
        try {
            switch ($action) {
                case 'save':
                    $vehicleId = (int)($_POST['vehicle_id'] ?? 0);
                    if ($vehicleId <= 0) {
                        throw new Exception('Invalid vehicle ID');
                    }
                    
                    $result = $this->savedVehicleModel->save($vehicleId);
                    if ($result) {
                        $response = [
                            'success' => true,
                            'message' => 'Vehicle saved successfully',
                            'count' => $this->savedVehicleModel->getSavedCount()
                        ];
                    } else {
                        throw new Exception('Failed to save vehicle or already saved');
                    }
                    break;
                    
                case 'unsave':
                    $vehicleId = (int)($_POST['vehicle_id'] ?? 0);
                    if ($vehicleId <= 0) {
                        throw new Exception('Invalid vehicle ID');
                    }
                    
                    $result = $this->savedVehicleModel->unsave($vehicleId);
                    $response = [
                        'success' => true,
                        'message' => 'Vehicle removed from saved list',
                        'count' => $this->savedVehicleModel->getSavedCount()
                    ];
                    break;
                    
                case 'check':
                    $vehicleId = (int)($_GET['vehicle_id'] ?? 0);
                    if ($vehicleId <= 0) {
                        throw new Exception('Invalid vehicle ID');
                    }
                    
                    $response = [
                        'success' => true,
                        'is_saved' => $this->savedVehicleModel->isSaved($vehicleId)
                    ];
                    break;
                    
                case 'get_count':
                    $response = [
                        'success' => true,
                        'count' => $this->savedVehicleModel->getSavedCount()
                    ];
                    break;
                    
                case 'get_ids':
                    $response = [
                        'success' => true,
                        'vehicle_ids' => $this->savedVehicleModel->getSavedIds()
                    ];
                    break;
                    
                case 'clear_all':
                    $this->savedVehicleModel->clearAll();
                    $response = [
                        'success' => true,
                        'message' => 'All saved vehicles cleared'
                    ];
                    break;
                    
                default:
                    throw new Exception('Invalid action');
            }
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        
        echo json_encode($response);
        exit;
    }
    
    /**
     * Show saved vehicles page
     */
    public function index() {
        $savedVehicles = $this->savedVehicleModel->getAllSaved();
        $savedCount = count($savedVehicles);
        
        $navbarLinks = $this->navbarModel->getAll();
        $footerSections = $this->footerSectionModel->getAllWithLinks();
        $socialLinks = $this->socialLinkModel->getAll();
        
        $getContent = function($section, $key) {
            return $this->contentModel->get($section, $key);
        };
        
        require_once __DIR__ . '/../views/frontend/saved_vehicles.php';
    }
    
    /**
     * Compare vehicles page
     */
    public function compare() {
        // Get vehicle IDs from URL
        $vehicleIds = [];
        if (isset($_GET['vehicles'])) {
            $vehicleIds = array_filter(array_map('intval', explode(',', $_GET['vehicles'])));
        }
        
        // Limit to 3 vehicles
        $vehicleIds = array_slice($vehicleIds, 0, 3);
        
        // Get vehicles for comparison
        $vehicles = [];
        if (!empty($vehicleIds)) {
            $vehicles = $this->savedVehicleModel->getComparisonVehicles($vehicleIds);
        }
        
        $navbarLinks = $this->navbarModel->getAll();
        $footerSections = $this->footerSectionModel->getAllWithLinks();
        $socialLinks = $this->socialLinkModel->getAll();
        
        $getContent = function($section, $key) {
            return $this->contentModel->get($section, $key);
        };
        
        require_once __DIR__ . '/../views/frontend/compare.php';
    }
}