<?php
// Test file to debug login issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting debug...<br>";

try {
    echo "1. Including core.php...<br>";
    require_once('lib/core.php');
    echo "2. Core.php loaded successfully<br>";
    
    echo "3. Including app.php...<br>";
    require_once('lib/app.php');
    echo "4. App.php loaded successfully<br>";
    
    echo "5. Calling sys_init()...<br>";
    sys_init();
    echo "6. sys_init() completed<br>";
    
    echo "7. Testing get_locations()...<br>";
    $locations = get_locations();
    echo "8. get_locations() returned: ";
    var_dump($locations);
    echo "<br>";
    
    echo "9. Testing user_login() existence...<br>";
    if (function_exists('user_login')) {
        echo "10. user_login() function exists<br>";
    } else {
        echo "10. ERROR: user_login() function does not exist<br>";
    }
    
    echo "11. All tests completed successfully!<br>";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString() . "<br>";
} catch (Error $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString() . "<br>";
}
?>