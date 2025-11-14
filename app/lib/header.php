<section class="content-header">
	<div class="row">
		<div class="col-xs-6 logo">
			<img src="img/logo.png" style="max-height: 40px; cursor: pointer;" onclick="go_to_page('main.php');" alt="Logo">
		</div>
		<div class="col-xs-6 mitem">
			<img src="img/find.png" style="max-height: 40px; cursor: pointer;" onclick="go_to_page('find.php');" alt="Search">
		</div>
	</div>
</section>
<?php if(isset($pdata['showmenu']) && $pdata['showmenu']) { ?>
<div class="row btn-nav-menu" style="margin-bottom: 20px;">
	<div class="col-xs-12">
		<button type="button" class="btn btn-default" onclick="go_to_page('list_item.php?section=<?php echo $pdata['page']['section']; ?>');">
			<?php echo $pdata['page']['pname']; ?>
		</button>
	</div>
</div>
<?php } ?>