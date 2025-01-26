<?php
require_once('lib/core.php');
require_once('lib/app.php');
sys_init();
$conn = db_connect();
user_login_check();
if(isset($_POST['locationid']))
{
	changelocation($_POST['locationid']);
}
// locations
$locations = get_locations();
?>
<!doctype html>
<html>
<head>
	<title>inventory info collection</title>
	<script src="lib/js/jquery-1.11.3.min.js"></script>
	<script src="lib/js/funcs.js"></script>
	<!-- <script language="javascript"> console.log(0);//alert(check_device());</script> -->
	<link rel="stylesheet" type="text/css" href="css/bootstrap-3.3.5-dist/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/reset.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap-fileinput-master/css/fileinput.min.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="css/ionicons.min.css">
	<!-- <link rel="stylesheet" type="text/css" href="css/admin-te.min.css"> -->
	<link href="css/_all-skins.min.css" rel="stylesheet" type="text/css" />
  	<link href="css/blue.css" rel="stylesheet" type="text/css" />
  	<link href="css/datepicker3.css" rel="stylesheet" type="text/css" />
	<script src="css/bootstrap-fileinput-master/js/fileinput.min.js"></script>
	<script src="lib/js/respond.min.js"></script>
	<script src="lib/js/html5shiv.min.js"></script>
</head>
<body>
<div class="content-wrapper">
		<!-- main content -->
		<section class="content">
			<div class="row">
				<div class="col-md-12 logo">
					<img src="img/logo_mobile.png" width="380" height="380" />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 logo">
					<h1 class="h1">inventory collection</h1>
				</div>
			</div>
			<form class="login_form" action="" method="post">
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
							<div class="form-group" id="lbox">
								<label for="exampleinputpassword1">location</label>
								<select class="form-control" id="location_input_select" name="locationid" onchange="switchlocationinput();">
									<?php if(!empty($locations)) { foreach ($locations as $location) { ?>
										<option value="<?php echo $location['flo_code']; ?>"<?php if($pdata['loc']['flo_code'] == $location['flo_code']) echo 'selected="selected"'; ?>><?php echo $location['flo_address']; ?></option>
									<?php }} else {  ?>
										<option value="1">test 01</option>
										<option value="2">test 02</option>
										<option value="3">test 03</option>
									<?php } ?>
									<option value="other">---other</option>
								</select>
								<input id="location_input_text" name="new_location" type="text" class="form-control" value="" placeholder="enter location"  onkeyup="switchlocationinput();">
							</div>
						</div><!-- /.box-body -->

						<div class="box-footer">
							<button type="submit" class="btn btn-default">submit</button>
						</div>
					</div><!-- /.box -->

				</div><!--/.col (left) -->
			</div>   <!-- /.row -->
		</form>
		</section><!-- /.content -->
</div>
<script>
$(document).on('change', '.btn-file :file', function() {
    var input = $(this),
        numfiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numfiles, label]);
});

$(document).ready(function(){
	$('#location_input_text').hide();
});

function loginerror()
{
	$('#username_box').attr('class', 'form-group has-error');
	$('#password_box').attr('class', 'form-group has-error');
}

<?php
if(!empty($_GET['action']))
	if($_GET['action'] == 'error')
		echo 'loginerror();';
?>
</script>
<script src="lib/js/jquery-ui.min.js" type="text/javascript"></script>
<script src="css/bootstrap-3.3.5-dist/js/bootstrap.min.js"></script>
<script src="lib/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="lib/js/app.min.js" type="text/javascript"></script>
<script src='js/fastclick.min.js'></script>
<script src="lib/js/login.js"></script>
</body>
</html>
<?php db_disconnect($conn); ?>