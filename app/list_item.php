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
	<!-- Native CSS and JavaScript - No External Dependencies -->
	<link rel="stylesheet" type="text/css" href="css/native.css">
	<link rel="stylesheet" type="text/css" href="css/reset.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<script src="js/funcs.js"></script>
	<script src="js/native.js"></script>
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
// Native JavaScript implementation - No jQuery dependencies

// Initialize DataTable when DOM is ready
domReady(function() {
    const table = document.getElementById('example1');
    if (table) {
        createNativeDataTable(table);
    }
});
</script>
</body>
</html>
