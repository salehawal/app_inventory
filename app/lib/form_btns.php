<div class="row btn-nav-form">
	<div class="col-xs-6">
		<button type="button" class="btn btn-default" onclick="go_to_page('list_item.php?section=<?php echo $pdata['page']['section']; ?>');" style="width: 100%;">
			← Back to List
		</button>
	</div>
	<div class="col-xs-6">
		<button id="btn-ok" type="button" class="btn btn-primary" onclick="submit_form();" style="width: 100%;">
			<?php echo isset($_GET['iid']) ? 'Update Item' : 'Save Item'; ?>
		</button>
	</div>
</div>