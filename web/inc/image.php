<?php
require_once('core.php');
require_once('app.php');
sys_init();
$conn = db_connect();
user_login_check();

if(isset($_GET['imgid']))
{
	$q 		= "select fim_image from fict_images where fim_code=:image_id";
	$st 	= $conn->prepare($q);
	$st->execute(array(':image_id'=>$_GET['imgid'])) or die ("unable to execute query");
	$st->bindColumn(1, $image, PDO::PARAM_LOB);
	$rs = $st->fetch(PDO::FETCH_BOUND);
	if($rs)
	{
		header("content-type: image");
		echo $image;
	}
}
elseif (isset($_GET['del']))
{
	remove_image($_GET['imgid']);
}
?>