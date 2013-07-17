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
            $this->CI->admin_navigation->child_link('livescore',10,'Strategies',site_url('admincp2/livescore/list_strategies')); 
            $this->CI->admin_navigation->child_link('livescore',20,'List matches',site_url('admincp/livescore/list'));            
            $this->CI->admin_navigation->child_link('livescore',30,'Add matches',site_url('admincp/livescore/add'));                                   
            //$this->CI->admin_navigation->child_link('livescore',40,'Site-uri',site_url('admincp/livescore/lista_sites'));             
            //$this->CI->admin_navigation->child_link('livescore',50,'Import produse linkshare',site_url('admincp/livescore/import_produse'));
            //$this->CI->admin_navigation->child_link('livescore',60,'Export categorii presta',site_url('admincp/livescore/export_categorii'));
            //$this->CI->admin_navigation->child_link('livescore',70,'Export produse presta',site_url('admincp/livescore/export_produse'));
            //$this->CI->admin_navigation->child_link('livescore',80,'Purge produse linkshare',site_url('admincp/livescore/purge_produse'));
            //$this->CI->admin_navigation->child_link('livescore',90,'test',site_url('admincp/livsescore/test'));
	}
		
}
