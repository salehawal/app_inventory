<?php
session_start();
require_once('lib/core.php');

// Simple direct database connection to avoid sys_init issues
$host = 'localhost';
$dbname = 'pr_inventory';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$error_message = '';

// Handle login
if (isset($_POST['username']) && isset($_POST['password'])) {
    $user = clean_sql($_POST['username']);
    $pass = clean_sql($_POST['password']);
    $location = $_POST['locationid'] ?? '';
    $new_location = $_POST['new_location'] ?? '';
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM fict_users WHERE fu_username = ? AND fu_password = ?");
        $stmt->execute([$user, $pass]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($userData) {
            // Check user's assigned locations
            $stmt = $pdo->prepare("
                SELECT l.* 
                FROM fict_location l 
                INNER JOIN fict_user_locations ula ON l.flo_code = ula.ula_location_code 
                WHERE ula.ula_user_code = ? AND ula.ula_status = 'active'
            ");
            $stmt->execute([$userData['fu_code']]);
            $userLocations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($userLocations)) {
                $error_message = "No locations assigned to this user. Please contact admin.";
            } else {
                $validLocation = false;
                $finalLocation = '';
                
                if ($location === 'other' && !empty($new_location)) {
                    // Allow new location entry for now
                    $validLocation = true;
                    $finalLocation = $new_location;
                } else if (!empty($location)) {
                    // Check if selected location is in user's assigned locations
                    foreach ($userLocations as $userLoc) {
                        if ($userLoc['flo_code'] === $location) {
                            $validLocation = true;
                            $finalLocation = $userLoc['flo_address'];
                            break;
                        }
                    }
                }
                
                if ($validLocation) {
                    // Set session
                    $_SESSION['user']['login'] = true;
                    $_SESSION['user']['data'] = $userData;
                    $_SESSION['user']['current_location'] = $finalLocation;
                    $_SESSION['user']['current_location_code'] = $location;
                    
                    // Redirect to main page
                    header("Location: main.php");
                    exit();
                } else {
                    $availableLocations = array_map(function($loc) {
                        return $loc['flo_address'];
                    }, $userLocations);
                    $error_message = "You are not authorized to access this location. Available locations: " . implode(', ', $availableLocations);
                }
            }
        } else {
            $error_message = "Invalid username or password.";
        }
    } catch (Exception $e) {
        $error_message = "Login error: Please try again.";
    }
}

// Get locations
$locations = [];
try {
    $stmt = $pdo->query("SELECT * FROM fict_location ORDER BY flo_address");
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $locations = [];
}
?>
<!doctype html>
<html>
<head>
	<title>Inventory Collection Login</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Optimized CSS for Full Screen Responsive Design -->
	<link rel="stylesheet" type="text/css" href="css/optimized.css">
	<script src="js/funcs.js"></script>
</head>
<body>
<div class="content">
		<!-- main content -->
	<!-- Logo Header -->
	<div class="row">
		<div class="col-xs-12 text-center" style="margin-bottom: 30px;">
			<img src="img/logo.png" style="max-height: 60px;" alt="Logo">
			<h1 style="margin: 15px 0; font-size: 24px; color: #333;">Inventory Collection</h1>
		</div>
	</div>
	
	<!-- Login Form -->
	<form id="login_form" action="login.php" method="post">
		<div class="row">
			<div class="col-xs-12">
				<div class="box">
					<div class="box-body">
						<?php if (!empty($error_message)): ?>
							<div style="background: #f2dede; color: #a94442; padding: 12px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #ebccd1;">
								<?php echo htmlspecialchars($error_message); ?>
							</div>
						<?php endif; ?>
						
						<?php if(isset($_GET['action'])): ?>
							<?php if($_GET['action'] == 'login'): ?>
								<div style="background: #f2dede; color: #a94442; padding: 12px; border-radius: 4px; margin-bottom: 15px;">
									Not allowed to view that page, please login first!
								</div>
							<?php elseif($_GET['action'] == 'error'): ?>
								<div style="background: #f2dede; color: #a94442; padding: 12px; border-radius: 4px; margin-bottom: 15px;">
									Username or password wrong, please try again!
								</div>
							<?php endif; ?>
						<?php endif; ?>
						
						<div class="form-group">
							<label for="inputusername">Name</label>
							<input type="text" name="username" class="form-control" id="inputusername" placeholder="Enter name" required>
						</div>
						
						<div class="form-group">
							<label for="inputpassword">Password</label>
							<input type="password" name="password" class="form-control" id="inputpassword" placeholder="Enter password" required>
						</div>
						
						<div class="form-group">
							<label for="locationid">Location</label>
							<select class="form-control" id="location_input_select" name="locationid" required>
								<option value="">Select a location...</option>
									<?php if(!empty($locations) && is_array($locations)) { foreach ($locations as $location) { ?>
										<option value="<?php echo htmlspecialchars($location['flo_code']); ?>"><?php echo htmlspecialchars($location['flo_address']); ?></option>
									<?php }} ?>
									<option value="other">other ... ( to enter )</option>
								</select>
								<input id="location_input_text" name="new_location" type="text" class="form-control" value="" placeholder="enter location">
								<small class="help-block">Note: You can only access locations assigned to your account.</small>
							</div>
							</div>
							
						</div><!-- /.box-body -->

						<div class="box-footer">
							<button type="button" class="btn btn-default" onclick="submitlogin();">login</button>
						
						<div class="form-group" style="margin-top: 20px;">
							<button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 16px; font-weight: bold;">
								Login
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
	
	<!-- Admin Link -->
	<div style="position: fixed; bottom: 10px; left: 10px; z-index: 1000;">
		<a href="admin/" style="font-size: 11px; color: #999; text-decoration: none; padding: 3px 6px; border-radius: 3px; background: rgba(255,255,255,0.1);">Admin</a>
	</div>
</div>

<style>
.admin-link-bottom {
	position: fixed;
	bottom: 10px;
	left: 10px;
	z-index: 1000;
}

.admin-link-small {
	font-size: 11px;
	color: #999;
	text-decoration: none;
	padding: 3px 6px;
	border-radius: 3px;
	background: rgba(255,255,255,0.1);
}

.admin-link-small:hover {
	color: #666;
	background: rgba(255,255,255,0.2);
	text-decoration: none;
}
</style>
<script>
// Native JavaScript implementation for login functionality
document.addEventListener('DOMContentLoaded', function() {
    // Hide location text input by default
    const locationTextInput = document.getElementById('location_input_text');
    if (locationTextInput) {
        locationTextInput.style.display = 'none';
    }
});

// Handle enter key navigation
document.addEventListener('keypress', function(e) {
    if (e.which === 13 || e.keyCode === 13) {
        const activeElement = document.activeElement;
        
        if (activeElement && activeElement.id === 'inputusername') {
            document.getElementById('inputpassword').focus();
        } else if (activeElement && activeElement.id === 'inputpassword') {
            document.getElementById('location_input_select').focus();
        } else if (activeElement && activeElement.id === 'location_input_select') {
            document.getElementById('inputusername').focus();
        } else {
            document.getElementById('inputusername').focus();
        }
        e.preventDefault();
    }
});

function submitlogin() {
    const username = document.getElementById('inputusername').value;
    const password = document.getElementById('inputpassword').value;
    const locationSelect = document.getElementById('location_input_select').value;
    const locationText = document.getElementById('location_input_text').value;
    
    // Check if all required fields are filled
    if (username !== "" && password !== "" && locationSelect !== "" && (locationSelect !== "other" || locationText !== "")) {
        document.getElementById('login_form').submit();
    } else {
        alert('Please fill in all required fields.');
    }
}

function loginerror() {
    const usernameBox = document.getElementById('username_box');
    const passwordBox = document.getElementById('password_box');
    
    if (usernameBox) usernameBox.className = 'form-group has-error';
    if (passwordBox) passwordBox.className = 'form-group has-error';
}

<?php
if(!empty($_GET['action']))
	if($_GET['action'] == 'error')
		echo 'loginerror();';
?>
</script>
<script src="js/login.js"></script>
</body>
</html>
<?php //db_disconnect($pdata['conn']); ?>
