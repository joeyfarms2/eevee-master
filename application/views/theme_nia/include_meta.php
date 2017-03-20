	<?php 
	$last_update = "12332232";
	?>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="<?=META_DESCRIPTION?>">
	<meta name="keyword" content="<?=META_KEYWORDS?>">
	<meta name="robots" content="index,follow">
	<meta name="author" content="Bookdose Co., Ltd.">
	<title><?=@$title?></title>
	
	<!-- Viewport metatags -->
	<meta name="HandheldFriendly" content="true" />
	<meta name="MobileOptimized" content="320" />
	<meta name='viewport' content='width=1280'> 
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- iOS webapp metatags -->
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />

	<!-- Favicons --> 
	<link rel="shortcut icon" href="<?=CSS_PATH?><?=CONST_CODENAME?>/images/favicons/favicon.ico?v=<?=$last_update?>" type="image/x-icon" />
	
	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->	
		
	<!-- We need to emulate IE7 only when we are to use excanvas -->
	<!--[if IE]>
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<![endif]-->

	<?php /*
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	*/ ?>


	<!-- Bootstrap  -->
	<link type="text/css" rel="stylesheet" href="<?=CSS_PATH?><?=CONST_CODENAME?>/bootstrap/css/bootstrap.css?v=<?=$last_update?>">
	<!-- plugin css  -->
	<link rel="stylesheet" type="text/css" href="<?=CSS_PATH?><?=CONST_CODENAME?>/js-plugin/animation-framework/animate.css?v=<?=$last_update?>" />
	<!-- Pop up-->
	<link rel="stylesheet" type="text/css" href="<?=CSS_PATH?><?=CONST_CODENAME?>/js-plugin/magnific-popup/magnific-popup.css?v=<?=$last_update?>" />
	<!-- Flex slider-->
	<link rel="stylesheet" type="text/css" href="<?=CSS_PATH?><?=CONST_CODENAME?>/js-plugin/flexslider/flexslider.css?v=<?=$last_update?>" />
	<link rel="stylesheet" href="<?=CSS_PATH?><?=CONST_CODENAME?>/js-plugin/owl.carousel/owl-carousel/owl.carousel.css?v=<?=$last_update?>">
	<link rel="stylesheet" href="<?=CSS_PATH?><?=CONST_CODENAME?>/js-plugin/owl.carousel/owl-carousel/owl.theme.css?v=<?=$last_update?>">
	<!-- layer slider -->
	<link rel="stylesheet" href="<?=CSS_PATH?><?=CONST_CODENAME?>/js-plugin/layerslider/layerslider/css/layerslider.css?v=<?=$last_update?>" type="text/css">
	<!-- icon fonts -->
	<link type="text/css" rel="stylesheet" href="<?=CSS_PATH?><?=CONST_CODENAME?>/font-icons/custom-icons/css/custom-icons.css?v=<?=$last_update?>">
	<link type="text/css" rel="stylesheet" href="<?=CSS_PATH?><?=CONST_CODENAME?>/font-icons/custom-icons/css/custom-icons-ie7.css?v=<?=$last_update?>">
	<!-- Your Custom Stylesheet --> 
	<link type="text/css" rel="stylesheet" href="<?=CSS_PATH?><?=CONST_CODENAME?>/css/layout.css?v=<?=$last_update?>">
	<?php /*<link type="text/css" id="colors" rel="stylesheet" href="<?=CSS_PATH?><?=CONST_CODENAME?>/css/blue.css">*/ ?>
	<link type="text/css" rel="stylesheet" href="<?=CSS_PATH?>/<?=CONST_CODENAME?>/bootstrap/css/bootstrap-reset.css?v=<?=$last_update?>" />
	<link type="text/css" rel="stylesheet" href="<?=CSS_PATH?>/<?=CONST_CODENAME?>/bootstrap/css/bootstrap-social.css?v=<?=$last_update?>" />
	<link type="text/css" rel="stylesheet" href="<?=CSS_PATH?><?=CONST_CODENAME?>/css/custom.css?v=<?=$last_update?>">
	<link type="text/css" rel="stylesheet" href="<?=CSS_PATH?>base.css?v=<?=$last_update?>" />
	
	<link type="text/css" rel="stylesheet" href="<?=CSS_PATH?><?=CONST_CODENAME?>/css/custom-responsive.css?v=<?=$last_update?>">


	<link href="<?=CSS_PATH?><?=CONST_CODENAME?>/font-icons/font-awesome/css/font-awesome.css?v=<?=$last_update?>" rel="stylesheet" />
	<link href="<?=CSS_PATH?><?=CONST_CODENAME?>/font-icons/Oswald/Oswald.css?v=<?=$last_update?>" rel="stylesheet" />
	<link href="<?=CSS_PATH?><?=CONST_CODENAME?>/font-icons/FF-THSarabunNew/thsarabunnew.css?v=<?=$last_update?>" rel="stylesheet" />
	<link href="<?=CSS_PATH?><?=CONST_CODENAME?>/font-icons/FF-ThaiSans Neue v1.0/thaisansneue.css?v=<?=$last_update?>" rel="stylesheet" />

	<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script> <![endif]-->
	<script src="<?=CSS_PATH?><?=CONST_CODENAME?>/js/modernizr-2.6.1.min.js?v=<?=$last_update?>"></script>

	<!-- Required JavaScript Files -->
	<script type="text/javascript" src="<?=CSS_PATH?><?=CONST_CODENAME?>/js-plugin/respond/respond.min.js?v=<?=$last_update?>"></script>
	<script type="text/javascript" src="<?=CSS_PATH?><?=CONST_CODENAME?>/js-plugin/jquery/1.8.3/jquery.min.js?v=<?=$last_update?>"></script>
    <script src="<?=CSS_PATH?><?=CONST_CODENAME?>/bootstrap/js/bootstrap.min.js?v=<?=$last_update?>"></script>

	<!-- Your Custom js --> 
	<script type="text/javascript" src="<?=JS_PATH?>variable.js?v=<?=$last_update?>"></script>
	<script type="text/javascript" src="<?=JS_PATH?>common.js?v=<?=$last_update?>"></script>
	<script type="text/javascript" src="<?=JS_PATH?>bookdose.js?v=<?=$last_update?>"></script>
	<script type="text/javascript" src="<?=JS_PATH?>order/order_front/basket.js?v=<?=$last_update?>"></script>
