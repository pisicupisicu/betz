<?php

/**
* Strategy Model
*
* Manages strategies
*
* @author Weblight.ro
* @copyright Weblight.ro
* @package BJ Tool

*/

	class Strategy_model extends CI_Model
	{
		private $CI;
	
		function __construct()
		{
			parent::__construct();
		
			$this->CI =& get_instance();
		}
	
        /**
	* Get strategies
	* @param array $params
	*
	* @return array
	*/
	function get_strategies ($params) {
		$row = array();	
                if (isset($params['limit'])) {
                            $offset = (isset($params['offset'])) ? $params['offset'] : 0;
                            $this->db->limit($params['limit'], $offset);
                    }
		$result = $this->db->get('z_strategies');
				
		foreach ($result->result_array() as $linie) {
                        $linie['is_computed'] = $this->is_computed($linie['id']);
			$row[] = $linie;                        
		}
                
		return $row;														
	}
                
	/**
	* Get Strategy
	*
	* @param int $id	
	*
	* @return array
	*/
	function get_strategy ($id) {
		$row = array();								
		$this->db->where('id',$id);
		$result = $this->db->get('z_strategies');
				
		foreach ($result->result_array() as $row) {
			return $row;
		}
		
		return $row;														
	}
	
	/**
	* Create New Strategies
	*
	* Creates a new strategy
	*
	* @param array $insert_fields	
	*
	* @return int $insert_id
	*/
	function new_strategy ($insert_fields) {																										
		$this->db->insert('z_strategies', $insert_fields);		
		$insert_id = $this->db->insert_id();
										
		return $insert_id;
	}
	
	/**
	* Update Strategy
	*
	* Updates strategy
	* 
	* @param array $update_fields
	* @param int $id	
	*
	* @return boolean TRUE
	*/
	function update_strategy ($update_fields,$id) {																										
				
		$this->db->update('z_strategies',$update_fields,array('id' => $id));
										
		return TRUE;
	}
	
	/**
	* Delete strategy
	*
	* Deletes strategy
	* 	
	* @param int $id	
	*
	* @return boolean TRUE
	*/
	function delete_strategy ($id) {																										
				
		$this->db->delete('z_strategies',array('id' => $id));
										
		return TRUE;
	}
        
        /**
	* Is strategy computed
	*
	* 
	* 	
	* @param int $id strategy id	
	*
	* @return boolean
	*/
        function is_computed($id)
        {                            
            $this->db->where('strategy_id',$id);
            $result = $this->db->get('z_steps');
                
            return $result->num_rows() ? TRUE : FALSE;
        }
                
}
