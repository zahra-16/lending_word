<?php
/**
 * AJAX API endpoint for saved vehicles operations
 * Place this file in: /lending_word/saved_vehicles_api.php (ROOT FOLDER)
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Start session
session_start();

// Log request for debugging
error_log("Saved Vehicles API Called - Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));
error_log("GET data: " . print_r($_GET, true));
error_log("Session ID: " . session_id());

try {
    require_once __DIR__ . '/app/models/SavedVehicle.php';
    
    $savedVehicleModel = new SavedVehicle();
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    error_log("Action: " . $action);
    
    $response = ['success' => false, 'message' => ''];
    
    switch ($action) {
        case 'save':
            $vehicleId = (int)($_POST['vehicle_id'] ?? 0);
            
            error_log("Attempting to save vehicle ID: " . $vehicleId);
            
            if ($vehicleId <= 0) {
                throw new Exception('Invalid vehicle ID: ' . $vehicleId);
            }
            
            $result = $savedVehicleModel->save($vehicleId);
            
            error_log("Save result: " . ($result ? 'success' : 'failed'));
            
            if ($result) {
                $count = $savedVehicleModel->getSavedCount();
                $response = [
                    'success' => true,
                    'message' => 'Vehicle saved successfully',
                    'count' => $count,
                    'vehicle_id' => $vehicleId
                ];
                error_log("Vehicle saved. New count: " . $count);
            } else {
                throw new Exception('Failed to save vehicle (may already be saved)');
            }
            break;
            
        case 'unsave':
            $vehicleId = (int)($_POST['vehicle_id'] ?? 0);
            
            error_log("Attempting to unsave vehicle ID: " . $vehicleId);
            
            if ($vehicleId <= 0) {
                throw new Exception('Invalid vehicle ID');
            }
            
            $result = $savedVehicleModel->unsave($vehicleId);
            $count = $savedVehicleModel->getSavedCount();
            
            $response = [
                'success' => true,
                'message' => 'Vehicle removed from saved list',
                'count' => $count,
                'vehicle_id' => $vehicleId
            ];
            
            error_log("Vehicle unsaved. New count: " . $count);
            break;
            
        case 'check':
            $vehicleId = (int)($_GET['vehicle_id'] ?? 0);
            
            if ($vehicleId <= 0) {
                throw new Exception('Invalid vehicle ID');
            }
            
            $isSaved = $savedVehicleModel->isSaved($vehicleId);
            
            $response = [
                'success' => true,
                'is_saved' => $isSaved,
                'vehicle_id' => $vehicleId
            ];
            
            error_log("Check vehicle {$vehicleId}: " . ($isSaved ? 'saved' : 'not saved'));
            break;
            
        case 'get_count':
            $count = $savedVehicleModel->getSavedCount();
            
            $response = [
                'success' => true,
                'count' => $count
            ];
            
            error_log("Get count: " . $count);
            break;
            
        case 'get_ids':
            $ids = $savedVehicleModel->getSavedIds();
            
            $response = [
                'success' => true,
                'vehicle_ids' => $ids,
                'count' => count($ids)
            ];
            
            error_log("Get IDs: " . implode(', ', $ids));
            break;
            
        case 'list':
            // Get all saved vehicles with full details
            $vehicles = $savedVehicleModel->getAllSaved();
            
            $response = [
                'success' => true,
                'vehicles' => $vehicles,
                'count' => count($vehicles)
            ];
            
            error_log("List vehicles: " . count($vehicles) . " found");
            break;
            
        case 'clear_all':
            $savedVehicleModel->clearAll();
            
            $response = [
                'success' => true,
                'message' => 'All saved vehicles cleared'
            ];
            
            error_log("All saved vehicles cleared");
            break;
            
        default:
            throw new Exception('Invalid action: ' . $action);
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage(),
        'error_details' => [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ];
    
    error_log("Error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
}

// Log response
error_log("Response: " . json_encode($response));

echo json_encode($response);