<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['your-library'] = "project_".CONST_PROJECT_CODE."/home_controller/your_library";
$route['library-today'] = "project_".CONST_PROJECT_CODE."/home_controller/home";
$route['all-categories'] = "project_".CONST_PROJECT_CODE."/home_controller/all_categories";
$route['ask-librarian'] = "project_".CONST_PROJECT_CODE."/home_controller/contact_form";
$route['ask-librarian/(:any)'] = "project_".CONST_PROJECT_CODE."/home_controller/contact_form/$1";
$route['ask-librarian-save'] = "project_".CONST_PROJECT_CODE."/home_controller/contact_save/";

$route['show-knowledge-resources'] = "project_".CONST_PROJECT_CODE."/home_controller/show_data_subscription/";
$route['show-online-book'] = "project_".CONST_PROJECT_CODE."/home_controller/show_online_book/";
$route['privacy-and-policy'] = "project_".CONST_PROJECT_CODE."/home_controller/privacy_and_policy/";