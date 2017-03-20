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

    <!-- for Facebook -->
    <meta property="og:title" content="Bookdose page Flips" />
    <meta property="og:type" content="image/jpeg" />
    <meta property="og:image" content="http://www.e-bookstudio.com/bookdosepageflips/pages/1-large.jpg" />
    <meta property="og:url" content="http://www.e-bookstudio.com/bookdosepageflips/index.php" />
    <meta property="og:description" content="ทำ E-Book ด้วยตัวเอง" />
    <!--<meta property="fb:app_id" content="624050914310178" />-->



    <!-- for Twitter -->
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="Bookdose page Flips" />
    <meta name="twitter:description" content="Bookdose page Flips" />
    <meta name="twitter:image" content="http://www.e-bookstudio.com/bookdosepageflips/pages/1-large.jpg" />


	<link type="text/css" href="<?=INCLUDE_PATH?>reader/css/prettify.css" rel="stylesheet" />
	<script  type="text/javascript"  src="<?=INCLUDE_PATH?>reader/js/jquery-1.9.1.min.js"></script> 
	<link type="text/css" rel="stylesheet" href="<?=INCLUDE_PATH?>reader/css/style.css" />
	<script type="text/javascript" src="<?=INCLUDE_PATH?>reader/js/script.js"></script>
	<link type="text/css" rel="stylesheet" href="<?=INCLUDE_PATH?>reader/css/tarn.css" />
	<script type="text/javascript" src="<?=INCLUDE_PATH?>reader/js/turn.js"></script>

	<script type="text/javascript" src="<?=JS_PATH?>common.js"></script>

  <?
		$num_of_images = 0;
		$file_path = @$file_path;
		$dirname = $file_path.'/';
		// echo "dirname = $dirname";
		$images = glob($dirname."*-thumb.jpg");
		$num_of_images = count($images);
		// echo $num_of_images;
		$images1 = glob($dirname."*-large.jpg");
		$n = count($images1);
		if($n==1){
			$size=getimagesize($images1[0]);  // $file คือ ไฟล์ที่เราต้องการดูขนาด
		}else{
			$size=getimagesize($images1[1]);  // $file คือ ไฟล์ที่เราต้องการดูขนาด
		}
		
		$img_w=$size[0];   // ขนาดความกว้าง
		$img_h=$size[1];   // ขนาดความสูง
?> 
<script>
//var currentPage =1;
var zoomLevel;
var mw;
var mh;

var testw, testh;

$(document).ready(function(){
	//$(".showArea").html(currentPage);
	
	var doc_width = $(document).width();
	var doc_height = $(document).height();
	var window_width = $(window).width();
	var window_height = $(window).height();
	//alert(window_width);
	//alert(window_height);
	//	alert(doc_width);
	//	alert(doc_height);
	
	var img_width = parseInt('<?=$img_w?>');
	var img_height = parseInt('<?=$img_h?>');
	
	testw = img_width;
	testh = img_height;
	//alert(img_height);
	if(img_width<img_height || img_width == img_height){
		var h = (window_height-160);
		var w = img_width * (h/img_height);
		var show_w=(w);
		var show_h=(h);
		var show_sum_w=(show_w*2);
		var show_sum_h=(show_h);

		var top =10;
		$(".boxBook").css({"width": show_sum_w+"px","height": show_sum_h+"px","margin-top":top+"px"});
	}else	if(img_width>img_height){
		var w = (window_width-200)/2;
		var h = img_height * (w/img_width);
		var show_w=(w);
		var show_h=(h);
		var show_sum_w=(show_w*2);
		var show_sum_h=(show_h);

		var top =10;
		$(".boxBook").css({"width": show_sum_w+"px","height": show_sum_h+"px","margin-top":top+"px"});
				
	}
	$("#stepzoom").css({"margin-left":"600px"});


	var bigBoxWidth = Math.floor(window_width/2);
	//alert(window_width);
	$(".bigBox").css("width", window_width+"px");
	$(".goToback").click(function(){
		
		$('#book').turn('previous');

	});
	
	$(".goToNext").click(function(){
		
		$('#book').turn('next');
		
		
	});

	
	$("#stepzoom").hide();
	
		
	$("#zoomin").click(function(){
		//var left_zoomin = 250;
		//alert("h = "+mh+" | w = "+mw);
		if(zoomLevel+1 > 25)
		{
			zoomLevel = 25;
			
			return;
		}
		else{
			zoomLevel++;
			//left_zoomin = left_zoomin+ 150;
		}
		//alert("zoomLevel = "+zoomLevel);
		var zoomLevelDefault = 15;
		var mww = mw * (zoomLevel/zoomLevelDefault);
		var mhh = mh * (zoomLevel/zoomLevelDefault);
		//alert("w = "+$(".myzoom").width()+ "h = "+$(".myzoom").height()+"|| zoomIN w = "+mw+"  ,h = "+mh);
		$(".myzoom").animate({'width':mww, 'height':mhh });
		$(".myzoom img").animate({'width':mww, 'height':mhh});
	});
	$("#zoomout").click(function(){
		//var left_zoomout = 250;
		if(zoomLevel-1 < 12)
		{
			zoomLevel = 12;
			
			return;
		}
		else{
			zoomLevel--;
			//left_zoomout = left_zoomout - 150;
		}
		//alert("zoomLevel = "+zoomLevel);
		var zoomLevelDefault = 15;
		var mww = mw * (zoomLevel/zoomLevelDefault);
		var mhh = mh * (zoomLevel/zoomLevelDefault);
		//alert("w = "+$(".myzoom").width()+ "h = "+$(".myzoom").height()+"|| zoomIN w = "+mw+"  ,h = "+mh);
		$(".myzoom").animate({'width':mww, 'height':mhh});
		$(".myzoom img").animate({'width':mww, 'height':mhh});
	});
	
	
	
});
</script>
<script language="javaScript"> 


</script> 

<title>Bookdose Page Flips</title>
 
</head>

<body>
<div class="bigBox" >
        

    <!--<div class="boxBook" style="width:<?//=$show_sum_w?>px;height:<?//=$show_sum_h?>px;margin-top:<?//=$top?>px">-->
    <div class="boxBook">
    	<div id="book" >
        	<div class="cover" >
            	<img  src="/<?=$dirname.get_text_pad('1', '0', 4).'-large.jpg'?>" style="width:100%;height:100%" class="page-1" id="imageFullScreen" title="double click to zoom"  />
            </div>
         </div>
	</div>
    <div class="boxMenu">
    		<a href="#" class="goToback"><li class="icon back"></li></a>
    		<!--<a href="https://www.facebook.com/sharer/sharer.php?u=http://www.e-bookstudio.com/bookdosepageflips/index.php" target="_blank"><li class="icon share-facebook"></li></a>-->
            <!--<a href="https://twitter.com/share?url=http://www.e-bookstudio.com/bookdosepageflips/index.php" target="_blank"><li class="icon share-twitter"></li></a>-->
            <!--<a href="https://plus.google.com/share?url=http://www.e-bookstudio.com/bookdosepageflips/index.php" target="_blank"><li class="icon share-google"></li></a>-->
            <a href="#" data-rel="popup" class="topopup2"><li class="icon index"></li></a>
            <!--<a href="#" data-rel="popup" class="topopup1"><li class="icon share-email"></li></a>-->
            <!--<li class="icon zoom-in"></li>
            <li class="icon zoom-out"></li>-->
            <!--<a href="#" data-role="popup" class="topopup"><li class="icon print"></li></a>-->
            <!--<a href="BookdosePageFlips.pdf" download="BookdosePageFlips.pdf" ><li class="icon pdf"></li></a>-->
            <a href="#" class="goToNext"><li class="icon next"></li></a>
            <!--<li class="showArea"></li>-->
         </div>


</div> 	

<div id="stepzoom" >
<button id="zoomin"><img width="100%" height="100%" src="<?=INCLUDE_PATH?>reader/images/zoomIn.png" /></button>
<button id="zoomout"><img width="100%" height="100%" src="<?=INCLUDE_PATH?>reader/images/ZoomOut.png" /></button>
<button class="BackPage"><img width="100%" height="100%" src="<?=INCLUDE_PATH?>reader/images/back.png" /></button>
<button class="NextPage"><img width="100%" height="100%" src="<?=INCLUDE_PATH?>reader/images/next.png" /></button>
<button class="backhome"><img width="100%" height="100%" src="<?=INCLUDE_PATH?>reader/images/index.png" /></button>
</div>

<script type="text/javascript">



// Sample using dynamic pages with turn.js

	var numberOfPages = <?=$num_of_images ?>;
	//alert(numberOfPages);
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
			element.html('<div class="data" ><img src="/<?=$dirname?>'+padString(page, 4, '0', 'left')+'-large.jpg"  class="page-'+page+'" style="width:100%;height:100%" id="imageFullScreen" title="double click to zoom" /></div>');
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
		//alert(pages);
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
		//alert(numberOfPages);
		$('#page-number').keydown(function(e){
		
		if (e.keyCode==13)
		$('#book').turn('page', $('#page-number').val());
		
		
		
		});
		
	});
		
		$(window).bind('keydown', function(e){
		
		if (e.target && e.target.tagName.toLowerCase()!='input')
		{
			if (e.keyCode==37)
			{
				$('#book').turn('previous');
				
			}
			else if (e.keyCode==39)
			{
				$('#book').turn('next');

			}
		
		
		
		}
		
		
		
		});
		
	$('.boxPage').click(function(event) {
		
		

		if (event.target && (page=/page-([0-9]+)/.exec($(event.target).attr('class'))) ) {
		
			$('#book').turn('page', page[1]);
			//alert(turn('page', page[1]));
			
		}
	});
	
	//show zoomPage
	$("body").on("dblclick", ".myzoom", function(){
		
		$("#stepzoom").hide();
		$(".myzoom").remove();
		$(".bigBox").fadeIn();
		$(".boxBook").fadeIn();
		$(".boxMenu").fadeIn();
	})
	
	 $(".boxBook").dblclick(function(event){
		 zoomLevel = 15;
		 $("#stepzoom").show();
		 $(".bigBox").hide();
		 $(".boxBook").hide();
		 $(".boxMenu").hide();
		 
		
		var screenWidth = $(document).width();
		var screenHeight = $(document).height();
		var screenWidth2 = ($(document).width()/1.5);
		
		
		if (event.target && (page=/page-([0-9]+)/.exec($(event.target).attr('class'))) ) {
		//$('<div class="myzoom" style="background-color:#909090;width:'+screenWidth2+'px;height:auto;position:relative;top:0;margin:auto  auto;"><img src="/<?=$dirname?>/'+padString(page[1], 4, '0', 'left')+'-large.jpg" style="width:'+screenWidth2+'px;height:auto;top:0;"  /></div>').appendTo("body");
		$('<div class="myzoom" style="background-color:#909090;width:'+testw+'px;height:auto;position:relative;top:0;margin:auto  auto;"><img src="/<?=$dirname?>/'+padString(page[1], 4, '0', 'left')+'-large.jpg" style="width:'+testw+'px;height:auto;top:0;"  /></div>').appendTo("body");
		
		//mw = $(".myzoom img").width();
		//mh = $(".myzoom img").height();
		
		mw = testw;
		mh = testh;
		}
	
		
		
	  });
	  
	  $(".BackPage").click(function(event){
		  var numpage = parseInt(page[1]);
		  if(numpage > 1){
		  	var sumpage = numpage - 1;
		  }else{
			var sumpage = numpage;  
		  }
		
		zoomLevel = 15;
		$('#book').turn('page',sumpage);
		//alert(numpage);
		//alert(sumpage);
		
		var screenWidth = $(document).width();
		var screenHeight = $(document).height();
		var screenWidth2 = ($(document).width()/1.5);
		
		$('.myzoom').remove();
		//$('.myzoom').turn($(this),sumpage);
		$('<div class="myzoom" style="background-color:#909090;width:'+screenWidth2+'px;position:relative;top:0;margin:auto auto;"><img src="/<?=$dirname?>'+padString(sumpage, 4, '0', 'left')+'-large.jpg" style="width:'+screenWidth2+'px;top:0;"  /></div>').appendTo("body");
		
		mw = $(".myzoom").width();
		mh = $(".myzoom").height();
		
		page[1] = sumpage;
			
	});
	
	$(".NextPage").click(function(event){
		
		  var numpage = parseInt(page[1]);
		  var sumnum = numberOfPages;
		  if(sumnum > numpage){
			//alert(numberOfPages);
		  	var sumpage = numpage + 1;
		  }else{
			var sumpage = numpage;  
		  }
		  
		
		zoomLevel = 15;
		$('#book').turn('page',sumpage);
		//alert(numpage);
		//alert(sumpage);
		var screenWidth = $(document).width();
		var screenHeight = $(document).height();
		var screenWidth2 = ($(document).width()/1.5);
		
		//$('.myzoom').turn($(this),sumpage);
		$('.myzoom').remove();
		$('<div class="myzoom" style="background-color:#909090;width:'+screenWidth2+'px;position:relative;top:0;margin:auto auto;"><img src="/<?=$dirname?>'+padString(sumpage, 4, '0', 'left')+'-large.jpg" style="width:'+screenWidth2+'px;top:0;"  /></div>').appendTo("body");
		
		mw = $(".myzoom").width();
		mh = $(".myzoom").height();
		
		page[1] = sumpage;
		
	});
	//$(".myzoom img").scroll(function(){
//		alert("ABCD");
//	});	
	 $(".backhome").click(function(event){
		$("#stepzoom").hide();
		$(".myzoom").remove();
		$(".bigBox").fadeIn();
		$(".boxBook").fadeIn();
		$(".boxMenu").fadeIn();
		
	});

$( ".myzoom" ).mousemove(function( event ) {
  var msg = "Handler for .mousemove() called at ";
  msg += event.pageX + ", " + event.pageY;
  
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
		<div id="popup_content" style="overflow-y: scroll;height:450px;"> <!--your content start-->
        <form action="print.php" method="post" name="form1" >
        <?php 
			for($print=0;$print<$num_of_images;$print++){
				if(($print%5)==0){
					echo "<br/><br/>";
				}	
				$num=$print+1;	
		?>
            <input id="check" type="checkbox" name="print[]" value="<?=$num?>"><img src="/<?=$dirname?><?=$num?>-thumb.jpg"  style="padding-right:18px" /></input>
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
                <td align="right">จาก E-MAIL</td>
                <td align="left"><input name="form_email"  id="form_email" type="text" size="40" /></td>
              </tr>
			 <tr>
                <td align="right">ถึง E-MAIL</td>
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
    
<div id="toPopup2" data-role="popup"> 
    	
        <div class="close2"></div>
       	<span class="ecs_tooltip2">Press Esc to close <span class="arrow2"></span></span>
		<div id="popup_content2" style="overflow-y:scroll;height:450px; margin:20px 30px auto 70px"> <!--your content start-->
        
        	
        <?php 
			for($menu=0;$menu<$num_of_images;$menu++){
				if(($menu%7)==0){
					
				}	
				$num2=$menu+1;	
				//echo $num2;
		?>
            	<a class="item" id="page-number" onclick="changePage('<?=$num2?>')"><img src="/<?=$dirname?><?=get_text_pad($num2, '0', 4)?>-thumb.jpg"  id="page-number"  /></a>&nbsp;&nbsp;
          <?php 
		  
			}
			echo "<br/><br/>";
		?> 
        			
        		
        	</div> <!--your content end-->
    	</div> <!--toPopup end-->
    
	<div class="loader2"></div>
   	<div id="backgroundPopup2"></div>
    

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

function changePage(page){
	$('#book').turn('page', page);
	
	
}
</script>  

</html>

