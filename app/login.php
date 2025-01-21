<?php
require_once('lib/core.php');
require_once('lib/app.php');
sys_init();// init page session
if(isset($_POST['username'])) user_login();
// locations
$locations = get_locations();
?>
<!doctype html>
<html>
<head>
	<title>inventory info collection</title>
	<script src="js/jquery-1.11.3.min.js"></script>
	<script src="js/funcs.js"></script>
	<!-- <script language="javascript"> //console.log(0);//alert(check_device());</script> -->
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
	<script src="js/respond.min.js"></script>
	<script src="js/html5shiv.min.js"></script>
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
								<label for="inputusername" styel="font-size:60px;">name</label>
								<input type="text" name="username" class="form-control" id="inputusername" placeholder="enter name">
							</div>
							<div class="form-group" id="password_box">
								<label for="inputpassword">id</label>
								<input type="text" name="password" class="form-control" id="inputpassword" placeholder="enter employee id">
							</div>
							<div class="form-group" id="lbox">
								<label for="locationid">location</label>
								<select class="form-control" id="location_input_select" name="locationid" onchange="switch_location_input();">
									<?php if(!empty($locations)) { foreach ($locations as $location) { ?>
										<option value="<?php echo $location['flo_code']; ?>"><?php echo $location['flo_address']; ?></option>
									<?php }} else {  ?>
										<option value="1">test 01</option>
										<option value="2">test 02</option>
										<option value="3">test 03</option>
									<?php } ?>
									<option value="other">other ... ( to enter )</option>
								</select>
								<input id="location_input_text" name="new_location" type="text" class="form-control" value="" placeholder="enter location"  onkeyup="switch_location_input();">
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
<script>
$(document).on('change', '.btn-file :file', function() {
    var input = $(this),
        numfiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numfiles, label]);
});

$('input').keypress(function(e) {
    if (e.which == 13)
    {
        $('this').next('input').focus();
        if($('#inputusername').is(':focus'))
        	$('#inputpassword').focus();
        else if($('#inputpassword').is(':focus'))
        	$('#location_input_select').focus();
        else if($('#location_input_select').is(':focus'))
        	$('#inputusername').focus();
        else
        	$('#inputusername').focus();
        e.preventdefault();
    }
    //submitlogin();
});

function submitlogin()
{
    if( $('#inputusername').val() != "" && $('#inputpassword').val() != "" && ($('#location_input_select').val() != "other" || $('#location_input_text').val() != ""))
    	$('#login_form').submit();
    	//console.log($('#inputusername').val());
    else
    	console.log('not ready...');
}

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
<script src="js/jquery-ui.min.js" type="text/javascript"></script>
<script src="css/bootstrap-3.3.5-dist/js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="js/app.min.js" type="text/javascript"></script>
<script src='js/fastclick.min.js'></script>
<script src="js/login.js"></script>
</body>
</html>
<?php //db_disconnect($pdata['conn']); ?>
