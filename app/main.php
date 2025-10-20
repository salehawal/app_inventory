<?php
require_once('lib/core.php');
require_once('lib/app.php');
sys_init();
user_login_check();
// sections
$sections = get_main_tables();
?>
<!doctype html>
<html>
<head>
	<title>inventory collection</title>
	<!-- Native CSS and JavaScript - No External Dependencies -->
	<link rel="stylesheet" type="text/css" href="css/native.css">
	<link rel="stylesheet" type="text/css" href="css/reset.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<script src="js/funcs.js"></script>
	<script src="js/native.js"></script>
</head>
<body>
<div class="content">
	<?php $pdata['showmenu'] = false; include('lib/header.php'); ?>
	<?php foreach ($sections as $id => $sec) { ?>
	<div class="row btn-nav-menu">
		<div class="col-xs-12 list-btn">
			<button type="button" class="btn btn-default" onclick="go_to_page('list_item.php?section=<?php echo $sec['section']; ?>');"><?php echo $sec['pname']; ?></button>
		</div>
	</div>
	<?php } ?>
	
	<!-- Logout button after the menu list -->
	<div class="row btn-nav-menu logout-section">
		<div class="col-xs-12">
			<button type="button" class="btn btn-danger btn-logout-main" onclick="confirmLogout();">Logout</button>
		</div>
	</div>
</div>

<style>
.logout-section {
	margin-top: 20px;
	padding-top: 20px;
	border-top: 2px solid #eee;
}

.btn-logout-main {
	width: 100%;
	padding: 50px 20px;
	font-size: 18px;
	font-weight: bold;
	background-color: #d9534f;
	border-color: #d43f3a;
	color: white;
	border-radius: 6px;
	text-transform: uppercase;
	letter-spacing: 1px;
}

.btn-logout-main:hover {
	background-color: #c9302c;
	border-color: #ac2925;
	color: white;
	transform: translateY(-2px);
	box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn-logout-main:active {
	transform: translateY(0);
	box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

/* Responsive design */
@media (max-width: 768px) {
	.btn-logout-main {
		padding: 40px 16px;
		font-size: 16px;
	}
}
</style>

<script>
// Native JavaScript implementation - No jQuery dependencies
function confirmLogout() {
	if (confirm('Are you sure you want to logout?')) {
		window.location.href = 'logout.php';
	}
}
</script>
</body>
</html>
