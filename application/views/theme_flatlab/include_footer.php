<?php
	$filename = 'version.txt';
	$vs = '';
	if (file_exists($filename)) {
		$handle = fopen($filename, "r");
		$vs = fread($handle, filesize($filename));
		fclose($handle);
		$txt_version = "version ".$vs;
	}
?>
<footer class="site-footer">
	<div class="text-center">
		<span style="color:#9f9f9f;"><?=(!empty($vs) ? $txt_version : '')?></span>
		Copyright &copy; Bookdose Co., Ltd. All Rights Reserverd.</a>
		<?php /*
		Powered by &nbsp;<img src="<?=IMAGE_PATH?>mlogo.png" style="vertical-align:middle" /><a href="http://www.momothinks.com" target="_blank"> Momothinks Co., Ltd.</a>
		*/ ?>
		<a href="#" class="go-top">
			<i class="fa fa-angle-up"></i>
		</a>
	</div>
</footer>
