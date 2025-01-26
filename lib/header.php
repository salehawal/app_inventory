<section class="content-header">
	<div class="row">
		<div class="col-lg-6 col-xs-6 logo">
			<img src="img/logo.png" border="0" onclick="go_to_page('main.php');">
		</div>
		<div class="col-lg-6 col-xs-6 mitem">
			<img src="img/find.png" onclick="go_to_page('find.php');">			
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