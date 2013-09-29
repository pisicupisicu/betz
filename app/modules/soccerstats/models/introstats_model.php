<?php



/**

* Introstats Model

*

* Manages Introstats

*

* @author Weblight.ro

* @copyright Weblight.ro

* @package BJ Tool



*/



	class Introstats_model extends CI_Model

	{

		private $CI;

	

		function __construct()

		{

			parent::__construct();

		

			$this->CI =& get_instance();

		}

        
	 /**
	* Get introstats
	*
	* 
	* @return array 
	*/
        
	function get_introstats($filters = array()) 
        {
            


			$order_dir = (isset($filters['sort_dir'])) ? $filters['sort_dir'] : 'ASC';  
				 if(isset($filters['sort']))  $this->db->order_by($filters['sort'], $order_dir);
		
 				if (isset($filters['introstats_id'])) {
		
					$this->db->where('introstats_id', $filters['introstats_id']);
		
				}
				
				 if (isset($filters['league_name'])) {
		
					$this->db->like('league_name', $filters['league_name']);
		
				}


		if (isset($filters['limit'])) {
                            $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
                            $this->db->limit($filters['limit'], $offset);
                    }
    
                
		$introstats = array();
                
                $this->db->join('s_leagues','s_leagues.league_id = s_introstats.league_name','inner');
                
                $result = $this->db->get('s_introstats');
                
                if ($result->num_rows() == 0) {
			return FALSE;
		}
                
                foreach ($result->result_array() as $stats) {
                    
                    $introstats[] = array(
                                        'introstats_id' => $stats['introstats_id'],
					'league_name' => $stats['league_name'],
                                        'matches_played' => $stats['matches_played'],
                                        'home_wins' => $stats['home_wins'],
                                        'draw' => $stats['draw'],
                                        'away_wins' => $stats['away_wins'],
                                        'goals_average' => $stats['goals_average'],
                                        'home_average' => $stats['home_average'],
                                        'away_average' => $stats['away_average'],
                                        'over_1_5' => $stats['over_1_5'],
                                        'over_2_5' => $stats['over_2_5'],
                                        'over_3_5' => $stats['over_3_5'],
				     );
		}
	
		return $introstats;

	}   
	
         /**

            * Create New Team

            *

            * Creates a new team

            *

            * @param array $insert_fields	

            *

            * @return int $insert_id

            */

            function new_introstats ($insert_fields) 
            {	
               $this->db->insert('s_introstats', $insert_fields);		
                    $insert_id = $this->db->insert_id();

                    return $insert_id;                              

            }
            
            function introstats_exists($id)
            {                                
                $this->db->where('league_name',$id);                
                $result = $this->db->get('s_introstats');

                return $result->num_rows();
            }
            
            function update_introstats ($update_fields,$id) 

            {		

                    $this->db->update('s_introstats',$update_fields,array('league_name' => $id));



                    return TRUE;

            }

        
        /**
	* Get introstats
	*
	* 
	* @return array 
	*/
            
    function num_rows_introstats($filters = array()) {
             if (isset($filters['id'])) {

			$this->db->where('introstats_id', $filters['id']);

		}
		                
                $result = $this->db->get('s_introstats');
                            
                return $result->num_rows();
        }         
  


}

