<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


/**

* Content Control Panel

* 

* Displays all control panel forms, datasets, and other displays

*

* @author Weblight.ro

* @copyright Weblight.ro

* @package BJ Tool

*

*/


class Admincp4 extends Admincp_Controller 

{

                

    function __construct()

    {



        parent::__construct();

                

        $this->admin_navigation->parent_active('livescore');

                                        

        //error_reporting(E_ALL^E_NOTICE);

        //error_reporting(E_WARNING);

    }

        

    function index () 

    {

            redirect('admincp/livescore/list_matches');        

    }

     /**
	* Add Match
	*
	* Add new Match form, preloaded with variables
	*
	* @param int $id the ID of the bet
    */

	function add_match($action = 'new', $id = false) {

		$this->load->model('match_model');
		$this->load->model('competition_model');
        $this->load->model('team_model');
		$this->load->model('country_model');
        
	    $match = $this->match_model->get_match($id);
        
        $countries = array();
        $countries = array_merge(array('Select country'),$countries);
	    $countries = $this->country_model->get_name_countries();

            $competition = array();
            $competition = $this->competition_model->get_competitions();           
            foreach ($competition as $comp) {		
			$competition_name[$comp['competition_id']] = $comp['name'];  
                }

            $teams = array();
            $teams = $this->team_model->get_teams();
            foreach ($teams as $team) {		
			$team_name[$team['team_id']] = $team['name'];
                        $team_country[$team['country_id']] = $team['country_name'];  
                }

            //echo"<pre>";
           //print_r ($match);
           //die;

		$data = array(
					'country_name' => $countries,
                    'competition_name' => $competition_name,
                    'team_name' => $team_name,
					'form' => $match,
					'form_title' => 'Add New Match',
					'form_action' => site_url('admincp4/livescore/post_match/new'),
                    'action' => 'new',
					);

		$this->load->view('add_match',$data);

	}
    
    /**
	* Edit Match
	*
	* Show the Match form, preloaded with variables
	*
	* @param int $id the ID of the bet
    */

	function edit_match($id) {

		$this->load->model('match_model');
		$this->load->model('competition_model');
        $this->load->model('team_model');
        $this->load->model('goal_model');
        $this->load->model('card_model');
		$this->load->model('country_model');


	    $match = $this->match_model->get_match($id);

        $filters['country_id'] = $match['country_id'];

        $countries = array();
        $countries = array_merge(array('Select country'),$countries);
	    $countries = $this->country_model->get_name_countries();

        $competition = array();
        $competition = $this->competition_model->get_competitions($filters);           
            foreach ($competition as $comp) {		
			$competition_name[$comp['competition_id']] = $comp['name'];  
                }

               
            $teams = array();
            $teams = $this->team_model->get_teams($filters);
            foreach ($teams as $team) {		
			$team_name[$team['team_id']] = $team['name'];
                        $team_country[$team['country_id']] = $team['country_name'];  
                }

        if(!isset($match) || empty($match)) {
            $this->notices->SetError('No match found');
            redirect('admincp/livescore/list_matches');
        }

        $home   =   $this->team_model->get_team($match['team1']);
        $away   =   $this->team_model->get_team($match['team2']);
        $goals  =   $this->goal_model->get_goals_by_match($id);
        $cards  =   $this->card_model->get_cards_by_match($id);

        //print_r($cards);

		$data = array(
                    'match' =>  $match,
                    'home'  =>  $home,
                    'away'  =>  $away,
                    'goals' =>  $goals,
                    'cards' =>  $cards,
					'id_match' => $match['id'],
					'id_country' => $match['country_id'],
					'country_name' => $countries,
                    'id_competition' => $match['competition_id'],
                    'competition_name' => $competition_name,
					'home_team_id' => $match['team1'],
					'away_team_id' => $match['team2'],
                    'team_name' => $team_name,
                    'score' => $match['score'],
					'livescore_link' => $match['link_match'],
					'match_date' => $match['match_date'],
					'form' => $match,
					'form_title' => 'Edit Match',
					'form_action' => site_url('admincp4/livescore/post_match/edit/'.$match['id']),
                    'action' => 'edit',
					);

		$this->load->view('edit_match',$data);

	} 
        
        /**
	* Handle New/Edit Match Post
	*/
	function post_match($action, $id = false){	       
		
		$this->load->model('match_model');
		
		// content
		$ID_match = $this->input->post('ID_match');
                $competition_name = $this->input->post('competition_name');
                $match_date = $this->input->post('match_date');
                $home_team = $this->input->post('home_team');
                $away_team = $this->input->post('away_team');                
                $score = $this->input->post('score');
                $livescore = $this->input->post('livescore');
                
                $update_fields = array(
					//'id'            => $ID_match,
					'competition_id'=> $competition_name,
					'match_date'    => $match_date,
					'team1'         => $home_team,
					'team2'         => $away_team,
					'score'         => $score,
					'link_complete' => $livescore,
						);
                
                 $insert_fields = array(
					'competition_id'=> $competition_name,
					'match_date'    => $match_date,
					'team1'         => $home_team,
					'team2'         => $away_team,
					'score'         => $score,
					'link'          => $livescore,
                    'link_complete' =>  'http://www.livescore.com/soccer/'.$livescore,
						);

                //print_r ($insert_fields);
                //die;
                
		if ($action == 'new') {
			$bet_id = $this->match_model->new_match($insert_fields);
												
			$this->notices->SetNotice('Match added successfully.');
            redirect('admincp4/livescore/step_two/'.$bet_id);
		}
		else {
			$bet_id = $this->match_model->update_match($update_fields,$ID_match);

			$this->notices->SetNotice('Match edited successfully.');
		}
		
                
		redirect('admincp/livescore/list_matches');
		
		return TRUE;
	}
	
	
    /**
	* Step 2 la formularul de add
	*
	* Show the Add form, preloaded with variables
	*
	* @param int $id the ID of the bet
    */

	function step_two($id) {

		$this->load->model('match_model');
		$this->load->model('competition_model');
        $this->load->model('team_model');
		$this->load->model('country_model');
        $this->load->model('goal_model');
        $this->load->model('card_model');
  
	    $match = $this->match_model->get_match($id);

        //$filters['country_id'] = $match['country_id'];
        $countries = array();
        $countries = array_merge(array('Select country'),$countries);
	    $countries = $this->country_model->get_name_countries();

        $competition = array();
        $competition = $this->competition_model->get_competitions();           
        foreach ($competition as $comp) {		
		$competition_name[$comp['competition_id']] = $comp['name'];  
                }

        $teams = array();
        $teams = $this->team_model->get_teams();
        foreach ($teams as $team) {		
		    $team_name[$team['team_id']] = $team['name'];
            $team_country[$team['country_id']] = $team['country_name'];  
        }

        $home   =   $this->team_model->get_team($match['team1']);
        $away   =   $this->team_model->get_team($match['team2']);
        $goals  =   $this->goal_model->get_goals_by_match($id);
        $cards  =   $this->card_model->get_cards_by_match($id);
                
		$data = array(
                    'match' =>  $match,
                    'home'  =>  $home,
                    'away'  =>  $away,
                    'goals' =>  $goals,
                    'cards' =>  $cards,
					'id_match' => $match['id'],
					'id_country' => $match['country_id'],
					'country_name' => $countries,
                    'id_competition' => $match['competition_id'],
                    'competition_name' => $competition_name,
					'home_team_id' => $match['team1'],
					'away_team_id' => $match['team2'],
                    'team_name' => $team_name,
                    'score' => $match['score'],
					'livescore_link' => $match['link_match'],
					'match_date' => $match['match_date'],
					'form' => $match,
					'form_title' => 'Add Score to this match',
					'form_action' => site_url('admincp4/livescore/post_step/new/'.$match['id']),
                    'action' => 'step',
					);

		$this->load->view('add_match',$data);

	} 
	
	/**
	* Handle NEW Score for Match
	*/
	function post_step($id = false){
		
		$this->load->model('goal_model');
		$this->load->model('card_model');
		$this->load->model('match_model');
		
		// content
		$ID_match = $this->input->post('ID_match');
		$minutes_select = $this->input->post('minutes_select');
        $event_types = $this->input->post('event_types');
        $card_types = $this->input->post('card_types');
        $card_owner = $this->input->post('card_owner');
		$score_step = $this->input->post('score_step');
        $goal_scorer = $this->input->post('goal_scorer');
		$assist = $this->input->post('assist');
		$type = $this->input->post('type');
        $team_types = $this->input->post('team_types');
		
		if ($event_types=="card") {
			
		$insert_fields = array(
					'match_id'   => $ID_match,
					'card_type'  => $card_types,
					'min'        => $minutes_select,
					'player'     => $card_owner,
					'team'       => $team_types,
						);
			
		$this->card_model->new_card($insert_fields);
														
		$this->notices->SetNotice('Score added successfully.');
        redirect('admincp4/livescore/step_two/'.$ID_match);
		
                //print_r ($card_fields);
                //die;
				
		}else { 
		
		$insert_fields = array(
					'match_id'  => $ID_match,
					'score'     => $score_step,
					'min'       => $minutes_select,
					'assist'    => $assist,
					'type'      => $type,
					'player'    => $goal_scorer,
					'team'      => $team_types,
						);
		
		$this->goal_model->new_goal($insert_fields);
												
		$this->notices->SetNotice('Score added successfully.');
        redirect('admincp4/livescore/step_two/'.$ID_match);
		
               //print_r ($card_fields);
               //die; 	
		}
		
		return TRUE;
	}

    /**
	*View Competition Selects description
	*
	*/
	
	function view_competitions_selects($id) 
	{	
	$this->load->model('competition_model');

        $filters['country_id'] = $id;
		
	$competition = array();

         $competition = $this->competition_model->get_competitions($filters);           

         foreach ($competition as $comp) {		

		 $competition_name[$comp['competition_id']] = $comp['name'];  

         }
       
        if(empty($competition_name)) $competition_name = array(0=>'None');              
		
		echo form_dropdown('competition_name',$competition_name);
		
	} 
    
    function view_competitions_selects_selected($id_country,$id_competition)
    {
        $this->load->model('competition_model');
        $this->load->model('country_model');
        $filters['country_id'] = $id_country;

            

            $countries = array();

            $countries = array_merge(array('Select country'),$countries);

	    $countries = $this->country_model->get_name_countries();



            $competition = array();

            $competition = $this->competition_model->get_competitions($filters);           

            foreach ($competition as $comp) {		

			$competition_name[$comp['competition_id']] = $comp['name'];  

                }
        echo form_dropdown('competition_name',$competition_name,$id_competition);
    }
	
	 /**
	*View Home team Selects description
	*
	*/
	
	function view_hometeam_selects($id) 
	{	
	$this->load->model('team_model');
	$filters['country_id'] = $id;
		
	$teams = array();
        $teams = $this->team_model->get_teams($filters);

         foreach ($teams as $team) {		
			$team_name[$team['team_id']] = $team['name'];
         }
       
        if(empty($team_name)) $team_name = array(0=>'None');              
		echo form_dropdown('home_team',$team_name);

	}
    
    function view_hometeam_selects_selected($id,$home_team_id) 
	{	
	$this->load->model('team_model');
	$filters['country_id'] = $id;
		
	$teams = array();
        $teams = $this->team_model->get_teams($filters);

         foreach ($teams as $team) {		
			$team_name[$team['team_id']] = $team['name'];
         }
       
        if(empty($team_name)) $team_name = array(0=>'None');              
		echo form_dropdown('home_team',$team_name,$home_team_id);

	}

     /**
	*View Home team Selects description
	*
	*/
	
	function view_awayteam_selects($id) 
	{	
	$this->load->model('team_model');
	$filters['country_id'] = $id;
		
	$teams = array();
        $teams = $this->team_model->get_teams($filters);

         foreach ($teams as $team) {		
			$team_name[$team['team_id']] = $team['name'];
         }
       
        if(empty($team_name)) $team_name = array(0=>'None');              
		echo form_dropdown('away_team',$team_name);

	}
    
    function view_awayteam_selects_selected($id,$away_team_id) 
	{	
	$this->load->model('team_model');
	$filters['country_id'] = $id;
		
	$teams = array();
        $teams = $this->team_model->get_teams($filters);

         foreach ($teams as $team) {		
			$team_name[$team['team_id']] = $team['name'];
         }
       
        if(empty($team_name)) $team_name = array(0=>'None');              
		echo form_dropdown('away_team',$team_name,$away_team_id);

	}
    
    function get_old_match($id)
    {
        $this->load->model('match_model');
        $match = $this->match_model->get_match($id);
        //iei meciu
        echo json_encode($match);
    }
    
    //**************************** Edit *******************//
    
        function edit_score($id)
    {
        $this->load->model('match_model');
        $this->load->model('team_model');
        $this->load->model('goal_model');
        $this->load->model('card_model');
                
        $match = $this->match_model->get_match($id);
        if(!isset($match) || empty($match)) {
            $this->notices->SetError('No match found');
            redirect('admincp/livescore/list_matches');
        }

        $home   =   $this->team_model->get_team($match['team1']);
        $away   =   $this->team_model->get_team($match['team2']);
        $goals  =   $this->goal_model->get_goals_by_match($id);
        $cards  =   $this->card_model->get_cards_by_match($id);

        //print_r($cards);

        $data = array(
                        'match' =>  $match,
                        'home'  =>  $home,
                        'away'  =>  $away,
                        'goals' =>  $goals,
                        'cards' =>  $cards,
                    );
        
        $this->load->view('view_match',$data);        
        
    }
    

    //********************************** Statistics *******************************// 



        /**

	* Stats Profit Loss in functie de markets

	*

	* Show Stats Profit Loss in functie de markets

	*

	* @param int $id the ID of the league

	*

	* @return string The email form view

	*/

	function profit_loss_stats() {

		$this->load->model('livescore/bet_model');

		$this->load->model('livescore/market_model');

		$this->load->model('livescore/stats_model');

		

		$markets = $this->market_model->get_markets();

		

		$profit = array();

		

		foreach($markets as $val) {

         

	// Afisez profitul pentru fiecare market in functie de ID-ul marketului respectiv



		$profit_markets = $this->stats_model->sum_profit_markets($val['ID_market']); 

                         

	// Afisez loss-ul pentru fiecare market in functie de ID-ul marketului respectiv



		$loss_markets = $this->stats_model->sum_loss_markets($val['ID_market']);

		

	// Afisez profit-ul pentru fiecare BACK / LAY bet pentru fiecare market in functie de ID-ul marketului respectiv



		$profit_back_bets = $this->stats_model->sum_back_profit($val['ID_market']);

		$profit_lay_bets = $this->stats_model->sum_lay_profit($val['ID_market']);



	// Bag valorile in array-uri



		$profit[] = $profit_markets;

		$loss[] = $loss_markets;

		

		$profit_back[] = $profit_back_bets;

		$profit_lay[] = $profit_lay_bets;

		

		}



	

		$data = array(

					  'markets' => $markets,

					  'profit' => $profit,

					  'loss' => $loss,

					  'profit_back' => $profit_back,

					  'profit_lay' => $profit_lay,

					  );

	

		$this->load->view('profit_loss_stats',$data);

	}         

        

//********************************** Statistics *******************************// 



}



