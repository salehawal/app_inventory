<section class="content-header">
	<div class="row">
		<div class="col-xs-2" style="text-align:center;">
			<a href="#">
				<img src="img/mlogo.png" width="150" height="150" border="0" />
			</a>
			<br>
			<h4 class="h4">inventory app</h4>
			<h4>location <?php echo $pdata['loc']['flo_address']; ?></h4>
			<h4><b>user:</b> <?php echo $_SESSION['user']['data']['fu_username']; ?></h4>
		</div>
		<div class="col-xs-10">
			<div class="row btn-nav-top">
				<div class="col-xs-3">
					<button type="button" class="btn btn-default tmenu" onclick="go_to_page('find.php');">find</button>
				</div>
				<div class="col-xs-3">
					<button type="button" class="btn btn-default tmenu" onclick="go_to_page('main.php');">menu</button>
				</div>
				<div class="col-xs-3">
					<button type="button" class="btn btn-default tmenu" onclick="go_to_page('location.php');">location</button>
				</div>
				<div class="col-xs-3">
					<button type="button" class="btn btn btn-default tmenu" onclick="go_to_page('login.php');">logout</button>
				</div>
			</div>
		</div>
	</div>
</section>
<?php if($pdata['showmenu']) { ?>
<div style="padding-bottom:40px;"></div>
<div class="row btn-nav-menu">
	<div class="col-xs-12 list-btn">
		<button type="button" class="btn btn-default" style="font-size: 45px;" onclick="go_to_page('list_item.php?section=<?php echo $pdata['page']['section']; ?>');"><?php echo $pdata['page']['pname']; ?></button>
	</div>
</div>
<div style="padding-bottom:40px;"></div>
<?php } ?>