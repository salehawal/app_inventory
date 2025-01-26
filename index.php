<?php 
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
require('login.php'); 
?>
<?php if($page='barcode') : ?>
	<p><img width="120" height="120" src="lib/php/qrcode.php?s=qrl&d=8675309"></p>
<?php endif; ?>
