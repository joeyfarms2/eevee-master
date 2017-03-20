<script type="text/javascript">
	jQuery(document).ready(function($){
		<?=@$message?>
		<?=@$js_code?>
	});
</script>

<section id="message-box">
	<div class="container">
		<div id="result-msg-box" class="hidden" ></div>
	</div>
</section>

<section id="projects">
	<div class="container mt15 book-box">

		<!-- call to action -->
		<div class="mt10 mb10">
			<div class="row">
				<div class="col-xs-4  mb20">
					<a href="<?=site_url('list-book/category/ebooks')?>"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/1.png" /></a>
				</div>

				<div class="col-xs-4 mb20">
					<a href="<?=site_url('list-magazine/category/emagazines')?>"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/2.png" /></a>
				</div>

				<div class="col-xs-4  mb20">
					<a href="<?=site_url('list-book/category/information')?>"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/3.png" /></a>
				</div>

				<div class="col-xs-4  mb20">
					<a href="<?=site_url('list-book/category/knowledge-resources')?>"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/4.png" /></a>
				</div>

				<div class="col-xs-4  mb20">
					<a href="<?=site_url('list-vdo/category/multimedia')?>"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/5.png" /></a>
				</div>

				<div class="col-xs-4  mb20">
					<a href="<?=site_url('news')?>"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/6.png" /></a>
				</div>



			</div>
		</div>
		<!-- call to action -->


	</div>
</section>
