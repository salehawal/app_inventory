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
	<title>Inventory Collection</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Optimized CSS for Full Screen Responsive Design -->
	<link rel="stylesheet" type="text/css" href="css/optimized.css">
	<script src="js/funcs.js"></script>
</head>
<body>
<div class="content-wrapper">
	<?php include('lib/header.php'); ?>
	
	<!-- Navigation Buttons -->
	<div class="row" style="margin-bottom: 15px;">
		<div class="col-xs-6">
			<button type="button" class="btn btn-default" onclick="go_to_page('main.php');" style="width: 100%;">
				← Back to Menu
			</button>
		</div>
		<div class="col-xs-6">
			<button type="button" class="btn btn-primary" onclick="go_to_page('item.php?section=<?php echo $pdata['page']['section']; ?>');" style="width: 100%;">
				Add New <?php echo $pdata['page']['pname']; ?>
			</button>
		</div>
	</div>
	
	<!-- Data Table -->
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title"><?php echo $pdata['page']['pname']; ?>s Data</h3>
				</div>
				<div class="box-body">
					<table class="table table-bordered table-striped">
						<thead>
							<?php echo $pdata['data']['th']; ?>
						</thead>
						<tbody>
							<?php echo $pdata['data']['td']; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
// Simple table enhancement without external dependencies
document.addEventListener('DOMContentLoaded', function() {
    // Add mobile data labels for responsive tables
    const table = document.querySelector('.table');
    if (table && window.innerWidth <= 480) {
        const headers = table.querySelectorAll('th');
        const rows = table.querySelectorAll('tbody tr');
        
        headers.forEach((header, index) => {
            rows.forEach(row => {
                const cell = row.children[index];
                if (cell) {
                    cell.setAttribute('data-label', header.textContent.trim());
                }
            });
        });
    }
});
</script>
</body>
</html>
