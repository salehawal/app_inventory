<?php
require_once('lib/core.php');
require_once('lib/app.php');
sys_init();
// get page data
if(isset($_GET['section']))
	p_init();
user_login_check();
if(!empty($_POST['locationid']))
	add_item();
if(isset($_POST['action'])) { print_r($_POST); exit; }
?>
<!doctype html>
<html>
<head>
	<title>inventory collection</title>
	<script src="js/funcs.js"></script>
	<script src="js/jquery-1.11.3.min.js"></script>
	<!-- <script language="javascript"> //console.log(0);//alert(check_device());</script> -->
	<link rel="stylesheet" type="text/css" href="css/bootstrap-3.3.5-dist/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/reset.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap-fileinput-master/css/fileinput.min.css">
	<link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="css/ionicons.min.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<!-- <link rel="stylesheet" type="text/css" href="css/admin-te.min.css"> -->
	<link href="css/_all-skins.min.css" rel="stylesheet" type="text/css" />
  	<link href="css/blue.css" rel="stylesheet" type="text/css" />
  	<link href="css/datepicker3.css" rel="stylesheet" type="text/css" />
	<script src="css/bootstrap-fileinput-master/js/fileinput.min.js"></script>
	<script src="js/respond.min.js"></script>
	<script src="js/html5shiv.min.js"></script>
	<link href="css/bootstrap-switch-master/dist/css/bootstrap3/bootstrap-switch.min.css" rel="stylesheet">
	<script src="css/bootstrap-switch-master/dist/js/bootstrap-switch.min.js"></script>
</head>
<body>
<form id="data_form" action="item.php?section=<?php echo $pdata['page']['section']; ?>" method="post" enctype="multipart/form-data">
	<input name="form-status" id="form-status" type="hidden" value="0" />
	<?php if(isset($_GET['iid'])) { ?>
	<input name="action" id="form-action" type="hidden" value="update" />
	<input name="iid" id="form-action" type="hidden" value="<?php echo $_GET['iid']; ?>" />
	<?php } else { ?>
	<input name="action" id="form-action" type="hidden" value="add" />
	<?php } ?>
	<input name="locationid" type="hidden" value="<?php echo $pdata['loc']['flo_code']; ?>" />
	<div class="content-wrapper">
		<?php include('lib/header.php'); ?>
		<!-- main content -->
		<section class="content">
			<?php if(isset($_GET['iid'])) { ?>
			<div class="row">
				<div class="col-md-12">
					<h1 class="h1" style="font-size: 90px; margin-top: -20px; padding-bottom: 60px;">item code: <?php echo $_GET['iid']; ?></h1>
				</div>
			</div>
			<?php } ?>
			<?php include('lib/form_btns.php'); ?>
			<br>
			<div class="row add-from">
				<div class="col-md-1"></div>
				<div class="col-md-10">
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">details</h3>
							<script type="text/javascript">
							<?php view_item(); ?>
							</script>
						</div>
					</div>
				</div>
				<div class="col-md-1"></div>
			</div>
			<div class="row add-from">
				<div class="col-md-1"></div>
				<div class="col-md-10">
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">pictures</h3>
							<div class="form-group" id="fupload">
									<label class="control-label">select file</label>
									<input name="images[]" id="input_image" class="file" multiple="true" type="file" data-preview-file-type="any" data-upload-url="#" accept="image/*;capture=camera" capture="camera" multiple accept="image/*">
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-1"></div>
			</div>
		</section>
		<section class="contect">
			<div class="row" style="text-align: center; width: 83%; margin: auto">
				<div class="col-xs-12">
					<?php if(isset($pdata['page']['item']) && gettype($pdata['page']['item']['images']) == 'array') foreach ($pdata['page']['item']['images'] as $key => $value) { ?>						
							<span style="width:300px; height: 200px; overflow: hidden; float:left; display: inline-block; padding: 2px; margin: 4px;"><a href="lib/image.php?imgid=<?php echo $value['fim_code']; ?>" target="_blank"><img src="lib/image.php?imgid=<?php echo $value['fim_code']; ?>"></a></span>
					<?php } ?>
				</div>
			</div>
		</section>
	</div>
</form>
<script src="js/jquery-ui.min.js" type="text/javascript"></script>
<script src="css/bootstrap-3.3.5-dist/js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="js/app.min.js" type="text/javascript"></script>
<script src='js/fastclick.min.js'></script>
<script>
$(document).on('change', '.btn-file :file', function() {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    	input.trigger('fileselect', [numFiles, label]);
});

// File Upload
$(document).ready(function(){
	$("#input_image").fileinput({
        showUpload: false,
        maxFileCount: 10,
        mainClass: "input-group-lg"
    });
	$('div').find('.file-drop-zone').attr('id','filebox');
	$('#filebox').click(function() { $('#input_image').click(); });

	// Bootstrap Switched
	enable_switch();
});

// Check forma
function checkForm()
{
	var status = false;
	$.each($('#data_form input, #data_form textarea, #data_form select'), function(ifield, ival) {
		if($(ival).is(':enabled') && !$(ival).is(':hidden'))
		{
			if($(ival).val() != "" && $(ival).val() != "--" && $(ival).is(':enabled'))
			{
				status = true;
				//console.log($(ival).prop('type')+' - '+$(ival).prop('id')+'  -  '+$(ival).val());
			}
		}
	});
	
	if(status)
		enable_submit();
	else
		disable_submit();
}

function enable_validation()
{
	$.each($('#data_form input, #data_form textarea, #data_form select'), function(ifield, ival) {
		if($(ival).is(':enabled'))
		{
			//$(ival).attr('onClick','javascript:checkForm();');
			//$(ival).attr('onFocus','javascript:checkForm();');
			$(ival).attr('onKeyDown','javascript:checkForm();');
			//$(ival).attr('onKeyPress','javascript:checkForm();');
			//$(ival).attr('onKeyRelease','javascript:checkForm();');
			//$(ival).attr('onBlure','javascript:checkForm();');
			$(ival).attr('onChange','javascript:checkForm();');
		}
	});
}

function submit_form()
{
	$('#data_form').submit();
}

function disable_submit()
{
	$('#btn-ok').attr('class','btn btn-info btn-info-disable form-menu-btn');
	$('#btn-ok').prop('disabled', true);
	$('#form-status').val(0);
}

function enable_submit()
{
	$('#btn-ok').attr('class','btn btn-info form-menu-btn');
	$('#btn-ok').prop('disabled', false);
	$('#form-status').val(1);
}

function enable_switch()
{
	$.each($('#data_form input'), function(ifield, ival) {
		//console.log(ifield);
		if($(ival).is(':radio') || $(ival).is(':checkbox'))
		{
			var iname = $(ival).attr('id');
			$("#"+iname).bootstrapSwitch();
		}
	});
}

//checkform();
disable_submit();
enable_validation();
</script>
</body>
</html>