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
				<div class="col-xs-4 mb20">
					<a href="<?=site_url('library-today')?>"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/1.jpg" /></a>
				</div>

				<div class="col-xs-4 mb20">
					<a href="<?=site_url('list-book/category/ebooks')?>"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/2.jpg" /></a>
				</div>

				<div class="col-xs-4 mb20">
					<a href="<?=site_url('list-book/category/book')?>"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/3.jpg" /></a>
				</div>

				<div class="col-xs-4 mb20">
					<a href="<?=site_url('all-categories')?>"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/4.jpg" /></a>
				</div>

				<div class="col-xs-4 mb20">
					<a href="<?=site_url('list-magazine/category/emagazines')?>"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/5.jpg" /></a>
				</div>

				<div class="col-xs-4 mb20">
					<a href="<?=site_url('list-book/category/data-subscription')?>"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/6.jpg" /></a>
				</div>

				<div class="col-xs-4 mb20">
					<a href="<?=site_url('ask-librarian')?>"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/7.jpg" /></a>
				</div>

				<div class="col-xs-4 mb20">
					<a href="<?=site_url('list-book/category/pttep-publications')?>"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/8.jpg" /></a>
				</div>

				<div class="col-xs-4 mb20">
					<a href="<?=site_url('list-vdo/category/multimedia')?>"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/9.jpg" /></a>
				</div>

			</div>
		</div>
		<!-- call to action -->


	</div>
</section>
