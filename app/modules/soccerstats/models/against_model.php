<?php



/**

* Aginst Model

*

* Aginst table cu echipele si odss

*

* @author Weblight.ro

* @copyright Weblight.ro

* @package BJ Tool



*/



	class Against_model extends CI_Model

	{

		private $CI;

	

		function __construct()

		{

			parent::__construct();

		

			$this->CI =& get_instance();

		}

          
      /**
	* Get get_against_value
	*
	* @param int $id
	* 
	* @return array 
	*/
	function get_against_value($id) 
        {
		
                    $row = array();								
                    $this->db->where('id_against',$id);
                    $result = $this->db->get('s_against');

                    foreach ($result->result_array() as $row) {
                            return $row;
                    }

                    return $row;	
	}
        
         
        
       
	 /**
	* Get get_against_values
	*
	* 
	* @return array 
	*/
        
	function get_against_values($filters = array()) 
        {

				$order_dir = (isset($filters['sort_dir'])) ? $filters['sort_dir'] : 'ASC';  
				 if(isset($filters['sort']))  $this->db->order_by($filters['sort'], $order_dir);
		
 				if (isset($filters['id_against'])) {
		
					$this->db->where('id_against', $filters['id_against']);
		
				}
				
				 if (isset($filters['zone'])) {
		
					$this->db->like('zone', $filters['zone']);
		
				}


				if (isset($filters['limit'])) {
                            $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
                            $this->db->limit($filters['limit'], $offset);
                    }
    
                
				//$introstats = array();
                
                //$this->db->join('s_leagues','s_leagues.league_id = s_introstats.league_name','inner');
                
                $result = $this->db->get('s_against');
                
                if ($result->num_rows() == 0) {
					return FALSE;
				}
                
                foreach ($result->result_array() as $stats) {
                    
                    $introstats[] = array(
                                        'id_against' => $stats['id_against'],
										'zone' => $stats['zone'],
                                        'favorite_name' => $stats['favorite_name'],
                                        'favorite_odds' => $stats['favorite_odds'],
                                        'draw_odds' => $stats['draw_odds'],
                                        'underdog_name' => $stats['underdog_name'],
                                        'underdog_odds' => $stats['underdog_odds'],
                                        'date_inserted' => $stats['date_inserted'],
                                        'result' => $stats['result'],
				     );
		}
	
		return $introstats;

	}   
	
	/**
	* Get Against List Rows
	*
	* 
	* @return array 
	*/
            
    function num_rows_against($filters = array()) {
             if (isset($filters['id_against'])) {

			$this->db->where('id_against', $filters['id_against']);

		}
              
                $result = $this->db->get('s_against');
             
                return $result->num_rows();
        }         
  
         /**
	* Create New Market
	*
	*
	* @return int $market_name
	*/
	function add_against_value ($zone,$favorite_name,$favorite_odds,$draw_odds,$underdog_name,$underdog_odds,$result) 
        {
		$insert_fields = array(
                                        'zone' => $zone,
                                        'favorite_name' => $favorite_name,
                                        'favorite_odds' => $favorite_odds,
                                        'draw_odds' => $draw_odds,
                                        'underdog_name' => $underdog_name,
                                        'underdog_odds' => $underdog_odds,
                                        'result' => $result
				);
						
		$this->db->insert('s_against', $insert_fields);

		return TRUE;
	}
          
        
        /**
	* Update Market
	*
	* @return void
	*/
	function edit_against_value ( $id_against,$zone,$favorite_name,$favorite_odds,$draw_odds,$underdog_name,$underdog_odds,$result) 
        {
		$update_fields = array(
					'zone' => $zone,
                                        'favorite_name' => $favorite_name,
                                        'favorite_odds' => $favorite_odds,
                                        'draw_odds' => $draw_odds,
                                        'underdog_name' => $underdog_name,
                                        'underdog_odds' => $underdog_odds,
                                        'result' => $result
				);

		$this->db->update('s_against', $update_fields, array('id_against' => $id_against));
		
								
		return TRUE;
	}
        
        
        /**
	* Delete Methods
	*
	* 
	* @return array 
	*/
            function delete_against_value($id) 
            {
                    $this->db->delete('s_against',array('id_against' => $id));

                    return TRUE;
            }


}

