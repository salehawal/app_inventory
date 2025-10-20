<?php
require_once('lib/core.php');
require_once('lib/app.php');
sys_init();
//user_login_check();
$pdata['showmenu'] = false;
if(isset($_POST['iid']))
	find_item();
?>
<!DOCTYPE html>
<html>
<head>
	<title>Inventory Collection</title>
	<!-- Native CSS and JavaScript - No External Dependencies -->
	<link rel="stylesheet" type="text/css" href="css/native.css">
	<link rel="stylesheet" type="text/css" href="css/reset.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<script src="js/funcs.js"></script>
	<script src="js/native.js"></script>
</head>
<body>
	<form class="" action="" method="post">
	<div class="content-wrapper">
		<?php include('lib/header.php'); ?>
		<!-- Main content -->
		<div class="mspace"></div>
		<section class="content">
			<div class="row">
				<div class="col-xs-12">
					<input type="text" name="iid" id="iid" class="form-control input_search" placeholder="Enter Item Code..." onkeyup="javascript:force_upper_case(this);">
				</div>
			</div>
			<br><br>
			<div class="row">
				<div class="col-xs-12" style="text-align: center">
					<input type="submit" value="FIND" class="btn btn-danger input_button">
				</div>
			</div>
		</section>
	</div>
</form>
<script src="js/bootstrap.min.js"></script>
<script src="js/app.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(function () {
	$("#example1").dataTable();
});

function notFound(iid)
{
	$('#iid').attr('class', 'form-group has-error');
	$('#iid').val(iid);
	$('#iid').css('color','red');
}

<?php if($_GET['action'] == 'notfound') { ?>
var iid = '<?php echo $_GET["iid"]; ?>';
notFound(iid);
<?php } ?>
$('#iid').focus();
</script>
</body>
</html>