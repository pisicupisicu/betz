<?php
	/**
	* Method Setting Model
	*
	* Manages Method Settings
	*
	* @author Weblight.ro
	* @copyright Weblight.ro
	* @package BJ Tool
	**/
	class Method_setting_model extends CI_Model
	{

		private $CI;
	
		function __construct()
		{

			parent::__construct();	
			$this->CI =& get_instance();
		}

           
	    /**
		* Get Method Setting
		*
		* @param int $id
		* 
		* @return array 
		*/
		function get_setting($id) 
	    {
			
	        $row = array();								
	        $this->db->where('id_setting',$id);	        
	        $result = $this->db->get('z_method_settings');

	        foreach ($result->result_array() as $row) {
	            return $row;
	        }

	        return $row;	
		}
        
       
       	function get_profit($id_setting)
       	{       		
	        $this->db->select('*,SUM(z_simulations.profit) AS profit,SUM(z_simulations.total_bets) AS total_bets,AVG(z_simulations.correct_percent) AS percent');
	        $this->db->where('id_setting', $id_setting);
	        $result = $this->db->get('z_simulations');
	        foreach ($result->result_array() as $sim) {
	        	return $sim;
	        }
	        
       	}  
        
		/**
		* Get Method Settings
		*
		* 
		* @return array 
		*/	        
		function get_settings($filters = array()) 
	    {        	        	        
	        if (isset($filters['display']) && $filters['display']) {
	        	$this->db->where('display', $filters['display']);
	        }
	        $this->db->join('z_methods','z_method_settings.id_method = z_methods.ID_method','inner');	        	        
	        $result = $this->db->get('z_method_settings');

			if (isset($filters['limit'])) {
		        $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
		        $this->db->limit($filters['limit'], $offset);
		    }
		
	                                       
			if ($result->num_rows() == 0) {
				return FALSE;
			}
	            
	            
			$settings = array();

		    foreach ($result->result_array() as $sim) {
		    	$temp = $this->get_profit($sim['id_setting']);		    	    			        
		        $settings[] = array(
		        		'id_setting' 			=> 	$sim['id_setting'],
	                    'id_method' 			=> 	$sim['id_method'],
						'method_name' 			=> 	$sim['method_name'],
						'min' 					=> 	$sim['min'],						
						'stake' 				=> 	$sim['stake'],
						'odds' 					=> 	$sim['odds'],
						'over'					=> 	$sim['over'],
						'alias'					=> 	$sim['alias'],
						'profit'				=> 	$temp['profit'],
						'percent'				=> 	number_format($temp['percent'], 2),
						'total_bets'			=>	$temp['total_bets'],												
		     	);
			}

			return $settings;

		}      
            
	    function get_num_rows($filters = array()) 
	    {
	     	if (isset($filters['id_setting'])) {
				$this->db->where('id_setting', $filters['id_setting']);
			}
			                
	        $result = $this->db->get('z_method_settings');                                
	        return $result->num_rows();
	    }         
  
	 	/**
		* Create New Method Setting
		*
		*
		* @return int $id_setting
		*/
		function new_setting ($insert_fields) 
	    {							
			$this->db->insert('z_method_settings', $insert_fields);

			return TRUE;
		}
          
        
	    /**
		* Update Method Setting
		*
		* @return void
		*/
		function update_setting ($id_setting,$update_fields) 
	    {		
			$this->db->update('z_method_settings', $update_fields, array('id_setting' => $id_setting));
										
			return TRUE;
		}
        
        
	    /**
		* Delete Method Settings
		*
		* 
		* @return array 
		*/
	    function delete_setting ($id_setting) 
	    {
	        $this->db->delete('z_method_settings',array('id_setting' => $id_setting));

	        return TRUE;
	    }
        
}

