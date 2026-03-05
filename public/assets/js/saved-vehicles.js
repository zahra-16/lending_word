/**
 * Saved Vehicles JavaScript Functionality
 * Place this file in: /lending_word/public/assets/js/saved-vehicles.js
 * 
 * UPDATED FOR ROOT FOLDER STRUCTURE (NO /api/ folder)
 */

(function() {
    'use strict';
    
    // Configuration - UNTUK STRUKTUR FOLDER ROOT (tanpa /api/)
    const API_ENDPOINT = '/lending_word/saved_vehicles_api.php';
    
    // Debug mode - set to false in production
    const DEBUG = true;
    
    function log(...args) {
        if (DEBUG) {
            console.log('[SavedVehicles]', ...args);
        }
    }
    
    // Initialize saved vehicles functionality
    function initSavedVehicles() {
        log('Initializing saved vehicles functionality...');
        log('API Endpoint:', API_ENDPOINT);
        
        // Load saved count and update UI
        updateSavedCount();
        
        // Check which vehicles are saved and update buttons
        updateSaveButtons();
        
        // Add event listeners to all save buttons
        attachSaveListeners();
        
        log('Initialization complete');
    }
    
    // Update saved count in header/navbar
    function updateSavedCount() {
        log('Fetching saved count...');
        
        fetch(API_ENDPOINT + '?action=get_count')
            .then(response => {
                log('Count response status:', response.status);
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status + ' - Check if file exists at: ' + API_ENDPOINT);
                }
                return response.json();
            })
            .then(data => {
                log('Count data:', data);
                if (data.success) {
                    updateCountBadge(data.count);
                } else {
                    console.error('Failed to get count:', data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching saved count:', error);
                console.error('Make sure file exists at:', API_ENDPOINT);
            });
    }
    
    // Update count badge in UI
    function updateCountBadge(count) {
        log('Updating count badge to:', count);
        
        const badges = document.querySelectorAll('.saved-count-badge');
        badges.forEach(badge => {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline-block' : 'none';
        });
    }
    
    // Check which vehicles are saved and update button states
    function updateSaveButtons() {
        log('Fetching saved vehicle IDs...');
        
        fetch(API_ENDPOINT + '?action=get_ids')
            .then(response => response.json())
            .then(data => {
                log('Saved IDs:', data);
                if (data.success && data.vehicle_ids) {
                    log('Found', data.vehicle_ids.length, 'saved vehicles');
                    data.vehicle_ids.forEach(vehicleId => {
                        updateButtonState(vehicleId, true);
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching saved IDs:', error);
            });
    }
    
    // Attach click listeners to save buttons
    function attachSaveListeners() {
        const buttons = document.querySelectorAll('[data-save-vehicle], [data-vehicle-id].btn-save');
        log('Attaching listeners to', buttons.length, 'buttons');
        
        buttons.forEach(button => {
            // Remove any existing listeners
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            
            newButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const vehicleId = this.dataset.saveVehicle || this.dataset.vehicleId;
                log('Button clicked for vehicle ID:', vehicleId);
                
                if (vehicleId) {
                    toggleSave(parseInt(vehicleId), this);
                } else {
                    console.error('No vehicle ID found on button');
                }
            });
        });
    }
    
    // Toggle save/unsave for a vehicle
    function toggleSave(vehicleId, button) {
        log('Toggle save for vehicle:', vehicleId);
        
        const isSaved = button.classList.contains('saved');
        const action = isSaved ? 'unsave' : 'save';
        
        log('Current state:', isSaved ? 'saved' : 'not saved', '- Action:', action);
        
        // Disable button during request
        button.disabled = true;
        
        // Optimistic UI update
        updateButtonState(vehicleId, !isSaved);
        
        const formData = new URLSearchParams();
        formData.append('action', action);
        formData.append('vehicle_id', vehicleId);
        
        log('Sending request:', API_ENDPOINT, formData.toString());
        
        fetch(API_ENDPOINT, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formData.toString()
        })
        .then(response => {
            log('Response status:', response.status);
            if (!response.ok) {
                throw new Error('HTTP ' + response.status + ' - API file not found or error');
            }
            return response.text().then(text => {
                log('Response text:', text);
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error('Invalid JSON response: ' + text);
                }
            });
        })
        .then(data => {
            log('Response data:', data);
            
            button.disabled = false;
            
            if (data.success) {
                // Update count
                if (data.count !== undefined) {
                    updateCountBadge(data.count);
                }
                
                // Show success message
                showNotification(
                    isSaved ? 'Vehicle removed from saved list' : 'Vehicle saved successfully',
                    'success'
                );
                
                log('Save operation successful');
            } else {
                // Revert UI on error
                updateButtonState(vehicleId, isSaved);
                showNotification(data.message || 'An error occurred', 'error');
                console.error('Save failed:', data.message);
            }
        })
        .catch(error => {
            console.error('Error during save:', error);
            button.disabled = false;
            
            // Revert UI on error
            updateButtonState(vehicleId, isSaved);
            showNotification('An error occurred. Please check console.', 'error');
        });
    }
    
    // Update button visual state
    function updateButtonState(vehicleId, isSaved) {
        log('Updating button state for vehicle', vehicleId, ':', isSaved ? 'saved' : 'not saved');
        
        const buttons = document.querySelectorAll(
            `[data-save-vehicle="${vehicleId}"], [data-vehicle-id="${vehicleId}"].btn-save`
        );
        
        log('Found', buttons.length, 'buttons to update');
        
        buttons.forEach(button => {
            if (isSaved) {
                button.classList.add('saved');
                button.style.background = '#e8f5e9';
                button.style.borderColor = '#4caf50';
                button.style.color = '#2e7d32';
                
                const icon = button.querySelector('i');
                if (icon) icon.className = 'fas fa-bookmark';
                
                const text = button.querySelector('span');
                if (text) text.textContent = 'Saved';
            } else {
                button.classList.remove('saved');
                button.style.background = '#fff';
                button.style.borderColor = '#ccc';
                button.style.color = '#000';
                
                const icon = button.querySelector('i');
                if (icon) icon.className = 'far fa-bookmark';
                
                const text = button.querySelector('span');
                if (text) text.textContent = 'Save';
            }
        });
    }
    
    // Show notification toast
    function showNotification(message, type = 'success') {
        log('Showing notification:', type, message);
        
        // Remove existing notifications
        const existing = document.querySelector('.save-notification');
        if (existing) {
            existing.remove();
        }
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `save-notification save-notification-${type}`;
        notification.innerHTML = `
            <div class="save-notification-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        // Add styles
        const styles = `
            .save-notification {
                position: fixed;
                top: 100px;
                right: 30px;
                background: #fff;
                padding: 16px 24px;
                border-radius: 8px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                z-index: 10000;
                animation: slideIn 0.3s ease;
            }
            
            .save-notification-success {
                border-left: 4px solid #4caf50;
            }
            
            .save-notification-error {
                border-left: 4px solid #f44336;
            }
            
            .save-notification-content {
                display: flex;
                align-items: center;
                gap: 12px;
                font-size: 0.95rem;
            }
            
            .save-notification-success i {
                color: #4caf50;
            }
            
            .save-notification-error i {
                color: #f44336;
            }
            
            @keyframes slideIn {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(400px);
                    opacity: 0;
                }
            }
        `;
        
        // Add styles if not already added
        if (!document.getElementById('save-notification-styles')) {
            const styleSheet = document.createElement('style');
            styleSheet.id = 'save-notification-styles';
            styleSheet.textContent = styles;
            document.head.appendChild(styleSheet);
        }
        
        // Add to page
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    // Global function for onclick attributes
    window.saveVehicle = function(vehicleId) {
        log('saveVehicle() called for ID:', vehicleId);
        const button = event.target.closest('button');
        if (button) {
            toggleSave(vehicleId, button);
        }
    };
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSavedVehicles);
    } else {
        initSavedVehicles();
    }
    
    // Expose public API
    window.SavedVehicles = {
        toggle: toggleSave,
        updateCount: updateSavedCount,
        refresh: updateSaveButtons,
        debug: {
            getEndpoint: () => API_ENDPOINT,
            testConnection: () => {
                console.log('Testing connection to:', API_ENDPOINT);
                fetch(API_ENDPOINT + '?action=get_count')
                    .then(r => {
                        console.log('Status:', r.status);
                        return r.json();
                    })
                    .then(d => console.log('Connection test SUCCESS:', d))
                    .catch(e => console.error('Connection test FAILED:', e));
            }
        }
    };
    
    log('SavedVehicles module loaded. Try: SavedVehicles.debug.testConnection()');
})();