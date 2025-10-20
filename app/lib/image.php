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
	$row = $st->fetch(PDO::FETCH_ASSOC);
	if($row && $row['fim_image'])
	{
		header("content-type: image/jpeg");
		echo $row['fim_image'];
	}
	else
	{
		header("HTTP/1.0 404 Not Found");
		echo "Image not found";
	}
}
elseif (isset($_GET['del']))
{
	remove_image($_GET['imgid']);
}
?>