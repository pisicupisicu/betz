<?php if (!defined('BASEPATH')) exit('No direct script access allowed');



/**

* Soccer stats Module Definition

*

* Declares the module, update code, etc.

*

* @author Weblight.ro

* @copyright Weblight.ro

* @package BJ Tool

*

*/



class Soccerstats extends Module {

	var $version = '1.0';

	var $name = 'soccerstats';



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

            $this->CI->admin_navigation->child_link('soccerstats',10,'Intro Stats',site_url('admincp/soccerstats/list_introstats'));     
			
			$this->CI->admin_navigation->child_link('soccerstats',20,'Bet vs. Favorite',site_url('admincp/soccerstats/list_against_favorite'));    

            $this->CI->admin_navigation->child_link('soccerstats',30,'Parse links',site_url('admincp/soccerstats/parse_links'));

            

	}

		

}

