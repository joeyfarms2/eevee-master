<!doctype html>
<html>
    <head> 
    <title><?=@$title?></title>              
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="initial-scale=1,user-scalable=no,maximum-scale=1,width=device-width" />
        <link href='http://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
       
       	<style type="text/css" media="screen">
        	html, body	{ height:100%; }
        	body { margin:0; padding:0; overflow:auto;}
        	.infoBox > * {font-family:Lato;}
        	#flashContent { display:none; }
        	.flexpaper_bttnPrint { display:none; }
        	.flexpaper_bttnDownload { display:none ;}

    	</style>
    <?php /*
 		<link rel="stylesheet" type="text/css" href="<?=INCLUDE_PATH?>pdf/css/flowpaper.css" />
    	<script type="text/javascript" src="<?=INCLUDE_PATH?>pdf/js/jquery.min.js"></script>
	    <script type="text/javascript" src="<?=INCLUDE_PATH?>pdf/js/jquery.extensions.min.js"></script>
	    <!--[if gte IE 10 | !IE ]><!-->
	    <script type="text/javascript" src="<?=INCLUDE_PATH?>pdf/js/three.min.js"></script>
	    <!--<![endif]-->
	    <script type="text/javascript" src="<?=INCLUDE_PATH?>pdf/js/flowpaper.js"></script>
	    <script type="text/javascript" src="<?=INCLUDE_PATH?>pdf/js/flowpaper_handlers.js"></script>
	 */ ?>
	 	<link rel="stylesheet" type="text/css" href="<?=INCLUDE_PATH?>pdf/css/flexpaper.css" />
    	<script type="text/javascript" src="<?=INCLUDE_PATH?>pdf/js/jquery.min.js"></script>
    	<script type="text/javascript" src="<?=INCLUDE_PATH?>pdf/js/jquery.extensions.min.js"></script>
    	<!--[if gte IE 10 | !IE ]><!-->
    	<script type="text/javascript" src="<?=INCLUDE_PATH?>pdf/js/three.min.js"></script>
    	<!--<![endif]-->
    	<script type="text/javascript" src="<?=INCLUDE_PATH?>pdf/js/flexpaper.js"></script>
    	<script type="text/javascript" src="<?=INCLUDE_PATH?>pdf/js/flexpaper_handlers.js"></script>
    
        <script type="text/javascript">
        	<?php
            	$file_path = "/".$path_upload;
            	$name = base64_encode($file_path);
            ?>
            
            var name = window.atob("<?=$name?>");
            
			$(document).ready(function(){		
    			$(document).bind("contextmenu",function(e){
    				//alert('Not Right Click!!');
            		return false;
				});
			});
        </script>
        
    </head> 
    <body id="body" style="padding:0; margin:0;" >
        <div id="documentViewer" class="flexpaper_viewer" style="position:absolute;width:100%;height:100%"></div>

		<script type="text/javascript">
		var startDocument = "Paper";
            
    $('#documentViewer').FlexPaperViewer(
            { config : {

				PDFFile : name,

                Scale : 0.6,
                ZoomTransition : 'easeOut',
                ZoomTime : 0.5,
                ZoomInterval : 0.1,
                FitPageOnLoad : true,
                FitWidthOnLoad : false,
                FullScreenAsMaxWindow : false,
                ProgressiveLoading : false,
                MinZoomSize : 0.2,
                MaxZoomSize : 5,
                SearchMatchAll : false,
                InitViewMode : '',
                RenderingOrder : 'html5,html',
                StartAtPage : '',

				key : "@e4ee7d16961fd8c89a6$e4e7935f5658eded48b",
				
                EnableWebGL : true,
                ViewModeToolsVisible : true,
                ZoomToolsVisible : false,
                NavToolsVisible : true,
                CursorToolsVisible : true,
                SearchToolsVisible : true,
                PrintPaperAsBitmap      : false,
                WMode : 'transparent',
                localeChain: 'en_US'
            }}
    );
</script>

        <script type="text/javascript">
            var url = window.location.href.toString();

            if(location.length==0){
                url = document.URL.toString();
            }

            if(url.indexOf("file:")>=0){
                jQuery('#documentViewer').html("<div style='position:relative;background-color:#ffffff;width:420px;font-family:Verdana;font-size:10pt;left:22%;top:20%;padding: 10px 10px 10px 10px;border-style:solid;border-width:5px;'><img src=''>&nbsp;<b>You are trying to use FlexPaper from a local directory.</b><br/><br/> FlexPaper needs to be copied to a web server before the viewer can display its document properly.<br/><br/>Please copy the FlexPaper files to a web server and access the viewer through a http:// url.</div>");
            }
        </script>

   </body> 
</html> 