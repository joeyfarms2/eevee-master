<?php
$this_front_tab_menu = @thisFrontTabMenu;
$this_front_sub_menu = @thisFrontSubMenu;
$master_product_main = @$master_product_main;
$this_product_main_name = @$this_product_main_name;
$page_name = @$page_name;
$banner_result = @$banner_result;

?>

<style>
.mySlides
{
	width: 100%;
}

.slide-animation
{
	animation: fading 3s infinite;
}

.w3-display-container 
{
    position: relative;
    display: block;
}

.w3-display-content
{
	position: relative;
}
.w3-display-left 
{
    position: absolute;
    top: 50%;
    left: 0%;
    transform: translate(0%,-50%);
    -ms-transform: translate(-0%,-50%);
}

.w3-display-right 
{
    position: absolute;
    top: 50%;
    right: 0%;
    transform: translate(0%,-50%);
    -ms-transform: translate(0%,-50%);
}

.w3-btn-floating 
{
	width: 40px;
    height: 40px;
    line-height: 40px;

	display: inline-block;
    text-align: center;
    color: #fff;
    background-color: #000;
    /*position: relative;*/
    overflow: hidden;
    z-index: 1;
    padding: 0;
    border-radius: 50%;
    cursor: pointer;
    font-size: 24px;
}

.w3-hover-dark-grey:hover 
{
	color: #fff !important;
    background-color: #616161!important;
    text-decoration: none;
}
</style>
<script>
var slideIndex = 0;
//showDivs(slideIndex);

var test = setInterval(plusDivs, 6000);

function plusDiv(n) {
	slideIndex += n;
	showDivs(slideIndex);
}

function plusDivs() {
  slideIndex += 1;
  showDivs(slideIndex);
}

function showDivs(n) {
  var i;
  var x = document.getElementsByClassName("mySlides");
  if (n > x.length) {slideIndex = 1}    
  if (n < 1) {slideIndex = x.length}
  for (i = 0; i < x.length; i++) {
     x[i].style.display = "none";  
  }
  x[slideIndex-1].style.display = "block";  
}
</script>

<div class="w3-content w3-display-container">
<?php if(is_var_array($banner_result)){
	?>
	<a class="w3-btn-floating w3-hover-dark-grey w3-display-left" onclick="plusDivs(-1)">&#10094;</a>
	<a class="w3-btn-floating w3-hover-dark-grey w3-display-right" onclick="plusDivs(1)">&#10095;</a>
	<?php
	$i = 0;
	foreach ($banner_result as $banner) 
	{
		$img_src = get_image(get_array_value($banner,"cover_image_actual_path",""),"small","off");
		$ref_link = get_array_value($banner,"ref_link",""); 
		$target = get_array_value($banner,"target","");
					 
		if(!is_blank($img_src))
		{ 
			if(!is_blank($ref_link))
			{ 
			?>
				<div class="w3-display-content"><a href="<?=$ref_link?>"><img src="<?=$img_src?>" class="mySlides slide-animation" style="<?php if($i>0){echo "display:none;";}?>" /></a></div>
			<?php }else{ ?>
				<div class="w3-display-content"><img class="mySlides slide-animation" src="<?=$img_src?>" style="<?php if($i>0){echo "display:none;";}?>" /></div>
			<?php } ?>
		<?php } ?>
	<?php $i++; } ?>
<?php } ?>
</div>
