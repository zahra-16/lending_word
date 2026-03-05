<?php
/**
 * PostgreSQL Array Helper Functions
 * Untuk konversi TEXT[] PostgreSQL ke PHP array
 */

/**
 * Convert PostgreSQL array string to PHP array
 * 
 * @param string|array $pgArray PostgreSQL array format: {item1,item2,item3}
 * @return array PHP array
 */
function pg_array_parse($pgArray) {
    // Jika sudah array, return langsung
    if (is_array($pgArray)) {
        return $pgArray;
    }
    
    // Jika null atau empty
    if (empty($pgArray) || $pgArray === '{}') {
        return [];
    }
    
    // Jika string PostgreSQL array format
    if (is_string($pgArray)) {
        // Remove curly braces
        $pgArray = trim($pgArray, '{}');
        
        // Jika kosong setelah trim
        if (empty($pgArray)) {
            return [];
        }
        
        // Split by comma (handle quoted strings)
        $result = [];
        $current = '';
        $inQuotes = false;
        
        for ($i = 0; $i < strlen($pgArray); $i++) {
            $char = $pgArray[$i];
            
            if ($char === '"') {
                $inQuotes = !$inQuotes;
                continue;
            }
            
            if ($char === ',' && !$inQuotes) {
                $result[] = trim($current);
                $current = '';
                continue;
            }
            
            $current .= $char;
        }
        
        // Add last item
        if ($current !== '') {
            $result[] = trim($current);
        }
        
        return $result;
    }
    
    return [];
}

/**
 * Get equipment highlights as array
 * Safe wrapper untuk menghindari foreach error
 * 
 * @param mixed $equipment
 * @return array
 */
function get_equipment_array($equipment) {
    if (is_array($equipment)) {
        return $equipment;
    }
    
    if (is_string($equipment)) {
        return pg_array_parse($equipment);
    }
    
    return [];
}

/**
 * Format PostgreSQL array untuk display
 * 
 * @param mixed $pgArray
 * @param string $separator
 * @return string
 */
function pg_array_display($pgArray, $separator = ', ') {
    $array = pg_array_parse($pgArray);
    return implode($separator, $array);
}