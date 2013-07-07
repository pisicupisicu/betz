<?php

/**
* Competition Model
*
* Manages competitions
*
* @author Weblight.ro
* @copyright Weblight.ro
* @package BJ Tool

*/

	class Competition_model extends CI_Model
	{
		private $CI;
	
		function __construct()
		{
			parent::__construct();
		
			$this->CI =& get_instance();
		}
	
        /**
	* Get Competitions
	*
	*
	* @return array
	*/
	function get_competitions () {
		$row = array();										
		$result = $this->db->get('z_competition');
				
		foreach ($result->result_array() as $linie) {
			$row[] = $linie;
		}
                
		return $row;														
	}
			
}
