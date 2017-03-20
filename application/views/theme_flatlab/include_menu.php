<?php
$master_product_main = @$master_product_main;
$this_admin_tab_menu = @thisAdminTabMenu;
$this_admin_sub_menu = @thisAdminSubMenu;
$this_action = @thisAction;
?>

<aside>
	<div id="sidebar"  class="nav-collapse ">
		<!-- sidebar menu start-->
		<ul class="sidebar-menu" id="nav-accordion">
			<li>
				<a class="<?=($this_admin_tab_menu == 'dashboard') ? 'active' : '';?>" href="<?=site_url('admin/dashboard')?>">
					<i class="fa fa-dashboard"></i>
					<span><?=get_language_line($this, 'ui_backend_menu_dashboard', 'Dashboard')?><!-- Dashboard --></span>
				</a>
			</li>
			<?php if(CONST_HAS_TRANSACTION == "1"){ ?>
			<li class="sub-menu">
				<a href="javascript:;" class="<?=($this_admin_tab_menu == 'p_report') ? 'active' : '';?>">
					<i class="fa fa-bar-chart-o"></i>
					<span>Library Report<!--Report --></span>
					<!-- <span>< ?=get_language_line($this, 'ui_backend_menu_report_transaction', 'Report transaction')?><!- - Report Transaction - -></span> -->
				</a>
				<ul class="sub">
				<?php /*?><li class="<?=($this_admin_sub_menu == 'transaction') ? 'active' : '';?>"><a  href="<?=site_url('admin/transaction')?>"><?=get_language_line($this, 'ui_backend_menu_transaction', 'Circulation')?><!-- Circulation --></a></li><?php */?>
                    <li class="<?=($this_admin_sub_menu == 'top_reader') ? 'active' : '';?>"><a  href="<?=site_url('admin/top_reader')?>">Top Readers</a></li>
                    <li class="<?=($this_admin_sub_menu == 'top_borrow') ? 'active' : '';?>"><a  href="<?=site_url('admin/top_borrow')?>">Top Borrowers</a></li>
                    <li class="<?=($this_admin_sub_menu == 'top_most_popular_categories') ? 'active' : '';?>"><a  href="<?=site_url('admin/top_most_popular_categories')?>">Top Popular Categories</a></li>
                    <li class="<?=($this_admin_sub_menu == 'top_most_popular_item') ? 'active' : '';?>"><a  href="<?=site_url('admin/top_most_popular_item')?>">Top Popular Items</a></li>
                    <li class="<?=($this_admin_sub_menu == 'cataloging_summary') ? 'active' : '';?>"><a  href="<?=site_url('admin/cataloging_summary')?>">Cataloging Summary</a></li>
                    <li class="<?=($this_admin_sub_menu == 'circulation_summary') ? 'active' : '';?>"><a  href="<?=site_url('admin/circulation_summary')?>">Circulation Summary</a></li>
                    <li class="<?=($this_admin_sub_menu == 'overdue_item') ? 'active' : '';?>"><a  href="<?=site_url('admin/overdue_item')?>">Overdue Item Rating</a></li>
                    <li class="<?=($this_admin_sub_menu == 'member_usage') ? 'active' : '';?>"><a  href="<?=site_url('admin/member_usage')?>">Member Usage Activities</a></li>
                    <li class="<?=($this_admin_sub_menu == 'not_borrow_item') ? 'active' : '';?>"><a  href="<?=site_url('admin/not_borrow_item')?>">Not Borrow Items</a></li>
                    <li class="<?=($this_admin_sub_menu == 'new_item') ? 'active' : '';?>"><a  href="<?=site_url('admin/new_item')?>">New Resources </a></li>
				</ul>
			</li>
			<?php } ?>
			<li class="sub-menu">
				<a href="javascript:;" class="<?=($this_admin_tab_menu == 'report') ? 'active' : '';?>">
					<i class="fa fa-bar-chart-o"></i>
					<span>Digital Report</span>
					<!-- <span>< ?=get_language_line($this, 'ui_backend_menu_report', 'Report')?><! - - Report - -></span> -->
				</a>
				<ul class="sub">
					<li class="<?=($this_admin_sub_menu == 'report_shelf_download_log') ? 'active' : '';?>"><a  href="<?=site_url('admin/report/shelf-download-log')?>">Download Log</a></li>
					<li class="<?=($this_admin_sub_menu == 'report_reserve_log') ? 'active' : '';?>"><a  href="<?=site_url('admin/report/reserve-log')?>">Reserve Log</a></li>
					<li class="<?=($this_admin_sub_menu == 'report_access_product_log') ? 'active' : '';?>"><a  href="<?=site_url('admin/report/access-knowledge-resources-log')?>">Knowledge Resources Log</a></li>
					<li class="<?=($this_admin_sub_menu == 'report_user_download_log') ? 'active' : '';?>"><a  href="<?=site_url('admin/report/user-download-log')?>">User Download Log</a></li>
					<li class="<?=($this_admin_sub_menu == 'report_top_reader_log') ? 'active' : '';?>"><a  href="<?=site_url('admin/report/top-reader-log')?>">User Top Readers Log</a></li>
					<li class="<?=($this_admin_sub_menu == 'report_user_log') ? 'active' : '';?>"><a  href="<?=site_url('admin/report/user-log')?>">User Login Log</a></li>
					<li class="<?=($this_admin_sub_menu == 'report_news_log') ? 'active' : '';?>"><a  href="<?=site_url('admin/report/news-log')?>">News Log</a></li>
					<li class="<?=($this_admin_sub_menu == 'export_all') ? 'active' : '';?>"><a  href="<?=site_url('admin/export/export-all')?>">Export all book</a></li>
				</ul>
			</li>

			
			<?php if(CONST_HAS_TRANSACTION == "1"){ ?>
			<!-- <li style="background:#92d050;padding:5px;">
				<span style="color:#FFFFFF;font-size:13px;">Circulation</span>
			</li> -->
			<li>
				<a class="<?=($this_admin_sub_menu == 'borrow') ? 'active' : '';?>" href="<?=site_url('admin/transaction/add')?>">
					<i class="fa fa-calendar"></i>
					<?=get_language_line($this, 'ui_backend_menu_borrow', 'Borrow & Return')?><!-- Borrow & Return -->
				</a>
			</li>
			<li>
				<a class="<?=($this_admin_sub_menu == 'transaction') ? 'active' : '';?>" href="<?=site_url('admin/transaction')?>">
					<i class="fa fa-book"></i>
					<?=get_language_line($this, 'ui_backend_menu_transaction_r', 'Transaction')?><!-- Transaction -->
				</a>
			</li>
			<li>
				<a class="<?=($this_admin_tab_menu == 'reservation') ? 'active' : '';?>" href="<?=site_url('admin/reservation-product')?>">
					<i class="fa fa-calendar"></i>
					<?=get_language_line($this, 'ui_backend_menu_reservation', 'Reservation')?><!-- Borrow & Return -->
				</a>
			</li>
			<?php } ?>
			<?php
				if(is_var_array($master_product_main)){
					foreach ($master_product_main as $item) {
						$product_type_cid = get_array_value($item,"product_type_cid","");
						$url = get_array_value($item,"url","");
						$name = get_array_value($item,"name","");
						$product_type_icon = get_array_value($item,"product_type_icon","");

						// echo 'this_admin_tab_menu = '.$this_admin_tab_menu.', product_'.$product_type_cid.'_'.$url;
						switch ($product_type_cid) {
							case 'book':
							case 'vdo':
							case 'others':
								?>
								<li class="sub-menu">
									<a href="javascript:;" class="<?=($this_admin_tab_menu == 'product_'.$product_type_cid.'_'.$url) ? 'active' : '';?>">
										<i class="fa fa-<?=$product_type_icon?>"></i>
										<span><?=$name?></span>
									</a>
									<ul class="sub">
										<li class="<?=($this_admin_sub_menu == 'general_info' || $this_admin_sub_menu == 'copy') ? 'active' : '';?>"><a  href="<?=site_url('admin/product-'.$url.'/'.$product_type_cid)?>"><?=$name?></a></li>
									</ul>
								</li>
								<?php
								break;
							
							case 'magazine':
								?>
								<li class="sub-menu">
									<a href="javascript:;" class="<?=($this_admin_tab_menu == 'product_'.$product_type_cid.'_'.$url) ? 'active' : '';?>">
										<i class="fa fa-<?=$product_type_icon?>"></i>
										<span><?=$name?></span>
									</a>
									<ul class="sub">
										<li class="<?=($this_admin_sub_menu == 'main_general_info' || $this_admin_sub_menu == 'main_subscribe') ? 'active' : '';?>"><a  href="<?=site_url('admin/product-'.$url.'/'.$product_type_cid.'-main')?>"><?=$name?></a></li>
										<li class="<?=($this_admin_sub_menu == 'general_info' || $this_admin_sub_menu == 'copy') ? 'active' : '';?>"><a  href="<?=site_url('admin/product-'.$url.'/'.$product_type_cid)?>"><?=$name?> Issue</a></li>
									</ul>
								</li>
								<?php
								break;
							
							default:
								break;
						}

			?>
			<?php
					}
				}
			?>
			<?php if(CONST_HAS_PRINT == '1'){ ?>
			<li class="sub-menu">
				<a href="javascript:;" class="<?=($this_admin_tab_menu == 'Label') ? 'active' : '';?>">
					<i class="fa fa-book"></i>
					<span><?=get_language_line($this, 'ui_backend_menu_label', 'Label')?><!-- Label --></span>
				</a>
				<ul class="sub">
					<li class="<?=(uri_string() == 'admin/print/print-next') ? 'active' : '';?>"><a  href="<?=site_url('admin/print/print-next')?>"><?=get_language_line($this, 'ui_backend_menu_print_in_advance', 'Print in advance')?><!-- Print in advance --></a></li>
					<li class="<?=(uri_string() == 'admin/print/add') ? 'active' : '';?>"><a  href="<?=site_url('admin/print/add')?>"><?=get_language_line($this, 'ui_backend_menu_Print_repairable', 'Print repairable')?><!-- Print repairable --></a></li>
					<li class="<?=(uri_string()== 'admin/print/print-card') ? 'active' : '';?>"><a  href="<?=site_url('admin/print/print-card')?>"><?=get_language_line($this, 'ui_backend_menu_Print_card', 'Print card')?><!-- Print repairable --></a></li>
				</ul>
			</li>
			<?php } ?>

			<?php if(CONST_HAS_BASKET == '1' || CONST_HAS_POINT == '1'){ ?>
			<li>
				<a class="<?=($this_admin_tab_menu == 'order') ? 'active' : '';?>" href="<?=site_url('admin/order')?>">
					<i class="fa fa-shopping-cart"></i>
					<span><?=get_language_line($this, 'ui_backend_menu_order', 'Order')?><!-- Order --></span>
				</a>
			</li>
			<?php } ?>

			<?php if(CONST_HAS_NEWS == '1'){ ?>
			<li class="sub-menu">
				<a href="javascript:;" class="<?=($this_admin_tab_menu == 'news') ? 'active' : '';?>">
					<i class="fa fa-bullhorn"></i>
					<span><?=get_language_line($this, 'ui_backend_menu_news', 'News')?><!-- News --></span>
				</a>
				<ul class="sub">
					<?php if(is_root_admin_or_higher()){ ?>
						<li class="<?=($this_admin_sub_menu == 'main') ? 'active' : '';?>"><a  href="<?=site_url('admin/news-main')?>"><?=get_language_line($this, 'ui_backend_menu_news_main', 'Main')?><!-- Main --></a></li>
					<?php } ?>
					<?php if(is_root_admin_or_higher() || CONST_NEWS_MODE == "1"){ ?>
					<li class="<?=($this_admin_sub_menu == 'category') ? 'active' : '';?>"><a  href="<?=site_url('admin/news-category')?>"><?=get_language_line($this, 'ui_backend_menu_news_category', 'Category')?><!-- Category --></a></li>
					<?php } ?>
					<li class="<?=( (($this_admin_sub_menu == 'general_info') || ($this_admin_sub_menu == 'gallery')) && $this->uri->segment(2) == 'news') ? 'active' : '';?>"><a  href="<?=site_url('admin/news')?>"><?=get_language_line($this, 'ui_backend_menu_news', 'News')?><!-- News --></a></li>
					<li class="<?=($this_admin_sub_menu == 'news' && $this->uri->segment(2) == 'news-comment') ? 'active' : '';?>"><a  href="<?=site_url('admin/news-comment')?>"><?=get_language_line($this, 'ui_backend_menu_news', 'Comments')?><!-- News --></a></li>
				</ul>
			</li>
			<?php } ?>

			<?php if(CONST_HAS_EVENT == '1'){ ?>
			<li class="sub-menu">
				<a href="javascript:;" class="<?=($this_admin_tab_menu == 'event') ? 'active' : '';?>">
					<i class="fa fa-calendar"></i>
					<span><?=get_language_line($this, 'ui_backend_menu_event', 'Event')?><!-- Event --></span>
				</a>
				<ul class="sub">
					<?php if(is_root_admin_or_higher()){ ?>
						<li class="<?=($this_admin_sub_menu == 'main') ? 'active' : '';?>"><a  href="<?=site_url('admin/event-main')?>"><?=get_language_line($this, 'ui_backend_menu_event_main', 'Main')?><!-- Main --></a></li>
					<?php } ?>
					<?php if(is_root_admin_or_higher() || CONST_EVENT_MODE == "1"){ ?>
					<li class="<?=($this_admin_sub_menu == 'category') ? 'active' : '';?>"><a  href="<?=site_url('admin/event-category')?>"><?=get_language_line($this, 'ui_backend_menu_event_category', 'Category')?><!-- Category --></a></li>
					<?php } ?>
					<li class="<?=($this_admin_sub_menu == 'event') ? 'active' : '';?>"><a  href="<?=site_url('admin/event')?>"><?=get_language_line($this, 'ui_backend_menu_event', 'Event')?><!-- Event --></a></li>
				</ul>
			</li>
			<?php } ?>

			<?php if(CONST_HAS_QUESTIONAIRE == '1'){ ?>
			<li class="sub-menu">
				<a href="javascript:;" class="<?=($this_admin_tab_menu == 'questionaire') ? 'active' : '';?>">
					<i class="fa fa-question-circle"></i>
					<span><?=get_language_line($this, 'ui_backend_menu_questionaire', 'Questionnaire')?><!-- Questionnaire --></span>
				</a>
				<ul class="sub">
					<?php if(is_owner_admin_or_higher()){ ?>
					<li class="<?=($this_admin_sub_menu == 'category') ? 'active' : '';?>"><a  href="<?=site_url('admin/questionaire-category')?>"><?=get_language_line($this, 'ui_backend_menu_questionaire_category', 'Category')?><!-- Category --></a></li>
					<?php } ?>
					<li class="<?=($this_admin_sub_menu == 'questionaire') ? 'active' : '';?>"><a  href="<?=site_url('admin/questionaire')?>"><?=get_language_line($this, 'ui_backend_menu_questionaire', 'Questionnaire')?><!-- Questionnaire --></a></li>
				</ul>
			</li>
			<?php } ?>
			<?php if(CONST_HAS_ADS == '1'){ ?>
			<li class="sub-menu">
				<a href="javascript:;" class="<?=($this_admin_tab_menu == 'ads') ? 'active' : '';?>">
					<i class="fa fa-rocket"></i>
					<span>Side Menu<!--Report --></span>
					<!-- <span>< ?=get_language_line($this, 'ui_backend_menu_ads', 'Ads')?><!- - Ads - -></span> -->
				</a>
				<ul class="sub">
					<?php if(is_root_admin_or_higher()){ ?>
					<li class="<?=($this_admin_sub_menu == 'category') ? 'active' : '';?>"><a  href="<?=site_url('admin/ads-category')?>"><?=get_language_line($this, 'ui_backend_menu_ads_category', 'Category')?><!-- Category --></a></li>
					<?php } ?>
					<li class="<?=($this_admin_sub_menu == 'ads') ? 'active' : '';?>"><a  href="<?=site_url('admin/ads')?>"><?=get_language_line($this, 'ui_backend_menu_ads', 'Ads')?></a></li>

					<!-- <li class="< ?=($this_admin_sub_menu == 'ads') ? 'active' : '';?>"><a  href="< ?=site_url('admin/ads')?>">< ?=get_language_line($this, 'ui_backend_menu_ads', 'Ads')?><!- - Ads - -></a></li> -->
				</ul>
			</li>
			<?php } ?>
			

			<li class="sub-menu">
				<a href="javascript:;" class="<?=($this_admin_tab_menu == 'user') ? 'active' : '';?>">
					<i class="fa fa-bullhorn"></i>
					<span><?=get_language_line($this, 'ui_backend_menu_user', 'User')?><!-- User --></span>
				</a>
				<ul class="sub">
					<li class="<?=($this_admin_sub_menu == 'user_section') ? 'active' : '';?>"><a  href="<?=site_url('admin/user-section')?>"><?=get_language_line($this, 'ui_backend_menu_user_group', 'User Group')?><!-- User Section --></a></li>
					<li class="<?=($this_admin_sub_menu == 'user') ? 'active' : '';?>"><a  href="<?=site_url('admin/user')?>"><?=get_language_line($this, 'ui_backend_menu_user', 'User')?><!-- User --></a></li>
				</ul>
			</li>

			<?php if(CONST_HAS_IPAD_APP == "1"){ ?>
				<li>
					<a class="<?=($this_admin_tab_menu == 'device') ? 'active' : '';?>" href="<?=site_url('admin/device-message')?>">
						<i class="fa fa-comment-o"></i>
						<span>Push Message</span>
					</a>
				</li>
			<?php } ?>


			<?php if(CONST_HAS_REDEEM == '1'){ ?>
			<li>
				<a class="<?=($this_admin_tab_menu == 'redeem') ? 'active' : '';?>" href="<?=site_url('admin/redeem')?>">
					<i class="fa fa-gift"></i>
					<span><?=get_language_line($this, 'ui_backend_menu_redeem', 'Redeem')?><!-- Redeem --></span>
				</a>
			</li>
			<?php } ?>

			<li class="sub-menu">
				<a href="javascript:;" class="<?=($this_admin_tab_menu == 'setting') ? 'active' : '';?>">
					<i class="fa fa-cogs"></i>
					<span><?=get_language_line($this, 'ui_backend_menu_setting', 'Setting')?><!-- Setting --></span>
				</a>
				<ul class="sub">
						<li class="<?=($this_admin_sub_menu == 'product_main' || $this_admin_sub_menu == 'product_main_field') ? 'active' : '';?>"><a  href="<?=site_url('admin/product-main')?>"><?=get_language_line($this, 'ui_backend_menu_product_main', 'Field Set')?><!-- Main Product [Field Set] --></a></li>
					<?php if(is_root_admin_or_higher()){ ?>
					<li class="<?=($this_admin_sub_menu == 'product_type') ? 'active' : '';?>"><a  href="<?=site_url('admin/product-type')?>"><?=get_language_line($this, 'ui_backend_menu_product_type', 'Product Type')?><!-- Product Type--></a></li>
					<?php } ?>
					<li class="hide <?=($this_admin_sub_menu == 'topic_main') ? 'active' : '';?>"><a  href="<?=site_url('admin/product-topic-main')?>"><?=get_language_line($this, 'ui_backend_menu_product_topic', 'Topic')?><!-- Topic --></a></li>
					<li class="<?=($this_admin_sub_menu == 'category') ? 'active' : '';?>"><a  href="<?=site_url('admin/product-category')?>"><?=get_language_line($this, 'ui_backend_menu_product_category', 'Category')?><!-- Category --></a></li>
					<li class="<?=(thisController == 'publisher_back_controller') ? 'active' : '';?>"><a  href="<?=site_url('admin/publisher')?>"><?=get_language_line($this, 'ui_backend_menu_product_publisher', 'Publisher')?><!-- Publisher --></a></li>
					<?php if(CONST_HAS_TRANSACTION == "1"){ ?>
					<li class="<?=($this_admin_sub_menu == 'holidays') ? 'active' : '';?>"><a  href="<?=site_url('admin/holiday')?>"><?=get_language_line($this, 'ui_backend_menu_holiday', 'Holidays')?></a></li> 
					<?php } ?>
					<?php if(CONST_LOGIN_BY_DOMAIN == '1'){ ?>
					<li class="<?=(thisController == 'user_domain_back_controller') ? 'active' : '';?>"><a  href="<?=site_url('admin/user-domain')?>">Domain for login<!-- User domain --></a></li>
					<?php } ?>
					<li class="<?=($this_admin_sub_menu == 'banner') ? 'active' : '';?>"><a  href="<?=site_url('admin/banner')?>">Header Banner<!-- Publisher --></a></li>
				</ul>
			</li>

			

			
			<?php if(is_root_admin_or_higher()){ ?>
			<li>
				<a class="<?=($this_admin_tab_menu == 'econtent_transfer') ? 'active' : '';?>" href="<?=site_url('admin/econtent_transfer')?>">
					<i class="fa fa-fighter-jet"></i>
					<span>E-Content Transfer</span>
				</a>
			</li>
			<?php } ?>


			<?php if(is_root_admin_or_higher()){ ?>
			<li>
				<a class="<?=($this_admin_tab_menu == 'log') ? 'active' : '';?>" href="<?=site_url('admin/log')?>">
					<i class="fa fa-dashboard"></i>
					<span>Logs</span>
				</a>
			</li>
			<?php } ?>

			<li>
				<br/><br/>
			</li>

		</ul>
		<!-- sidebar menu end-->
	</div>
</aside>