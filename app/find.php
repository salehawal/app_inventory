<?php
require_once('lib/core.php');
require_once('lib/app.php');
sys_init();
user_login_check();
$pdata['showmenu'] = false;
if(isset($_POST['iid']))
	find_item();
?>
<!DOCTYPE html>
<html>
<head>
	<title>Find Item - Inventory Collection</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Optimized CSS for Full Screen Responsive Design -->
	<link rel="stylesheet" type="text/css" href="css/optimized.css">
	<script src="js/funcs.js"></script>
</head>
<body>
	<form class="" action="" method="post">
	<div class="content-wrapper">
		<?php include('lib/header.php'); ?>
		
		<!-- Back Button -->
		<div class="row" style="margin-bottom: 20px;">
			<div class="col-xs-12">
				<button type="button" class="btn btn-default" onclick="go_to_page('main.php');" style="width: 100%;">
					← Back to Menu
				</button>
			</div>
		</div>
		
		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-xs-12">
					<input type="text" name="iid" id="iid" class="form-control" placeholder="Enter Item Code..." onkeyup="javascript:force_upper_case(this);" style="padding: 20px; font-size: 20px; text-align: center;">
				</div>
			</div>
			<div class="row" style="margin-top: 20px;">
				<div class="col-xs-12">
					<button type="submit" class="btn btn-primary" style="width: 100%; padding: 20px; font-size: 18px; font-weight: bold;">
						🔍 Find Item
					</button>
				</div>
			</div>
		</section>
	</div>
</form>
<script type="text/javascript">
function notFound(iid) {
	const input = document.getElementById('iid');
	input.style.borderColor = 'red';
	input.style.backgroundColor = '#ffebee';
	input.value = iid;
	input.style.color = 'red';
}

<?php if(isset($_GET['action']) && $_GET['action'] == 'notfound') { ?>
var iid = '<?php echo $_GET["iid"]; ?>';
notFound(iid);
<?php } ?>

// Auto-focus on input field
document.getElementById('iid').focus();
</script>
</body>
</html>