<?php



/**

* Currency Model
*
* Manages Currency
*
* @author Weblight.ro

* @copyright Weblight.ro

* @package BJ Tool



**/



	class House_model extends CI_Model

	{

		private $CI;

	

		function __construct()

		{

			parent::__construct();

		

			$this->CI =& get_instance();

		}

           
      /**
	* Get House
	*
	* @param int $id
	* 
	* @return arraycurrency 
	*/
	function get_house($id) 
        {
		
                    $row = array();								
                    $this->db->where('id_house',$id);
                    $result = $this->db->get('z_houses');

                    foreach ($result->result_array() as $row) {
                            return $row;
                    }

                    return $row;	
	}
        
         
        
	 /**
	* Get houses
	*
	* 
	* @return array 
	*/
        
	function get_houses($filters = array()) 
        {

	

		if (isset($filters['id_house'])) {

		   $this->db->where('id_house', $filters['id_house']);

		   }

		if (isset($filters['limit'])) {
			$offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
			$this->db->limit($filters['limit'], $offset);
           }

        $result = $this->db->get('z_houses');      

		if ($result->num_rows() == 0) {
			return FALSE;
		}
        
		$houses = array();
        
        foreach ($result->result_array() as $house) {
                    
				$houses[] = array(
				 'id_house' => $house['id_house'],
				 'name_house' => $house['name_house'],
                 'logo_house' => $house['logo_house'],
				 'link_house' => $house['link_house'],
				 );
		}
	
		return $houses;

	}      
            
    function num_rows_houses($filters = array()) {
         
		 if (isset($filters['id'])) {

			$this->db->where('id_house', $filters['id']);

		  }
          
			$result = $this->db->get('z_houses');
			
			return $result->num_rows();
        }         
  
    /**
	* Create New house
	*
	*
	* @return true
	*/
	function new_house ($insert_fields) 
        {
						
		$this->db->insert('z_houses', $insert_fields);

		return TRUE;
	}
          
        
    /**
	* Update house
	*
	* @return true
	*/
	function update_house ($update_fields,$id_house) 
        {

		$this->db->update('z_houses', $update_fields, array('id_house' => $id_house));
		
								
		return TRUE;
	}
        
        
    /**
	* Delete house
	*
	* 
	* @return array 
	*/
            function delete_house ($id) 
            {
                    $this->db->delete('z_houses',array('id_house' => $id));

                    return TRUE;
            }
        

}

