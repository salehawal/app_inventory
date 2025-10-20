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
// Native JavaScript implementation - No jQuery dependencies

domReady(function(){
	var locationInputText = document.getElementById('location_input_text');
	if (locationInputText) {
		locationInputText.style.display = 'none';
	}
});

function loginerror()
{
	var usernameBox = document.getElementById('username_box');
	var passwordBox = document.getElementById('password_box');
	if (usernameBox) {
		usernameBox.className = 'form-group has-error';
	}
	if (passwordBox) {
		passwordBox.className = 'form-group has-error';
	}
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
<?php db_disconnect($conn); ?>