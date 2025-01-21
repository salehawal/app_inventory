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
	<script src="js/funcs.js"></script>
	<script language="javascript"> //alert(check_device());</script>
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
	<script src="js/jquery-1.11.3.min.js"></script>
	<script src="css/bootstrap-fileinput-master/js/fileinput.min.js"></script>
	<script src="js/respond.min.js"></script>
	<script src="js/html5shiv.min.js"></script>
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
</body>
</html>
