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

        if (isset($filters['country_id'])) {
            $this->db->where('z_countries.ID', $filters['country_id']);
        }
		
		if (isset($filters['odds'])) {
			$this->db->where('odds', $filters['odds']);
		}

		if (isset($filters['strategy_name'])) {
			$this->db->like('z_methods.method_name', $filters['strategy_name']);
		}

        if (isset($filters['method_id'])) {
          $this->db->where('z_methods.ID_method', $filters['method_id']);  
        }
   
		if (isset($filters['limit'])) {
            $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
            $this->db->limit($filters['limit'], $offset);
        }
   		    
		$bets = array();
	                
        $this->db->join('z_methods','z_methods.ID_method = z_bets.strategy','left');
		$this->db->join('z_markets','z_markets.ID_market = z_bets.market_type','inner');
        $this->db->join('z_markets_selects','z_markets_selects.market_select_id = z_bets.market_select','inner');
		$this->db->join('z_competitions','z_competitions.competition_id = z_bets.event_type','left');
		$this->db->join('z_countries','z_countries.ID = z_bets.country_name','left');
        $this->db->join('users','users.user_id = z_bets.username','inner');
                
        // if is not an admin user he see only his bets
        $username=$this->user_model->get('id');
        if ($this->user_model->logged_in() and !$this->user_model->is_admin()) {
             
             $this->db->where('username', $username);   
         }
         
        // if is an admin user he see all bets
        $this->db->select('*,z_competitions.name AS competition_name,z_countries.ID AS country_id');
        $result = $this->db->get('z_bets');
        
        if ($result->num_rows() == 0) {
			return FALSE;
		}
                
        foreach ($result->result_array() as $bet) {            
            $bets[] = array(
                'ID_bet' 		=> $bet['ID_bet'],
				'event_name' 	=> $bet['event_name'],
                'event_date' 	=> $bet['event_date'],
                'country_name' 	=> $bet['country_name'],
                'country_id'    => $bet['country_id'],
                'stake' 		=> $bet['stake'],
                'profit' 		=> $bet['profit'],
                'loss' 			=> $bet['loss'],
                'event_type' 	=> $bet['competition_name'],
                'bet_type' 		=> $bet['bet_type'],
                'odds' 			=> $bet['odds'],
				'market_id' 	=> $bet['market_type'],
                'market_type' 	=> $bet['market_name'],
                'market_select' => $bet['market_select_name'],
                'comment' 		=> $bet['comment'],
				'strategy_id' 	=> $bet['strategy'],
                'strategy' 		=> $bet['method_name'],
				'username' 		=> $bet['user_username'],
				'final_score' 	=> $bet['final_score'],
				'live_tv' 		=> $bet['live_tv'],
				'paper_bet' 	=> $bet['paper_bet'],
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

		if (isset($filters['country_name'])) {
			$this->db->like('z_countries.country_name', $filters['country_name']);
		}

        if (isset($filters['country_id'])) {
            $this->db->where('z_countries.ID', $filters['country_id']);
        }
	
		if (isset($filters['odds'])) {
			$this->db->where('odds', $filters['odds']);
		}

		if (isset($filters['strategy_name'])) {
			$this->db->like('z_methods.method_name', $filters['strategy_name']);
		}

        if (isset($filters['method_id'])) {
          $this->db->where('z_methods.ID_method', $filters['method_id']);  
        }

		$this->db->join('z_methods','z_methods.ID_method = z_bets.strategy','left');
		$this->db->join('z_markets','z_markets.ID_market = z_bets.market_type','inner');
        $this->db->join('z_markets_selects','z_markets_selects.market_select_id = z_bets.market_select','inner');
		$this->db->join('z_competitions','z_competitions.competition_id = z_bets.event_type','left');
		$this->db->join('z_countries','z_countries.ID = z_bets.country_name','left');
        $this->db->join('users','users.user_id = z_bets.username','inner');
            
        // if is not an admin user he see only his bets
        $username=$this->user_model->get('id');
        if ($this->user_model->logged_in() and !$this->user_model->is_admin()) {	             
             $this->db->where('username', $username);   
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
        $this->db->where('event_date_time',$param['event_date_time']);
        $this->db->where('odds',$param['odds']);
        $this->db->where('stake',$param['stake']);
        $this->db->where('username',$param['username']);
        $result = $this->db->get('z_bets');
        
        return $result->num_rows();
    }

    function get_bet_stats_methods()
    {
    	$bets = $this->get_bets();
    	$stats = array();

    	$stats['count'] = count($bets);

    // 	$bets[] = array(
    //             'ID_bet' 		=> $bet['ID_bet'],
				// 'event_name' 	=> $bet['event_name'],
    //             'event_date' 	=> $bet['event_date'],
    //             'country_name' 	=> $bet['country_name'],
    //             'country_id'     => $bet['country_id'],    
    //             'stake' 		=> $bet['stake'],
    //             'profit' 		=> $bet['profit'],
    //             'loss' 			=> $bet['loss'],
    //             'event_type' 	=> $bet['competition_name'],
    //             'bet_type' 		=> $bet['bet_type'],
    //             'odds' 			=> $bet['odds'],
				// 'market_id' 	=> $bet['market_type'],
    //             'market_type' 	=> $bet['market_name'],
    //             'market_select' => $bet['market_select_name'],
    //             'comment' 		=> $bet['comment'],
				// 'strategy_id' 	=> $bet['strategy'],
    //             'strategy' 		=> $bet['method_name'],
				// 'username' 		=> $bet['user_username'],
				// 'final_score' 	=> $bet['final_score'],
				// 'live_tv' 		=> $bet['live_tv'],
				// 'paper_bet' 	=> $bet['paper_bet'],
    //         );

    	$stake = 0;
    	$profit = 0;
    	$loss = 0;
    	$odds = 0;

    	$strategies = array();

    	foreach ($bets as $bet) {
    		$stake += $bet['stake'];

    		if (!is_null($bet['profit'])) {
    			$profit += $bet['profit'];
    		}

    		if (!is_null($bet['loss'])) {
    			$loss += $bet['loss'];
    		}

    		$odds += $bet['odds'];

    		if (!array_key_exists($bet['strategy_id'], $strategies)) {
    			$strategies[$bet['strategy_id']] = array();
    			$strategies[$bet['strategy_id']]['strategy'] = "<a href='/admincp6/livescore/stats_bet_by_method/{$bet['strategy_id']}' target='_blank'>{$bet['strategy']}</a>";
    			$strategies[$bet['strategy_id']]['count'] = 0;
    			$strategies[$bet['strategy_id']]['profit'] = 0;
    			$strategies[$bet['strategy_id']]['profit_count'] = 0;
    			$strategies[$bet['strategy_id']]['loss'] = 0;
    			$strategies[$bet['strategy_id']]['loss_count'] = 0;
    		}

    		$strategies[$bet['strategy_id']]['count']++;

    		if (!is_null($bet['profit'])) {
    			$strategies[$bet['strategy_id']]['profit'] += $bet['profit'];
    			$strategies[$bet['strategy_id']]['profit_count']++;
    		}

    		if (!is_null($bet['loss'])) {
    			$strategies[$bet['strategy_id']]['loss'] += $bet['loss'];
    			$strategies[$bet['strategy_id']]['loss_count']++;
    		}
    	}

    	foreach($strategies as $key => $row) {
    		$strategies[$key]['balance'] = $strategies[$key]['profit'] - $strategies[$key]['loss'];
    		$strategies[$key]['balance_count'] = $strategies[$key]['profit_count'] - $strategies[$key]['loss_count'];
            $strategies[$key]['percentage'] = round($strategies[$key]['profit_count'] * 100 / $strategies[$key]['count'], 2);
    	}

        uasort($strategies,array('Bet_model','cmp')); 

    	$stats['average_stake'] = round($stake / $stats['count'], 2).' &euro;';
    	$stats['profit'] = $profit.' &euro;';
    	$stats['loss'] = $loss.' &euro;';
    	$stats['balance'] = ($profit - $loss).' &euro;';
    	$stats['odds'] = round($odds / $stats['count'], 2);
    	$stats['strategy'] = $strategies;

    	//print_r($strategies);

    	return $stats;

    }

    function get_bet_stats_by_method($filters)
    {
        $bets = $this->get_bets($filters);
        $stats = array();

        $stats['count'] = count($bets);

    //  $bets[] = array(
    //             'ID_bet'         => $bet['ID_bet'],
                // 'event_name'     => $bet['event_name'],
    //             'event_date'     => $bet['event_date'],
    //             'country_name'   => $bet['country_name'],
    //             'country_id'     => $bet['country_id'],     
    //             'stake'      => $bet['stake'],
    //             'profit'         => $bet['profit'],
    //             'loss'           => $bet['loss'],
    //             'event_type'     => $bet['competition_name'],
    //             'bet_type'       => $bet['bet_type'],
    //             'odds'           => $bet['odds'],
                // 'market_id'  => $bet['market_type'],
    //             'market_type'    => $bet['market_name'],
    //             'market_select' => $bet['market_select_name'],
    //             'comment'        => $bet['comment'],
                // 'strategy_id'    => $bet['strategy'],
    //             'strategy'       => $bet['method_name'],
                // 'username'       => $bet['user_username'],
                // 'final_score'    => $bet['final_score'],
                // 'live_tv'        => $bet['live_tv'],
                // 'paper_bet'  => $bet['paper_bet'],
    //         );

        $stake = 0;
        $profit = 0;
        $loss = 0;
        $odds = 0;

        $countries = array();

        foreach ($bets as $bet) {
            $stats['method_name'] = $bet['strategy'];

            $stake += $bet['stake'];

            if (!is_null($bet['profit'])) {
                $profit += $bet['profit'];
            }

            if (!is_null($bet['loss'])) {
                $loss += $bet['loss'];
            }

            $odds += $bet['odds'];

            if (!array_key_exists($bet['country_id'], $countries)) {
                $countries[$bet['country_id']] = array();
                $countries[$bet['country_id']]['country'] = "<a href='/admincp6/livescore/list_bets_by_method_and_country/{$filters['method_id']}/{$bet['country_id']}' target='_blank'>{$bet['country_name']}</a>";
                $countries[$bet['country_id']]['count'] = 0;
                $countries[$bet['country_id']]['profit'] = 0;
                $countries[$bet['country_id']]['profit_count'] = 0;
                $countries[$bet['country_id']]['loss'] = 0;
                $countries[$bet['country_id']]['loss_count'] = 0;
            }

            $countries[$bet['country_id']]['count']++;

            if (!is_null($bet['profit'])) {
                $countries[$bet['country_id']]['profit'] += $bet['profit'];
                $countries[$bet['country_id']]['profit_count']++;
            }

            if (!is_null($bet['loss'])) {
                $countries[$bet['country_id']]['loss'] += $bet['loss'];
                $countries[$bet['country_id']]['loss_count']++;
            }
        }

        foreach($countries as $key => $row) {
            $countries[$key]['balance'] = $countries[$key]['profit'] - $countries[$key]['loss'];
            $countries[$key]['balance_count'] = $countries[$key]['profit_count'] - $countries[$key]['loss_count'];
            $countries[$key]['percentage'] = round($countries[$key]['profit_count'] * 100 / $countries[$key]['count'], 2);
        }

        uasort($countries,array('Bet_model','cmp')); 
        
        $stats['average_stake'] = round($stake / $stats['count'], 2).' &euro;';
        $stats['profit'] = $profit.' &euro;';
        $stats['loss'] = $loss.' &euro;';
        $stats['balance'] = ($profit - $loss).' &euro;';
        $stats['odds'] = round($odds / $stats['count'], 2);
        $stats['country'] = $countries;

        //print_r($countries);

        return $stats;

    }

    function get_bet_stats_countries()
    {
        $bets = $this->get_bets();
        $stats = array();

        $stats['count'] = count($bets);

    //  $bets[] = array(
    //             'ID_bet'         => $bet['ID_bet'],
                // 'event_name'     => $bet['event_name'],
    //             'event_date'     => $bet['event_date'],
    //             'country_name'   => $bet['country_name'],
    //             'country_id'     => $bet['country_id'],    
    //             'stake'      => $bet['stake'],
    //             'profit'         => $bet['profit'],
    //             'loss'           => $bet['loss'],
    //             'event_type'     => $bet['competition_name'],
    //             'bet_type'       => $bet['bet_type'],
    //             'odds'           => $bet['odds'],
                // 'market_id'  => $bet['market_type'],
    //             'market_type'    => $bet['market_name'],
    //             'market_select' => $bet['market_select_name'],
    //             'comment'        => $bet['comment'],
                // 'strategy_id'    => $bet['strategy'],
    //             'strategy'       => $bet['method_name'],
                // 'username'       => $bet['user_username'],
                // 'final_score'    => $bet['final_score'],
                // 'live_tv'        => $bet['live_tv'],
                // 'paper_bet'  => $bet['paper_bet'],
    //         );

        $stake = 0;
        $profit = 0;
        $loss = 0;
        $odds = 0;

        $countries = array();

        foreach ($bets as $bet) {
            $stake += $bet['stake'];

            if (!is_null($bet['profit'])) {
                $profit += $bet['profit'];
            }

            if (!is_null($bet['loss'])) {
                $loss += $bet['loss'];
            }

            $odds += $bet['odds'];

            if (!array_key_exists($bet['country_id'], $countries)) {
                $countries[$bet['country_id']] = array();
                $countries[$bet['country_id']]['country'] = "<a href='/admincp6/livescore/stats_bet_by_country/{$bet['country_id']}' target='_blank'>{$bet['country_name']}</a>";
                $countries[$bet['country_id']]['count'] = 0;
                $countries[$bet['country_id']]['profit'] = 0;
                $countries[$bet['country_id']]['profit_count'] = 0;
                $countries[$bet['country_id']]['loss'] = 0;
                $countries[$bet['country_id']]['loss_count'] = 0;
            }

            $countries[$bet['country_id']]['count']++;

            if (!is_null($bet['profit'])) {
                $countries[$bet['country_id']]['profit'] += $bet['profit'];
                $countries[$bet['country_id']]['profit_count']++;
            }

            if (!is_null($bet['loss'])) {
                $countries[$bet['country_id']]['loss'] += $bet['loss'];
                $countries[$bet['country_id']]['loss_count']++;
            }
        }

        foreach($countries as $key => $row) {
            $countries[$key]['balance'] = $countries[$key]['profit'] - $countries[$key]['loss'];
            $countries[$key]['balance_count'] = $countries[$key]['profit_count'] - $countries[$key]['loss_count'];
            $countries[$key]['percentage'] = round($countries[$key]['profit_count'] * 100 / $countries[$key]['count'], 2);
        }

        uasort($countries,array('Bet_model','cmp')); 

        $stats['average_stake'] = round($stake / $stats['count'], 2).' &euro;';
        $stats['profit'] = $profit.' &euro;';
        $stats['loss'] = $loss.' &euro;';
        $stats['balance'] = ($profit - $loss).' &euro;';
        $stats['odds'] = round($odds / $stats['count'], 2);
        $stats['country'] = $countries;

        //print_r($countries);

        return $stats;

    }

    function get_bet_stats_by_country($filters)
    {
        $bets = $this->get_bets($filters);
        $stats = array();

        $stats['count'] = count($bets);

    //  $bets[] = array(
    //             'ID_bet'         => $bet['ID_bet'],
                // 'event_name'     => $bet['event_name'],
    //             'event_date'     => $bet['event_date'],
    //             'country_name'   => $bet['country_name'],
    //             'country_id'     => $bet['country_id'],     
    //             'stake'      => $bet['stake'],
    //             'profit'         => $bet['profit'],
    //             'loss'           => $bet['loss'],
    //             'event_type'     => $bet['competition_name'],
    //             'bet_type'       => $bet['bet_type'],
    //             'odds'           => $bet['odds'],
                // 'market_id'  => $bet['market_type'],
    //             'market_type'    => $bet['market_name'],
    //             'market_select' => $bet['market_select_name'],
    //             'comment'        => $bet['comment'],
                // 'strategy_id'    => $bet['strategy'],
    //             'strategy'       => $bet['method_name'],
                // 'username'       => $bet['user_username'],
                // 'final_score'    => $bet['final_score'],
                // 'live_tv'        => $bet['live_tv'],
                // 'paper_bet'  => $bet['paper_bet'],
    //         );

        $stake = 0;
        $profit = 0;
        $loss = 0;
        $odds = 0;

        $strategies = array();

        foreach ($bets as $bet) {
            $stats['country_name'] = $bet['country_name'];

            $stake += $bet['stake'];

            if (!is_null($bet['profit'])) {
                $profit += $bet['profit'];
            }

            if (!is_null($bet['loss'])) {
                $loss += $bet['loss'];
            }

            $odds += $bet['odds'];

            if (!array_key_exists($bet['strategy_id'], $strategies)) {
                $strategies[$bet['strategy_id']] = array();
                $strategies[$bet['strategy_id']]['strategy'] = "<a href='/admincp6/livescore/list_bets_by_method_and_country/{$bet['strategy_id']}/{$filters['country_id']}' target='_blank'>{$bet['strategy']}</a>";
                $strategies[$bet['strategy_id']]['count'] = 0;
                $strategies[$bet['strategy_id']]['profit'] = 0;
                $strategies[$bet['strategy_id']]['profit_count'] = 0;
                $strategies[$bet['strategy_id']]['loss'] = 0;
                $strategies[$bet['strategy_id']]['loss_count'] = 0;
            }

            $strategies[$bet['strategy_id']]['count']++;

            if (!is_null($bet['profit'])) {
                $strategies[$bet['strategy_id']]['profit'] += $bet['profit'];
                $strategies[$bet['strategy_id']]['profit_count']++;
            }

            if (!is_null($bet['loss'])) {
                $strategies[$bet['strategy_id']]['loss'] += $bet['loss'];
                $strategies[$bet['strategy_id']]['loss_count']++;
            }
        }

        foreach($strategies as $key => $row) {
            $strategies[$key]['balance'] = $strategies[$key]['profit'] - $strategies[$key]['loss'];
            $strategies[$key]['balance_count'] = $strategies[$key]['profit_count'] - $strategies[$key]['loss_count'];
            $strategies[$key]['percentage'] = round($strategies[$key]['profit_count'] * 100 / $strategies[$key]['count'], 2);
        }

        uasort($strategies,array('Bet_model','cmp')); 
        
        $stats['average_stake'] = round($stake / $stats['count'], 2).' &euro;';
        $stats['profit'] = $profit.' &euro;';
        $stats['loss'] = $loss.' &euro;';
        $stats['balance'] = ($profit - $loss).' &euro;';
        $stats['odds'] = round($odds / $stats['count'], 2);
        $stats['strategy'] = $strategies;

        //print_r($strategies);

        return $stats;

    }

    private static function cmp($a, $b)
    {
        if ($a['percentage'] == $b['percentage']) {
            return 0;
        }
        return ($a['percentage'] > $b['percentage']) ? -1 : 1;
    }
        
}