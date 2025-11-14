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
	<title>Inventory Collection</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Optimized CSS for Full Screen Responsive Design -->
	<link rel="stylesheet" type="text/css" href="css/optimized.css">
	<script src="js/funcs.js"></script>
</head>
<body>
<div class="content">
	<?php $pdata['showmenu'] = false; include('lib/header.php'); ?>
	
	<!-- Main Navigation Menu -->
	<?php foreach ($sections as $id => $sec) { ?>
	<div class="row btn-nav-menu">
		<div class="col-xs-12">
			<button type="button" class="btn btn-default" onclick="go_to_page('list_item.php?section=<?php echo $sec['section']; ?>');">
				<?php echo $sec['pname']; ?>
			</button>
		</div>
	</div>
	<?php } ?>
	
	<!-- Logout Section -->
	<div class="row btn-nav-menu">
		<div class="col-xs-12">
			<button type="button" class="btn btn-danger btn-logout-main" onclick="confirmLogout();">
				Logout
			</button>
		</div>
	</div>
</div>

<script>
function confirmLogout() {
	if (confirm('Are you sure you want to logout?')) {
		window.location.href = 'logout.php';
	}
}
</script>
</body>
</html>
