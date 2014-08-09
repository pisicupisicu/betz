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

class Admincp6 extends Admincp_Controller 
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
            redirect('admincp6/livescore/list_statistics');		
	}
    
    /**
    * List with buttons to evry match statistics
    * 
    */   
    public function list_statistics()
	{

		$this->load->view('list_statistics');
	}

    /**
    * List with buttons to evry match statistics
    * 
    */   
    public function totals_stats()
	{
        $this->load->model('livescore/goal_model');
		$this->load->model('livescore/card_model');
             
        $data = array ();
        
        $mins = range(1,90);
		foreach($mins as $val) {
			$total_goals[] = $this->goal_model->count_goals($val); 
            $total_yellow[] = $this->card_model->count_yellow($val);
            $total_red = $this->card_model->count_red($val);
            $total_yellow_red = $this->card_model->count_yellow_red($val);
            $all_reds[] = $total_red + $total_yellow_red;

            
            //echo "min {$val} Total Goals:".$total_goals." &nbsp Total Yellow: ".$total_yellow." &nbsp Total Red: ".$all_reds."<br>";

		} 
        
       $data = array(
					'min' => $mins,
                    'goals' => $total_goals,
                    'yellows' => $total_yellow,
                    'reds' => $all_reds,
           			'form_title' => 'Total Goals & Cards per minutes',
					);
        
//        echo "<pre>";
//        print_r ($data);
//        die;
        
		$this->load->view('totals_stats',$data);
	}
    
    
    /**
    * List with buttons to every match statistics
    * 
    */   

    public function range_stats()
	{   
        $this->load->model('livescore/goal_model');
		$this->load->model('livescore/card_model');
        $this->load->model('livescore/match_model');
        $this->load->library('dataset');
        
        if(isset($_GET['score'])) $filters['score'] = $_GET['score']; 
        $filters=array();
        $matches = $this->match_model->get_matches_by_score($filters); 
        
        $total_matches = count($matches); // Numarul total de meciuri cu scorul introdus de user, il folosesc la calculul procentului 
            
       $all_scores = array();
        
        foreach ($matches as $val) {                        
            $details = $this->match_model->get_match_details($val['id']);            
              foreach ($details as $detail) {

                if(!array_key_exists($detail['score'], $all_scores)) {
                    $mins = range(0,120);                
                    foreach($mins as $min) {
                        $all_scores[$detail['score']][$min] = 0;
                    }
                 }
                 
                 $all_scores[$detail['score']][$detail['min']]++;            
            
              }

        }

        echo "<pre>";
        print_r ($all_scores); 
        
        die;
        
//        foreach($all_scores as $key=>$val) {
//            asort($all_scores[$key],SORT_NUMERIC);
//            $all_scores[$key] = array_reverse($all_scores[$key], true);
//        }
          
        $total_match = $this->match_model->get_num_rows(); // Numarul total de meciuri il folosesc la calculul procentului        

       $data = array(
	                'score' => $_GET['score'],
           			'form_title' => 'Range Selector per minutes',
                    'form_action' => site_url('admincp6/livescore/range_stats'),
					);
  
		$this->load->view('range_stats',$data);
	}
    
/************************************************************************************************
* List overs by country statistics
* 
***********************************************************************************************/   

    public function over_country($setter=2.5)
	{ 
        $this->load->model('match_model'); 
        $this->load->model('goal_model');
        $this->load->library('dataset');
        $this->load->library('asciihex');
        
        $columns = array(
                            array(
                                    'name' => 'Country',
                                    'width' => '70%',                                        
                                    'filter' => 'country_name',
                                    'type' => 'text',                                        
                                    ),
            
                            array(
                                    'name' => 'OVER',
                                    'width' => '10%',                                                                           
                                    ),
            
                            array(
                                    'name' => 'Number of Matches',
                                    'width' => '10%',                                                                           
                                    ),
                            
                            array(
                                    'name' => 'OVER',
                                    'width' => '10%',                                                                         
                                    'type' => 'text',
                                    ),                                                 
                                         
                    );        

        $filters = array();
        if(isset($_GET['filters'])) {
                $filters_decode = unserialize(base64_decode($this->asciihex->HexToAscii($_GET['filters'])));
            }

        
        if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset']; 
        
        if(!isset($_GET['setter'])) $_GET['setter'] = 2.5; 
        $filters['setter'] = $_GET['setter'];
        
        $this->dataset->columns($columns);
        $this->dataset->datasource('goal_model','over_stats',$filters);
        $this->dataset->base_url(site_url('admincp6/livescore/over_country'));
        $this->dataset->rows_per_page(300);

        $this->dataset->initialize(); 
        
        $data = array(
	                'setter' => $_GET['setter'],
                    'form_action' => site_url('admincp6/livescore/over_country'),
					);
        
        $this->load->view('over_country_stats',$data); 
        
    }
    
/**
* List Over scores by competitions statistics
* 
*/   

    public function over_competition($country,$setter=2.5)
	{ 
        $this->load->model('match_model'); 
        $this->load->model('goal_model');
        $this->load->library('dataset');
        $this->load->library('asciihex');
        
        $columns = array(
                            array(
                                    'name' => 'Competition',
                                    'width' => '60%',                                        
                                    'filter' => 'competition_name',
                                    'type' => 'text',                                        
                                    ),
            
                           array(
                                    'name' => 'Country',
                                    'width' => '10%',                                                                           
                                    ),
            
                            array(
                                    'name' => 'OVER',
                                    'width' => '10%',                                                                           
                                    ),
            
                            array(
                                    'name' => 'Number of Matches',
                                    'width' => '10%',                                                                           
                                    ),
                            
                            array(
                                    'name' => 'OVER',
                                    'width' => '10%',                                                                         
                                    'type' => 'text',
                                    ),                                                 
                                         
                    );        

        $filters = array();
        if(isset($_GET['filters'])) {
                $filters_decode = unserialize(base64_decode($this->asciihex->HexToAscii($_GET['filters'])));
            }

        
        if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset']; 
        
        if(!isset($_GET['setter'])) $_GET['setter'] = 2.5; 
        $filters['setter'] = $_GET['setter'];
        $filters['country_name'] = $country;
        
        $this->dataset->columns($columns);
        $this->dataset->datasource('goal_model','over_competitions',$filters);
        $this->dataset->base_url(site_url('admincp6/livescore/over_country'));
        $this->dataset->rows_per_page(300);

        $this->dataset->initialize(); 
        
        $data = array(
	                'setter' => $_GET['setter'],
                        'country' => $country,
                        'form_action' => site_url('admincp6/livescore/over_competition'),

					);
        
        $this->load->view('over_competition_stats',$data); 
        
    }
    
/*********
* List Over scores by ALL Competitions statistics
* 
*********/   

    public function over_competition_list($setter=2.5)
	{ 
        $this->load->model('match_model'); 
        $this->load->model('goal_model');
        $this->load->library('dataset');
        $this->load->library('asciihex');
        
        $columns = array(
                            array(
                                    'name' => 'Competition',
                                    'width' => '60%',                                        
                                    'filter' => 'competition_name',
                                    'type' => 'text',                                        
                                    ),
            
                           array(
                                    'name' => 'Country',
                                    'width' => '10%',                                                                           
                                    ),
            
                            array(
                                    'name' => 'OVER',
                                    'width' => '10%',                                                                           
                                    ),
            
                            array(
                                    'name' => 'Number of Matches',
                                    'width' => '10%',                                                                           
                                    ),
                            
                            array(
                                    'name' => 'OVER',
                                    'width' => '10%',                                                                         
                                    'type' => 'text',
                                    ),                                                 
                                         
                    );        

        $filters = array();
        if(isset($_GET['filters'])) {
                $filters_decode = unserialize(base64_decode($this->asciihex->HexToAscii($_GET['filters'])));
            }

        
        if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset']; 
        
        if(!isset($_GET['setter'])) $_GET['setter'] = 2.5; 
        $filters['setter'] = $_GET['setter'];
        
        $this->dataset->columns($columns);
        $this->dataset->datasource('goal_model','over_competitions',$filters);
        $this->dataset->base_url(site_url('admincp6/livescore/over_country'));
        $this->dataset->rows_per_page(300);

        $this->dataset->initialize(); 
        
        $data = array(
	                'setter' => $_GET['setter'],
                        'form_action' => site_url('admincp6/livescore/over_competition_list'),

					);
        
        $this->load->view('over_competition_list',$data); 
        
    }
    
/***********************************************************************************************
* List Under by Country
* 
************************************************************************************************/     
    public function under_country($setter=2.5)
	{ 
        $this->load->model('match_model'); 
        $this->load->model('goal_model');
        $this->load->library('dataset');
        $this->load->library('asciihex');
        
        $columns = array(
                            array(
                                    'name' => 'Country',
                                    'width' => '80%',                                        
                                    'filter' => 'country_name',
                                    'type' => 'text',                                        
                                    ),
            
                            array(
                                    'name' => 'UNDER',
                                    'width' => '10%',                                                                           
                                    ),
            
                            array(
                                    'name' => 'Number of Matches',
                                    'width' => '10%',                                                                           
                                    ),
                            
                            array(
                                    'name' => 'UNDER',
                                    'width' => '10%',                                                                         
                                    'type' => 'text',
                                    ),                                                 
                                         
                    );        

        $filters = array();
        if(isset($_GET['filters'])) {
                $filters_decode = unserialize(base64_decode($this->asciihex->HexToAscii($_GET['filters'])));
            }

        
        if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset']; 
        
        if(!isset($_GET['setter'])) $_GET['setter'] = 2.5; 
        $filters['setter'] = $_GET['setter'];
        
        $this->dataset->columns($columns);
        $this->dataset->datasource('goal_model','under_stats',$filters);
        $this->dataset->base_url(site_url('admincp6/livescore/under_country'));
        $this->dataset->rows_per_page(300);

        $this->dataset->initialize(); 
        
        $data = array(
	                'setter' => $_GET['setter'],
                        'form_action' => site_url('admincp6/livescore/under_country'),
					);
        
        $this->load->view('under_country_stats',$data); 
        
    }
/**
* List Under scores by competitions statistics
* 
*/   
    public function under_competition($country,$setter=2.5)
	{ 
        $this->load->model('match_model'); 
        $this->load->model('goal_model');
        $this->load->library('dataset');
        $this->load->library('asciihex');
        
        $columns = array(
                            array(
                                    'name' => 'Competition',
                                    'width' => '60%',                                        
                                    'filter' => 'country_name',
                                    'type' => 'text',                                        
                                    ),
                            array(
                                    'name' => 'Country',
                                    'width' => '10%',                                                                           
                                    ),
                            array(
                                    'name' => 'UNDER',
                                    'width' => '10%',                                                                           
                                    ),
            
                            array(
                                    'name' => 'Number of Matches',
                                    'width' => '10%',                                                                           
                                    ),
                            
                            array(
                                    'name' => 'UNDER',
                                    'width' => '10%',                                                                         
                                    'type' => 'text',
                                    ),                                                 
                                         
                    );        

        $filters = array();
        if(isset($_GET['filters'])) {
                $filters_decode = unserialize(base64_decode($this->asciihex->HexToAscii($_GET['filters'])));
            }

        
        if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset']; 
        
        if(!isset($_GET['setter'])) $_GET['setter'] = 2.5; 
        $filters['setter'] = $_GET['setter'];
        $filters['country_name'] = $country;
                
        $this->dataset->columns($columns);
        $this->dataset->datasource('goal_model','under_competitions',$filters);
        $this->dataset->base_url(site_url('admincp6/livescore/under_country'));
        $this->dataset->rows_per_page(300);

        $this->dataset->initialize(); 
        
        $data = array(
	                'setter' => $_GET['setter'],
                        'country' => $country,
                        'form_action' => site_url('admincp6/livescore/under_competition'),
					);
        
        $this->load->view('under_competition_stats',$data); 
        
    }
    
/**
* List Under scores list by ALL Competitions
* 
*/   
    public function under_competition_list($setter=2.5)
	{ 
        $this->load->model('match_model'); 
        $this->load->model('goal_model');
        $this->load->library('dataset');
        $this->load->library('asciihex');
        
        $columns = array(
                            array(
                                    'name' => 'Competition',
                                    'width' => '60%',                                        
                                    'filter' => 'country_name',
                                    'type' => 'text',                                        
                                    ),
                            array(
                                    'name' => 'Country',
                                    'width' => '10%',                                                                           
                                    ),
                            array(
                                    'name' => 'UNDER',
                                    'width' => '10%',                                                                           
                                    ),
            
                            array(
                                    'name' => 'Number of Matches',
                                    'width' => '10%',                                                                           
                                    ),
                            
                            array(
                                    'name' => 'UNDER',
                                    'width' => '10%',                                                                         
                                    'type' => 'text',
                                    ),                                                 
                                         
                    );        

        $filters = array();
        if(isset($_GET['filters'])) {
                $filters_decode = unserialize(base64_decode($this->asciihex->HexToAscii($_GET['filters'])));
            }

        
        if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset']; 
        
        if(!isset($_GET['setter'])) $_GET['setter'] = 2.5; 
        $filters['setter'] = $_GET['setter'];
                
        $this->dataset->columns($columns);
        $this->dataset->datasource('goal_model','under_competitions',$filters);
        $this->dataset->base_url(site_url('admincp6/livescore/under_country'));
        $this->dataset->rows_per_page(300);

        $this->dataset->initialize(); 
        
        $data = array(
	                'setter' => $_GET['setter'],
                        'form_action' => site_url('admincp6/livescore/under_competition_list'),
					);
        
        $this->load->view('under_competition_list',$data); 
        
    }

    public function stats_bet_methods()
    {
        $this->load->model('bet_model');
        $this->load->library('dataset');
        $this->load->library('asciihex');

        $filters = array();

        $columns = array(
            array(
                    'name' => 'STAT',
                    'width' => '50%',                                                            
                    'type' => 'text',                                        
                    ),
            array(
                    'name' => 'RESULT',
                    'width' => '50%',                                                            
                    'type' => 'text',                                        
                    ),                                                                                                        
        );

        $this->dataset->columns($columns);
        $this->dataset->datasource('bet_model','get_bet_stats_methods',$filters);
        $this->dataset->base_url(site_url('admincp6/livescore/stats_bet'));
        $this->dataset->rows_per_page(300);

        $this->dataset->initialize();

        //$data = $this->bet_model->get_bet_stats();

        $this->load->view('stats_bet_methods');
    }

    public function stats_bet_by_method($method_id)
    {
        $this->load->model('bet_model');
        $this->load->library('dataset');
        $this->load->library('asciihex');

        $filters = array();
        $filters['method_id'] = $method_id;

        $columns = array(
            array(
                    'name' => 'STAT',
                    'width' => '50%',                                                            
                    'type' => 'text',                                        
                    ),
            array(
                    'name' => 'RESULT',
                    'width' => '50%',                                                            
                    'type' => 'text',                                        
                    ),                                                                                                        
        );

        $this->dataset->columns($columns);
        $this->dataset->datasource('bet_model','get_bet_stats_by_method',$filters);
        $this->dataset->base_url(site_url('admincp6/livescore/stats_bet_by_method'));
        $this->dataset->rows_per_page(300);

        $this->dataset->initialize();
        
        $this->load->view('stats_bet_by_method');
    }

    public function stats_bet_countries()
    {
        $this->load->model('bet_model');
        $this->load->library('dataset');
        $this->load->library('asciihex');

        $filters = array();

        $columns = array(
            array(
                    'name' => 'STAT',
                    'width' => '50%',                                                            
                    'type' => 'text',                                        
                    ),
            array(
                    'name' => 'RESULT',
                    'width' => '50%',                                                            
                    'type' => 'text',                                        
                    ),                                                                                                        
        );

        $this->dataset->columns($columns);
        $this->dataset->datasource('bet_model','get_bet_stats_countries',$filters);
        $this->dataset->base_url(site_url('admincp6/livescore/stats_bet_countries'));
        $this->dataset->rows_per_page(300);

        $this->dataset->initialize();

        $this->load->view('stats_bet_countries');
    }

    public function stats_bet_by_country($country_id)
    {
        $this->load->model('bet_model');
        $this->load->library('dataset');
        $this->load->library('asciihex');

        $filters = array();
        $filters['country_id'] = $country_id;

        $columns = array(
            array(
                    'name' => 'STAT',
                    'width' => '50%',                                                            
                    'type' => 'text',                                        
                    ),
            array(
                    'name' => 'RESULT',
                    'width' => '50%',                                                            
                    'type' => 'text',                                        
                    ),                                                                                                        
        );

        $this->dataset->columns($columns);
        $this->dataset->datasource('bet_model','get_bet_stats_by_country',$filters);
        $this->dataset->base_url(site_url('admincp6/livescore/stats_bet_by_country'));
        $this->dataset->rows_per_page(300);

        $this->dataset->initialize();
        
        $this->load->view('stats_bet_by_country');
    }

    function list_bets_by_method_and_country($method_id, $country_id)
    {
        $this->load->library('dataset');
            
        $columns = array(
                        array(
                            'name' => 'ID',
                            'type' => 'id',
                            'width' => '1%',
                           ),                       
                        array(
                            'name' => 'PB',
                            'width' => '2%',
                            'type' => 'text',                            
                            ),
                        array(
                            'name' => 'TV',
                            'width' => '2%',
                            'type' => 'text',                            
                            ),
                        array(
                            'name' => 'Event Name',
                            'type' => 'text',
                            'width' => '17%',                            
                            ),
                        array(
                            'name' => 'Event Date',
                            'type' => 'text',
                            'width' => '8%',                            
                            ),
                       array(
                            'name' => 'Country',
                            'type' => 'text',
                            'width' => '10%',                            
                            ),
                        array(
                            'name' => 'Event',
                            'type' => 'text',
                            'width' => '8%',
                            ),
                        array(
                            'name' => 'Stake',
                            'type' => 'text',
                            'width' => '3%',
                            ),
                        array(
                            'name' => 'Profit/Loss',
                            'width' => '5%',
                            'type' => 'text',
                            ),
                        array(
                            'name' => 'Odds',
                            'width' => '4%',
                            'type' => 'text',                            
                        ),
                        array(
                            'name' => 'Mkt Type',
                            'width' => '11%',
                            'type' => 'text',
                        ),
                        array(
                            'name' => 'Mkt Select',
                            'width' => '5%',
                            'type' => 'text',
                        ),                        
                        array(
                            'name' => 'Strategy Name',
                            'width' => '11%',
                            'type' => 'text',                            
                        ),
                        array(
                            'name' => 'Score',
                            'width' => '3%',
                            'type' => 'text',
                        ),
            );
        if ($this->user_model->logged_in() and $this->user_model->is_admin()) {
            $columns[] = array(
                            'name' => 'User',
                            'width' => '5%',
                            'type' => 'text',
                            'filter' => 'username',
                            'sort_column' => 'username',
                           );
        }
          $columns[] =             
                        array(
                            'name' => '',
                            'width' => '5%',
                            'type' => 'text',
                        );
                        
                    
                
        $filters = array();    
        $filters['limit'] = 30;

        if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset'];
        else $filters['offset'] = 0;

        $filters['method_id'] = $method_id;
        $filters['country_id'] = $country_id;

        $this->load->model('method_model');
        $temp = $this->method_model->get_method($method_id);
        $method = $temp['method_name'];

        $this->load->model('country_model');
        $temp = $this->country_model->get_country($country_id);
        $country = $temp['country_name'];        
                
        $this->dataset->columns($columns);
        $this->dataset->datasource('bet_model','get_bets',$filters);
        $this->dataset->base_url(site_url('admincp6/livescore/list_bets_by_method_and_country/'.$method_id.'/'.$country_id));
        $this->dataset->rows_per_page($filters['limit']);

        // total rows
        $this->load->model('bet_model');
        $total_rows = $this->bet_model->get_num_rows_bets($filters);

        $this->dataset->total_rows($total_rows);
        
        // initialize the dataset
        $this->dataset->initialize();
        
        $data = array(
                'limit' => $filters['limit'],
                'offset' => $filters['offset'],
                'method' => $method,
                'country' => $country
        );
        
        $this->load->view('list_bets_by_method_and_country', $data);
    }
    
}