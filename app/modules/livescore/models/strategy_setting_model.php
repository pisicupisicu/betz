<?php
	/**
	* Strategy Setting Model
	*
	* Manages Strategy Settings
	*
	* @author Weblight.ro
	* @copyright Weblight.ro
	* @package BJ Tool
	**/
	class Strategy_setting_model extends CI_Model
	{

		private $CI;
	
		function __construct()
		{

			parent::__construct();	
			$this->CI =& get_instance();
		}

           
	    /**
		* Get Strategy Setting
		*
		* @param int $id
		* 
		* @return array 
		*/
		function get_setting($id) 
	    {
			
	        $row = array();								
	        $this->db->where('id_setting',$id);
	        $result = $this->db->get('z_strategy_settings');

	        foreach ($result->result_array() as $row) {
	            return $row;
	        }

	        return $row;	
		}
        
         
        
		/**
		* Get Strategy Settings
		*
		* 
		* @return array 
		*/
	        
		function get_settings($filters = array()) 
	    {        
	        $result = $this->db->get('z_strategy_settings');
	        if (isset($filters['id_setting'])) {
				$this->db->where('id_setting', $filters['id_setting']);
			}

			if (isset($filters['limit'])) {
		        $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
		        $this->db->limit($filters['limit'], $offset);
		    }
		
	                                       
			if ($result->num_rows() == 0) {
				return FALSE;
			}
	            
	            
			$settings = array();

		    foreach ($result->result_array() as $sim) {
		        
		        $settings[] = array(
	                    'id_settings' 	=> $sim['id_settings'],
						'name' 			=> $sim['name'],
						'start' 		=> $sim['start'],
						'stake' 		=> $sim['stake'],
						'odds' 			=> $sim['odds'],						
		     	);
			}

			return $settings;

		}      
            
	    function num_rows_settings($filters = array()) 
	    {
	     	if (isset($filters['id_setting'])) {
				$this->db->where('id_setting', $filters['id_setting']);
			}
			                
	        $result = $this->db->get('z_strategy_settings');                                
	        return $result->num_rows();
	    }         
  
	 	/**
		* Create New Strategy Setting
		*
		*
		* @return int $id_setting
		*/
		function new_setting ($insert_fields) 
	    {							
			$this->db->insert('z_strategy_settings', $insert_fields);

			return TRUE;
		}
          
        
	    /**
		* Update Strategy Setting
		*
		* @return void
		*/
		function update_setting ($id_setting,$update_fields) 
	    {		
			$this->db->update('z_strategy_settings', $update_fields, array('id_setting' => $id_setting));
										
			return TRUE;
		}
        
        
	    /**
		* Delete Strategy Settings
		*
		* 
		* @return array 
		*/
	    function delete_setting ($id_setting) 
	    {
	        $this->db->delete('z_strategy_settings',array('id_setting' => $id_setting));

	        return TRUE;
	    }
        
}

