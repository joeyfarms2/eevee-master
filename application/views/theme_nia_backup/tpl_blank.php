<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="en">
<!--<![endif]-->
<head>
	<?php include_once("include_meta.php"); ?>

<style type="text/css">
body{background-color:#FFF; margin:0; padding:0; color:#366092} 
td{padding:5px 0;font-size:12px; font-family:Tahoma; color:#366092;} 
.hcenter{text-align:center;}
.hright{text-align:right;}
.hleft{text-align:left;}
.linedown{border-bottom:1px solid #CCC;}
.header{font-size:12px;color:#FFF;background-color:#366092;font-weight:bold;}
.h2{font-size:24px;font-weight:normal;}
.footer{padding:0px}
.bill-header{font-size:12px;font-weight:normal;padding:0px;}
a.button{padding:10px;text-decoration:none;font-weight:bold;border-radius:5px;min-width:200px;}
a.confirm{color:#465821;background-color:#9CC746;}
a.cancel{color:#621C1A;background-color:#CE3B37;}

</style>
</head>

<script type="text/javascript">
	jQuery(document).ready(function() {

	});
</script>

<?php

?>

<body class="header2">
	<!-- globalWrapper -->
	<div id="globalWrapper" class="localscroll">
		<?php include_once("include_header_blank.php"); ?>
		<section id="content">
			<!-- title -->
			<div class="container">

				<div class="row">
					<div class="col-sm-12 custom-content-box">
						<!-- Content -->
						<?php
						include(get_content_file(@$view_the_content));
						?>
						<!-- End : Content -->
					</div>
				</div>
			</div>
		</section>
		<?php include_once("include_footer_blank.php"); ?>
	</div>
</body>
</html>
