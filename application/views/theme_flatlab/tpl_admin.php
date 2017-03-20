<?php 
	$last_update = "234325235";
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="<?=META_DESCRIPTION?>">
		<meta name="keyword" content="<?=META_KEYWORDS?>">
		<meta name="robots" content="index,follow">
		<meta name="author" content="Bookdose Co., Ltd.">
		<title><?=@$title?></title>

		<!-- Favicons --> 
		<link rel="shortcut icon" href="<?=CSS_PATH?><?=CONST_CODENAME?>/images/favicons/favicon.ico?v=<?=$last_update?>" type="image/x-icon" />

		<!-- Bootstrap core CSS -->
		<link href="<?=THEME_ADMIN_PATH?>css/bootstrap.min.css" rel="stylesheet">
		<link href="<?=THEME_ADMIN_PATH?>css/bootstrap-reset.css" rel="stylesheet">

		<!--external css-->
		<link href="<?=THEME_ADMIN_PATH?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />

		<link href="<?=THEME_ADMIN_PATH?>css/owl.carousel.css" rel="stylesheet" type="text/css">
		<link href="<?=THEME_ADMIN_PATH?>assets/advanced-datatable/media/css/demo_page.css" rel="stylesheet" />
		<link href="<?=THEME_ADMIN_PATH?>assets/advanced-datatable/media/css/demo_table.css" rel="stylesheet" />
		<link href="<?=THEME_ADMIN_PATH?>assets/data-tables/DT_bootstrap.css" rel="stylesheet" />
		<link href="<?=THEME_ADMIN_PATH?>assets/jquery-easy-pie-chart/jquery.easy-pie-chart.css" rel="stylesheet" type="text/css" media="screen"/>
		<link href="<?=THEME_ADMIN_PATH?>assets/bootstrap-fileupload/bootstrap-fileupload.css" rel="stylesheet" type="text/css" />
		<link href="<?=THEME_ADMIN_PATH?>assets/bootstrap-wysihtml5/bootstrap-wysihtml5.css" rel="stylesheet" type="text/css" />
		<link href="<?=THEME_ADMIN_PATH?>assets/bootstrap-datepicker/css/datepicker.css" rel="stylesheet" type="text/css" />
		<link href="<?=THEME_ADMIN_PATH?>assets/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />


		<!-- Custom styles for this template -->
		<link href="<?=THEME_ADMIN_PATH?>css/style.css?v=<?=$last_update?>" rel="stylesheet">
		<link href="<?=THEME_ADMIN_PATH?>css/style-responsive.css?v=<?=$last_update?>" rel="stylesheet" />
		<link href="<?=THEME_ADMIN_PATH?>css/custom.css?v=<?=$last_update?>" rel="stylesheet">

		<link href="<?=CSS_PATH?>base.css" rel="stylesheet">

		<?php /*
		<script src="<?=THEME_ADMIN_PATH?>js/jquery.js"></script>
		<script src="<?=THEME_ADMIN_PATH?>js/jquery-1.8.3.min.js"></script> 
		<script src="<?=SCRIPT_PATH?>additional/nicedit/nicEdit.js" ></script>
		*/?>
		<script src="<?=THEME_ADMIN_PATH?>js/jquery-3.1.0.min.js"></script>
		<script src="<?=THEME_ADMIN_PATH?>js/bootstrap.min.js"></script>
		<script src="<?=THEME_ADMIN_PATH?>js/jquery.cookie.js"></script>
		<script src="<?=THEME_ADMIN_PATH?>js/jquery-migrate-3.0.0.min.js"></script>
		
		<!-- Your Custom js --> 
		<script src="<?=JS_PATH?>variable.js?v=<?=$last_update?>" type="text/javascript"></script>
		<script src="<?=JS_PATH?>bookdose.js?v=<?=$last_update?>" type="text/javascript"></script>
		<script src="<?=JS_PATH?>common.js?v=<?=$last_update?>" type="text/javascript"></script>

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
		<!--[if lt IE 9]>
		<script src="<?=THEME_ADMIN_PATH?>js/html5shiv.js"></script>
		<script src="<?=THEME_ADMIN_PATH?>js/respond.min.js"></script>
		<![endif]-->
	</head>

	<body>

		<section id="container" >
			<!--header start-->
			<?php include_once("include_header.php"); ?>
			<!--header end-->
			<!--sidebar start-->
			<?php include_once("include_menu.php"); ?>
			<!--sidebar end-->
			<!--main content start-->
			<section id="main-content">
				<section class="wrapper">
					<?php
						include(get_content_file(@$view_the_content));
					?>
				</section>
			</section>
			<!--main content end-->
			<!--footer start-->
			<?php include_once("include_footer.php"); ?>
			<!--footer end-->
		</section>

		<!-- js placed at the end of the document so the pages load faster -->
		<script src="<?=THEME_ADMIN_PATH?>js/jquery.dcjqaccordion.2.7.js" type="text/javascript" class="include"></script>
		<script src="<?=THEME_ADMIN_PATH?>js/jquery.scrollTo.min.js"></script>
		<script src="<?=THEME_ADMIN_PATH?>js/jquery.nicescroll.js" type="text/javascript"></script>
		<script src="<?=THEME_ADMIN_PATH?>js/owl.carousel.js" ></script>
		<script src="<?=THEME_ADMIN_PATH?>js/jquery.customSelect.min.js" ></script>
		<script src="<?=THEME_ADMIN_PATH?>js/jquery.validate.min.js" type="text/javascript"></script>
		<script src="<?=THEME_ADMIN_PATH?>js/jquery.validate.additional.js" type="text/javascript"></script>
		<script src="<?=THEME_ADMIN_PATH?>js/respond.min.js" ></script>

		<script src="<?=THEME_ADMIN_PATH?>assets/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>

		<!--common script for all pages-->
		<script src="<?=THEME_ADMIN_PATH?>js/common-scripts.js"></script>

  <script type="text/javascript">

      //custom select box

      $(function(){
          $('select.styled').customSelect();

          // Advanced Search Panel
          if ($('#adv-area').length > 0) {
            $('#adv-area').siblings('.panel-heading').mouseenter(function(e) {
              $('#adv-area').siblings('.panel-heading').css('cursor', 'pointer');
            });

            $('#adv-area').siblings('.panel-heading').click(function(e) {
              e.preventDefault();
              e.stopImmediatePropagation();
              if(!$('#adv-area').is(':visible')) {
                $('#adv-icon').addClass('fa-chevron-down');
                $('#adv-icon').removeClass('fa-chevron-up');
                $('#adv-area').css('display', 'block');
              } else {
                $('#adv-icon').removeClass('fa-chevron-down');
                $('#adv-icon').addClass('fa-chevron-up');
                $('#adv-area').css('display', 'none');
              }
            });
          }
      });

  </script>
  
    <!-- ui-dialog -->
    <div class="modal fade" id="dialog_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="modal-header"><!-- Title goes here...--></h4>
          </div>
          <div class="modal-body" id="modal-msg">
            <!-- Body goes here...-->
          </div>
          <div class="modal-footer" id="modal-button">
            <!-- Button goes here...-->
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
