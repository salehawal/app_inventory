<?php
require_once('inc/core.php');
require_once('inc/app.php');
sys_init();
user_login_check();
$pdata['showmenu'] = false;
if(isset($_POST['iid']))
	find_item();
?>
<!DOCTYPE html>
<html>
<head>
	<title>Inventory Collection</title>
	<script src="js/funcs.js"></script>
	<script language="javascript"> console.log(0);//alert(check_device());</script>
	<link rel="stylesheet" type="text/css" href="css/bootstrap-3.3.5-dist/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/reset.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap-fileinput-master/css/fileinput.min.css">
	<link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="css/ionicons.min.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<link rel="stylesheet" type="text/css" href="css/main_mobile.css">
	<link href="css/_all-skins.min.css" rel="stylesheet" type="text/css" />
  	<link href="css/blue.css" rel="stylesheet" type="text/css" />
  	<link href="css/datepicker3.css" rel="stylesheet" type="text/css" />

	<script src="js/jquery-1.11.3.min.js"></script>
	<script src="css/bootstrap-fileinput-master/js/fileinput.min.js"></script>
	<script src="js/respond.min.js"></script>
	<script src="js/html5shiv.min.js"></script>
</head>
<body>
	<form class="" action="" method="post">
	<div class="content-wrapper">
		<?php include('inc/header.php'); ?>
		<!-- Main content -->
		<div style="padding-bottom:140px;"></div>
		<section class="content">
			<div class="row">
				<div class="col-xs-12">
					<input type="text" name="iid" id="iid" class="form-control" placeholder="Enter Item Code..." style="padding:120px; font-size:80px;" onkeyup="javascript:force_upper_case(this);">
				</div>
			</div>
			<br><br>
			<div class="row">
				<div class="col-xs-12" style="text-align: center">
					<input type="submit" value="SEARCH" class="btn btn-danger" style="padding:120px; font-size:90px;">
				</div>
			</div>
		</section>
	</div>
</form>
<script src="js/jquery-ui.min.js" type="text/javascript"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="js/dataTables.bootstrap.min.js" type="text/javascript"></script>
<script src="js/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="js/jQuery-2.1.4.min.js"></script>
<script src="js/bootstrap.min.js" type="text/javascript"></script>
<script src="js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="js/dataTables.bootstrap.min.js" type="text/javascript"></script>
<script src="js/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src='js/fastclick.min.js'></script>
<script src="js/app.min.js" type="text/javascript"></script>
<script src="js/demo.js" type="text/javascript"></script>
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