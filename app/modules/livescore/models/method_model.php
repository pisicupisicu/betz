<?php

/**
* Method Model
*
* Manages method
*
* @author Weblight.ro
* @copyright Weblight.ro
* @package BJ Tool

**/

	class Method_model extends CI_Model
	{
		private $CI;
	
		function __construct()
		{
			parent::__construct();
		
			$this->CI =& get_instance();
		}
	
        /**
	* Get Method
	*
	* @param int $method_id
	* 
	* @return array 
	*/
	function get_method($id) 
        {
		
                    $row = array();								
                    $this->db->where('id_method',$id);
                    $result = $this->db->get('z_methods');

                    foreach ($result->result_array() as $row) {
                            return $row;
                    }

                    return $row;	
	}
      
	 /** Get Method Details by ID
	*
	* @param int $method_id
	* 
	* @return array 
	*/
	
	function get_method_by_id($id) 
        {
		
                    $row = array();								
                    $this->db->where('id_method',$id);
                    $result = $this->db->get('z_methods');

                    foreach ($result->result_array() as $row) {
                            $descriere[$row['ID_method']] = $row['method_description'];
                    }

                    return $descriere;	
	}
	  
	  
       
	 /**
	* Get Methods
	*
	* 
	* @return array 
	*/
        
	function get_methods ($filters = array()) 
        {
            

                
	

                if (isset($filters['ID_method'])) {

			$this->db->where('ID_method', $filters['ID_method']);

		}
                
                        
                if (isset($filters['method_name'])) {

			$this->db->like('method_name', $filters['method_name']);

		}
            
		if (isset($filters['limit'])) {
                            $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
                            $this->db->limit($filters['limit'], $offset);
                    }
		
                  $result = $this->db->get('z_methods');              
               
		if ($result->num_rows() == 0) {
			return FALSE;
		}
                
                
		$methods = array();
	

                foreach ($result->result_array() as $method) {
                    
                    $methods[] = array(
                                        'ID_method' => $method['ID_method'],
					'method_name' => $method['method_name'],
					'method_description' => $method['method_description'],
                                                        );
		}
		//print_r(array_values($methods));
	
		//var_dump ($methods);
		//die;
	
		return $methods;

	}
 
        /**
	* Get methods row
	*
	* 
	* @return array 
	*/
                
        function get_num_rows_methods($filters = array())
        {
                
                  if (isset($filters['ID_method'])) {

			$this->db->where('ID_method', $filters['ID_method']);

		}
                
                        
                if (isset($filters['method_name'])) {

			$this->db->like('method_name', $filters['method_name']);

		}
                 
                 
                $result = $this->db->get('z_methods');
                return $result->num_rows();        
        }
        
            
        /**
	* Create New Method
	*
	*
	* @return int $email_id
	*/
	function new_method ($method_name,$method_description) 
        {
		$insert_fields = array(
							'method_name' => $method_name,
							'method_description' => $method_description,								
						);
						
		$this->db->insert('z_methods', $insert_fields);

		return TRUE;
	}
          
        
        /**
	* Update Method
	*
	* @return void
	*/
	function update_method ( $id_method,$method_name,$method_description) 
        {
		$update_fields = array(
							'method_name' => $method_name,
							'method_description' => $method_description,
						);

		$this->db->update('z_methods', $update_fields, array('ID_method' => $id_method));
		
								
		return TRUE;
	}
        
        
        /**
	* Delete Methods
	*
	* 
	* @return array 
	*/
            function delete_method ($id) 
            {
                    $this->db->delete('z_methods',array('id_method' => $id));

                    return TRUE;
            }
			
}