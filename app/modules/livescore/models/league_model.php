<?php



/**

* League Model
*
* Manages Leagues
*
* @author Weblight.ro

* @copyright Weblight.ro

* @package BJ Tool



**/



	class League_model extends CI_Model

	{

		private $CI;

	

		function __construct()

		{

			parent::__construct();

		

			$this->CI =& get_instance();

		}

           
      /**
	* Get League
	*
	* @param int $id
	* 
	* @return array 
	*/
	function get_league($id) 
        {
		
                    $row = array();								
                    $this->db->where('id_league',$id);
                    $result = $this->db->get('z_leagues');

                    foreach ($result->result_array() as $row) {
                            return $row;
                    }

                    return $row;	
	}
        
         
        
	 /**
	* Get Leagues
	*
	* 
	* @return array 
	*/
        
	function get_leagues($filters = array()) 
        {
            

                $result = $this->db->get('z_leagues');
	

                if (isset($filters['id_league'])) {

			$this->db->where('ID_league', $filters['id_league']);

		}

		if (isset($filters['limit'])) {
                            $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
                            $this->db->limit($filters['limit'], $offset);
                    }
		
                                
               
		if ($result->num_rows() == 0) {
			return FALSE;
		}
                
                
		$leagues = array();

                foreach ($result->result_array() as $league) {
                    
                    $leagues[] = array(
                                        'ID_league' => $league['ID_league'],
					'league_name' => $league['league_name'],
				     );
		}
	
		return $leagues;

	}      
            
    function num_rows_leagues($filters = array()) {
             if (isset($filters['id'])) {

			$this->db->where('ID_leagues', $filters['id']);

		}

		                
                $result = $this->db->get('z_leagues');
                
                
                return $result->num_rows();
        }         
  
         /**
	* Create New league
	*
	*
	* @return int $league_name
	*/
	function new_league ($league_name) 
        {
		$insert_fields = array(
							'league_name' => $league_name,
			
						);
						
		$this->db->insert('z_leagues', $insert_fields);

		return TRUE;
	}
          
        
        /**
	* Update League
	*
	* @return void
	*/
	function update_league ( $id_league,$league_name) 
        {
		$update_fields = array(
							'league_name' => $league_name,
						);

		$this->db->update('z_leagues', $update_fields, array('ID_league' => $id_league));
		
								
		return TRUE;
	}
        
        
        /**
	* Delete Leagues
	*
	* 
	* @return array 
	*/
            function delete_league ($id) 
            {
                    $this->db->delete('z_leagues',array('id_league' => $id));

                    return TRUE;
            }
        

}

