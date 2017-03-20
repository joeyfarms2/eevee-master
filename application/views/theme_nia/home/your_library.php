<script type="text/javascript">
	jQuery(document).ready(function($){
		<?=@$message?>
		<?=@$js_code?>
	});
</script>
<style>
.mb20 a img {
    width: 100% !important;
}

@media (min-width:360px) and (max-width:479px)
{
	.mb20 a img {
    	width: 100% !important;
    }
}

@media (min-width:320px) and (max-width:359px)
{
	.mb20 a img {
    	width: 100% !important;
    }
}
</style>
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
				<div class="col-xs-6 col-sm-4 mb20">
					<a href="<?=site_url('list-book/category/ebooks')?>">
						<img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/1.png"  onmouseover="this.src='<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/1_1.png';" onmouseout="this.src='<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/1.png';" />
					</a>
				</div>

				<div class="col-xs-6 col-sm-4 mb20">
					<a href="<?=site_url('list-magazine/category/emagazines')?>">
						<img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/2.png"  onmouseover="this.src='<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/2_1.png';" onmouseout="this.src='<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/2.png';" />
					</a>
				</div>

				<div class="col-xs-6 col-sm-4 mb20">
					<a href="<?=site_url('list-book/category/books')?>">
						<img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/3.png"  onmouseover="this.src='<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/3_1.png';" onmouseout="this.src='<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/3.png';" />
					</a>
				</div>

				<div class="col-xs-6 col-sm-4 mb20">
					<a href="<?=site_url('list-book/category/information')?>">
						<img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/4.png"  onmouseover="this.src='<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/4_1.png';" onmouseout="this.src='<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/4.png';" />
					</a>
				</div>

				<div class="col-xs-6 col-sm-4 mb20">
					<a href="<?=site_url('list-vdo/category/multimedia')?>">
						<img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/5.png"  onmouseover="this.src='<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/5_1.png';" onmouseout="this.src='<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/5.png';" />
					</a>
				</div>

				<div class="col-xs-6 col-sm-4 mb20">
					<a href="<?=site_url('list-book/category/knowledge-resources')?>">
						<img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/6.png"  onmouseover="this.src='<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/6_1.png';" onmouseout="this.src='<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/6.png';" />
					</a>
				</div>

				<div class="col-xs-6 col-sm-4 mb20">
					<a href="<?=site_url('list-book/category/cd-dvd')?>">
						<img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/7.png"  onmouseover="this.src='<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/7_1.png';" onmouseout="this.src='<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/your-library/7.png';" />
					</a>
				</div>


				

			</div>
		</div>
		<!-- call to action -->


	</div>
</section>
