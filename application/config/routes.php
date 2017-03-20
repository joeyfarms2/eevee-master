<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "project_".CONST_PROJECT_CODE."/home_controller/home";
$route['404_override'] = "";

$route['/'] = "project_".CONST_PROJECT_CODE."/home_controller/home";
$route['welcome'] = "welcome/welcome_controller/welcome";
$route['intro'] = "welcome/welcome_controller/intro";
$route['home'] = "project_".CONST_PROJECT_CODE."/home_controller/home";
$route['home/status'] = "project_".CONST_PROJECT_CODE."/home_controller/status";
$route['home/status/(:any)'] = "project_".CONST_PROJECT_CODE."/home_controller/status/$1";
$route['index'] = "project_".CONST_PROJECT_CODE."/home_controller/home";
$route['contact'] = "project_".CONST_PROJECT_CODE."/home_controller/contact";
$route['ajax-change-language/(en|th)/(:any)'] = "project_".CONST_PROJECT_CODE."/home_controller/ajax_change_language/$1";
$route['ajax-clear-session/(:any)'] = "project_".CONST_PROJECT_CODE."/home_controller/ajax_clear_session/$1";

require_once('config-'.CONST_PROJECT_CODE.'/routes.php');

$route['list-(:any)/category/(:any)-list/c-(:any)/sort-(:any)/page-(:num)'] = "product/product_front_controller/category/list/$1/$2/$3/0/$4/$5";
$route['list-(:any)/category/(:any)-list/p-(:any)/sort-(:any)/page-(:num)'] = "product/product_front_controller/category/list/$1/$2/0/$3/$4/$5";
$route['list-(:any)/category/(:any)-list/sort-(:any)/page-(:num)'] = "product/product_front_controller/category_main_sort_page/list/$1/$2/$3/$4";
$route['list-(:any)/category/(:any)-list/c-(:any)/page-(:num)'] = "product/product_front_controller/category_all_page/list/$1/$2/$3/0/$4";
$route['list-(:any)/category/(:any)-list/p-(:any)/page-(:num)'] = "product/product_front_controller/category_all_page/list/$1/$2/0/$3/$4";
$route['list-(:any)/category/(:any)-list/c-(:any)/sort-(:any)'] = "product/product_front_controller/category/list/$1/$2/$3/0/$4";
$route['list-(:any)/category/(:any)-list/p-(:any)/sort-(:any)'] = "product/product_front_controller/category/list/$1/$2/0/$3/$4";
$route['list-(:any)/category/(:any)-list/page-(:num)'] = "product/product_front_controller/category_main_page/list/$1/$2/$3";
$route['list-(:any)/category/(:any)-list/sort-(:any)'] = "product/product_front_controller/category_main_sort/list/$1/$2/$3";
$route['list-(:any)/category/(:any)-list/c-(:any)'] = "product/product_front_controller/category/list/$1/$2/$3";
$route['list-(:any)/category/(:any)-list/p-(:any)'] = "product/product_front_controller/category/list/$1/$2/0/$3";
$route['list-(:any)/category/(:any)-list'] = "product/product_front_controller/category/list/$1/$2";

$route['list-(:any)/category/(:any)/c-(:any)/sort-(:any)/page-(:num)'] = "product/product_front_controller/category/shelf/$1/$2/$3/0/$4/$5";
$route['list-(:any)/category/(:any)/p-(:any)/sort-(:any)/page-(:num)'] = "product/product_front_controller/category/shelf/$1/$2/0/$3/$4/$5";
$route['list-(:any)/category/(:any)/sort-(:any)/page-(:num)'] = "product/product_front_controller/category_main_sort_page/shelf/$1/$2/$3/$4";
$route['list-(:any)/category/(:any)/c-(:any)/page-(:num)'] = "product/product_front_controller/category_all_page/shelf/$1/$2/$3/0/$4";
$route['list-(:any)/category/(:any)/p-(:any)/page-(:num)'] = "product/product_front_controller/category_all_page/shelf/$1/$2/0/$3/$4";
$route['list-(:any)/category/(:any)/c-(:any)/sort-(:any)'] = "product/product_front_controller/category/shelf/$1/$2/$3/0/$4";
$route['list-(:any)/category/(:any)/p-(:any)/sort-(:any)'] = "product/product_front_controller/category/shelf/$1/$2/0/$3/$4";
$route['list-(:any)/category/(:any)/page-(:num)'] = "product/product_front_controller/category_main_page/shelf/$1/$2/$3";
$route['list-(:any)/category/(:any)/sort-(:any)'] = "product/product_front_controller/category_main_sort/shelf/$1/$2/$3";
$route['list-(:any)/category/(:any)/c-(:any)'] = "product/product_front_controller/category/shelf/$1/$2/$3";
$route['list-(:any)/category/(:any)/p-(:any)'] = "product/product_front_controller/category/shelf/$1/$2/0/$3";
$route['list-(:any)/category/(:any)'] = "product/product_front_controller/category/shelf/$1/$2";

$route['main-(:any)/category/(:any)/c-(:any)'] = "product/product_front_controller/category_set/shelf/$1/$2/$3";
$route['main-(:any)/category/(:any)/p-(:any)'] = "product/product_front_controller/category_set/shelf/$1/$2/0/$3";
$route['main-(:any)/category/(:any)'] = "product/product_front_controller/category_set/shelf/$1/$2";

$route['ajax-add-product-to-shelf/(:any)/(:any)/(:num)'] = "product/product_front_controller/ajax_add_product_to_shelf/$1/$2/$3";
$route['ajax-add-vdo-to-shelf/(:any)/(:any)/(:num)'] = "product/product_front_controller/ajax_add_vdo_to_shelf/$1/$2/$3";
$route['ajax-get-product-option/(:any)'] = "product/product_front_controller/ajax_get_product_option/$1/";

$route['(:any)-detail/(:num)'] = "product/product_front_controller/detail/$1/$2";
$route['(:any)-detail/(:num)/(:any)'] = "product/product_front_controller/detail/$1/$2/$3";
$route['(:any)/show-product/(:any)'] = "product/product_front_controller/show_product/$1/$2";

$route['(:any)/ajax-open-product/(:any)/(:any)'] = "product/product_front_controller/ajax_open_product/$1/$2/$3";

$route['my-cart'] = "order/basket_front_controller/show";
$route['my-cart/status'] = "order/basket_front_controller/status";
$route['my-cart/status/(:any)'] = "order/basket_front_controller/status/$1";

$route['basket/ajax-get-basket-badge/(:any)'] = "order/basket_front_controller/ajax_get_basket_badge/$1";
$route['basket/ajax-get-basket/(:num)'] = "order/basket_front_controller/ajax_get_basket/$1";
$route['basket/ajax-add-basket/(:num)'] = "order/basket_front_controller/ajax_add_basket/$1";
$route['basket/ajax-remove-basket/(:num)'] = "order/basket_front_controller/ajax_remove_basket/$1";
$route['basket/ajax-update-basket/(:num)'] = "order/basket_front_controller/ajax_update_basket/$1";
$route['basket/ajax-refresh-basket/(:num)'] = "order/basket_front_controller/ajax_refresh_basket/$1";
$route['basket/ajax-clear-basket/(:num)'] = "order/basket_front_controller/ajax_clear_basket/$1";
$route['basket/ajax-change-payment-type/(:any)'] = "order/basket_front_controller/ajax_change_payment_type/$1";
$route['basket/ajax-add-redeem/(:any)'] = "order/basket_front_controller/ajax_add_redeem/$1";
$route['basket/ajax-update-status/(:any)'] = "order/basket_front_controller/ajax_update_status/$1";

$route['basket/confirm/save'] = "order/basket_front_controller/confirm_save";
$route['basket/confirm'] = "order/basket_front_controller/confirm";
$route['basket/confirm/status/(:any)'] = "order/basket_front_controller/confirm_status/$1";
$route['basket/confirm/status/(:any)/(:any)'] = "order/basket_front_controller/confirm_status/$1/$2";
$route['basket/confirm/ajax-update-status/(:any)'] = "order/basket_front_controller/ajax_update_status/$1";

$route['order/package-point'] = "order/package_point_front_controller/show";
$route['order/package-point/confirm/package-(:num)'] = "order/package_point_front_controller/buy_package_point/$1";
$route['order/package-point/redeem-point-status/(:any)'] = "order/package_point_front_controller/redeem_point_status/$1";
$route['order/package-point/redeem-point-status/(:any)/(:any)'] = "order/package_point_front_controller/redeem_point_status/$1/$2";
$route['order/package-point/status/(:any)'] = "order/package_point_front_controller/status/$1";
$route['order/package-point/status/(:any)/(:any)'] = "order/package_point_front_controller/status/$1/$2";
$route['order/package-point/ajax-update-status/(:any)'] = "order/package_point_front_controller/ajax_update_status/$1";

$route['paysbuy/save-(basket|point)-back-from-paysbuy-front'] = "order/payment_paysbuy_controller/save_back_from_paysbuy_front/$1";
$route['paysbuy/save-(basket|point)-back-from-paysbuy-back'] = "order/payment_paysbuy_controller/save_back_from_paysbuy_back/$1";

$route['paypal/save-(basket|point)-back-from-paypal-front'] = "order/payment_paypal_controller/save_back_from_paypal_front/$1";
$route['paypal/save-(basket|point)-back-from-paypal-back'] = "order/payment_paypal_controller/save_back_from_paypal_back/$1";

$route['point/confirm/save-with-point'] = "order/payment_point_controller/confirm_save_with_point";

$route['search/(:any)/option-(:any)/page-(:num)'] = "search/search_front_controller/index/$1/$2/$3";
$route['search/(:any)/option-(:any)'] = "search/search_front_controller/index/$1/$2";
$route['search/(:any)/page-(:num)'] = "search/search_front_controller/index/$1/and/$3";
$route['search/(:any)'] = "search/search_front_controller/index/$1";
$route['search'] = "search/search_front_controller/index";


$route['issue/print/(:any)/(:any)/(:any)'] = "product/issue_front_controller/print_barcode/$1/$2/$3";
$route['issue/print/(:any)/(:any)'] = "product/issue_front_controller/print_barcode/$1/$2";

$route['my-bookshelf'] = "shelf/shelf_front_controller/my_bookshelf/shelf/date_d/1";
$route['my-bookshelf/sort-(:any)'] = "shelf/shelf_front_controller/my_bookshelf/shelf/$1/1";
$route['my-bookshelf/page-(:num)'] = "shelf/shelf_front_controller/my_bookshelf/shelf/date_d/$1";
$route['my-bookshelf/sort-(:any)/page-(:num)'] = "shelf/shelf_front_controller/my_bookshelf/shelf/$1/$2";

$route['my-bookshelf-list'] = "shelf/shelf_front_controller/my_bookshelf/list/date_d/1";
$route['my-bookshelf-list/sort-(:any)'] = "shelf/shelf_front_controller/my_bookshelf/list/$1/1";
$route['my-bookshelf-list/page-(:num)'] = "shelf/shelf_front_controller/my_bookshelf/list/date_d/$1";
$route['my-bookshelf-list/sort-(:any)/page-(:num)'] = "shelf/shelf_front_controller/my_bookshelf/list/$1/$2";

$route['my-bookshelf/ajax-delete-my-bookshelf/(:any)/(:any)/(:any)'] = "shelf/shelf_front_controller/delete_my_bookshelf/$1/$2/$3";
$route['my-bookshelf/ajax-get-badge-my-bookshelf/(:any)'] = "shelf/shelf_front_controller/ajax_get_badge_my_bookshelf/$1";

$route['my-bookshelf-vdo'] = "shelf/shelf_vdo_front_controller/my_bookshelf/shelf/date_d/1";
$route['my-bookshelf-vdo/sort-(:any)'] = "shelf/shelf_vdo_front_controller/my_bookshelf/shelf/$1/1";
$route['my-bookshelf-vdo/page-(:num)'] = "shelf/shelf_vdo_front_controller/my_bookshelf/shelf/date_d/$1";
$route['my-bookshelf-vdo/sort-(:any)/page-(:num)'] = "shelf/shelf_vdo_front_controller/my_bookshelf/shelf/$1/$2";

$route['my-bookshelf-vdo-list'] = "shelf/shelf_vdo_front_controller/my_bookshelf/list/date_d/1";
$route['my-bookshelf-vdo-list/sort-(:any)'] = "shelf/shelf_vdo_front_controller/my_bookshelf/list/$1/1";
$route['my-bookshelf-vdo-list/page-(:num)'] = "shelf/shelf_vdo_front_controller/my_bookshelf/list/date_d/$1";
$route['my-bookshelf-vdo-list/sort-(:any)/page-(:num)'] = "shelf/shelf_vdo_front_controller/my_bookshelf/list/$1/$2";

$route['my-bookshelf-vdo/ajax-delete-my-bookshelf/(:any)/(:any)/(:any)'] = "shelf/shelf_vdo_front_controller/delete_my_bookshelf/$1/$2/$3";
$route['my-bookshelf-vdo/ajax-get-badge-my-bookshelf/(:any)'] = "shelf/shelf_vdo_front_controller/ajax_get_badge_my_bookshelf/$1";

$route['comment/ajax-get-comment/(:any)'] = "comment/comment_front_controller/ajax_get_comment/$1";
$route['comment/ajax-add-comment/(:any)'] = "comment/comment_front_controller/ajax_add_comment/$1";
$route['comment/ajax-set-comment-status/(:any)'] = "comment/comment_front_controller/ajax_set_comment_status/$1";

/* Registration */
$route['registration'] = "user/registration_controller/index";
$route['registration/save'] = "user/registration_controller/save";
$route['registration/status/(:any)'] = "user/registration_controller/status/$1";

/*Activation */
$route['activation'] = "user/activation_controller/index";
$route['activation/verify'] = "user/activation_controller/verify";
$route['activation/status'] = "user/activation_controller/status";
$route['activation/status/(:any)'] = "user/activation_controller/status/$1";
$route['activation/(:any)/(:any)'] = "user/activation_controller/index/$1/$2";
$route['activation/(:any)'] = "user/activation_controller/index/$1";

/* User */
$route['login'] = "user/login_controller/login";
$route['login/verify'] = "user/login_controller/verify";
$route['login/status'] = "user/login_controller/status";
$route['login/status/(:any)'] = "user/login_controller/status/$1";
$route['logout'] = "user/login_controller/logout";

$route['ajax-fb-check-login/(:any)'] = "user/facebook_controller/ajax_fb_check/$1";

$route['forgot'] = "user/forgot_controller/index";
$route['forgot/verify'] = "user/forgot_controller/verify";
$route['forgot/status'] = "user/forgot_controller/status";
$route['forgot/status/(:any)'] = "user/forgot_controller/status/$1";
$route['forgot/change/save'] = "user/forgot_controller/change_save";
$route['forgot/change/(:any)/(:any)'] = "user/forgot_controller/change/$1/$2";

$route['change-password'] = "user/change_password_controller/form";
$route['change-password/save'] = "user/change_password_controller/save";
$route['change-password/status'] = "user/change_password_controller/status";
$route['change-password/status/(:any)'] = "user/change_password_controller/status/$1";

$route['my-account'] = "user/my_account_controller/form";
$route['my-account/save'] = "user/my_account_controller/save";
$route['my-account/status'] = "user/my_account_controller/status";
$route['my-account/status/(:any)'] = "user/my_account_controller/status/$1";


/* Reserve */
$route['ajax-save-reserve-product/(:any)/(:any)/(:num)/(:any)'] = "product/product_front_controller/ajax_save_reserve_product/$1/$2/$3/$4";
$route['ajax-cancel-reserve-product/(:any)/(:any)/(:num)/(:any)'] = "product/product_front_controller/ajax_cancel_reserve_product/$1/$2/$3/$4";
$route['reservation-confirm/(:num)-(:num)-(:num)-(:any)'] = "product/reserve_front_controller/confirm/$1/$2/$3/$4";
$route['reservation-cancel/(:num)-(:num)-(:num)-(:any)'] = "product/reserve_front_controller/cancel/$1/$2/$3/$4";
$route['reservation/status/(:any)'] = "product/reserve_front_controller/status/$1";
$route['webservice/reserve_product'] = "webservice/webservice_reserve_controller/reserve_product";
$route['webservice/get_my_reserve_list'] = "webservice/webservice_reserve_controller/get_my_reserve_list";
$route['webservice/cancel_my_reserve'] = "webservice/webservice_reserve_controller/cancel_my_reserve";
$route['webservice/confirm_my_reserve'] = "webservice/webservice_reserve_controller/confirm_my_reserve";
$route['webservice/get_noti_reserve_list'] = "webservice/webservice_reserve_controller/get_noti_reserve_list";

$route['admin/reservation-(product|digital)'] = "product/reserve_back_controller/index/$1";
$route['admin/reservation-(product|digital)/show'] = "product/reserve_back_controller/show/$1";
$route['admin/reservation-(product|digital)/add'] = "product/reserve_back_controller/add/$1";
$route['admin/reservation-(product|digital)/edit/(:num)'] = "product/reserve_back_controller/edit/$1/$2";
$route['admin/reservation-(product|digital)/save'] = "product/reserve_back_controller/save/$1";
$route['admin/reservation-(product|digital)/ajax-set-value/(:any)'] = "product/reserve_back_controller/ajax_set_value/$1/$2";
$route['admin/reservation-(product|digital)/ajax-delete-one/(:any)'] = "product/reserve_back_controller/ajax_delete_one/$1/$2";
$route['admin/reservation-(product|digital)/ajax-get-main-list/(:any)'] = "product/reserve_back_controller/ajax_get_main_list/$1/$2";
$route['admin/reservation-(product|digital)/status/'] = "product/reserve_back_controller/status/$1";
$route['admin/reservation-(product|digital)/status/(:any)'] = "product/reserve_back_controller/status/$1/$2";


/* Review */
$route['review/ajax-get-main-list/(:any)'] = "product/review_front_controller/ajax_get_main_list/$1";
$route['review/ajax-save-review/(:any)'] = "product/review_front_controller/ajax_save_review/$1/";
$route['review/ajax-hide-review/(:any)'] = "product/review_front_controller/ajax_set_value/$1/";
$route['webservice/get_review_list'] = "webservice/webservice_review_controller/get_review_list";
$route['webservice/add_review'] = "webservice/webservice_review_controller/add_review";


/* Receipt */
$route['receipt/(:num)'] = "sale/receipt_controller/show/$1";


/* News */
$route['news'] = "news/news_front_controller/home";
$route['news/status/'] = "news/news_front_controller/status";
$route['news/status/(:any)'] = "news/news_front_controller/status/$1";
$route['news/home'] = "news/news_front_controller/home";
$route['news/category/(:any)/c-(:any)/sort-(:any)/page-(:num)'] = "news/news_front_controller/category/$1/$2/$3/$4";
$route['news/category/(:any)/sort-(:any)/page-(:num)'] = "news/news_front_controller/category_main_sort_page/$1/$2/$3";
$route['news/category/(:any)/c-(:any)/page-(:num)'] = "news/news_front_controller/category_all_page/$1/$2/$3";
$route['news/category/(:any)/c-(:any)/sort-(:any)'] = "news/news_front_controller/category/$1/$2/$3";
$route['news/category/(:any)/page-(:num)'] = "news/news_front_controller/category_main_page/$1/$2";
$route['news/category/(:any)/sort-(:any)'] = "news/news_front_controller/category_main_sort/$1/$2";
$route['news/category/(:any)/c-(:any)'] = "news/news_front_controller/category/$1/$2";
$route['news/category/(:any)'] = "news/news_front_controller/home/$1";
$route['news/detail/(:any)'] = "news/news_front_controller/detail/$1";

$route['news/ajax-add-comment/(:any)'] = "news/news_front_controller/ajax_add_comment/$1";
$route['news/ajax-delete-comment/(:any)'] = "news/news_front_controller/ajax_delete_comment/$1";
$route['news/ajax-hide-comment/(:any)'] = "news/news_front_controller/ajax_hide_comment/$1";
$route['news/ajax-wow/(:any)'] = "news/news_front_controller/ajax_wow/$1";
$route['news/ajax-unwow/(:any)'] = "news/news_front_controller/ajax_wow/$1";
$route['news/ajax-cheer/(:any)'] = "news/news_front_controller/ajax_cheer/$1";
$route['news/ajax-uncheer/(:any)'] = "news/news_front_controller/ajax_cheer/$1";
$route['news/ajax-thanks/(:any)'] = "news/news_front_controller/ajax_thanks/$1";
$route['news/ajax-unthanks/(:any)'] = "news/news_front_controller/ajax_thanks/$1";

$route['news/ajax-wow-comment/(:any)'] = "news/news_front_controller/ajax_wow_comment/$1";
$route['news/ajax-unwow-comment/(:any)'] = "news/news_front_controller/ajax_wow_comment/$1";
$route['news/ajax-load-user-panels/(:any)'] = "news/news_front_controller/ajax_load_panel_comment_and_activity/$1";
$route['news/ajax-load-news-feed/(:any)'] = "news/news_front_controller/ajax_load_news_feed/$1";
$route['news/ajax-load-who-wow/(:any)'] = "news/news_front_controller/ajax_load_who_wow/$1";
$route['news/ajax-load-who-cheer/(:any)'] = "news/news_front_controller/ajax_load_who_cheer/$1";
$route['news/ajax-load-who-thanks/(:any)'] = "news/news_front_controller/ajax_load_who_thanks/$1";
$route['news/ajax-load-who-comment/(:any)'] = "news/news_front_controller/ajax_load_who_comment/$1";
$route['news/ajax-load-who-wow-this-comment/(:any)'] = "news/news_front_controller/ajax_load_who_wow_this_comment/$1";
$route['news/ajax-load-view-all-comments/(:any)'] = "news/news_front_controller/ajax_load_view_all_comments/$1";

$route['news/upload'] = "news/news_upload_front_controller/form";
$route['news/upload/save'] = "news/news_upload_front_controller/save";
$route['news/upload/status'] = "news/news_upload_front_controller/status";
$route['news/upload/status/(:any)'] = "news/news_upload_front_controller/status/$1";

$route['news/post'] = "news/news_front_controller/save_and_publish";
$route['news/ajax-edit-one/(:any)'] = "news/news_front_controller/ajax_edit_one/$1";
$route['news/ajax-delete-one/(:any)'] = "news/news_front_controller/ajax_delete_one/$1";
$route['news/ajax-delete-one-gallery-photo/(:any)'] = "news/news_front_controller/ajax_delete_one_gallery_photo/$1";
$route['news/ajax-extract-url-process/(:any)'] = "news/news_front_controller/ajax_extract_url_process/$1";
$route['news/ajax-set-cover-image/(:any)'] = "news/news_front_controller/ajax_set_cover_image/$1";


/* Event */
$route['event'] = "event/event_front_controller/home";
$route['event/status/'] = "event/event_front_controller/status";
$route['event/status/(:any)'] = "event/event_front_controller/status/$1";
$route['event/home'] = "event/event_front_controller/home";
$route['event/category/(:any)/c-(:any)/sort-(:any)/page-(:num)'] = "event/event_front_controller/category/$1/$2/$3/$4";
$route['event/category/(:any)/sort-(:any)/page-(:num)'] = "event/event_front_controller/category_main_sort_page/$1/$2/$3";
$route['event/category/(:any)/c-(:any)/page-(:num)'] = "event/event_front_controller/category_all_page/$1/$2/$3";
$route['event/category/(:any)/c-(:any)/sort-(:any)'] = "event/event_front_controller/category/$1/$2/$3";
$route['event/category/(:any)/page-(:num)'] = "event/event_front_controller/category_main_page/$1/$2";
$route['event/category/(:any)/sort-(:any)'] = "event/event_front_controller/category_main_sort/$1/$2";
$route['event/category/(:any)/c-(:any)'] = "event/event_front_controller/category/$1/$2";
$route['event/category/(:any)'] = "event/event_front_controller/home/$1";
$route['event/detail/(:any)'] = "event/event_front_controller/detail/$1";
$route['event/ajax-save-action-join/(:any)'] = "event/event_front_controller/ajax_save_action_join/$1";
$route['event/ajax-load-who-join/(:any)'] = "event/event_front_controller/ajax_load_who_join/$1";
$route['event/ajax-load-event-feed/(:any)'] = "event/event_front_controller/ajax_load_event_feed/$1";

$route['event/ajax-add-comment/(:any)'] = "event/event_front_controller/ajax_add_comment/$1";
$route['event/ajax-delete-comment/(:any)'] = "event/event_front_controller/ajax_delete_comment/$1";
$route['event/ajax-hide-comment/(:any)'] = "event/event_front_controller/ajax_hide_comment/$1";
$route['event/ajax-wow-comment/(:any)'] = "event/event_front_controller/ajax_wow_comment/$1";
$route['event/ajax-unwow-comment/(:any)'] = "event/event_front_controller/ajax_wow_comment/$1";
$route['event/ajax-load-user-panels/(:any)'] = "event/event_front_controller/ajax_load_panel_comment_and_activity/$1";
$route['event/ajax-load-who-wow-this-comment/(:any)'] = "event/event_front_controller/ajax_load_who_wow_this_comment/$1";
$route['event/ajax-load-view-all-comments/(:any)'] = "event/event_front_controller/ajax_load_view_all_comments/$1";

$route['event/upload'] = "event/event_upload_front_controller/form";
$route['event/upload/save'] = "event/event_upload_front_controller/save";
$route['event/upload/status'] = "event/event_upload_front_controller/status";
$route['event/upload/status/(:any)'] = "event/event_upload_front_controller/status/$1";

$route['event/calendar'] = "event/event_front_controller/home_calendar";
$route['event/calendar/ajax-load-calendar-feed/(:any)'] = "event/event_front_controller/ajax_load_calendar_feed";


/* Questionaire */
$route['questionaire'] = "questionaire/questionaire_front_controller/home";
$route['questionaire/status/'] = "questionaire/questionaire_front_controller/status";
$route['questionaire/status/(:any)'] = "questionaire/questionaire_front_controller/status/$1";
$route['questionaire/status/(:any)/(:any)'] = "questionaire/questionaire_front_controller/status/$1/$2";
$route['questionaire/home'] = "questionaire/questionaire_front_controller/home";
$route['questionaire/category/(:any)'] = "questionaire/questionaire_front_controller/home/$1";
$route['questionaire/form/(:any)'] = "questionaire/questionaire_front_controller/form/$1";
$route['questionaire/submit'] = "questionaire/questionaire_front_controller/submit_form/$1";


/* Admin */
$route['admin'] = "report/report_dashboard_controller/dashboard";
$route['admin/dashboard'] = "report/report_dashboard_controller/dashboard";
$route['admin/dashboard/ajax-get-data-summary/(:any)'] = "report/report_dashboard_controller/ajax_get_data_summary/$1";
$route['admin/dashboard/ajax-get-data-product-book-by-product-main/(:any)'] = "report/report_dashboard_controller/ajax_get_data_product_book_by_product_main/$1";
$route['admin/dashboard/ajax-get-data-product-book-and-copy-by-product-main/(:any)'] = "report/report_dashboard_controller/ajax_get_data_product_book_and_copy_by_product_main/$1";
$route['admin/dashboard/ajax-get-data-user-login-by-device/(:any)'] = "report/report_dashboard_controller/ajax_get_data_user_login_by_device/$1";
$route['admin/dashboard/ajax-get-data-user-registration-by-device/(:any)'] = "report/report_dashboard_controller/ajax_get_data_user_registration_by_device/$1";
$route['admin/dashboard/ajax-get-data-product-book-by-category/(:any)'] = "report/report_dashboard_controller/ajax_get_data_product_book_by_category/$1";
$route['admin/dashboard/ajax-get-data-product-vdo-by-category/(:any)'] = "report/report_dashboard_controller/ajax_get_data_product_vdo_by_category/$1";
$route['admin/dashboard/ajax-get-data-product-book-popular-download/(:any)'] = "report/report_dashboard_controller/ajax_get_data_product_book_popular_download/$1";
$route['admin/dashboard/ajax-get-data-search-popular/(:any)'] = "report/report_dashboard_controller/ajax_get_data_search_popular/$1";
$route['admin/dashboard/ajax-get-data-download-by-product-main/(:any)'] = "report/report_dashboard_controller/ajax_get_data_download_by_product_main/$1";
$route['admin/dashboard/ajax-get-data-reserve-popular/(:any)'] = "report/report_dashboard_controller/ajax_get_data_popular_reserve/$1";

$route['admin/dashboard/ajax-get-data-event-by-product-main/(:any)'] = "report/report_dashboard_controller/ajax_get_data_event_by_product_main/$1";
$route['admin/dashboard/ajax-get-data-popular-biblio-download/(:any)'] = "report/report_dashboard_controller/ajax_get_data_popular_biblio_download/$1";
$route['admin/dashboard/ajax-get-data-popular-biblio-rental/(:any)'] = "report/report_dashboard_controller/ajax_get_data_popular_biblio_rental/$1";

$route['admin/user'] = "user/user_back_controller/index";
$route['admin/user/show'] = "user/user_back_controller/show";
$route['admin/user/add'] = "user/user_back_controller/add";
$route['admin/user/edit/(:num)'] = "user/user_back_controller/edit/$1";
$route['admin/user/save'] = "user/user_back_controller/save";
$route['admin/user/detail'] = "user/user_back_controller/detail";
$route['admin/user/ajax-set-value/(:any)'] = "user/user_back_controller/ajax_set_value/$1";
$route['admin/user/ajax-delete-one/(:any)'] = "user/user_back_controller/ajax_delete_one/$1";
$route['admin/user/ajax-get-main-list/(:any)'] = "user/user_back_controller/ajax_get_main_list/$1";
$route['admin/user/ajax-get-popup-list/(:any)'] = "user/user_back_controller/ajax_get_popup_list/$1";
$route['admin/user/status/'] = "user/user_back_controller/status";
$route['admin/user/status/(:any)'] = "user/user_back_controller/status/$1";
$route['admin/user/export-user'] = "user/user_back_controller/export_user";

$route['admin/user-domain'] = "user/user_domain_back_controller/index";
$route['admin/user-domain/show'] = "user/user_domain_back_controller/show";
$route['admin/user-domain/add'] = "user/user_domain_back_controller/add";
$route['admin/user-domain/edit/(:num)'] = "user/user_domain_back_controller/edit/$1";
$route['admin/user-domain/save'] = "user/user_domain_back_controller/save";
$route['admin/user-domain/detail'] = "user/user_domain_back_controller/detail";
$route['admin/user-domain/ajax-set-value/(:any)'] = "user/user_domain_back_controller/ajax_set_value/$1";
$route['admin/user-domain/ajax-delete-one/(:any)'] = "user/user_domain_back_controller/ajax_delete_one/$1";
$route['admin/user-domain/ajax-get-main-list/(:any)'] = "user/user_domain_back_controller/ajax_get_main_list/$1";
$route['admin/user-domain/status/'] = "user/user_domain_back_controller/status";
$route['admin/user-domain/status/(:any)'] = "user/user_domain_back_controller/status/$1";

$route['admin/user-section'] = "user/user_section_back_controller/index";
$route['admin/user-section/show'] = "user/user_section_back_controller/show";
$route['admin/user-section/add'] = "user/user_section_back_controller/add";
$route['admin/user-section/edit/(:num)'] = "user/user_section_back_controller/edit/$1";
$route['admin/user-section/save'] = "user/user_section_back_controller/save";
$route['admin/user-section/detail'] = "user/user_section_back_controller/detail";
$route['admin/user-section/ajax-set-value/(:any)'] = "user/user_section_back_controller/ajax_set_value/$1";
$route['admin/user-section/ajax-delete-one/(:any)'] = "user/user_section_back_controller/ajax_delete_one/$1";
$route['admin/user-section/ajax-get-main-list/(:any)'] = "user/user_section_back_controller/ajax_get_main_list/$1";
$route['admin/user-section/status/'] = "user/user_section_back_controller/status";
$route['admin/user-section/status/(:any)'] = "user/user_section_back_controller/status/$1";

$route['admin/user-generate'] = "user/user_generate_back_controller/index";
$route['admin/user-generate/show'] = "user/user_generate_back_controller/show";
$route['admin/user-generate/add'] = "user/user_generate_back_controller/add";
$route['admin/user-generate/edit/(:num)'] = "user/user_generate_back_controller/edit/$1";
$route['admin/user-generate/save'] = "user/user_generate_back_controller/save";
$route['admin/user-generate/detail'] = "user/user_generate_back_controller/detail";
$route['admin/user-generate/ajax-set-value/(:any)'] = "user/user_generate_back_controller/ajax_set_value/$1";
$route['admin/user-generate/ajax-delete-one/(:any)'] = "user/user_generate_back_controller/ajax_delete_one/$1";
$route['admin/user-generate/ajax-get-main-list/(:any)'] = "user/user_generate_back_controller/ajax_get_main_list/$1";
$route['admin/user-generate/status/'] = "user/user_generate_back_controller/status";
$route['admin/user-generate/status/(:any)'] = "user/user_generate_back_controller/status/$1";

$route['admin/publisher'] = "product/publisher_back_controller/index";
$route['admin/publisher/show'] = "product/publisher_back_controller/show";
$route['admin/publisher/add'] = "product/publisher_back_controller/add";
$route['admin/publisher/edit/(:num)'] = "product/publisher_back_controller/edit/$1";
$route['admin/publisher/save'] = "product/publisher_back_controller/save";
$route['admin/publisher/detail'] = "product/publisher_back_controller/detail";
$route['admin/publisher/ajax-set-value/(:any)'] = "product/publisher_back_controller/ajax_set_value/$1";
$route['admin/publisher/ajax-delete-one/(:any)'] = "product/publisher_back_controller/ajax_delete_one/$1";
$route['admin/publisher/ajax-get-main-list/(:any)'] = "product/publisher_back_controller/ajax_get_main_list/$1";
$route['admin/publisher/status/'] = "product/publisher_back_controller/status";
$route['admin/publisher/status/(:any)'] = "product/publisher_back_controller/status/$1";
$route['admin/publisher/add-if-not-exist'] = "product/publisher_back_controller/add_if_not_exist";

$route['admin/product-main'] = "product/product_main_back_controller/index";
$route['admin/product-main/show'] = "product/product_main_back_controller/show";
$route['admin/product-main/add'] = "product/product_main_back_controller/add";
$route['admin/product-main/edit/(:num)'] = "product/product_main_back_controller/edit/$1";
$route['admin/product-main/save'] = "product/product_main_back_controller/save";
$route['admin/product-main/ajax-set-value/(:any)'] = "product/product_main_back_controller/ajax_set_value/$1";
$route['admin/product-main/ajax-delete-one/(:any)'] = "product/product_main_back_controller/ajax_delete_one/$1";
$route['admin/product-main/ajax-get-main-list/(:any)'] = "product/product_main_back_controller/ajax_get_main_list/$1";
$route['admin/product-main/status/'] = "product/product_main_back_controller/status";
$route['admin/product-main/status/(:any)'] = "product/product_main_back_controller/status/$1";
$route['admin/product-main/ajax-get-product-main/(:any)/(:num)'] = "product/product_main_back_controller/ajax_get_product_main/$1/$2";

$route['admin/product-main-field/(:num)'] = "product/product_main_field_back_controller/index/$1";
$route['admin/product-main-field/(:num)/show'] = "product/product_main_field_back_controller/show/$1/show";
$route['admin/product-main-field/(:num)/add'] = "product/product_main_field_back_controller/add/$1";
$route['admin/product-main-field/(:num)/edit/(:num)'] = "product/product_main_field_back_controller/edit/$1/$2";
$route['admin/product-main-field/(:num)/get-init-field'] = "product/product_main_field_back_controller/get_init_field/$1";
$route['admin/product-main-field/save'] = "product/product_main_field_back_controller/save";
$route['admin/product-main-field/ajax-set-value/(:any)'] = "product/product_main_field_back_controller/ajax_set_value/$1";
$route['admin/product-main-field/ajax-delete-one/(:any)'] = "product/product_main_field_back_controller/ajax_delete_one/$1";
$route['admin/product-main-field/ajax-get-main-list/(:any)'] = "product/product_main_field_back_controller/ajax_get_main_list/$1";
$route['admin/product-main-field/(:num)/status/'] = "product/product_main_field_back_controller/status/$1";
$route['admin/product-main-field/(:num)/status/(:any)'] = "product/product_main_field_back_controller/status/$1/$2";
$route['admin/product-main-field/ajax-get-tag-tree/(:any)'] = "product/product_main_field_back_controller/ajax_get_tag_tree/$1";
$route['admin/product-main-field/ajax-get-field-list-by-product-main-aid/(:any)'] = "product/product_main_field_back_controller/ajax_get_field_list_by_product_main_aid/$1";

$route['admin/product-category'] = "product/product_category_back_controller/index";
$route['admin/product-category/show'] = "product/product_category_back_controller/show";
$route['admin/product-category/add'] = "product/product_category_back_controller/add";
$route['admin/product-category/edit/(:num)'] = "product/product_category_back_controller/edit/$1";
$route['admin/product-category/save'] = "product/product_category_back_controller/save";
$route['admin/product-category/ajax-set-value/(:any)'] = "product/product_category_back_controller/ajax_set_value/$1";
$route['admin/product-category/ajax-delete-one/(:any)'] = "product/product_category_back_controller/ajax_delete_one/$1";
$route['admin/product-category/ajax-get-main-list/(:any)'] = "product/product_category_back_controller/ajax_get_main_list/$1";
$route['admin/product-category/status/'] = "product/product_category_back_controller/status";
$route['admin/product-category/status/(:any)'] = "product/product_category_back_controller/status/$1";
$route['admin/product-category/ajax-get-category-by-product-main/(:any)/(:num)'] = "product/product_category_back_controller/ajax_get_category_by_product_main/$1/$2";

$route['admin/product-topic-main'] = "product/product_topic_main_back_controller/index";
$route['admin/product-topic-main/show'] = "product/product_topic_main_back_controller/show";
$route['admin/product-topic-main/add'] = "product/product_topic_main_back_controller/add";
$route['admin/product-topic-main/edit/(:num)'] = "product/product_topic_main_back_controller/edit/$1";
$route['admin/product-topic-main/save'] = "product/product_topic_main_back_controller/save";
$route['admin/product-topic-main/ajax-set-status/(:any)/(:any)'] = "product/product_topic_main_back_controller/ajax_set_status/$1/$2";
$route['admin/product-topic-main/ajax-delete-one/(:any)'] = "product/product_topic_main_back_controller/ajax_delete_one/$1";
$route['admin/product-topic-main/ajax-get-main-list/(:any)'] = "product/product_topic_main_back_controller/ajax_get_main_list/$1";
$route['admin/product-topic-main/status/'] = "product/product_topic_main_back_controller/status";
$route['admin/product-topic-main/status/(:any)'] = "product/product_topic_main_back_controller/status/$1";

$route['admin/product-topic/(:num)'] = "product/product_topic_back_controller/index/$1";
$route['admin/product-topic/(:num)/show'] = "product/product_topic_back_controller/show/$1";
$route['admin/product-topic/add'] = "product/product_topic_back_controller/add";
$route['admin/product-topic/edit/(:num)'] = "product/product_topic_back_controller/edit/$1";
$route['admin/product-topic/save'] = "product/product_topic_back_controller/save";
$route['admin/product-topic/ajax-set-status/(:any)/(:any)'] = "product/product_topic_back_controller/ajax_set_status/$1/$2";
$route['admin/product-topic/ajax-delete-one/(:any)'] = "product/product_topic_back_controller/ajax_delete_one/$1";
$route['admin/product-topic/ajax-get-main-list/(:any)'] = "product/product_topic_back_controller/ajax_get_main_list/$1";
$route['admin/product-topic/status/'] = "product/product_topic_back_controller/status";
$route['admin/product-topic/status/(:any)'] = "product/product_topic_back_controller/status/$1";
$route['admin/product-topic/ajax-get-topic-tree-by-cid/(:any)/(:num)'] = "product/product_topic_back_controller/ajax_get_topic_tree_by_cid/$1/$2";
$route['admin/product-topic/ajax-get-topic-tree/(:any)/(:any)'] = "product/product_topic_back_controller/ajax_get_topic_tree/$1/$2";
$route['admin/product-topic/ajax-save-topic-tree-parent-aid/(:num)'] = "product/product_topic_back_controller/ajax_save_topic_tree_parent_aid/$1";
$route['admin/product-topic/ajax-save-topic-tree-name/(:any)'] = "product/product_topic_back_controller/ajax_save_topic_tree_name/$1";
$route['admin/product-topic/ajax-add-topic-tree/(:any)'] = "product/product_topic_back_controller/ajax_add_topic_tree/$1";

$route['admin/product-type'] = "product/product_type_back_controller/index";
$route['admin/product-type/show'] = "product/product_type_back_controller/show";
$route['admin/product-type/add'] = "product/product_type_back_controller/add";
$route['admin/product-type/edit/(:num)'] = "product/product_type_back_controller/edit/$1";
$route['admin/product-type/save'] = "product/product_type_back_controller/save";
$route['admin/product-type/ajax-set-value/(:any)'] = "product/product_type_back_controller/ajax_set_value/$1";
$route['admin/product-type/ajax-delete-one/(:any)'] = "product/product_type_back_controller/ajax_delete_one/$1";
$route['admin/product-type/ajax-get-main-list/(:any)'] = "product/product_type_back_controller/ajax_get_main_list/$1";
$route['admin/product-type/status/'] = "product/product_type_back_controller/status";
$route['admin/product-type/status/(:any)'] = "product/product_type_back_controller/status/$1";

$route['admin/product-type-minor/ajax-get-product-type-minor-by-product-main/(:any)/(:num)'] = "product/product_type_minor_back_controller/ajax_get_product_type_minor_by_product_main/$1/$2";

$route['admin/product-(:any)/book']  = "product/product_book_back_controller/show/$1";
$route['admin/product-(:any)/book/add'] = "product/product_book_back_controller/add/$1";
$route['admin/product-(:any)/book/edit/(:num)'] = "product/product_book_back_controller/edit/$1/$2";
$route['admin/product-(:any)/book/status/'] = "product/product_book_back_controller/status/$1";
$route['admin/product-(:any)/book/status/(:any)'] = "product/product_book_back_controller/status/$1/$2";
$route['admin/product/book/save'] = "product/product_book_back_controller/save";
$route['admin/product/book/ajax-set-value/(:any)'] = "product/product_book_back_controller/ajax_set_value/$1";
$route['admin/product/book/ajax-delete-one/(:any)'] = "product/product_book_back_controller/ajax_delete_one/$1";
$route['admin/product/book/ajax-get-main-list/(:any)'] = "product/product_book_back_controller/ajax_get_main_list/$1";
$route['admin/product/book/ajax-get-product-main/(:any)/(:num)'] = "product/product_book_back_controller/ajax_get_product_main/$1/$2";
$route['admin/product/book/add_new_book'] = "product/product_book_back_controller/add_new_book";

$route['admin/product-(:any)/book/edit/(:num)/field'] = "product/product_book_field_back_controller/show/$1/$2";
$route['admin/product-(:any)/book/edit/(:num)/field/add'] = "product/product_book_field_back_controller/add/$1/$2";
$route['admin/product-(:any)/book/edit/(:num)/field/edit/(:num)'] = "product/product_book_field_back_controller/edit/$1/$2/$3";
$route['admin/product-(:any)/book/edit/(:num)/field/status'] = "product/product_book_field_back_controller/status/$1/$2";
$route['admin/product-(:any)/book/edit/(:num)/field/status/(:any)'] = "product/product_book_field_back_controller/status/$1/$2/$3";
$route['admin/product/book-field/ajax-get-main-list/(:any)'] = "product/product_book_field_back_controller/ajax_get_main_list/$1";
$route['admin/product/book-field/save'] = "product/product_book_field_back_controller/save";
$route['admin/product/book-field/ajax-delete-one/(:any)'] = "product/product_book_field_back_controller/ajax_delete_one/$1";

$route['admin/product-(:any)/book/edit/(:num)/copy'] = "product/product_book_copy_back_controller/show/$1/$2";
$route['admin/product-(:any)/book/edit/(:num)/copy/add'] = "product/product_book_copy_back_controller/add/$1/$2";
$route['admin/product-(:any)/book/edit/(:num)/copy/edit/(:num)'] = "product/product_book_copy_back_controller/edit/$1/$2/$3";
$route['admin/product-(:any)/book/edit/(:num)/copy/status'] = "product/product_book_copy_back_controller/status/$1/$2";
$route['admin/product-(:any)/book/edit/(:num)/copy/status/(:any)'] = "product/product_book_copy_back_controller/status/$1/$2/$3";
$route['admin/product/book-copy/ajax-set-value/(:any)'] = "product/product_book_copy_back_controller/ajax_set_value/$1";
$route['admin/product/book-copy/ajax-get-main-list/(:any)'] = "product/product_book_copy_back_controller/ajax_get_main_list/$1";
$route['admin/product/book-copy/ajax-get-popup-list/(:any)'] = "product/product_book_copy_back_controller/ajax_get_popup_list/$1";
$route['admin/product/book-copy/save'] = "product/product_book_copy_back_controller/save";
$route['admin/product/book-copy/ajax-delete-one/(:any)'] = "product/product_book_copy_back_controller/ajax_delete_one/$1";
$route['admin/product/book-copy/add_new_book'] = "product/product_book_copy_back_controller/add_new_book";

$route['admin/product-(:any)/magazine-main']  = "product/product_magazine_main_back_controller/show/$1";
$route['admin/product-(:any)/magazine-main/add'] = "product/product_magazine_main_back_controller/add/$1";
$route['admin/product-(:any)/magazine-main/edit/(:num)'] = "product/product_magazine_main_back_controller/edit/$1/$2";
$route['admin/product-(:any)/magazine-main/status/'] = "product/product_magazine_main_back_controller/status/$1";
$route['admin/product-(:any)/magazine-main/status/(:any)'] = "product/product_magazine_main_back_controller/status/$1/$2";
$route['admin/product/magazine-main/save'] = "product/product_magazine_main_back_controller/save";
$route['admin/product/magazine-main/ajax-set-value/(:any)'] = "product/product_magazine_main_back_controller/ajax_set_value/$1";
$route['admin/product/magazine-main/ajax-delete-one/(:any)'] = "product/product_magazine_main_back_controller/ajax_delete_one/$1";
$route['admin/product/magazine-main/ajax-get-main-list/(:any)'] = "product/product_magazine_main_back_controller/ajax_get_main_list/$1";
$route['admin/product/magazine-main/ajax-get-magazine-main-by-aid/(:any)'] = "product/product_magazine_main_back_controller/ajax_get_magazine_main_by_aid/$1";

$route['admin/product-(:any)/magazine']  = "product/product_magazine_back_controller/show/$1";
$route['admin/product-(:any)/magazine/add'] = "product/product_magazine_back_controller/add/$1";
$route['admin/product-(:any)/magazine/add/(:num)'] = "product/product_magazine_back_controller/add/$1/$2";
$route['admin/product-(:any)/magazine/edit/(:num)'] = "product/product_magazine_back_controller/edit/$1/$2";
$route['admin/product-(:any)/magazine/status/'] = "product/product_magazine_back_controller/status/$1";
$route['admin/product-(:any)/magazine/status/(:any)'] = "product/product_magazine_back_controller/status/$1/$2";
$route['admin/product/magazine/save'] = "product/product_magazine_back_controller/save";
$route['admin/product/magazine/ajax-set-value/(:any)'] = "product/product_magazine_back_controller/ajax_set_value/$1";
$route['admin/product/magazine/ajax-delete-one/(:any)'] = "product/product_magazine_back_controller/ajax_delete_one/$1";
$route['admin/product/magazine/ajax-get-main-list/(:any)'] = "product/product_magazine_back_controller/ajax_get_main_list/$1";
$route['admin/product/magazine/ajax-get-product-main/(:any)/(:num)'] = "product/product_magazine_back_controller/ajax_get_product_main/$1/$2";

$route['admin/product-(:any)/magazine/edit/(:num)/field'] = "product/product_magazine_field_back_controller/show/$1/$2";
$route['admin/product-(:any)/magazine/edit/(:num)/field/add'] = "product/product_magazine_field_back_controller/add/$1/$2";
$route['admin/product-(:any)/magazine/edit/(:num)/field/edit/(:num)'] = "product/product_magazine_field_back_controller/edit/$1/$2/$3";
$route['admin/product-(:any)/magazine/edit/(:num)/field/status'] = "product/product_magazine_field_back_controller/status/$1/$2";
$route['admin/product-(:any)/magazine/edit/(:num)/field/status/(:any)'] = "product/product_magazine_field_back_controller/status/$1/$2/$3";
$route['admin/product/magazine-field/ajax-get-main-list/(:any)'] = "product/product_magazine_field_back_controller/ajax_get_main_list/$1";
$route['admin/product/magazine-field/save'] = "product/product_magazine_field_back_controller/save";
$route['admin/product/magazine-field/ajax-delete-one/(:any)'] = "product/product_magazine_field_back_controller/ajax_delete_one/$1";

$route['admin/product-(:any)/magazine/edit/(:num)/copy'] = "product/product_magazine_copy_back_controller/show/$1/$2";
$route['admin/product-(:any)/magazine/edit/(:num)/copy/add'] = "product/product_magazine_copy_back_controller/add/$1/$2";
$route['admin/product-(:any)/magazine/edit/(:num)/copy/edit/(:num)'] = "product/product_magazine_copy_back_controller/edit/$1/$2/$3";
$route['admin/product-(:any)/magazine/edit/(:num)/copy/status'] = "product/product_magazine_copy_back_controller/status/$1/$2";
$route['admin/product-(:any)/magazine/edit/(:num)/copy/status/(:any)'] = "product/product_magazine_copy_back_controller/status/$1/$2/$3";
$route['admin/product/magazine-copy/ajax-set-value/(:any)'] = "product/product_magazine_copy_back_controller/ajax_set_value/$1";
$route['admin/product/magazine-copy/ajax-get-main-list/(:any)'] = "product/product_magazine_copy_back_controller/ajax_get_main_list/$1";
$route['admin/product/magazine-copy/ajax-get-popup-list/(:any)'] = "product/product_magazine_copy_back_controller/ajax_get_popup_list/$1";
$route['admin/product/magazine-copy/save'] = "product/product_magazine_copy_back_controller/save";
$route['admin/product/magazine-copy/ajax-delete-one/(:any)'] = "product/product_magazine_copy_back_controller/ajax_delete_one/$1";

$route['admin/product-(:any)/others']  = "product/product_others_back_controller/show/$1";
$route['admin/product-(:any)/others/add'] = "product/product_others_back_controller/add/$1";
$route['admin/product-(:any)/others/edit/(:num)'] = "product/product_others_back_controller/edit/$1/$2";
$route['admin/product-(:any)/others/status/'] = "product/product_others_back_controller/status/$1";
$route['admin/product-(:any)/others/status/(:any)'] = "product/product_others_back_controller/status/$1/$2";
$route['admin/product/others/save'] = "product/product_others_back_controller/save";
$route['admin/product/others/ajax-set-value/(:any)'] = "product/product_others_back_controller/ajax_set_value/$1";
$route['admin/product/others/ajax-delete-one/(:any)'] = "product/product_others_back_controller/ajax_delete_one/$1";
$route['admin/product/others/ajax-get-main-list/(:any)'] = "product/product_others_back_controller/ajax_get_main_list/$1";
$route['admin/product/others/ajax-get-product-main/(:any)/(:num)'] = "product/product_others_back_controller/ajax_get_product_main/$1/$2";

$route['admin/product-(:any)/others/edit/(:num)/field'] = "product/product_others_field_back_controller/show/$1/$2";
$route['admin/product-(:any)/others/edit/(:num)/field/add'] = "product/product_others_field_back_controller/add/$1/$2";
$route['admin/product-(:any)/others/edit/(:num)/field/edit/(:num)'] = "product/product_others_field_back_controller/edit/$1/$2/$3";
$route['admin/product-(:any)/others/edit/(:num)/field/status'] = "product/product_others_field_back_controller/status/$1/$2";
$route['admin/product-(:any)/others/edit/(:num)/field/status/(:any)'] = "product/product_others_field_back_controller/status/$1/$2/$3";
$route['admin/product/others-field/ajax-get-main-list/(:any)'] = "product/product_others_field_back_controller/ajax_get_main_list/$1";
$route['admin/product/others-field/save'] = "product/product_others_field_back_controller/save";
$route['admin/product/others-field/ajax-delete-one/(:any)'] = "product/product_others_field_back_controller/ajax_delete_one/$1";

$route['admin/product-(:any)/others/edit/(:num)/copy'] = "product/product_others_copy_back_controller/show/$1/$2";
$route['admin/product-(:any)/others/edit/(:num)/copy/add'] = "product/product_others_copy_back_controller/add/$1/$2";
$route['admin/product-(:any)/others/edit/(:num)/copy/edit/(:num)'] = "product/product_others_copy_back_controller/edit/$1/$2/$3";
$route['admin/product-(:any)/others/edit/(:num)/copy/status'] = "product/product_others_copy_back_controller/status/$1/$2";
$route['admin/product-(:any)/others/edit/(:num)/copy/status/(:any)'] = "product/product_others_copy_back_controller/status/$1/$2/$3";
$route['admin/product/others-copy/ajax-set-value/(:any)'] = "product/product_others_copy_back_controller/ajax_set_value/$1";
$route['admin/product/others-copy/ajax-get-main-list/(:any)'] = "product/product_others_copy_back_controller/ajax_get_main_list/$1";
$route['admin/product/others-copy/save'] = "product/product_others_copy_back_controller/save";
$route['admin/product/others-copy/ajax-delete-one/(:any)'] = "product/product_others_copy_back_controller/ajax_delete_one/$1";

$route['admin/product-(:any)/vdo']  = "product/product_vdo_back_controller/show/$1";
$route['admin/product-(:any)/vdo/add'] = "product/product_vdo_back_controller/add/$1";
$route['admin/product-(:any)/vdo/edit/(:num)'] = "product/product_vdo_back_controller/edit/$1/$2";
$route['admin/product-(:any)/vdo/status/'] = "product/product_vdo_back_controller/status/$1";
$route['admin/product-(:any)/vdo/status/(:any)'] = "product/product_vdo_back_controller/status/$1/$2";
$route['admin/product/vdo/save'] = "product/product_vdo_back_controller/save";
$route['admin/product/vdo/ajax-set-value/(:any)'] = "product/product_vdo_back_controller/ajax_set_value/$1";
$route['admin/product/vdo/ajax-delete-one/(:any)'] = "product/product_vdo_back_controller/ajax_delete_one/$1";
$route['admin/product/vdo/ajax-get-main-list/(:any)'] = "product/product_vdo_back_controller/ajax_get_main_list/$1";
$route['admin/product/vdo/ajax-get-product-main/(:any)/(:num)'] = "product/product_vdo_back_controller/ajax_get_product_main/$1/$2";

$route['admin/product-(:any)/vdo/edit/(:num)/field'] = "product/product_vdo_field_back_controller/show/$1/$2";
$route['admin/product-(:any)/vdo/edit/(:num)/field/add'] = "product/product_vdo_field_back_controller/add/$1/$2";
$route['admin/product-(:any)/vdo/edit/(:num)/field/edit/(:num)'] = "product/product_vdo_field_back_controller/edit/$1/$2/$3";
$route['admin/product-(:any)/vdo/edit/(:num)/field/status'] = "product/product_vdo_field_back_controller/status/$1/$2";
$route['admin/product-(:any)/vdo/edit/(:num)/field/status/(:any)'] = "product/product_vdo_field_back_controller/status/$1/$2/$3";
$route['admin/product/vdo-field/ajax-get-main-list/(:any)'] = "product/product_vdo_field_back_controller/ajax_get_main_list/$1";
$route['admin/product/vdo-field/save'] = "product/product_vdo_field_back_controller/save";
$route['admin/product/vdo-field/ajax-delete-one/(:any)'] = "product/product_vdo_field_back_controller/ajax_delete_one/$1";

$route['admin/product-(:any)/vdo/edit/(:num)/copy'] = "product/product_vdo_copy_back_controller/show/$1/$2";
$route['admin/product-(:any)/vdo/edit/(:num)/copy/add'] = "product/product_vdo_copy_back_controller/add/$1/$2";
$route['admin/product-(:any)/vdo/edit/(:num)/copy/edit/(:num)'] = "product/product_vdo_copy_back_controller/edit/$1/$2/$3";
$route['admin/product-(:any)/vdo/edit/(:num)/copy/status'] = "product/product_vdo_copy_back_controller/status/$1/$2";
$route['admin/product-(:any)/vdo/edit/(:num)/copy/status/(:any)'] = "product/product_vdo_copy_back_controller/status/$1/$2/$3";
$route['admin/product/vdo-copy/ajax-set-value/(:any)'] = "product/product_vdo_copy_back_controller/ajax_set_value/$1";
$route['admin/product/vdo-copy/ajax-get-main-list/(:any)'] = "product/product_vdo_copy_back_controller/ajax_get_main_list/$1";
$route['admin/product/vdo-copy/save'] = "product/product_vdo_copy_back_controller/save";
$route['admin/product/vdo-copy/ajax-delete-one/(:any)'] = "product/product_vdo_copy_back_controller/ajax_delete_one/$1";

$route['admin/product/ajax-generate-file/(:any)'] = "product/product_init_controller/ajax_generate_file/$1";
$route['admin/product/ajax-generate-update-file/(:any)'] = "product/product_init_controller/ajax_generate_update_file/$1";

$route['admin/event-main'] = "event/event_main_back_controller/index";
$route['admin/event-main/show'] = "event/event_main_back_controller/show";
$route['admin/event-main/add'] = "event/event_main_back_controller/add";
$route['admin/event-main/edit/(:num)'] = "event/event_main_back_controller/edit/$1";
$route['admin/event-main/save'] = "event/event_main_back_controller/save";
$route['admin/event-main/ajax-set-value/(:any)'] = "event/event_main_back_controller/ajax_set_value/$1";
$route['admin/event-main/ajax-delete-one/(:any)'] = "event/event_main_back_controller/ajax_delete_one/$1";
$route['admin/event-main/ajax-get-main-list/(:any)'] = "event/event_main_back_controller/ajax_get_main_list/$1";
$route['admin/event-main/status/'] = "event/event_main_back_controller/status";
$route['admin/event-main/status/(:any)'] = "event/event_main_back_controller/status/$1";
$route['admin/event-main/ajax-get-event-main/(:any)/(:num)'] = "event/event_main_back_controller/ajax_get_event_main/$1/$2";

$route['admin/event-category'] = "event/event_category_back_controller/index";
$route['admin/event-category/show'] = "event/event_category_back_controller/show";
$route['admin/event-category/add'] = "event/event_category_back_controller/add";
$route['admin/event-category/edit/(:num)'] = "event/event_category_back_controller/edit/$1";
$route['admin/event-category/save'] = "event/event_category_back_controller/save";
$route['admin/event-category/ajax-set-value/(:any)'] = "event/event_category_back_controller/ajax_set_value/$1";
$route['admin/event-category/ajax-delete-one/(:any)'] = "event/event_category_back_controller/ajax_delete_one/$1";
$route['admin/event-category/ajax-get-main-list/(:any)'] = "event/event_category_back_controller/ajax_get_main_list/$1";
$route['admin/event-category/status/'] = "event/event_category_back_controller/status";
$route['admin/event-category/status/(:any)'] = "event/event_category_back_controller/status/$1";
$route['admin/event-category/ajax-get-category-by-event-main/(:any)/(:num)'] = "event/event_category_back_controller/ajax_get_category_by_event_main/$1/$2";

$route['admin/event'] = "event/event_back_controller/index";
$route['admin/event/show'] = "event/event_back_controller/show";
$route['admin/event/add'] = "event/event_back_controller/add";
$route['admin/event/edit/(:num)'] = "event/event_back_controller/edit/$1";
$route['admin/event/save'] = "event/event_back_controller/save";
$route['admin/event/ajax-set-value/(:any)'] = "event/event_back_controller/ajax_set_value/$1";
$route['admin/event/ajax-delete-one/(:any)'] = "event/event_back_controller/ajax_delete_one/$1";
$route['admin/event/ajax-get-main-list/(:any)'] = "event/event_back_controller/ajax_get_main_list/$1";
$route['admin/event/status/'] = "event/event_back_controller/status";
$route['admin/event/status/(:any)'] = "event/event_back_controller/status/$1";

$route['admin/event/edit/(:num)/gallery'] = "event/event_gallery_back_controller/show/$1";
$route['admin/event/edit/(:num)/gallery/add'] = "event/event_gallery_back_controller/add/$1";
$route['admin/event/edit/(:num)/gallery/edit/(:num)'] = "event/event_gallery_back_controller/edit/$1/$2";
$route['admin/event/edit/(:num)/gallery/status'] = "event/event_gallery_back_controller/status/$1";
$route['admin/event/edit/(:num)/gallery/status/(:any)'] = "event/event_gallery_back_controller/status/$1/$2";
$route['admin/event-gallery/ajax-set-status/(:any)/(:any)'] = "event/event_gallery_back_controller/ajax_set_status/$1/$2";
$route['admin/event-gallery/ajax-get-main-list/(:any)'] = "event/event_gallery_back_controller/ajax_get_main_list/$1";
$route['admin/event-gallery/save'] = "event/event_gallery_back_controller/save";
$route['admin/event-gallery/ajax-delete-one/(:any)'] = "event/event_gallery_back_controller/ajax_delete_one/$1";

$route['admin/event-upload'] = "event/event_upload_back_controller/show";
$route['admin/event-upload/save'] = "event/event_upload_back_controller/save";
$route['admin/event-upload/detail/(:num)'] = "event/event_upload_back_controller/detail/$1";
$route['admin/event-upload/ajax-set-status/(:any)/(:any)'] = "event/event_upload_back_controller/ajax_set_status/$1/$2";
$route['admin/event-upload/ajax-get-main-list/(:any)'] = "event/event_upload_back_controller/ajax_get_main_list/$1";
$route['admin/event-upload/status/'] = "event/event_upload_back_controller/status";
$route['admin/event-upload/status/(:any)'] = "event/event_upload_back_controller/status/$1";


$route['admin/news-main'] = "news/news_main_back_controller/index";
$route['admin/news-main/show'] = "news/news_main_back_controller/show";
$route['admin/news-main/add'] = "news/news_main_back_controller/add";
$route['admin/news-main/edit/(:num)'] = "news/news_main_back_controller/edit/$1";
$route['admin/news-main/save'] = "news/news_main_back_controller/save";
$route['admin/news-main/ajax-set-value/(:any)'] = "news/news_main_back_controller/ajax_set_value/$1";
$route['admin/news-main/ajax-delete-one/(:any)'] = "news/news_main_back_controller/ajax_delete_one/$1";
$route['admin/news-main/ajax-get-main-list/(:any)'] = "news/news_main_back_controller/ajax_get_main_list/$1";
$route['admin/news-main/status/'] = "news/news_main_back_controller/status";
$route['admin/news-main/status/(:any)'] = "news/news_main_back_controller/status/$1";
$route['admin/news-main/ajax-get-news-main/(:any)/(:num)'] = "news/news_main_back_controller/ajax_get_news_main/$1/$2";

$route['admin/news-category'] = "news/news_category_back_controller/index";
$route['admin/news-category/show'] = "news/news_category_back_controller/show";
$route['admin/news-category/add'] = "news/news_category_back_controller/add";
$route['admin/news-category/edit/(:num)'] = "news/news_category_back_controller/edit/$1";
$route['admin/news-category/save'] = "news/news_category_back_controller/save";
$route['admin/news-category/ajax-set-value/(:any)'] = "news/news_category_back_controller/ajax_set_value/$1";
$route['admin/news-category/ajax-delete-one/(:any)'] = "news/news_category_back_controller/ajax_delete_one/$1";
$route['admin/news-category/ajax-get-main-list/(:any)'] = "news/news_category_back_controller/ajax_get_main_list/$1";
$route['admin/news-category/status/'] = "news/news_category_back_controller/status";
$route['admin/news-category/status/(:any)'] = "news/news_category_back_controller/status/$1";
$route['admin/news-category/ajax-get-category-by-news-main/(:any)/(:num)'] = "news/news_category_back_controller/ajax_get_category_by_news_main/$1/$2";

$route['admin/news'] = "news/news_back_controller/index";
$route['admin/news/show'] = "news/news_back_controller/show";
$route['admin/news/add'] = "news/news_back_controller/add";
$route['admin/news/edit/(:num)'] = "news/news_back_controller/edit/$1";
$route['admin/news/save'] = "news/news_back_controller/save_and_publish";
$route['admin/news/ajax-set-value/(:any)'] = "news/news_back_controller/ajax_set_value/$1";
$route['admin/news/ajax-delete-one/(:any)'] = "news/news_back_controller/ajax_delete_one/$1";
$route['admin/news/ajax-get-main-list/(:any)'] = "news/news_back_controller/ajax_get_main_list/$1";
$route['admin/news/ajax-save-preview/(:any)'] = "news/news_back_controller/ajax_save_preview/$1";
$route['admin/news/(:num)/preview/(:any)'] = "news/news_back_controller/preview_detail/$1/$2";
$route['admin/news/status/'] = "news/news_back_controller/status";
$route['admin/news/status/(:any)'] = "news/news_back_controller/status/$1";

$route['admin/news-comment'] = "news/news_back_controller/show_comments";
$route['admin/news-comment/ajax-get-main-list/(:any)'] = "news/news_back_controller/ajax_get_comment_main_list/$1";
$route['admin/news-comment/ajax-set-value/(:any)'] = "news/news_back_controller/ajax_set_comment_value/$1";
$route['admin/news-comment/ajax-delete-one/(:any)'] = "news/news_back_controller/ajax_delete_comment_one/$1";

$route['admin/news/edit/(:num)/gallery'] = "news/news_gallery_back_controller/show/$1";
$route['admin/news/edit/(:num)/gallery/add'] = "news/news_gallery_back_controller/add/$1";
$route['admin/news/edit/(:num)/gallery/edit/(:num)'] = "news/news_gallery_back_controller/edit/$1/$2";
$route['admin/news/edit/(:num)/gallery/status'] = "news/news_gallery_back_controller/status/$1";
$route['admin/news/edit/(:num)/gallery/status/(:any)'] = "news/news_gallery_back_controller/status/$1/$2";
$route['admin/news-gallery/ajax-set-value/(:any)'] = "news/news_gallery_back_controller/ajax_set_value/$1";
$route['admin/news-gallery/ajax-get-main-list/(:any)'] = "news/news_gallery_back_controller/ajax_get_main_list/$1";
$route['admin/news-gallery/save'] = "news/news_gallery_back_controller/save";
$route['admin/news-gallery/ajax-delete-one/(:any)'] = "news/news_gallery_back_controller/ajax_delete_one/$1";

$route['admin/news-upload'] = "news/news_upload_back_controller/show";
$route['admin/news-upload/save'] = "news/news_upload_back_controller/save";
$route['admin/news-upload/detail/(:num)'] = "news/news_upload_back_controller/detail/$1";
$route['admin/news-upload/ajax-set-status/(:any)/(:any)'] = "news/news_upload_back_controller/ajax_set_status/$1/$2";
$route['admin/news-upload/ajax-get-main-list/(:any)'] = "news/news_upload_back_controller/ajax_get_main_list/$1";
$route['admin/news-upload/status/'] = "news/news_upload_back_controller/status";
$route['admin/news-upload/status/(:any)'] = "news/news_upload_back_controller/status/$1";

$route['admin/questionaire-category'] = "questionaire/questionaire_category_back_controller/index";
$route['admin/questionaire-category/show'] = "questionaire/questionaire_category_back_controller/show";
$route['admin/questionaire-category/add'] = "questionaire/questionaire_category_back_controller/add";
$route['admin/questionaire-category/edit/(:num)'] = "questionaire/questionaire_category_back_controller/edit/$1";
$route['admin/questionaire-category/save'] = "questionaire/questionaire_category_back_controller/save";
$route['admin/questionaire-category/ajax-set-value/(:any)'] = "questionaire/questionaire_category_back_controller/ajax_set_value/$1";
$route['admin/questionaire-category/ajax-delete-one/(:any)'] = "questionaire/questionaire_category_back_controller/ajax_delete_one/$1";
$route['admin/questionaire-category/ajax-get-main-list/(:any)'] = "questionaire/questionaire_category_back_controller/ajax_get_main_list/$1";
$route['admin/questionaire-category/status/'] = "questionaire/questionaire_category_back_controller/status";
$route['admin/questionaire-category/status/(:any)'] = "questionaire/questionaire_category_back_controller/status/$1";
$route['admin/questionaire-category/ajax-get-category-by-questionaire-main/(:any)/(:num)'] = "questionaire/questionaire_category_back_controller/ajax_get_category_by_questionaire_main/$1/$2";

$route['admin/questionaire'] = "questionaire/questionaire_back_controller/index";
$route['admin/questionaire/show'] = "questionaire/questionaire_back_controller/show";
$route['admin/questionaire/add'] = "questionaire/questionaire_back_controller/add";
$route['admin/questionaire/edit/(:num)'] = "questionaire/questionaire_back_controller/edit/$1";
$route['admin/questionaire/save'] = "questionaire/questionaire_back_controller/save_and_publish";
$route['admin/questionaire/(:num)/export-excel'] = "questionaire/questionaire_back_controller/export_excel/$1";
$route['admin/questionaire/ajax-set-value/(:any)'] = "questionaire/questionaire_back_controller/ajax_set_value/$1";
$route['admin/questionaire/ajax-delete-one/(:any)'] = "questionaire/questionaire_back_controller/ajax_delete_one/$1";
$route['admin/questionaire/ajax-get-main-list/(:any)'] = "questionaire/questionaire_back_controller/ajax_get_main_list/$1";
$route['admin/questionaire/ajax-save-preview/(:any)'] = "questionaire/questionaire_back_controller/ajax_save_preview/$1";
$route['admin/questionaire/(:num)/preview/(:any)'] = "questionaire/questionaire_back_controller/preview_detail/$1/$2";
$route['admin/questionaire/status/'] = "questionaire/questionaire_back_controller/status";
$route['admin/questionaire/status/(:any)'] = "questionaire/questionaire_back_controller/status/$1";

/** Print Barcode */
$route['admin/print/add'] = "print/printbarcode_back_controller/add";
$route['admin/print/print-pdf'] = "print/printbarcode_back_controller/print_pdf";
$route['admin/print/print-next'] = "print/printbarcode_back_controller/print_barcode_next";
$route['admin/print/print-pdf-next'] = "print/printbarcode_back_controller/print_pdf_next";
$route['admin/print/print-card'] = "print/printbarcode_back_controller/print_card";
$route['admin/print/print-return-card'] = "print/printbarcode_back_controller/print_return_card";

/** Transaction */
$route['admin/transactions'] = "transaction/transaction_back_controller/index";
$route['admin/transactions/filter/borrowing'] = "transaction/transaction_back_controller/get_filter/borrowing";
$route['admin/transactions/filter/returning'] = "transaction/transaction_back_controller/get_filter/returning";
$route['admin/transactions/return'] = "transaction/transaction_back_controller/get_return";
$route['admin/transactions/no-return'] = "transaction/transaction_back_controller/get_no_return";
$route['admin/transactions/not-found'] = "transaction/transaction_back_controller/get_no_return/xyz";
$route['admin/transactions/member'] = "transaction/transaction_back_controller/index";
$route['admin/transactions/member/(:num)'] = "transaction/transaction_back_controller/index/$1";
$route['admin/transactions/member/(:num)/filter/borrowing'] = "transaction/transaction_back_controller/get_filter/borrowing/$1";
$route['admin/transactions/member/(:num)/filter/returning'] = "transaction/transaction_back_controller/get_filter/returning/$1";
$route['admin/transactions/member/(:num)/return'] = "transaction/transaction_back_controller/get_return/$1";
$route['admin/transactions/member/(:num)/no-return'] = "transaction/transaction_back_controller/get_no_return/$1";
// $route['admin/transactions/member/(:num)/no-return/extended'] = "transaction/transaction_back_controller/get_no_return/$1";
$route['admin/transactions/find-member'] = "transaction/transaction_back_controller/find_full_member_cid";
$route['admin/transactions/do-return/(:num)/(:num)/(:any)/(:any)'] = "transaction/transaction_back_controller/ajax_process_return/$1/$2/$3";
$route['admin/transactions/undo-return/(:num)/(:any)'] = "transaction/transaction_back_controller/ajax_process_undo_return/$1";
$route['admin/transactions/save'] = "transaction/transaction_back_controller/save";
$route['admin/transactions/ajax-get-transactions/(:any)/(:any)'] = "transaction/transaction_back_controller/ajax_get_transactions/$1";
$route['admin/transactions/ajax-get-transactions'] = "transaction/transaction_back_controller/ajax_get_transactions";
$route['admin/transactions/ajax-delete-transaction/(:num)/(:any)'] = "transaction/transaction_back_controller/ajax_delete_transaction/$1";
$route['admin/transactions/ajax-calculate-fee/(:num)/(:any)'] = "transaction/transaction_back_controller/ajax_calculate_fee/$1";
$route['admin/transactions/ajax-check-borrowing-status/(:any)'] = "transaction/transaction_back_controller/ajax_check_borrowing_status/$1";
$route['admin/transactions/ajax-save-income/(:any)'] = "transaction/transaction_back_controller/ajax_save_income/$1";
$route['admin/transactions/reserved'] = "transaction/transaction_back_controller/book_reserved";
$route['admin/transactions/reserved/(:num)/add'] = "transaction/transaction_back_controller/book_reserved_add/$1";
$route['admin/transactions/reserved/add'] = "transaction/transaction_back_controller/book_reserved_add";
$route['admin/transactions/reserved/save'] = "transaction/transaction_back_controller/book_reserved_save";
$route['admin/transactions/ajax-delete-book-reserved/(:num)/(:any)'] = "transaction/transaction_back_controller/ajax_delete_book_reserved/$1";
$route['admin/transactions/reserved/(:num)/edit'] = "transaction/transaction_back_controller/book_reserved_edit/$1";
$route['admin/transactions/reserved/(:num)'] = "transaction/transaction_back_controller/book_reserved/$1";
$route['admin/transactions/export-excel'] = "transaction/transaction_back_controller/export_excel";
$route['admin/transactions/ajax-get-biblio-info/(:any)/(:any)'] = "transaction/transaction_back_controller/ajax_get_biblio_info/$1";
$route['admin/transactions/ajax-get-members'] = "transaction/transaction_back_controller/ajax_get_members";
$route['admin/transactions/ajax-get-biblio-copies'] = "transaction/transaction_back_controller/ajax_get_biblio_copies";
$route['admin/transactions/ajax-renew-transaction/(:num)/(:any)'] = "transaction/transaction_back_controller/ajax_renew_transaction/$1";
$route['admin/transactions/ajax-send-mail-overdue/(:any)'] = "transaction/transaction_back_controller/ajax_send_mail_overdue/$1";

// $route['admin/members'] = "member/member_back_controller/index";
// $route['admin/members/add'] = "member/member_back_controller/add";
// $route['admin/members/(:num)/edit'] = "member/member_back_controller/edit/$1";
// $route['admin/members/save'] = "member/member_back_controller/save";
$route['admin/members/ajax-get-info/(:any)/(:any)'] = "member/member_back_controller/ajax_get_info/$1";
// $route['admin/members/ajax-set-got-membercard/(:num)/(:num)'] = "member/member_back_controller/ajax_set_got_membercard/$1/$2";
$route['admin/members/ajax-update-remark/(:num)/(:any)'] = "member/member_back_controller/ajax_update_remark/$1/$2";
$route['admin/members/ajax-update-remark/(:num)'] = "member/member_back_controller/ajax_update_remark/$1";
$route['admin/members/ajax-update-remark'] = "member/member_back_controller/ajax_update_remark";
$route['admin/members/ajax-delete-member/(:num)/(:any)'] = "member/member_back_controller/ajax_delete_member/$1";
$route['admin/members/do-blacklist/(:num)/(:num)'] = "member/member_back_controller/ajax_do_blacklist/$1/$2";
// $route['admin/members/ajax-do-extend-membership'] = "member/member_back_controller/ajax_do_extend_membership";
$route['admin/members/ajax-load'] = "member/member_back_controller/ajax_get_members";
// $route['admin/members/ajax-load-autocomplete'] = "member/member_back_controller/ajax_get_members_autocomplete";

$route['admin/holiday'] = "holiday/holiday_back_controller/show";
$route['admin/holiday/(:num)/edit'] = "holiday/holiday_back_controller/edit/$1";
$route['admin/holiday/add'] = "holiday/holiday_back_controller/add";
$route['admin/holiday/edit/(:num)'] = "holiday/holiday_back_controller/edit/$1";
$route['admin/holiday/save'] = "holiday/holiday_back_controller/save";
$route['admin/holiday/ajax-get-main-list/(:any)'] = "holiday/holiday_back_controller/ajax_get_main_list/$1";
$route['admin/holiday/ajax-delete-one/(:any)'] = "holiday/holiday_back_controller/ajax_delete_one/$1";
$route['admin/holiday/ajax-save-weekend'] = "holiday/holiday_back_controller/ajax_save_weekend_holidays";

$route['admin/order'] = "order/order_back_controller/index";
$route['admin/order/show'] = "order/order_back_controller/show";
$route['admin/order/add'] = "order/order_back_controller/add";
$route['admin/order/edit/(:num)'] = "order/order_back_controller/edit/$1";
$route['admin/order/save'] = "order/order_back_controller/save";
$route['admin/order/ajax-set-value/(:any)'] = "order/order_back_controller/ajax_set_value/$1";
$route['admin/order/ajax-delete-one/(:any)'] = "order/order_back_controller/ajax_delete_one/$1";
$route['admin/order/ajax-get-main-list/(:any)'] = "order/order_back_controller/ajax_get_main_list/$1";
$route['admin/order/status/'] = "order/order_back_controller/status";
$route['admin/order/status/(:any)'] = "order/order_back_controller/status/$1";
$route['admin/order/ajax-get-order/(:any)/(:num)'] = "order/order_back_controller/ajax_get_product_main/$1/$2";

/** Report **/
$route['report-publisher/summary/ajax-get-summary-list/(:any)/(:any)'] = "report/report_publisher_back_controller/ajax_get_summary_list/$1/$2";
$route['report-publisher/summary'] = "report/report_publisher_back_controller/summary";

$route['purchase-history/summary/ajax-get-summary-list/(:any)/(:any)'] = "report/report_member_controller/ajax_get_summary_list/$1/$2";
$route['purchase-history/summary'] = "report/report_member_controller/summary";
$route['purchase-history/invoice-detail/(:any)'] = "report/report_member_controller/invoice_detail/$1";

$route['admin/report/publisher-summary/ajax-get-publisher-summary-list/(:any)/(:any)'] = "report/report_admin_controller/ajax_get_publisher_summary_list/$1/$2";
$route['admin/report/publisher-summary'] = "report/report_admin_controller/publisher_summary";

$route['admin/report/issue-summary/ajax-get-issue-summary-list/(:any)/(:any)'] = "report/report_admin_controller/ajax_get_issue_summary_list/$1/$2";
$route['admin/report/issue-summary/(:num)'] = "report/report_admin_controller/issue_summary/$1";
$route['admin/report/issue-summary'] = "report/report_admin_controller/issue_summary";

$route['admin/report/biblio/ajax-get-biblio-list/(:any)/(:any)'] = "report/report_biblio_controller/ajax_get_biblio_list/$1/$2";
$route['admin/report/biblio/(:num)'] = "report/report_biblio_controller/biblio/$1";
$route['admin/report/biblio'] = "report/report_biblio_controller/biblio";

$route['admin/report/biblio-log/ajax-get-biblio-log-list/(:any)/(:any)'] = "report/report_biblio_controller/ajax_get_biblio_log_list/$1/$2";
$route['admin/report/biblio-log/(:num)'] = "report/report_biblio_controller/biblio_log/$1";
$route['admin/report/biblio-log'] = "report/report_biblio_controller/biblio_log";

$route['admin/report/user-log/export-user-log'] = "report/report_user_controller/export_user_log";
$route['admin/report/user-log/ajax-get-user-log-list/(:any)/(:any)'] = "report/report_user_controller/ajax_get_user_log_list/$1/$2";
$route['admin/report/user-log/(:num)'] = "report/report_user_controller/user_log/$1";
$route['admin/report/user-log'] = "report/report_user_controller/user_log";

$route['admin/report/reserve-log/export-reserve-log'] = "report/report_reserve_controller/export_reserve_log";
$route['admin/report/reserve-log/ajax-get-reserve-log-list/(:any)/(:any)'] = "report/report_reserve_controller/ajax_get_reserve_log_list/$1/$2";
$route['admin/report/reserve-log/(:num)'] = "report/report_reserve_controller/reserve_log/$1";
$route['admin/report/reserve-log'] = "report/report_reserve_controller/reserve_log";

$route['admin/report/access-(:any)-log/export-access-product-log'] = "report/report_access_product_controller/export_access_product_log/$1";
$route['admin/report/access-(:any)-log/ajax-get-access-product-log-list/(:any)/(:any)'] = "report/report_access_product_controller/ajax_get_access_product_log_list/$1/$2/$3";
$route['admin/report/access-(:any)-log/(:num)'] = "report/report_access_product_controller/access_product_log/$1/$2";
$route['admin/report/access-(:any)-log'] = "report/report_access_product_controller/access_product_log/$1";

$route['admin/report/shelf-download-log/export-download-log'] = "report/report_shelf_download_controller/export_download_log";
$route['admin/report/shelf-download-log/ajax-get-shelf-download-log-list/(:any)/(:any)'] = "report/report_shelf_download_controller/ajax_get_shelf_download_log_list/$1/$2";
$route['admin/report/shelf-download-log/(:num)'] = "report/report_shelf_download_controller/shelf_download_log/$1";
$route['admin/report/shelf-download-log'] = "report/report_shelf_download_controller/shelf_download_log";

$route['admin/report/user-download-log/export-user-download-log'] = "report/report_user_download_controller/export_user_download_log";
$route['admin/report/user-download-log/ajax-get-user-download-log-list/(:any)/(:any)'] = "report/report_user_download_controller/ajax_get_user_download_log_list/$1/$2";
$route['admin/report/user-download-log/(:num)'] = "report/report_user_download_controller/user_download_log/$1";
$route['admin/report/user-download-log'] = "report/report_user_download_controller/user_download_log";

$route['admin/report/top-reader-log/export-top-reader-log'] = "report/report_top_reader_controller/export_top_reader_log";
$route['admin/report/top-reader-log/ajax-get-top-reader-log-list/(:any)/(:any)'] = "report/report_top_reader_controller/ajax_get_top_reader_log_list/$1/$2";
$route['admin/report/top-reader-log/(:num)'] = "report/report_top_reader_controller/top_reader_log/$1";
$route['admin/report/top-reader-log'] = "report/report_top_reader_controller/top_reader_log";

$route['admin/report/news-log/export-news-log'] = "report/report_news_controller/export_news_log";
$route['admin/report/news-log/ajax-get-news-log-list/(:any)/(:any)'] = "report/report_news_controller/ajax_get_news_log_list/$1/$2";
$route['admin/report/news-log/(:num)'] = "report/report_news_controller/news_log/$1";
$route['admin/report/news-log'] = "report/report_news_controller/news_log";


$route['admin/report/event/ajax-get-event-list/(:any)/(:any)'] = "report/report_event_controller/ajax_get_event_list/$1/$2";
$route['admin/report/event/(:num)'] = "report/report_event_controller/event/$1";
$route['admin/report/event'] = "report/report_event_controller/event";

$route['admin/export/export-all/export-to-excel'] = "export/export_all_controller/export_all";
$route['admin/export/export-all/(:num)'] = "export/export_all_controller/show/$1";
$route['admin/export/export-all'] = "export/export_all_controller/show";

/** Log **/
$route['admin/log'] = "log/log_back_controller/show";
$route['admin/log/ajax-get-main-list/(:any)'] = "log/log_back_controller/ajax_get_main_list/$1";

/** Ads */
$route['admin/ads'] = "ads/ads_back_controller/index";
$route['admin/ads/show'] = "ads/ads_back_controller/show";
$route['admin/ads/add'] = "ads/ads_back_controller/add";
$route['admin/ads/edit/(:num)'] = "ads/ads_back_controller/edit/$1";
$route['admin/ads/save'] = "ads/ads_back_controller/save";
$route['admin/ads/ajax-set-value/(:any)'] = "ads/ads_back_controller/ajax_set_value/$1";
$route['admin/ads/ajax-delete-one/(:any)'] = "ads/ads_back_controller/ajax_delete_one/$1";
$route['admin/ads/ajax-get-main-list/(:any)'] = "ads/ads_back_controller/ajax_get_main_list/$1";
$route['admin/ads/status/'] = "ads/ads_back_controller/status";
$route['admin/ads/status/(:any)'] = "ads/ads_back_controller/status/$1";

$route['admin/ads-category'] = "ads/ads_category_back_controller/index";
$route['admin/ads-category/show'] = "ads/ads_category_back_controller/show";
$route['admin/ads-category/add'] = "ads/ads_category_back_controller/add";
$route['admin/ads-category/edit/(:num)'] = "ads/ads_category_back_controller/edit/$1";
$route['admin/ads-category/save'] = "ads/ads_category_back_controller/save";
$route['admin/ads-category/ajax-set-value/(:any)'] = "ads/ads_category_back_controller/ajax_set_value/$1";
$route['admin/ads-category/ajax-delete-one/(:any)'] = "ads/ads_category_back_controller/ajax_delete_one/$1";
$route['admin/ads-category/ajax-get-main-list/(:any)'] = "ads/ads_category_back_controller/ajax_get_main_list/$1";
$route['admin/ads-category/status/'] = "ads/ads_category_back_controller/status";
$route['admin/ads-category/status/(:any)'] = "ads/ads_category_back_controller/status/$1";

/** Redeem */
$route['redeem/point'] = "redeem/redeem_front_controller/redeem_point/$1";

$route['admin/redeem'] = "redeem/redeem_back_controller/index";
$route['admin/redeem/show'] = "redeem/redeem_back_controller/show";
$route['admin/redeem/add'] = "redeem/redeem_back_controller/add";
$route['admin/redeem/edit/(:num)'] = "redeem/redeem_back_controller/edit/$1";
$route['admin/redeem/save'] = "redeem/redeem_back_controller/save";
$route['admin/redeem/ajax-set-value/(:any)'] = "redeem/redeem_back_controller/ajax_set_value/$1";
$route['admin/redeem/ajax-delete-one/(:any)'] = "redeem/redeem_back_controller/ajax_delete_one/$1";
$route['admin/redeem/ajax-get-main-list/(:any)'] = "redeem/redeem_back_controller/ajax_get_main_list/$1";
$route['admin/redeem/status/'] = "redeem/redeem_back_controller/status";
$route['admin/redeem/status/(:any)'] = "redeem/redeem_back_controller/status/$1";

$route['admin/redeem/edit/(:num)/detail'] = "redeem/redeem_detail_back_controller/show/$1";
$route['admin/redeem/edit/(:num)/detail/add'] = "redeem/redeem_detail_back_controller/add/$1";
$route['admin/redeem/edit/(:num)/detail/edit/(:num)'] = "redeem/redeem_detail_back_controller/edit/$1/$2";
$route['admin/redeem/edit/(:num)/detail/status'] = "redeem/redeem_detail_back_controller/status/$1";
$route['admin/redeem/edit/(:num)/detail/status/(:any)'] = "redeem/redeem_detail_back_controller/status/$1/$2";
$route['admin/redeem-detail/ajax-set-value/(:any)'] = "redeem/redeem_detail_back_controller/ajax_set_value/$1";
$route['admin/redeem-detail/ajax-get-main-list/(:any)'] = "redeem/redeem_detail_back_controller/ajax_get_main_list/$1";
$route['admin/redeem-detail/save'] = "redeem/redeem_detail_back_controller/save";
$route['admin/redeem-detail/ajax-delete-one/(:any)'] = "redeem/redeem_detail_back_controller/ajax_delete_one/$1";


/** Banner */
$route['admin/banner'] = "banner/banner_back_controller/index";
$route['admin/banner/show'] = "banner/banner_back_controller/show";
$route['admin/banner/add'] = "banner/banner_back_controller/add";
$route['admin/banner/edit/(:num)'] = "banner/banner_back_controller/edit/$1";
$route['admin/banner/save'] = "banner/banner_back_controller/save";
$route['admin/banner/ajax-set-value/(:any)'] = "banner/banner_back_controller/ajax_set_value/$1";
$route['admin/banner/ajax-delete-one/(:any)'] = "banner/banner_back_controller/ajax_delete_one/$1";
$route['admin/banner/ajax-get-main-list/(:any)'] = "banner/banner_back_controller/ajax_get_main_list/$1";
$route['admin/banner/status/'] = "banner/banner_back_controller/status";
$route['admin/banner/status/(:any)'] = "banner/banner_back_controller/status/$1";


/** Web Service **/
$route['webservice/remove_expire_ebook_from_shelf'] = "webservice/check_controller/remove_expire_ebook_from_shelf";

$route['webservice/get_product_list'] = "webservice/webservice_product_controller/get_product_list";
$route['webservice/get_product_detail'] = "webservice/webservice_product_controller/get_product_detail";

$route['webservice/get_product_main_list'] = "webservice/webservice_product_main_controller/get_product_main_list";
$route['webservice/get_product_category_list'] = "webservice/webservice_product_category_controller/get_product_category_list";

$route['webservice/get_mybookshelf'] = "webservice/webservice_shelf_controller/get_mybookshelf";
$route['webservice/get_history'] = "webservice/webservice_shelf_controller/get_shelf_history";
$route['webservice/add_book_to_mybookshelf'] = "webservice/webservice_shelf_controller/add_book_to_mybookshelf";
$route['webservice/remove_book_from_mybookshelf'] = "webservice/webservice_shelf_controller/remove_book_from_mybookshelf";
$route['webservice/check_book_in_mybookshelf'] = "webservice/webservice_shelf_controller/check_book_in_mybookshelf";
$route['webservice/check_mybookshelf_license'] = "webservice/webservice_shelf_controller/check_mybookshelf_license";

$route['webservice/get_mybookshelf_vdo'] = "webservice/webservice_shelf_vdo_controller/get_mybookshelf_vdo";
//$route['webservice/get_vdo_history'] = "webservice/webservice_shelf_vdo_controller/get_shelf_vdo_history";
$route['webservice/add_vdo_to_mybookshelf'] = "webservice/webservice_shelf_vdo_controller/add_vdo_to_mybookshelf";
$route['webservice/remove_vdo_from_mybookshelf'] = "webservice/webservice_shelf_vdo_controller/remove_vdo_from_mybookshelf";
//$route['webservice/check_vdo_in_mybookshelf'] = "webservice/webservice_shelf_controller/check_book_in_mybookshelf";

$route['webservice/login'] = "webservice/webservice_user_controller/login";
$route['webservice/login_ad'] = "webservice/webservice_user_controller/login_ad";
$route['webservice/logout'] = "webservice/webservice_user_controller/logout";
$route['webservice/create_user'] = "webservice/webservice_user_controller/create_user";
$route['webservice/get_mypoint'] = "webservice/webservice_user_controller/get_mypoint";
$route['webservice/edit_my_profile'] = "webservice/webservice_user_controller/edit_my_profile";

$route['webservice/get_publisher_list'] = "webservice/webservice_publisher_controller/get_publisher_list";

$route['webservice/news/get_all_categories'] = "webservice/webservice_news_controller/get_all_categories";
$route['webservice/news/get_news'] = "webservice/webservice_news_controller/get_news";
$route['webservice/news/get_news_detail'] = "webservice/webservice_news_controller/get_news_detail";
$route['webservice/news/get_news_photo_gallery'] = "webservice/webservice_news_controller/get_news_photo_gallery";
$route['webservice/news/wow_news'] = "webservice/webservice_news_controller/wow_news";
$route['webservice/news/cheer_news'] = "webservice/webservice_news_controller/cheer_news";
$route['webservice/news/thanks_news'] = "webservice/webservice_news_controller/thanks_news";
$route['webservice/news/set_news_status'] = "webservice/webservice_news_controller/set_news_status";
$route['webservice/news/add_news'] = "webservice/webservice_news_controller/add_news";
$route['webservice/news/update_news'] = "webservice/webservice_news_controller/update_news";
$route['webservice/news/delete_news'] = "webservice/webservice_news_controller/delete_news";
$route['webservice/news/get_comments'] = "webservice/webservice_news_controller/get_comments";
$route['webservice/news/add_comment'] = "webservice/webservice_news_controller/add_comment";
$route['webservice/news/delete_comment'] = "webservice/webservice_news_controller/delete_comment";
$route['webservice/news/wow_comment'] = "webservice/webservice_news_controller/wow_comment";
$route['webservice/news/set_comment_status'] = "webservice/webservice_news_controller/set_comment_status";
$route['webservice/news/get_list_popular'] = "webservice/webservice_news_controller/get_list_popular";
$route['webservice/news/get_list_top_commenters'] = "webservice/webservice_news_controller/get_list_top_commenters";
$route['webservice/news/get_list_recommended'] = "webservice/webservice_news_controller/get_list_recommended";
$route['webservice/news/get_list_talk_of_the_town'] = "webservice/webservice_news_controller/get_list_talk_of_the_town";
$route['webservice/news/get_who_wow_news'] = "webservice/webservice_news_controller/get_who_wow_news";
$route['webservice/news/get_who_cheer_news'] = "webservice/webservice_news_controller/get_who_cheer_news";
$route['webservice/news/get_who_thanks_news'] = "webservice/webservice_news_controller/get_who_thanks_news";
$route['webservice/news/get_who_comment_news'] = "webservice/webservice_news_controller/get_who_comment_news";
$route['webservice/news/get_who_wow_comment'] = "webservice/webservice_news_controller/get_who_wow_comment";

$route['webservice/news/add_news_photo_gallery'] = "webservice/webservice_news_controller/add_news_photo_gallery";
$route['webservice/news/delete_news_photo_gallery'] = "webservice/webservice_news_controller/delete_news_photo_gallery";

$route['webservice/event/get_all_categories'] = "webservice/webservice_event_controller/get_all_categories";
$route['webservice/event/get_incoming_events'] = "webservice/webservice_event_controller/get_incoming_events";
$route['webservice/event/get_all_events_by_period'] = "webservice/webservice_event_controller/get_all_events_by_period";
$route['webservice/event/get_event_detail'] = "webservice/webservice_event_controller/get_event_detail";
$route['webservice/event/accept_invitation'] = "webservice/webservice_event_controller/accept_invitation";

$route['webservice/contact/get_contact_topic'] = "webservice/webservice_contact_controller/get_contact_topic";
$route['webservice/contact/save_contact'] = "webservice/webservice_contact_controller/save_contact";

$route['admin/device-message'] = "device/device_message_back_controller/index";
$route['admin/device-message/show'] = "device/device_message_back_controller/show";
$route['admin/device-message/add'] = "device/device_message_back_controller/add";
$route['admin/device-message/edit/(:num)'] = "device/device_message_back_controller/edit/$1";
$route['admin/device-message/save'] = "device/device_message_back_controller/save";
$route['admin/device-message/ajax-set-value/(:any)'] = "device/device_message_back_controller/ajax_set_value/$1";
$route['admin/device-message/ajax-delete-one/(:any)'] = "device/device_message_back_controller/ajax_delete_one/$1";
$route['admin/device-message/ajax-get-main-list/(:any)'] = "device/device_message_back_controller/ajax_get_main_list/$1";
$route['admin/device-message/status/'] = "device/device_message_back_controller/status";
$route['admin/device-message/status/(:any)'] = "device/device_message_back_controller/status/$1";
$route['admin/device-message/ajax-send-message/(:any)'] = "device/device_message_back_controller/ajax_send_message/$1";
$route['admin/device-message/ajax-send-message-with-test-pem/(:any)'] = "device/device_message_back_controller/ajax_send_message_with_test_pem/$1";
$route['webservice/device/get_device_checkin'] = "webservice/webservice_device_controller/get_device_checkin";
$route['webservice/device/check_device_register'] = "webservice/webservice_device_controller/check_device_register";
$route['webservice/device/register_device'] = "webservice/webservice_device_controller/register_device";

$route['webservice/request_download'] = "webservice/webservice_product_controller/request_download";

$route['webservice/add-book-and-book-copy'] = "webservice/webservice_product_controller/add_book_and_book_copy";
$route['webservice/add-magazine-and-magazine-copy'] = "webservice/webservice_product_controller/add_magazine_and_magazine_copy";
$route['webservice/add-marc/(:num)/(:num)/(:num)'] = "webservice/webservice_product_controller/add_marc/$1/$2/$3";
/***chack_bookdose****/
$route['webservice/check_bookdose'] = "webservice/webservice_user_controller/check_bookdose";


$route['webservice/search'] = "webservice/webservice_product_controller/search";

/** Transaction */
$route['my-transaction'] = "transaction/transaction_front_controller/my_transaction/shelf/date_d/1";
$route['my-transaction/sort-(:any)'] = "transaction/transaction_front_controller/my_transaction/shelf/$1/1";
$route['my-transaction/page-(:num)'] = "transaction/transaction_front_controller/my_transaction/shelf/date_d/$1";
$route['my-transaction/sort-(:any)/page-(:num)'] = "transaction/transaction_front_controller/my_transaction/shelf/$1/$2";

$route['my-transaction-list'] = "transaction/transaction_front_controller/my_transaction/list/date_d/1";
$route['my-transaction-list/sort-(:any)'] = "transaction/transaction_front_controller/my_transaction/list/$1/1";
$route['my-transaction-list/page-(:num)'] = "transaction/transaction_front_controller/my_transaction/list/date_d/$1";
$route['my-transaction-list/sort-(:any)/page-(:num)'] = "transaction/transaction_front_controller/my_transaction/list/$1/$2";

$route['admin/transaction'] = "transaction/transaction_back_controller/index";
$route['admin/transaction/show'] = "transaction/transaction_back_controller/show";
$route['admin/transaction/add'] = "transaction/transaction_back_controller/add";
$route['admin/transaction/user/(:num)'] = "transaction/transaction_back_controller/user/$1";
$route['admin/transaction/edit/(:num)'] = "transaction/transaction_back_controller/edit/$1";
$route['admin/transaction/save'] = "transaction/transaction_back_controller/save";
$route['admin/transaction/ajax-set-value/(:any)'] = "transaction/transaction_back_controller/ajax_set_value/$1";
$route['admin/transaction/ajax-delete-one/(:any)'] = "transaction/transaction_back_controller/ajax_delete_one/$1";
$route['admin/transaction/ajax-get-main-list/(:any)'] = "transaction/transaction_back_controller/ajax_get_main_list/$1";
$route['admin/transaction/status/'] = "transaction/transaction_back_controller/status";
$route['admin/transaction/status/(:any)'] = "transaction/transaction_back_controller/status/$1";
$route['admin/transaction/ajax-get-transaction-list-by-user/(:any)'] = "transaction/transaction_back_controller/ajax_get_transaction_list_by_user/$1";
$route['admin/transaction/ajax-add-product-to-transaction-by-user/(:any)'] = "transaction/transaction_back_controller/ajax_add_product_to_transaction_by_user/$1";
$route['admin/transaction/ajax-remove-product-to-transaction-by-user/(:any)'] = "transaction/transaction_back_controller/ajax_remove_product_to_transaction_by_user/$1";
$route['admin/transaction/ajax-clear-product-to-transaction-by-user/(:any)'] = "transaction/transaction_back_controller/ajax_clear_product_to_transaction_by_user/$1";
$route['admin/transaction/ajax-change-due-date-product-to-transaction-by-user/(:any)'] = "transaction/transaction_back_controller/ajax_change_due_date_product_to_transaction_by_user/$1";
$route['admin/transaction/ajax-save-product-transaction-by-user/(:any)'] = "transaction/transaction_back_controller/ajax_save_product_transaction_by_user/$1";


/**report library**/
$route['admin/top_reader'] = "p_report/topread_back_controller/book";
$route['admin/top_reader/export'] = "p_report/topread_back_controller/exportbook";

$route['admin/top_borrow'] = "p_report/topread_back_controller/topborrow";
$route['admin/top_borrow/export'] = "p_report/topread_back_controller/exporttopborrow";

$route['admin/top_most_popular_categories'] = "p_report/topread_back_controller/top_most_popular_categories";
$route['admin/top_most_popular_categories/export'] = "p_report/topread_back_controller/export_top_most_popular_categories";

$route['admin/top_most_popular_item'] = "p_report/topread_back_controller/top_most_popular_item";
$route['admin/top_most_popular_item/ajax'] = "p_report/topread_back_controller/ajax_category";
$route['admin/top_most_popular_item/export'] = "p_report/topread_back_controller/export_top_most_popular_item";

$route['admin/cataloging_summary'] = "p_report/topread_back_controller/cataloging_summary";
$route['admin/cataloging_summary/export'] = "p_report/topread_back_controller/exportcataloging_summary";

$route['admin/circulation_summary'] = "p_report/topread_back_controller/circulation_summary";
$route['admin/circulation_summary/export'] = "p_report/topread_back_controller/exportcirculation_summary";

$route['admin/overdue_item'] = "p_report/topread_back_controller/overdue_item";
$route['admin/overdue_item/export'] = "p_report/topread_back_controller/exportoverdue_item";

$route['admin/member_usage'] = "p_report/topread_back_controller/member_usage";
$route['admin/member_usage/export'] = "p_report/topread_back_controller/exportmember_usage_item";

$route['admin/getdatauser'] = "p_report/topread_back_controller/getdatauser";
//$route['admin/overdue_item/export'] = "p_report/topread_back_controller/exportoverdue_item";

$route['admin/not_borrow_item'] = "p_report/topread_back_controller/not_borrow_item";
$route['admin/not_borrow_item/export'] = "p_report/topread_back_controller/exportnot_borrow_item";

$route['admin/new_item'] = "p_report/topread_back_controller/new_item";
$route['admin/new_item/export'] = "p_report/topread_back_controller/exportnew_item";
/**report library**/


/** E-Content Transfer **/
$route['admin/econtent_transfer'] = "econtent_transfer/econtent_transfer_controller/index";


//Cron Job
$route['update_all_data'] = "product/product_init_controller/update_all_product_view";
$route['cron_hourly'] = "webservice/check_controller/cron_hourly";
$route['cron_mail_daily'] = "webservice/check_controller/cron_mail_daily";

//Test Zone
$route['_test/mail/test_send_mail'] = "test/test_controller/test_send_mail";
$route['_test/created_folder_for_biblio'] = "test/test_controller/created_folder_for_biblio";
$route['_test/test_paypal'] = "test/test_controller/test_paypal";
$route['_test/test_convert_img_2_base64'] = "test/test_controller/test_convert_img_2_base64";
$route['_test/phpinfo'] = "test/test_controller/phpinfo";



// This must be place in the lasted line
$route[CONST_STATIC_PAGE_PATH.'(:any)'] = CONST_PROJECT_CODE."/static_controller/index/$1";


/* End of file routes.php */
/* Location: ./application/config/routes.php */