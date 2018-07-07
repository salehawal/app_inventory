<?php
require_once('lib/core.php');
require_once('lib/app.php');
sys_init();
// get page data
if(isset($_GET['section']))
	p_init();
// delete item
if(isset($_GET['action']) && $_GET['action'] == 'del')
	remove_item($pdata['page']['tname'],$pdata['page']['tidfield'],$_GET['iid']);
user_login_check();
// load data
get_items();
?>
<!doctype html>
<html>
<head>
	<title>inventory collection</title>
	<script src="js/funcs.js"></script>
	<!-- <script language="javascript"> //alert(check_device());</script> -->
	<link rel="stylesheet" type="text/css" href="css/bootstrap-3.3.5-dist/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/reset.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap-fileinput-master/css/fileinput.min.css">
	<link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="css/ionicons.min.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<link href="css/_all-skins.min.css" rel="stylesheet" type="text/css" />
  	<link href="css/blue.css" rel="stylesheet" type="text/css" />
  	<link href="css/datepicker3.css" rel="stylesheet" type="text/css" />
	<script src="js/jquery-1.11.3.min.js"></script>
	<script src="css/bootstrap-fileinput-master/js/fileinput.min.js"></script>
	<script src="js/respond.min.js"></script>
	<script src="js/html5shiv.min.js"></script>
</head>
<body>
<div class="content-wrapper">
	<?php include('lib/header.php'); ?>
	<!-- main content -->
	<section class="content">
		<div class="row">
			<div class="input-btn col-xs-12" style="text-align:left;">
				<input name="brn01" type="button" class="btn btn-danger action_btn" value="add new" onclick="go_to_page('item.php?section=<?php echo $pdata['page']['section']; ?>');" />
			</div>
		</div><br><br>
		<div class="row data-table">
			<div class="col-xs-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><?php echo $pdata['page']['pname']; ?>s data...</h3>
					</div><!-- /.box-header -->
					<div class="box-body">
					<table class="table table-bordered table-striped idata-table">
					  <thead>
				          <?php echo $pdata['data']['th']; ?>
				      </thead>
						<tbody>
							<?php echo $pdata['data']['td']; ?>
						</tbody>
					</table>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
		</div>
	</section>
</div>
<script>
$(document).on('change', '.btn-file :file', function() {
    var input = $(this),
        numfiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numfiles, label]);
});
</script>
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
		$("#example1").datatable();
	});
</script>
</body>
</html>
