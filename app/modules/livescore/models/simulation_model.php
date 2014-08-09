<?php
	/**
	* Simulation Model
	*
	* Manages Simulations
	*
	* @author Weblight.ro
	* @copyright Weblight.ro
	* @package BJ Tool
	**/
	class Simulation_model extends CI_Model
	{

		private $CI;
	
		function __construct()
		{

			parent::__construct();	
			$this->CI =& get_instance();
		}

           
	    /**
		* Get Simulation
		*
		* @param int $id
		* 
		* @return array 
		*/
		function get_simulation($id) 
	    {
			
	        $row = array();								
	        $this->db->where('id_simulation',$id);
	        $result = $this->db->get('z_simulations');

	        foreach ($result->result_array() as $row) {
	            return $row;
	        }

	        return $row;	
		}
        
        function get_profit_by_setting($id_setting) 
        {
         	$row = array();								
	        $this->db->where('id_setting',$id_setting);
	        $this->db->select('SUM(profit) AS profit');
	        $result = $this->db->get('z_simulations');

	        foreach ($result->result_array() as $row) {
	            return $row;
	        }

	        return $row;
         }
        
		/**
		* Get Simulations
		*
		* 
		* @return array 
		*/
	        
		function get_simulations($filters = array()) 
	    {        
	        $result = $this->db->get('z_simulations');
	        if (isset($filters['id_simulation'])) {
				$this->db->where('id_simulation', $filters['id_simulation']);
			}

			if (isset($filters['limit'])) {
		        $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
		        $this->db->limit($filters['limit'], $offset);
		    }
		
	                                       
			if ($result->num_rows() == 0) {
				return FALSE;
			}
	            
	            
			$simulations = array();

		    foreach ($result->result_array() as $sim) {
		        
		        $simulations[] = array(
	                    'id_simulation' 	=> $sim['id_simulation'],
	                    'id_setting' 		=> $sim['id_setting'],
						'match_date' 		=> $sim['match_date'],
						'correct_bets' 		=> $sim['correct_bets'],
						'wrong_bets' 		=> $sim['wrong_bets'],
						'correct_percent' 	=> $sim['correct_percent'],
						'wrong_percent' 	=> $sim['wrong_percent'],
						'total_bets' 		=> $sim['total_bets'],
						'total_games' 		=> $sim['total_games'],
						'profit' 			=> $sim['profit'],
						
		     	);
			}

			return $simulations;

		}      
            
	    function num_rows_simulations($filters = array()) 
	    {
	     	if (isset($filters['id_setting'])) {
				$this->db->where('id_setting', $filters['id_setting']);
			}

			if (isset($filters['match_date'])) {
				$this->db->where('match_date', $filters['match_date']);
			}
			                
	        $result = $this->db->get('z_simulations');                                
	        return $result->num_rows();
	    }         
  
	 	/**
		* Create New simulation
		*
		*
		* @return int $simulation_id
		*/
		function new_simulation ($insert_fields) 
	    {							
			$this->db->insert('z_simulations', $insert_fields);

			return TRUE;
		}
          
        
	    /**
		* Update Simulation
		*
		* @return void
		*/
		function update_simulation ($id_simulation,$update_fields) 
	    {		
			$this->db->update('z_simulations', $update_fields, array('id_simulation' => $id_simulation));
										
			return TRUE;
		}
        
        
	    /**
		* Delete Simulations
		*
		* 
		* @return array 
		*/
	    function delete_simulation ($id_simulation) 
	    {
	        $this->db->delete('z_simulations',array('id_simulation' => $id_simulation));

	        return TRUE;
	    }

	    function count_intervals(&$id_match, &$one, &$two, &$three, &$four, &$five, &$six)
	    {
	    	$this->load->model('goal_model');
	    	$goals = $this->goal_model->get_goals_by_match($id_match);

	    	foreach ($goals as $goal) {
	    		if ($goal['min'] <= 15) {
	    			$one++;
	    		} elseif(16 <= $goal['min'] && $goal['min'] <= 30) {
	    			$two++;
	    		} elseif(31 <= $goal['min'] && $goal['min'] <= 45) {
	    			$three++;
	    		} elseif(46 <= $goal['min'] && $goal['min'] <= 60) {
	    			$four++;
	    		} elseif(61 <= $goal['min'] && $goal['min'] <= 75) {
	    			$five++;
	    		} elseif(76 <= $goal['min']) {
	    			$six++;
	    		}
	    	}
	    	
	    	$id_match++;
	    }

	    /**
		*	Simulate the method for a given date and insert in the simulation date
		*	@return int|string simulation id or error message
		*/
	    function simulate($id_setting, $date)
	    {	    	
			$filters = array('id_setting' => $id_setting, 'match_date' => $date);		    	
	    	if ($this->num_rows_simulations($filters)) {
	    		return 'The date '.$date.' is already simulated for method with setting id '.$id_setting;
	    	}
	    		    		    
	    	$this->db->where('match_date',$date);
	    	$result = $this->db->get('z_matches');
	    	
	    	$this->db->where('id_setting',$id_setting);
	    	$result_settings = $this->db->get('z_method_settings');
	    	$result_settings = $result_settings->result_array();
	    	$settings = $result_settings[0];

	    	$this->load->model('method_setting_model');
	    	$this->load->model('method_model');
	    	$method = $this->method_model->get_method($settings['id_method']);
	    	
	    	$return = array();	    	
	    	
	    	$correct_percent = $wrong_percent = $profit = 0;
	    	
	    	if(strstr($settings['alias'],'CounterDiff')) {
	    		$return = $this->simulateCounterDiff($result, $settings);
	    	}
	    	elseif(strstr($settings['alias'],'Diff')) {
	    		$return = $this->simulateDiff($result, $settings);
	    	}
	    	elseif(strstr($settings['alias'],'MixReverse')) {
	    		$return = $this->simulateMixReverse($result, $settings);
	    	}
	    	elseif(strstr($settings['alias'],'Mix')) {
	    		$return = $this->simulateMix($result, $settings);
	    	}
	    	elseif(strstr($settings['alias'],'East17Under3.5')) {
	    		$return = $this->simulateEast17Under35($result, $settings);
	    	}
	    	elseif(strstr($settings['alias'],'Final85Million')) {
	    		$return = $this->simulateFinal85Million($result, $settings);
	    	}
	    	elseif(strstr($settings['alias'],'East17Million')) {
	    		$return = $this->simulateEast17Million($result, $settings);
	    	}
	    	elseif(strstr($method['method_name'],'EAST 17')) {
	    		$return = $this->simulateEast17($result, $settings);
	    	}	
	    	elseif(strstr($method['method_name'],'COUNTER 17')) { 
	    		$return = $this->simulateCounter17($result, $settings);
	    	}	
	    	elseif(strstr($method['method_name'],'FINAL 85')) { 
	    		$return = $this->simulateFinal85($result, $settings);
	    	}
	    	elseif(strstr($method['method_name'],'FINAL 45')) {
	    		$return = $this->simulateFinal45($result, $settings);
	    	}
	    	elseif(strstr($method['method_name'],'Test')) {
	    		$return = $this->simulateTest($result, $settings);
	    	}
	    	elseif(strstr($method['method_name'],'Test2')) {
	    		$return = $this->simulateTest2($result, $settings);
	    	}
	    	else {
	    		echo 'Unknown method';
	    		return;
	    	}

	    	$return['total_bets'] 		= $return['correct_bets'] + $return['wrong_bets'];
        	$return['correct_percent'] 	= $return['total_bets'] ? number_format($return['correct_bets']*100/$return['total_bets'], 2) : 0;        
        	$return['wrong_percent'] 	= $return['total_bets'] ? number_format($return['wrong_bets']*100/$return['total_bets'], 2) : 0;

			$insert_fields = array(
				'id_setting'		=> 	$id_setting,
				'match_date'		=>	$date,
				'correct_bets'		=> 	$return['correct_bets'],
				'wrong_bets'		=>  $return['wrong_bets'],
				'correct_percent'	=> 	$return['correct_percent'],
				'wrong_percent'		=> 	$return['wrong_percent'],
				'total_bets'		=> 	$return['total_bets'],
				'total_games'		=>  $return['total_games'],
				'profit'			=>  $return['profit'],
				'avg_stake'			=>	@$return['avg_stake'] ? @$return['avg_stake'] : 0
			);    	
	        
	    	return $this->new_simulation($insert_fields);
	    	                 		    		  
    }

    protected function simulateEast17Million($result, $settings)
    {
    	echo 'East17Million<br/>';
    	$east17 = false;
    	$this->load->model('step_model');
    	$strategies = array(6,7);    	
    	$strategy = $strategies[0];
    	
    	$return = array(
			'correct_bets' 	=>	0,
			'wrong_bets'	=>	0,
			'profit'		=>	0,
			'total_games'	=>	0,
			'avg_stake'		=>	0
		);

		$aux = $this->get_profit_by_setting($settings['id_setting']);
		$bank = $aux['profit'];

		if ($bank > 10000) {
			$strategy = $strategies[1];
		} 

		if ($bank <= 21) {
			$stake = 2;
		} else {
			$params['limit'] = 1;
    		$params['strategy_id'] = $strategy;
    		$params['amount'] = $bank;
    		
    		$temp = $this->step_model->get_steps($params);    		
    		if (isset($temp[0])) {
    			$stake = $temp[0]['stake'];
    		} else {
    			$stake = 3000;
    		}
    		
		}

    	foreach ($result->result_array() as $linie) {            	            
            $this->db->where('match_id',$linie['id']);
            $this->db->order_by('min');
            $result2 = $this->db->get('z_goals');                

            foreach ($result2->result_array() as $linie2) {
                //print_r($linie2);            	
            	if($linie2['min'] <= $settings['min']) {
                    $east17 = true;
            	}
            }
            	
            if ($east17) {
                if ($this->isOver($linie['score'], $settings['over'])) {
                    $return['correct_bets']++;                    
                    $return['profit'] += $stake * $settings['odds'] - $stake;                    
                } else {
                    $return['wrong_bets']++;                    
                    $return['profit'] -= $stake;                    
                }

                $return['total_games']++;
            }    
            	                                                                             	
    		// reset counters
    		$east17 = false;           
        }

        $return['avg_stake'] = $stake;

        return $return;
    }

    protected function simulateFinal85Million($result, $settings)
    {
    	echo 'Final85Million<br/>';
    	$final85 = false;

		$this->load->model('step_model');
    	$strategies = array(6,7);    	
    	$strategy = $strategies[0];
    	
    	$return = array(
			'correct_bets' 	=>	0,
			'wrong_bets'	=>	0,
			'profit'		=>	0,
			'total_games'	=>	0,
			'avg_stake'		=>	0
		);

		$aux = $this->get_profit_by_setting($settings['id_setting']);
		$bank = $aux['profit'];

		if ($bank > 10000) {
			$strategy = $strategies[1];
		} 

		if ($bank <= 21) {
			$stake = 2;
		} else {
			$params['limit'] = 1;
    		$params['strategy_id'] = $strategy;
    		$params['amount'] = $bank;
    		
    		$temp = $this->step_model->get_steps($params);    		
    		if (isset($temp[0])) {
    			$stake = $temp[0]['stake'];
    		} else {
    			$stake = 3000;
    		}
    		
		}		
		
    	foreach ($result->result_array() as $linie) {    		
            $this->db->where('match_id',$linie['id']);
            $this->db->order_by('min');
            $result2 = $this->db->get('z_goals');                

            foreach ($result2->result_array() as $linie2) {
                //print_r($linie2);            	            	
            	if($linie2['min'] >= $settings['min']) {
                    $final85 = true;
                }
            }
            	
            if ($final85) {                
                    $return['correct_bets']++;                    
                    $return['profit'] += $stake * $settings['odds'] - $stake;
            }                            
            else {
                $return['wrong_bets']++;                    
                $return['profit'] -= $stake;                    
            }              
            	                                                                     
        	$return['total_games']++;

    		// reset counters
    		$final85 = false;     		       
        }

        $return['avg_stake'] = $stake;

        return $return;
    }

    protected function simulateMix($result, $settings)
    {
    	echo 'Mix<br/>';
    	$east17 = false;
    	$return = array(
			'correct_bets' 	=>	0,
			'wrong_bets'	=>	0,
			'profit'		=>	0,
			'total_games'	=>	0
		);

    	foreach ($result->result_array() as $linie) {            	            
            $this->db->where('match_id',$linie['id']);
            $this->db->order_by('min');
            $result2 = $this->db->get('z_goals');

            $goals = array(0 => 0, 1 => 0, 2 => 0);                

            foreach ($result2->result_array() as $key => $linie2) {
                //print_r($linie2);            	
            	if($linie2['min'] <= $settings['min']) {
                    $east17 = true;
            	}

            	$goals[$key] = $linie2['min'];
            }
            
            if ($east17) {
            	if (!$goals[1]) {
            		$diff = false;
            	} else {
            		$temp = $goals[1] - $goals[0];
            		if ($temp < 20) {
            			$settings['stake'] = 1.3;
            		}
            	}            	
            }
            	
            if ($east17) {
                if ($this->isOver($linie['score'], $settings['over'])) {
                    $return['correct_bets']++;                    
                    $return['profit'] += $settings['stake'] * $settings['odds'] - $settings['stake'];                    
                } else {
                    $return['wrong_bets']++;                    
                    $return['profit'] -= 2;                    
                }

                $return['total_games']++;
            }    
            	                                                                             	
    		// reset counters
    		$east17 = false;           
        }

        return $return;
    }

    protected function simulateMixReverse($result, $settings)
    {
    	echo 'Mix Reverse<br/>';
    	$east17 = false;
    	$return = array(
			'correct_bets' 	=>	0,
			'wrong_bets'	=>	0,
			'profit'		=>	0,
			'total_games'	=>	0
		);

		$diff = false;

    	foreach ($result->result_array() as $linie) {            	            
            $this->db->where('match_id',$linie['id']);
            $this->db->order_by('min');
            $result2 = $this->db->get('z_goals');

            $goals = array(0 => 0, 1 => 0, 2 => 0);                

            foreach ($result2->result_array() as $key => $linie2) {
                //print_r($linie2);            	
            	if($linie2['min'] <= $settings['min']) {
                    $east17 = true;
            	}

            	$goals[$key] = $linie2['min'];
            }
            
            if ($east17) {
            	if (!$goals[1]) {
            		$diff = false;
            	} else {
            		$temp = $goals[1] - $goals[0];
            		if ($temp < 20) {
            			$diff = true;
            			$settings['stake'] = 1.3;
            		}
            	}            	
            }
            	
            if ($east17) {
                if ($this->isOver($linie['score'], $settings['over']) == $diff) {
                    $return['correct_bets']++;                    
                    $return['profit'] += $settings['stake'] * $settings['odds'] - $settings['stake'];                    
                } else {
                    $return['wrong_bets']++;                    
                    $return['profit'] -= 2;                    
                }

                $return['total_games']++;
            }    
            	                                                                             	
    		// reset counters
    		$east17 = false;           
        }

        return $return;
    }

    protected function simulateDiff($result, $settings)
    {
    	echo 'Diff<br/>';
    	$east17 = false;
    	$return = array(
			'correct_bets' 	=>	0,
			'wrong_bets'	=>	0,
			'profit'		=>	0,
			'total_games'	=>	0
		);

    	foreach ($result->result_array() as $linie) {            	            
            $this->db->where('match_id',$linie['id']);
            $this->db->order_by('min');
            $result2 = $this->db->get('z_goals');

            $goals = array(0 => 0, 1 => 0, 2 => 0);                

            foreach ($result2->result_array() as $key => $linie2) {
                //print_r($linie2);            	
            	if($linie2['min'] <= $settings['min']) {
                    $east17 = true;
            	}

            	$goals[$key] = $linie2['min'];
            }
            
            if ($east17) {
            	if (!$goals[1]) {
            		$east17 = true;
            	} elseif (!$goals[2]) {
            		$east17 = true;
            	} elseif (!$goals[2] || !$goals[1]) {
            		$east17 = true;
            	} else {
            		$diff1 = $goals[1] - $goals[0];
            		//$diff2 = $goals[2] - $goals[1];
            		// not eneough time to get 1.5 odds
            		//if ($diff1 < 20 || $diff2 < 20) {
            		if ($diff1 < 20) {
            			$east17 = false;
            		}
            	}            	
            }
            	
            if ($east17) {
                if ($this->isOver($linie['score'], $settings['over'])) {
                    $return['correct_bets']++;                    
                    $return['profit'] += $settings['stake'] * $settings['odds'] - $settings['stake'];                    
                } else {
                    $return['wrong_bets']++;                    
                    $return['profit'] -= 2;                    
                }

                $return['total_games']++;
            }    
            	                                                                             	
    		// reset counters
    		$east17 = false;           
        }

        return $return;
    }

    protected function simulateCounterDiff($result, $settings)
    {
    	echo 'CounterDiff<br/>';
    	$east17 = false;
    	$diff = false;
    	$return = array(
			'correct_bets' 	=>	0,
			'wrong_bets'	=>	0,
			'profit'		=>	0,
			'total_games'	=>	0
		);

    	foreach ($result->result_array() as $linie) {            	            
            $this->db->where('match_id',$linie['id']);
            $this->db->order_by('min');
            $result2 = $this->db->get('z_goals');

            $goals = array(0 => 0, 1 => 0, 2 => 0);                

            foreach ($result2->result_array() as $key => $linie2) {
                //print_r($linie2);            	
            	if($linie2['min'] <= $settings['min']) {
                    $east17 = true;
            	}

            	$goals[$key] = $linie2['min'];
            }
            
            if ($east17) {
            	if (!$goals[1]) {
            		$east17 = true;
            	} elseif (!$goals[2]) {
            		$east17 = true;
            	} elseif (!$goals[2] || !$goals[1]) {
            		$east17 = true;
            	} else {
            		$diff1 = $goals[1] - $goals[0];
            		$diff2 = $goals[2] - $goals[1];
            		// not eneough time to get 1.5 odds
            		if ($diff1 < 20 || $diff2 < 20) {            			
            			$east17 = false;
            		}
            	}            	
            }
            	
            if ($east17) {
                if (!$this->isOver($linie['score'], $settings['over'])) {
                    $return['correct_bets']++;                    
                    $return['profit'] += $settings['stake'] * $settings['odds'] - $settings['stake'];                    
                } else {
                    $return['wrong_bets']++;                    
                    $return['profit'] -= 2;                    
                }

                $return['total_games']++;
            }                	                                                                             	

    		// reset counters
    		$east17 = false;           
        }

        return $return;
    }

    protected function simulateEast17($result, $settings)
    {
    	echo 'East 17<br/>';
    	$east17 = false;
    	$return = array(
			'correct_bets' 	=>	0,
			'wrong_bets'	=>	0,
			'profit'		=>	0,
			'total_games'	=>	0
		);

    	foreach ($result->result_array() as $linie) {            	            
            $this->db->where('match_id',$linie['id']);
            $this->db->order_by('min');
            $result2 = $this->db->get('z_goals');                

            foreach ($result2->result_array() as $linie2) {
                //print_r($linie2);            	
            	if($linie2['min'] <= $settings['min']) {
                    $east17 = true;
            	}
            }
            	
            if ($east17) {
                if ($this->isOver($linie['score'], $settings['over'])) {
                    $return['correct_bets']++;                    
                    $return['profit'] += $settings['stake'] * $settings['odds'] - $settings['stake'];                    
                } else {
                    $return['wrong_bets']++;                    
                    $return['profit'] -= 2;                    
                }

                $return['total_games']++;
            }    
            	                                                                             	
    		// reset counters
    		$east17 = false;           
        }

        return $return;
    }

    protected function simulateEast17Under35($result, $settings)
    {
    	echo 'East 17 Under 3.5<br/>';
    	$east17 = false;
    	$return = array(
			'correct_bets' 	=>	0,
			'wrong_bets'	=>	0,
			'profit'		=>	0,
			'total_games'	=>	0
		);

    	foreach ($result->result_array() as $linie) {            	            
            $this->db->where('match_id',$linie['id']);
            $this->db->order_by('min');
            $result2 = $this->db->get('z_goals');                

            foreach ($result2->result_array() as $linie2) {
                //print_r($linie2);            	
            	if($linie2['min'] <= $settings['min']) {
                    $east17 = true;
            	}
            }
            	
            if ($east17) {
                if (!$this->isOver($linie['score'], $settings['over'])) {
                    $return['correct_bets']++;                    
                    $return['profit'] += $settings['stake'] * $settings['odds'] - $settings['stake'];                    
                } else {
                    $return['wrong_bets']++;                    
                    $return['profit'] -= 2;                    
                }

                $return['total_games']++;
            }    
            	                                                                             	
    		// reset counters
    		$east17 = false;           
        }

        return $return;
    }

    protected function simulateCounter17($result, $settings)
    {
    	$counter17 = true;
    	$return = array(
			'correct_bets' 	=>	0,
			'wrong_bets'	=>	0,
			'profit'		=>	0,
			'total_games'	=>	0
		);

    	foreach ($result->result_array() as $linie) {            	            
            $this->db->where('match_id',$linie['id']);
            $this->db->order_by('min');
            $result2 = $this->db->get('z_goals');                

            foreach ($result2->result_array() as $linie2) {
                //print_r($linie2);            	            	
            	if($linie2['min'] < $settings['min']) {
                    $counter17 = false;
                }
            }
            
            if ($counter17) {
                if ($this->isOver($linie['score'], $settings['over'])) {
                    $return['wrong_bets']++;                                        
                    $return['profit'] -= 2;                    
                } else {
                    $return['correct_bets']++;                    
                    $return['profit'] += $settings['stake'] * $settings['odds'] - $settings['stake'];                    
                }

                $return['total_games']++;   
            }              
            	                                                                             	
    		// reset counters
    		$counter17 = true;           
        }

        return $return;
    }

    protected function simulateFinal85($result, $settings)
    {
    	$final85 = false;
    	$return = array(
			'correct_bets' 	=>	0,
			'wrong_bets'	=>	0,
			'profit'		=>	0,
			'total_games'	=>	0
		);

    	foreach ($result->result_array() as $linie) {            	            
            $this->db->where('match_id',$linie['id']);
            $this->db->order_by('min');
            $result2 = $this->db->get('z_goals');                

            foreach ($result2->result_array() as $linie2) {
                //print_r($linie2);            	            	
            	if($linie2['min'] >= $settings['min']) {
                    $final85 = true;
                }
            }
            	
            if ($final85) {                
                    $return['correct_bets']++;                    
                    $return['profit'] += $settings['stake'] * $settings['odds'] - $settings['stake'];
            }                            
            else {
                $return['wrong_bets']++;                    
                $return['profit'] -= 2;                    
            }              
            	                                                                     
        	$return['total_games']++;

    		// reset counters
    		$final85 = false;           
        }

        return $return;
    }

    protected function simulateFinal45($result, $settings)
    {
    	$final45 = true;
    	$return = array(
			'correct_bets' 	=>	0,
			'wrong_bets'	=>	0,
			'profit'		=>	0,
			'total_games'	=>	0
		);

    	foreach ($result->result_array() as $linie) {            	            
            $this->db->where('match_id',$linie['id']);
            $this->db->order_by('min');
            $result2 = $this->db->get('z_goals');                

            foreach ($result2->result_array() as $linie2) {
                //print_r($linie2);            	            	
            	if($linie2['min'] < $settings['min']) {
                    $final45 = false;
                }                
            }
            
            if ($final45) {
                if ($this->isOver($linie['score'], $settings['over'])) {
                	$return['correct_bets']++;                    
                    $return['profit'] += $settings['stake'] * $settings['odds'] - $settings['stake'];                                     
                } else {
                    $return['wrong_bets']++;                                        
                    $return['profit'] -= 2;                        
                }   
            }
                                      	                                                                    
        	$return['total_games']++;

    		// reset counters
    		$final45 = true;           
        }

        return $return;
    }

    protected function simulateTest($result, $settings)
    {
    	$this->load->model('step_model');
    	$strategies = array(6,7);
    	$stake = $settings['stake'];
    	$strategy = $strategies[0];

    	$east17 = false;
    	$return = array(
			'correct_bets' 	=>	0,
			'wrong_bets'	=>	0,
			'profit'		=>	0,
			'total_games'	=>	0,
			'avg_stake'		=>	0
		);

		$aux = $this->get_profit_by_setting($settings['id_setting']);
		$bank = $aux['profit'];

		if ($bank > 10000) {
			$strategy = $strategies[1];
		} 

		if ($bank <= 21) {
			$stake = 2;
		} else {
			$params['limit'] = 1;
    		$params['strategy_id'] = $strategy;
    		$params['amount'] = $bank;

    		$temp = $this->step_model->get_steps($params);
    		$stake = $temp[0]['stake'];
		}		

		$i = 0;

    	foreach ($result->result_array() as $linie) {    		   		
    		$return['avg_stake'] += $stake;
    		    		    		
            $this->db->where('match_id',$linie['id']);
            $this->db->order_by('min');
            $result2 = $this->db->get('z_goals');                

            foreach ($result2->result_array() as $linie2) {
                //print_r($linie2);            	
            	if($linie2['min'] <= $settings['min']) {
                    $east17 = true;
            	}
            }
            	
            if ($east17) {
                if ($this->isOver($linie['score'], $settings['over'])) {
                    $return['correct_bets']++;                    
                    $return['profit'] += $stake * $settings['odds'] - $stake;                    
                } else {
                    $return['wrong_bets']++;                    
                    $return['profit'] -= $stake;                    
                }
            }    
            	                                                                     
        	$return['total_games']++;

    		// reset counters
    		$east17 = false;
    		$i++;           
        }

        if ($i) {
        	$return['avg_stake'] = number_format($return['avg_stake'] / $i, 2);
        }
        
        return $return;
    }

    protected function simulateTest2($result, $settings)
    {
    	$this->load->model('step_model');
    	$strategies = array(6,7);
    	$stake = $settings['stake'];
    	$strategy = $strategies[0];

    	$east17 = false;
    	$return = array(
			'correct_bets' 	=>	0,
			'wrong_bets'	=>	0,
			'profit'		=>	0,
			'total_games'	=>	0,
			'avg_stake'		=>	0
		);
				
		$i = 0;
		$aux = $this->get_profit_by_setting(48);
		$bank = $aux['profit'];
		if ($bank > 10000) {
			$strategy = $strategies[1];
		}

    	foreach ($result->result_array() as $linie) {    					 
			if ($bank <= 21) {
				$stake = 2;
			} else {
				$params['limit'] = 1;
	    		$params['strategy_id'] = $strategy;
	    		$params['amount'] = $bank + $return['profit'];

	    		$temp = $this->step_model->get_steps($params);
	    		$stake = $temp[0]['stake'];
			}

    		$return['avg_stake'] += $stake;
    		    		    		
            $this->db->where('match_id',$linie['id']);
            $this->db->order_by('min');
            $result2 = $this->db->get('z_goals');                

            foreach ($result2->result_array() as $linie2) {
                //print_r($linie2);            	
            	if($linie2['min'] <= $settings['min']) {
                    $east17 = true;
            	}
            }
            	
            if ($east17) {
                if ($this->isOver($linie['score'], $settings['over'])) {
                    $return['correct_bets']++;                    
                    $return['profit'] += $stake * $settings['odds'] - $stake;                    
                } else {
                    $return['wrong_bets']++;                    
                    $return['profit'] -= $stake;                    
                }
            }    
            	                                                                     
        	$return['total_games']++;

    		// reset counters
    		$east17 = false;
    		$i++;           
        }

        if ($i) {
        	$return['avg_stake'] = number_format($return['avg_stake'] / $i, 2);
        }
        
        return $return;
    }

    protected function isOver($score, $goalNumber) 
    {
        $goals = explode('-', $score);
        $sum = $goals[0]+$goals[1];

        if($sum >= $goalNumber) return true;

        return false;
    }

    public function view_settings($filters = array())
    {
    	if (isset($filters['id_setting'])) {
			$this->db->where('id_setting', $filters['id_setting']);
		}
    	$result = $this->db->get('z_simulations');
        		                        
		if ($result->num_rows() == 0) {
			return FALSE;
		}
		
		$simulations = array();
		for($i=0;$i<7;$i++) {
			$simulations[$i]['profit'] = 0;
			$simulations[$i]['correct_percent'] = 0;
			$simulations[$i]['total_bets'] = 0;
			$simulations[$i]['count'] = 0;
		}

	    foreach ($result->result_array() as $sim) {
	        $date = date('w', strtotime($sim['match_date']));
	        $simulations[$date]['profit'] += $sim['profit'];
	        $simulations[$date]['correct_percent'] += $sim['correct_percent'];
	        $simulations[$date]['total_bets'] += $sim['total_bets']; 
	        $simulations[$date]['count']++;
		}

		foreach($simulations as $key=>$val) {
			$simulations[$key]['correct_percent'] = number_format($simulations[$key]['correct_percent']/$simulations[$key]['count'], 2);
		}

		print '<pre>';
		print_r($simulations);
		die;

		return $simulations;
    }
        
}

