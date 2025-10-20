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
	<title>inventory info collection</title>
	<!-- Native CSS and JavaScript - No External Dependencies -->
	<link rel="stylesheet" type="text/css" href="css/native.css">
	<link rel="stylesheet" type="text/css" href="css/reset.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<script src="js/funcs.js"></script>
	<script src="js/native.js"></script>
</head>
<body>
<div class="content-wrapper">
		<!-- main content -->
		<section class="content">
			<?php $pdata['showmenu'] = false; include('lib/header.php'); ?>
			<div class="row">
				<div class="col-md-12 logo">
					<h1 class="h1">inventory collection</h1>
				</div>
			</div>
			<form id="login_form" class="login_form" action="login.php" method="post">
			<div class="row">
				<!-- left column -->
				<div class="col-md-12">
					<!-- general form elements -->
					<div class="box box-primary">
						<!-- <div class="box-header">
							<h3 class="box-title">system login...</h3>
						</div> -->
						<!-- /.box-header -->
						<!-- form start -->
						<div class="box-body">
							
							<?php if (!empty($error_message)): ?>
								<div class="alert alert-danger">
									<?php echo htmlspecialchars($error_message); ?>
								</div>
							<?php endif; ?>
							
							<?php
								if(isset($_GET['action']))
								{
									if($_GET['action'] == 'login')
									{
										echo '<h3 class="h3 text-danger">not alowed to view that page, please login first!</h3>';
									}
									elseif($_GET['action'] == 'error')
									{
										echo '<h3 class="h3 text-danger">username or password wrong, plese try agine!</h3>';
									}
								}
							?>
							<div class="form-group" id="username_box">
								<label for="inputusername">Name</label>
								<input type="text" name="username" class="form-control" id="inputusername" placeholder="enter name" required>
							</div>
							<div class="form-group" id="password_box">
								<label for="inputpassword">Password</label>
								<input type="password" name="password" class="form-control" id="inputpassword" placeholder="enter password" required>
							</div>
							<div class="form-group" id="lbox">
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
						</div>
					</div><!-- /.box -->

				</div><!--/.col (left) -->
			</div>   <!-- /.row -->
		</form>
		</section><!-- /.content -->
</div>

<!-- Admin link at bottom left -->
<div class="admin-link-bottom">
	<a href="admin/" class="admin-link-small">Admin</a>
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
