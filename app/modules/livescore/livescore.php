<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* Livescore Module Definition
*
* Declares the module, update code, etc.
*
* @author Weblight.ro
* @copyright Weblight.ro
* @package BJ Tool
*
*/

class Livescore extends Module {
	var $version = '1.0';
	var $name = 'livescore';

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
            $this->CI->admin_navigation->child_link('livescore',10,'List competitions',site_url('admincp/livescore/list_competitions'));
            $this->CI->admin_navigation->child_link('livescore',20,'List teams',site_url('admincp/livescore/list_teams'));
            $this->CI->admin_navigation->child_link('livescore',30,'List matches',site_url('admincp/livescore/list_matches'));                        
            $this->CI->admin_navigation->child_link('livescore',40,'Bets List',site_url('admincp2/livescore/list_bets')); 
            $this->CI->admin_navigation->child_link('livescore',50,'Steps List',site_url('admincp2/livescore/list_strategies')); 
            $this->CI->admin_navigation->child_link('livescore',60,'Markets Types List',site_url('admincp2/livescore/list_markets')); 
            $this->CI->admin_navigation->child_link('livescore',70,'Events Types List',site_url('admincp2/livescore/list_leagues')); 
            $this->CI->admin_navigation->child_link('livescore',80,'Methods List',site_url('admincp2/livescore/list_methods'));
            $this->CI->admin_navigation->child_link('livescore',90,'Parse Matches',site_url('admincp3/livescore/parse_matches'));
            $this->CI->admin_navigation->child_link('livescore',100,'Stats',site_url('admincp4/livescore/profit_loss_stats'));
            
	}
		
}
