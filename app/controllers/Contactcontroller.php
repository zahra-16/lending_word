<?php
require_once __DIR__ . '/../models/Content.php';
require_once __DIR__ . '/../models/NavbarLink.php';
require_once __DIR__ . '/../models/FooterSection.php';
require_once __DIR__ . '/../models/Vehicle.php';
require_once __DIR__ . '/../models/PorscheCenter.php';
require_once __DIR__ . '/../models/PorscheCenterHours.php';
require_once __DIR__ . '/../models/VehicleInquiry.php';

class ContactController {
    private $vehicleModel;
    private $porscheCenterModel;
    private $hoursModel;
    private $inquiryModel;
    private $contentModel;
    private $navbarModel;
    private $footerSectionModel;
    private $socialLinkModel;

    public function __construct() {
        $this->vehicleModel       = new Vehicle();
        $this->porscheCenterModel = new PorscheCenter();
        $this->hoursModel         = new PorscheCenterHours();
        $this->inquiryModel       = new VehicleInquiry();
        $this->contentModel       = new Content();
        $this->navbarModel        = new NavbarLink();
        $this->footerSectionModel = new FooterSection();
        $this->socialLinkModel    = new SocialLink();
    }

    /**
     * Show the contact/inquiry page for a vehicle
     * URL: /lending_word/finder_contact.php?id=123&tab=message
     */
    public function show(int $vehicleId): void {
        $vehicle = $this->vehicleModel->getById($vehicleId);
        if (!$vehicle) {
            header('Location: /lending_word/finder.php');
            exit;
        }

        $tab     = $_GET['tab'] ?? 'message';
        $success = false;

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'send_inquiry') {
            $success = $this->handleSubmit($vehicle);
        }

        // Load center data
        $center       = null;
        $openingHours = [];
        if (!empty($vehicle['center_id'])) {
            $center       = $this->porscheCenterModel->getById($vehicle['center_id']);
            $openingHours = $this->hoursModel->getByCenter((int)$vehicle['center_id']);
        }

        // Shared layout data
        $navbarLinks    = $this->navbarModel->getAll();
        $footerSections = $this->footerSectionModel->getAllWithLinks();
        $socialLinks    = $this->socialLinkModel->getAll();
        $getContent     = function ($section, $key) {
            return $this->contentModel->get($section, $key);
        };

        require_once __DIR__ . '/../views/frontend/finder_contact.php';
    }

    /**
     * Process the POST submission
     */
    private function handleSubmit(array $vehicle): bool {
        // Basic validation
        if (empty($_POST['first_name']) || empty($_POST['last_name'])) {
            return false;
        }
        if (empty($_POST['privacy_agreed'])) {
            return false;
        }

        $result = $this->inquiryModel->create([
            'vehicle_id'          => $vehicle['id'],
            'center_id'           => $vehicle['center_id'] ?? null,
            'inquiry_type'        => $_POST['inquiry_type'] ?? 'message',
            'salutation'          => $_POST['salutation']           ?? null,
            'first_name'          => $_POST['first_name']           ?? '',
            'last_name'           => $_POST['last_name']            ?? '',
            'email'               => $_POST['email']                ?? null,
            'phone_country_code'  => $_POST['phone_country_code']   ?? null,
            'phone_number'        => $_POST['phone_number']         ?? null,
            'message'             => $_POST['message']              ?? null,
            'callback_time'       => $_POST['callback_time']        ?? null,
            'privacy_agreed'      => true,
            'ip_address'          => $_SERVER['REMOTE_ADDR']        ?? null,
            'user_agent'          => $_SERVER['HTTP_USER_AGENT']    ?? null,
        ]);

        return $result !== false;
    }
}