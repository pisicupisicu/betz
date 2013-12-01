<?php

/**
* Competition Model
*
* Add New Bets
*
* @author Weblight.ro
* @copyright Weblight.ro
* @package BJ Tool

*/

	class Bet_model extends CI_Model
	{
		private $CI;
	
		function __construct()
		{
			parent::__construct();

		}
	
            
    /**
	* Get Bets
	*
	* @param int $bets_id
	* 
	* @return array 
	*/
	function get_bet ($id) 
        {
		
                    $row = array();								
                    $this->db->where('ID_bet',$id);
                    $result = $this->db->get('z_bets');

                    foreach ($result->result_array() as $row) {
                            return $row;
                    }

                    return $row;	
	}
        
         
        
	 /**
	* Get Bets
	*
	* 
	* @return array 
	*/
        
	function get_bets ($filters = array()) 
        {
            		
		 $order_dir = (isset($filters['sort_dir'])) ? $filters['sort_dir'] : 'ASC';  
         if(isset($filters['sort']))  $this->db->order_by($filters['sort'], $order_dir);

         if (isset($filters['id'])) {

			$this->db->where('ID_bet', $filters['id']);

		}
		
		 if (isset($filters['event_name'])) {

			$this->db->like('event_name', $filters['event_name']);

		}
		
		 if (isset($filters['country_name'])) {

			$this->db->like('z_countries.country_name', $filters['country_name']);

		}
		
		 if (isset($filters['odds'])) {

			$this->db->where('odds', $filters['odds']);

		}

		if (isset($filters['strategy_name'])) {

			$this->db->like('z_methods.method_name', $filters['strategy_name']);

		}
   
		if (isset($filters['limit'])) {
                            $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
                            $this->db->limit($filters['limit'], $offset);
                    }
   
		
    
		$bets = array();
	                
                $this->db->join('z_methods','z_methods.ID_method = z_bets.strategy','left');
		$this->db->join('z_markets','z_markets.ID_market = z_bets.market_type','inner');
                $this->db->join('z_markets_selects','z_markets_selects.market_select_id = z_bets.market_select','inner');
		$this->db->join('z_leagues','z_leagues.ID_league = z_bets.event_type','left');
		$this->db->join('z_countries','z_countries.ID = z_bets.country_name','left');
                $this->db->join('users','users.user_id = z_bets.username','inner');
                
                // if is not an admin user he see only his bets
                $username=$this->user_model->get('id');
                if ($this->user_model->logged_in() and !$this->user_model->is_admin()) {
                     
                     $this->db->where('username', $username);   
                 }
                 
                // if is an admin user he see all bets 
                $result = $this->db->get('z_bets');
                
                if ($result->num_rows() == 0) {
			return FALSE;
		}
                
                foreach ($result->result_array() as $bet) {
                    
                    $bets[] = array(
                                        'ID_bet' => $bet['ID_bet'],
					'event_name' => $bet['event_name'],
                                        'event_date' => $bet['event_date'],
                                        'country_name' => $bet['country_name'],
                                        'stake' => $bet['stake'],
                                        'profit' => $bet['profit'],
                                        'loss' => $bet['loss'],
                                        'event_type' => $bet['league_name'],
                                        'bet_type' => $bet['bet_type'],
                                        'odds' => $bet['odds'],
					'market_id' => $bet['market_type'],
                                        'market_type' => $bet['market_name'],
                                        'market_select' => $bet['market_select_name'],
                                        'comment' => $bet['comment'],
					'strategy_id' => $bet['strategy'],
                                        'strategy' => $bet['method_name'],
					'username' => $bet['user_username'],
					'paper_bet' => $bet['paper_bet'],
                                                        );
		}
		//print_r(array_values($methods));
	
		//var_dump ($methods);
		//die;
	
		return $bets;

	}
        
        /**
	* Get bets row
	*
	* 
	* @return array 
	*/
                
        function get_num_rows_bets($filters = array())
        {
                
                  if (isset($filters['id_bet'])) {

			$this->db->where('ID_bet', $filters['id_bet']);

		}
                
                if (isset($filters['event_name'])) {

			$this->db->like('event_name', $filters['event_name']);

		}
                 
                 
                $result = $this->db->get('z_bets');
                return $result->num_rows();        
        }
		
	/**
	* Create New Bet
	*
	*/
	function new_bet($insert_fields) 
        {
		
		$this->db->insert('z_bets', $insert_fields);

		return TRUE;
	}
    /**
	* Update Bet
	*
	* @return void
	*/
	function update_bet($update_fields,$id_bet) 
        {
		

		$this->db->update('z_bets', $update_fields, array('ID_bet' => $id_bet));
		
								
		return TRUE;
	}
        
        
        /**
	* Delete Bets
	*
	* 
	* @return array 
	*/
    function delete_bet($id) 
    {
            $this->db->delete('z_bets',array('ID_bet' => $id));

            return TRUE;
    } 
            
    /**
	* 
	*
	* 
	* @return int 
	*/
    function bet_exists($param)
    {                      
        $this->db->where('event_name',$param['event_name']);
        $this->db->where('event_date',$param['event_date']);
        $this->db->where('odds',$param['odds']);
        $this->db->where('stake',$param['stake']);
        $result = $this->db->get('z_bets');
        
        return $result->num_rows();
    }
        
}