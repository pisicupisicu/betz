<?php if (!defined('BASEPATH')) exit('No direct script access allowed');



/**

* FLASHSCORE Module Definition

*

* Declares the module, update code, etc.

*

* @author Weblight.ro

* @copyright Weblight.ro

* @package BJ Tool

*

*/



class Flashscore extends Module {

	var $version = '1.0';

	var $name = 'flashscore';



	function __construct () {

		// set the active module

		$this->active_module = $this->name;	

		

		parent::__construct();

	}

	

	/*

	* Pre-admin function

	*

	* Initiate navigation in control panel

	*/

	function admin_preload ()

	{

         $this->CI->admin_navigation->child_link('flashscore',10,'Parse Form',site_url('admincp/flashscore/parse_form'));     
			
	 $this->CI->admin_navigation->child_link('flashscore',20,'List Matches',site_url('admincp/flashscore/list_matches'));    
     

	}

		

}

