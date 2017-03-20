<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/initcontroller.php");

class Welcome_controller extends Initcontroller {

	function __construct()
	{
		parent::__construct();
		$this->data["mode"] = "front";
		
		define("thisFrontTabMenu",'home');
		define("thisFrontSubMenu",'');
		@define("folderName",'welcome/');
		
	}
	
	function index()
	{
		$this->welcome();
	}
	
	function welcome()
	{
		@define("thisAction","welcome");
		$this->data["title"] = DEFAULT_TITLE;
		$this->load->view($this->default_theme_front . '/welcome/welcome', $this->data);
	}
		
	function intro()
	{
		$this->comming_soon();
	}
		
	function comming_soon()
	{
		@define("thisAction","comming_soon");
		$this->data["title"] = DEFAULT_TITLE;
		$this->load->view($this->default_theme_front . '/welcome/comming_soon', $this->data);
	}
	function privacy_and_policy()
	{
		@define("thisAction","privacy_and_policy");
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["page_title"] = 'Privacy Policy';
		$this->data["view_the_content"] = $this->default_theme_front . '/home/privacy_and_policy';
		$this->load->view($this->default_theme_front . '/tpl_full', $this->data);
	}
		
}

?>