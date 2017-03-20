<?php if(is_var_array(@$banner_result)){ ?>
<section id="layer-slider">
	<div id="layerslider-container-fw"> 
		<div id="layerslider" style="width: 100%; height: 400px;" class="mb20">
			<?php 
				foreach ($banner_result as $banner) {
					$img_src = get_image(get_array_value($banner,"cover_image_actual_path",""),"small","off");
					$ref_link = get_array_value($banner,"ref_link",""); 
					$target = get_array_value($banner,"target","");
					 
				?>
				<?php if(!is_blank($img_src)){ 
					
				?>
				<div class="ls-layer" style="slidedirection: right; transition2d: 5; ">
					<?php if(!is_blank($ref_link)){ 
						
					?>
						<a href="<?=$ref_link?>"><img src="<?=$img_src?>" class="ls-bg button" alt="" onclick="processRedirect('<?=$ref_link?>', '<?=$target?>')" /></a>
					<?php }else{ ?>
						<img src="<?=$img_src?>" class="ls-bg" alt="" />
					<?php } ?>
					<!-- <div class="ls-s-1" style="position: absolute; top:130px; left: 715px;padding-top: 5px; padding:30px; color:#FFF; background-color: rgba(0, 0, 0, .5); font-weight:300; slidedirection : top; slideoutdirection : right; durationin : 1000; durationout : 1000; easingin : easeOutElastic; easingout : easeInOutQuint; delayin : 500;">
						<h1><b><span class="textStart">กรม</span><span class="textSub">สรรพสามิต</span></b></h1>
						<p style="font-size:25px;line-height:26px;">
							องค์กรพลวัต เพื่อการจัดเก็บภาษีที่มีมาตรฐานสากล ปกป้องสังคม สิ่งแวดล้อม และพลังงาน
						</p>
					</div> -->
				</div>
				<?php } ?>
			<?php } ?>
		</div>

	</div>
</section>
<?php } ?>
