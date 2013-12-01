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
    
    /**
    * List overs by country statistics
    * 
    */   

    public function over_country($setter=2.5)
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
                                    'name' => 'Number of Mathes',
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
                                    'name' => 'Number of Mathes',
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
    
}