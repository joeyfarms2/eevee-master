<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="en">
<!--<![endif]-->
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="<?=META_DESCRIPTION?>">
	<meta name="keyword" content="<?=META_KEYWORDS?>">
	<meta name="robots" content="index,follow">
	<meta name="author" content="Bookdose Co., Ltd.">
	<title><?=@$title?></title>
	
	<!-- Viewport metatags -->
	<meta name="HandheldFriendly" content="true" />
	<meta name="MobileOptimized" content="320" />
	<?php /** <meta name="viewport" content="width=device-width"> **/ ?>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!-- iOS webapp metatags -->
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<script type="text/javascript" src="<?=INCLUDE_PATH?>pdf/js/jquery.min.js"></script>
	<!-- Favicons --> 
	<link rel="shortcut icon" href="<?=CSS_PATH?><?=CONST_CODENAME?>/images/favicons/favicon.ico" />
	<!-- <link rel="icon" type="image/png" HREF="<?=CSS_PATH?><?=CONST_CODENAME?>/images/favicons/favicon.png"/>
	<link rel="apple-touch-icon" HREF="<?=CSS_PATH?><?=CONST_CODENAME?>/images/favicons/apple-touch-icon.png" />
	 -->
	<!-- iOS webapp icons -->
	<!-- <link rel="apple-touch-icon-precomposed" sizes="48x48" href="<?=CSS_PATH?><?=CONST_CODENAME?>/images/favicons/apple-touch-icon-48.png" />
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?=CSS_PATH?><?=CONST_CODENAME?>/images/favicons/apple-touch-icon-72x72.png" />
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?=CSS_PATH?><?=CONST_CODENAME?>/images/favicons/apple-touch-icon-114x114.png" />
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?=CSS_PATH?><?=CONST_CODENAME?>/images/favicons/apple-touch-icon-144x144.png" />
	 -->
	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->	
		
	<!-- We need to emulate IE7 only when we are to use excanvas -->
	<!--[if IE]>
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<![endif]-->

	<script type="text/javascript">

	$(document).ready(function(){
		//$(".showArea").html(currentPage);
		
		var doc_width = $(document).width();
		var doc_height = $(document).height();
		var window_width = $(window).width();
		var window_height = $(window).height();
		// alert(window_width);
		// alert(window_height);
		// 	alert(doc_width);
		// 	alert(doc_height);
		$("#reader").css({"height": window_height+"px");
		
		});
	</script>


<body style="padding:0; margin:0;">
<iframe id="reader" name="reader" src="<?=@$src?>" frameborder="0" marginheight="0" marginwidth="0" style="position:absolute;width:100%;height:100%"></iframe>
</body>
</html>

