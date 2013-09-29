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
                    $this->db->where('league_id',$id);
                    $result = $this->db->get('s_leagues');

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
            

                $result = $this->db->get('s_leagues');
	

                if (isset($filters['league_id'])) {

			$this->db->where('league_id', $filters['league_id']);

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
                                        'league_id' => $league['league_id'],
					'league_name' => $league['league_name'],
				     );
		}
	
		return $leagues;

	}      
            
    function num_rows_leagues($filters = array()) {
             if (isset($filters['id'])) {

			$this->db->where('league_id', $filters['id']);

		}

		                
                $result = $this->db->get('s_leagues');
                
                
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
						
		$this->db->insert('s_leagues', $insert_fields);

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

		$this->db->update('s_leagues', $update_fields, array('league_id' => $id_league));
		
								
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
                    $this->db->delete('s_leagues',array('league_id' => $id));

                    return TRUE;
            }
        
         function get_league_by_name ($name) 

            {                   
                    $row = array();								                                        

                    $this->db->where('league_name',$name);

                    $result = $this->db->get('s_leagues');



                    foreach ($result->result_array() as $row) {                           
                            return $row['league_id'];

                    }

                    

                   return false;                 

            }   
}

