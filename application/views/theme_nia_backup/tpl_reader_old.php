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

	<!-- Favicons --> 
	<link rel="shortcut icon" href="<?=CSS_PATH?><?=CONST_CODENAME?>/images/favicons/favicon.ico" />
	<link rel="icon" type="image/png" HREF="<?=CSS_PATH?><?=CONST_CODENAME?>/images/favicons/favicon.png"/>
	<link rel="apple-touch-icon" HREF="<?=CSS_PATH?><?=CONST_CODENAME?>/images/favicons/apple-touch-icon.png" />
	
	<!-- iOS webapp icons -->
	<link rel="apple-touch-icon-precomposed" sizes="48x48" href="<?=CSS_PATH?><?=CONST_CODENAME?>/images/favicons/apple-touch-icon-48.png" />
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?=CSS_PATH?><?=CONST_CODENAME?>/images/favicons/apple-touch-icon-72x72.png" />
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?=CSS_PATH?><?=CONST_CODENAME?>/images/favicons/apple-touch-icon-114x114.png" />
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?=CSS_PATH?><?=CONST_CODENAME?>/images/favicons/apple-touch-icon-144x144.png" />
	
	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->	
		
	<!-- We need to emulate IE7 only when we are to use excanvas -->
	<!--[if IE]>
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<![endif]-->

    <?
    $num_of_images = 0;
    $file_path = @$file_path;
    // echo "file_path = $file_path <BR />";
    $full_file_path = '/'.$file_path;
    $images = glob($file_path."/*-thumb.jpg");
    // print_r($images);
    $num_of_images = count($images);
    // echo "num_of_images = $num_of_images <BR />";

    $first_image = $file_path.'/1-large.jpg';
	// echo "first_image = $first_image <BR />";
    if(is_file($first_image)){
    	list($width, $height, $type, $attr) = getimagesize($first_image);
    	// echo "width = $width , height = $height <BR />";
    	if($width == $height){
    		// echo "square";
    		$css_file = "tarnWH.css";
    	}else if($width < $height){
    		// echo "Portait";
    		$css_file = "tarn.css";
    	}else{
    		// echo "Landscape";
    		$css_file = "tarnWup.css";
    	}
    	// echo "css = $css_file <BR />";
    }
    ?>

	<!-- Owl Carousel Assets -->
	<link type="text/css" href="<?=INCLUDE_PATH?>reader/css/owl.carousel.css" rel="stylesheet" /> 
	<link type="text/css" href="<?=INCLUDE_PATH?>reader/css/owl.theme.css" rel="stylesheet" />
	<link type="text/css" href="<?=INCLUDE_PATH?>reader/css/prettify.css" rel="stylesheet" />
	<script  type="text/javascript"  src="<?=INCLUDE_PATH?>reader/js/jquery-1.9.1.min.js"></script> 
	<script type="text/javascript"  src="<?=INCLUDE_PATH?>reader/js/owl.carousel.js"></script>
	<link type="text/css" rel="stylesheet" href="<?=INCLUDE_PATH?>reader/css/style.css" />
	<script type="text/javascript" src="<?=INCLUDE_PATH?>reader/js/script.js"></script>

	<link type="text/css" rel="stylesheet" href="<?=INCLUDE_PATH?>reader/css/<?=$css_file?>" />
	<script type="text/javascript" src="<?=INCLUDE_PATH?>reader/js/turn.min.js"></script>
	<script type="text/javascript" src="<?=INCLUDE_PATH?>reader/js/turn.js"></script>


<body>
<div class="bigBox">
	<div class="boxName">Bookdose Page Flips</div>
        <div class="boxMenu">
    		<a href="https://www.facebook.com/sharer/sharer.php?u=http://www.e-bookstudio.com/bookdosepageflipsNew/index.php" target="_blank"><li class="icon share-facebook"></li></a>
            <a href="https://twitter.com/share?url=http://www.e-bookstudio.com/bookdosepageflipsNew/index.php" target="_blank"><li class="icon share-twitter"></li></a>
            <a href="https://plus.google.com/share?url=http://www.e-bookstudio.com/bookdosepageflipsNew/index.php" target="_blank"><li class="icon share-google"></li></a>
            <a href="#" data-rel="popup" class="topopup1"><li class="icon share-email"></li></a>
            <!--<li class="icon zoom-in"></li>
            <li class="icon zoom-out"></li>-->
            <a href="#" data-role="popup" class="topopup"><li class="icon print"></li></a>
            <a href="BookdosePageFlips.pdf" download="BookdosePageFlips.pdf" ><li class="icon pdf"></li></a>
         </div>


    <div class="boxBook">
    	<div id="book" ><div class="cover" ><img  src="<?=$full_file_path?>/1-large.jpg" style="width:100%;height:100%" class="page-1" id="imageFullScreen"  /></div></div>

    </div>
    <div class="boxGoto">
    	<div id="controls" >
        	<label for="page-number">Page:</label> <input type="text" size="2" id="page-number"> of <span id="number-pages"></span>
		</div>
	</div>


    <div class="boxPage">
 
    	<div id="owl-demo" class="owl-carousel">
			<? $i=1;
            if($num_of_images % 2 == 0)
            {
                while($i <= $num_of_images){
                    if($i == 1)
                    {?>
    
                            <a class="item"><img src="<?=$full_file_path?>/<?=$i?>-thumb.jpg" class="page-<?=$i?>"></a>
    
    
                        <? $i++; }
                    else if($i<$num_of_images)
                    {?>
    
                        <a class="item"><img src="<?=$full_file_path?>/<?=$i?>-thumb.jpg"  class="page-<?=$i?>"></a>
                        <a class="item"><img src="<?=$full_file_path?>/<?=$i+1?>-thumb.jpg"  class="page-<?=$i+1?>"></a>
    
    
                        <? $i = $i+2; }
                    else{?>
    
                            <a class="item"><img src="<?=$full_file_path?>/<?=$i?>-thumb.jpg"  class="page-<?=$i?>"></a>
    
    
                        <? $i++; }
    
                }
            }
            else{
                while($i <= $num_of_images){
                    if($i == 1)
                    {?>
    
                            <a class="item"><img src="<?=$full_file_path?>/<?=$i?>-thumb.jpg"  class="page-<?=$i?>"></a>
    
    
                        <? $i++; }
                    else if($i<$num_of_images)
                    {?>
    
                            <a class="item"><img src="<?=$full_file_path?>/<?=$i?>-thumb.jpg"  class="page-<?=$i?>"></a>
                            <a class="item"><img src="<?=$full_file_path?>/<?=$i+1?>-thumb.jpg"  class="page-<?=$i+1?>"></a>
    
    
                        <? $i = $i+2; }
    
                }
            }
    
            ?>
		</div>
        
    </div> 
 </div>	

<style type="text/css">
    #owl-demo .item{
        display: block;
        cursor: pointer;
        
        padding: 10px 0px;
        margin: 5px;
        color: #FFF;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        text-align: center;
        -webkit-tap-highlight-color: rgba(255, 255, 255, 0); 
    }
    #owl-demo .item:hover{
		
    }
</style>

<!--<div class="zoom" >
	<div id="book" >
    	<div class="cover"><img src="pages/1-large.jpg" width="50%" height="50%" /></div>
    </div>
</div>-->
<script type="text/javascript">


	var full_file_path = '<?=$full_file_path?>';
// Sample using dynamic pages with turn.js

	var numberOfPages = <?=$num_of_images?>;
/*	var sync1 = $("#book");
    var sync2 = $("#owl-demo");*/
	

// Adds the pages that the Book will need
function addPage(page, book) {
// First check if the page is already in the book
		if (!book.turn('hasPage', page)) {
		// Create an element for this page
			var element = $('<div />', {'class': 'page '+((page%2==0) ? 'odd' : 'even'), 'id': 'page-'+page}).html('<i class="loader"></i>');
		// If not then add the page
			book.turn('addPage', element, page);
		// Let's assum that the data is comming from the server and the request takes 1s.
		setTimeout(function(){
			element.html('<div class="data" ><img src="'+full_file_path+'/'+page+'-large.jpg"  class="page-'+page+'" style="width:100%;height:100%" id="imageFullScreen" /></div>');
			}, 100);
		}
	}

	$(window).ready(function(){
		/*$(".zoom").hide();*/
		$('#book').turn({acceleration: true,
		pages: numberOfPages,
		elevation: 50,
		gradients: !$.isTouch,
		when: {
		turning: function(e, page, view) {

// Gets the range of pages that the book needs right now
		var range = $(this).turn('range', page);

// Check if each page is within the book
		for (page = range[0]; page<=range[1]; page++)
		addPage(page, $(this));

		},

		turned: function(e, page) {
		$('#page-number').val(page);
		
	}
	}
		
	
});
		
		$('#number-pages').html(numberOfPages);
		
		$('#page-number').keydown(function(e){
		
		if (e.keyCode==13)
		$('#book').turn('page', $('#page-number').val());
		
		});
		
	});
		
		$(window).bind('keydown', function(e){
		
		if (e.target && e.target.tagName.toLowerCase()!='input')
		if (e.keyCode==37)
		$('#book').turn('previous');
		else if (e.keyCode==39)
		$('#book').turn('next');
		
		});
		
		/*$("#book").turn("zoom", 0.5);*/
		$("#owl-demo").owlCarousel({
                items : 15,
                //itemsTablet : [768, 4],
                
				itemsDesktopSmall     : [1000,8],
				itemsDesktop      : [1400,9],
        		itemsTablet       : [768,7],
        		itemsMobile       : [479,5],
        		
				paginationNumbers : true

            });
			
	$('.boxPage').click(function(event) {
		
		

		if (event.target && (page=/page-([0-9]+)/.exec($(event.target).attr('class'))) ) {
		
			$('#book').turn('page', page[1]);
			//alert(turn('page', page[1]));
			
		}
	});
	
	$("body").on("dblclick", ".myzoom", function(){
		$(".myzoom").remove();
		$(".boxBook").fadeIn();
	})
	
	 $(".boxBook").dblclick(function(event){
		 $(".boxBook").hide();
		 
		
		
		var screenWidth = $(document).width();
		var screenHeight = $(document).height();
		//$('<div class="myzoom" style="position:fixed; top:0; margin:0 auto; z-index; width:'+screenWidth+'px; height:'+screenHeight+'px; background-color:#0F9;"><div id="book" style="margin:0"><div class="cover" ><img src="pages/1-large.jpg" style="width:100%;height:100%" /></div></div></div>').appendTo(".boxPage");
		if (event.target && (page=/page-([0-9]+)/.exec($(event.target).attr('class'))) ) {
		$('<div class="myzoom" style="background-color:#909090;height:'+screenHeight+'px; width:'+screenWidth+'px;position:absolute;top:0;margin:0;"><div id="book" style=" position:absolute;top:0;height:'+screenHeight+'px; width:'+screenWidth+'px; margin:0;"><img src="'+full_file_path+'/'+page[1]+'-large.jpg" style="width:90%;height:90%;margin-left:50px;margin-top:50px;"  /></div></div>').appendTo("body");
		
		
		}
		
		
	  });
	  
	  
		
</script>

<style>
.test{
	background-color:#909090;
	position:relative;
}
</style>
<div id="toPopup" data-role="popup"> 
    	
        <div class="close"></div>
       	<span class="ecs_tooltip">Press Esc to close <span class="arrow"></span></span>
		<div id="popup_content" style="overflow-y: scroll;"> <!--your content start-->
        <form action="print.php" method="post" name="form1" >
        <?php 
			for($print=0;$print<$num_of_images;$print++){
				if(($print%5)==0){
					echo "<br/><br/>";
				}	
				$num=$print+1;	
		?>
            <input id="check" type="checkbox" name="print[]" value="<?=$num?>"><img src="<?=$full_file_path?>/<?=$num?>-thumb.jpg"  style="padding-right:18px" /></input>
          <?php 
			}
		?> 
         
            <p><!--<input type="button" value="Print" onclick="printDiv("divprint")" />-->
            <button type="submit" id="submit">Print</button></p>
            </form>
            
            <p><button name="checkall" id="checkall">Check All</button><button type="button" id="uncheckall">Uncheck All</button>
            
        </div> <!--your content end-->
    
    </div> <!--toPopup end-->
    
	<div class="loader"></div>
   	<div id="backgroundPopup"></div>

    <div id="toPopup1" data-role="popup"> 
    	
        <div class="close1"></div>
       	<span class="ecs_tooltip1">Press Esc to close <span class="arrow1"></span></span>
		<div id="popup_content1"> <!--your content start-->
        <form action="sendEmail.php" method="post"  name="f1">
        	<table width="500" border="0" cellpadding="0" cellspacing="10">
			 <tr>
                <td align="right">E-mail</td>
                <td align="left"><input name="txt_email"  id="txt_email" type="text" size="40" /></td>
              </tr>
              <tr>
                <td align="right">รายละเอียด</td>
                <td align="left"><textarea name="area_detail" id="area_detail" cols="40" rows="5"></textarea></td>
              </tr>
              <tr>
                <td align="right">&nbsp;</td>
                <td align="left">
                	<input name="submit" id="submit" type="submit" value="ส่ง" style="width:80px; height:30px;" />
                    <input name="reset" type="reset" value="ล้างหน้าจอ" style="width:80px; height:30px;" />
                 </td>
              </tr>
            </table>
</form>
       
            
        </div> <!--your content end-->
    
    </div> <!--toPopup end-->
    
	<div class="loader1"></div>
   	<div id="backgroundPopup1"></div>


</body>
<script type="text/javascript">
$(function(){
	
 
	$('#checkall').click(function() {
	        $('input:checkbox').each(function(index) {
	            $(this).attr('checked', true);
	        });
	    });
	     
	    $('#uncheckall').click(function() {
	        $('input:checkbox').each(function(index) {
	            $(this).attr('checked', false);
	        });
	    });
 
});

</script>
<script type="text/javascript">  
function printDiv(divName) {  
     var printContents = document.getElementById(divName).innerHTML;  
     var originalContents = document.body.innerHTML;  
  
     document.body.innerHTML = printContents;  
     window.print();  
  
     document.body.innerHTML = originalContents;  
}  
</script>  

</html>

