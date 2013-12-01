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

class Admincp7 extends Admincp_Controller 
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
            redirect('admincp/livescore/list_competitions');		
	}

    function update_filters()
    {
        $this->load->library('asciihex');
        $filters = array();

        foreach($_POST as $key=>$val) {
                if(in_array($val,array('filter results','start date','end date'))) {
                    unset($_POST[$key]);
                }
            }

        if(!empty($_POST['match_date_start'])) {
           $filters['match_date_start'] = $_POST['match_date_start'];
        }
        if(!empty($_POST['match_date_end'])) {
           $filters['match_date_end'] = $_POST['match_date_end'];
        }
        if(!empty($_POST['country_name'])) {
           $filters['country_name'] = $_POST['country_name'];
        }
        if(!empty($_POST['team1'])) {
           $filters['team1'] = $_POST['team1'];
        }
        if(!empty($_POST['team2'])) {
           $filters['team2'] = $_POST['team2'];
        }
        if(!empty($_POST['score'])) {
           $filters['score'] = $_POST['score'];
        }
        if(!empty($_POST['min'])) {
           $filters['min'] = $_POST['min'];
        }        

        $filters = $this->CI->asciihex->AsciiToHex(base64_encode(serialize($filters)));

        echo $filters;   
    }    

	function list_matches()
    {                    
        $this->load->model('match_model');
        $this->load->model('card_model');
        $this->load->library('dataset');

        $filters    =   array();
        $filters['parsed']  =   0;
        $unparsed   =   $this->match_model->get_num_rows($filters);

        $this->admin_navigation->module_link('Parse results : '.$unparsed,site_url('admincp5/livescore/parse_matches'));
        $this->admin_navigation->module_link('Add match',site_url('admincp4/livescore/add_match'));  
        
        $columns = array(
                            array(
                                    'name' => 'COUNTRY',
                                    'width' => '10%',                                        
                                    'filter' => 'country_name',
                                    'type' => 'text',                                        
                                    'sort_column' => 'country_name',
                                    ),
                            array(
                                    'name' => 'COMPETITION',
                                    'width' => '10%',
                                    'filter' => 'competition_name',
                                    'type' => 'name',                                    
                                    'sort_column' => 'competition_name',                                        
                                    ),
                            array(
                                    'name' => 'DATE',
                                    'width' => '15%',                                     
                                    'filter' => 'match_date',
                                    'type' => 'date',
                                    'field_start_date' => '2013-01-01',
                                    'field_end_date' => '2013-12-31',
                                    'sort_column' => 'match_date',                                       
                                    ),
                            array(
                                    'name' => 'HOME',
                                    'width' => '15%',                                        
                                    'filter' => 'team1',
                                    'type' => 'text',                                        
                                    'sort_column' => 'team1',
                                    ),
                            array(
                                    'name' => 'AWAY',
                                    'width' => '15%',                                        
                                    'filter' => 'team2',
                                    'type' => 'text',                                        
                                    'sort_column' => 'team2',
                                    ),
                            array(
                                    'name' => 'SCORE',
                                    'width' => '5%',
                                    'filter'    =>  'score',                                        
                                    'type' => 'text',
                                    'sort_column'   =>  'score',
                                    ),                                         
                           array(
                                    'name' => 'LINK COMPLETE',
                                    'width' => '20%',                                        
                                    'type' => 'text,'
                                    ),
                            array(
                                    'name' => 'View',
                                    'width' => '5%',                                        
                                    'type' => 'text,'
                                    ), 
                            array(
                                    'name' => 'Edit',
                                    'width' => '5%',                                        
                                    'type' => 'text,'
                                    ),
                                                                             
                                    
                    );
        
            $filters = array();    
            $filters['limit'] = 20;

            if(isset($_GET['filters'])) {
                $filters_decode = unserialize(base64_decode($this->asciihex->HexToAscii($_GET['filters'])));
            }
                       
            if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset'];                 
            if(isset($_GET['country_name'])) $filters['country_name'] = $_GET['country_name'];
            if(isset($_GET['competition_name'])) $filters['competition_name'] = $_GET['competition_name'];
            if(isset($_GET['team1'])) $filters['team1'] = $_GET['team1'];
            if(isset($_GET['team2'])) $filters['team2'] = $_GET['team2'];
            if(isset($_GET['score'])) $filters['score'] = $_GET['score'];
            if(isset($_GET['match_date_start'])) $filters['match_date_start'] = $_GET['match_date_start'];
            if(isset($_GET['match_date_end'])) $filters['match_date_end'] = $_GET['match_date_end'];
            
            if(isset($filters_decode) && !empty($filters_decode)) {
               foreach($filters_decode as $key=>$val) {
                    $filters[$key] = $val;
                } 
            }

            foreach($filters as $key=>$val) {
                if(in_array($val,array('filter results','start date','end date'))) {
                    unset($filters[$key]);
                }
            }

            $this->dataset->columns($columns);
            $this->dataset->datasource('match_model','get_matches_and_goals',$filters);
            $this->dataset->base_url(site_url('admincp7/livescore/list_matches'));
            $this->dataset->rows_per_page($filters['limit']);

            

            // total rows
            unset($filters['limit']);
            $total_rows = $this->match_model->get_num_rows($filters);
            $this->dataset->total_rows($total_rows);
           
            // initialize the dataset
            $this->dataset->initialize();               
            // add actions
            $this->dataset->action('Delete','admincp/livescore/delete_match');                
            $this->load->view('list_matches_minute');            
    }

    
    function list_matches_first_goal()
    {                    
        $this->load->model('match_model');        
        $this->load->library('dataset');

        $filters    =   array();
        $filters['parsed']  =   0;
        $unparsed   =   $this->match_model->get_num_rows($filters);

        $this->admin_navigation->module_link('Parse results : '.$unparsed,site_url('admincp5/livescore/parse_matches'));
        $this->admin_navigation->module_link('Add match',site_url('admincp4/livescore/add_match'));
        $this->admin_navigation->module_link('Stats first goal',site_url('admincp7/livescore/stats_first_goal'));    
        
        $columns = array(
                            array(
                                    'name' => 'COUNTRY',
                                    'width' => '10%',                                        
                                    'filter' => 'country_name',
                                    'type' => 'text',                                        
                                    'sort_column' => 'country_name',
                                    ),
                            array(
                                    'name' => 'COMPETITION',
                                    'width' => '10%',
                                    'filter' => 'competition_name',
                                    'type' => 'name',                                    
                                    'sort_column' => 'competition_name',                                        
                                    ),
                            array(
                                    'name' => 'DATE',
                                    'width' => '15%',                                     
                                    'filter' => 'match_date',
                                    'type' => 'date',
                                    'field_start_date' => '2013-01-01',
                                    'field_end_date' => '2013-12-31',
                                    'sort_column' => 'match_date',                                       
                                    ),
                            array(
                                    'name' => 'HOME',
                                    'width' => '15%',                                        
                                    'filter' => 'team1',
                                    'type' => 'text',                                        
                                    'sort_column' => 'team1',
                                    ),
                            array(
                                    'name' => 'AWAY',
                                    'width' => '15%',                                        
                                    'filter' => 'team2',
                                    'type' => 'text',                                        
                                    'sort_column' => 'team2',
                                    ),
                            array(
                                    'name' => 'SCORE',
                                    'width' => '5%',
                                    'filter'    =>  'score',                                        
                                    'type' => 'text',
                                    'sort_column'   =>  'score',
                                    ),
                            array(
                                    'name' => 'MIN',
                                    'width' => '5%',
                                    'filter'    =>  'min',                                        
                                    'type' => 'text',
                                    'sort_column'   =>  'min',
                                    ),                                                 
                           array(
                                    'name' => 'LINK COMPLETE',
                                    'width' => '15%',                                        
                                    'type' => 'text,'
                                    ),
                            array(
                                    'name' => 'View',
                                    'width' => '5%',                                        
                                    'type' => 'text,'
                                    ), 
                            array(
                                    'name' => 'Edit',
                                    'width' => '5%',                                        
                                    'type' => 'text,'
                                    ),
                                                                             
                                    
                    );
        
            $filters = array();    
            $filters['limit'] = 20;            

            if(isset($_GET['filters'])) {
                $filters_decode = unserialize(base64_decode($this->asciihex->HexToAscii($_GET['filters'])));
            }
                       
            if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset'];                 
            if(isset($_GET['country_name'])) $filters['country_name'] = $_GET['country_name'];
            if(isset($_GET['competition_name'])) $filters['competition_name'] = $_GET['competition_name'];
            if(isset($_GET['team1'])) $filters['team1'] = $_GET['team1'];
            if(isset($_GET['team2'])) $filters['team2'] = $_GET['team2'];
            if(isset($_GET['score'])) $filters['score'] = $_GET['score'];
            if(isset($_GET['min'])) {
                $filters['min'] = $_GET['min'];
            } else {
                //default
                $filters['min'] = 10;
            }
            if(isset($_GET['match_date_start'])) $filters['match_date_start'] = $_GET['match_date_start'];
            if(isset($_GET['match_date_end'])) $filters['match_date_end'] = $_GET['match_date_end'];
            
            if(isset($filters_decode) && !empty($filters_decode)) {
               foreach($filters_decode as $key=>$val) {
                    $filters[$key] = $val;
                } 
            }

            foreach($filters as $key=>$val) {
                if(in_array($val,array('filter results','start date','end date'))) {
                    unset($filters[$key]);
                }
            }

            $this->dataset->columns($columns);
            $this->dataset->datasource('match_model','first_goal',$filters);
            $this->dataset->base_url(site_url('admincp7/livescore/list_matches_first_goal'));
            $this->dataset->rows_per_page($filters['limit']);

            

            // total rows
            unset($filters['limit']);
            $matches = $this->match_model->first_goal($filters);
            $total_rows = count($matches);
            //asta cu get_num_rows nu merge bine 
            //$total_rows = $this->match_model->get_num_rows($filters);
            $this->dataset->total_rows($total_rows);
           
            // initialize the dataset
            $this->dataset->initialize();               
            // add actions
            $this->dataset->action('Delete','admincp/livescore/delete_match');                
            $this->load->view('list_matches_first_goal');            
    }   
    
    function stats_first_goal($min=10)
    {
        $this->load->model('match_model');        
        $this->load->library('dataset');        

        $columns = array(
                            array(
                                    'name' => 'COUNTRY',
                                    'width' => '10%',                                        
                                    'filter' => 'country_name',
                                    'type' => 'text',                                        
                                    'sort_column' => 'country_name',
                                    ),
                            array(
                                    'name' => 'COMPETITION',
                                    'width' => '10%',
                                    'filter' => 'competition_name',
                                    'type' => 'name',                                    
                                    'sort_column' => 'competition_name',                                        
                                    ),
                            array(
                                    'name' => 'DATE',
                                    'width' => '10%',                                     
                                    'filter' => 'match_date',
                                    'type' => 'date',
                                    'field_start_date' => '2013-01-01',
                                    'field_end_date' => '2013-12-31',
                                    'sort_column' => 'match_date',                                       
                                    ),
                            array(
                                    'name' => 'HOME',
                                    'width' => '10%',                                        
                                    'filter' => 'team1',
                                    'type' => 'text',                                        
                                    'sort_column' => 'team1',
                                    ),
                            array(
                                    'name' => 'AWAY',
                                    'width' => '10%',                                        
                                    'filter' => 'team2',
                                    'type' => 'text',                                        
                                    'sort_column' => 'team2',
                                    ),
                            array(
                                    'name' => 'SCORE',
                                    'width' => '10%',
                                    'filter'    =>  'score',                                        
                                    'type' => 'text',
                                    'sort_column'   =>  'score',
                                    ),
                            array(
                                    'name' => 'MIN',
                                    'width' => '10%',
                                    'filter'    =>  'min',                                        
                                    'type' => 'text',
                                    'sort_column'   =>  'min',
                                    ),
                            array(
                                    'name' => 'TOTAL MATCHES',
                                    'width' => '20%',                                    
                                    'type' => 'text',                                                                            
                                    ),
                            array(
                                    'name' => 'PERCENT',
                                    'width' => '10%',                                                                         
                                    'type' => 'text',                                                                        
                                    ),                                                 
                                                                                                                                                                     
                    );        

        $filters    =   array();
        $filters['min'] = $min;
        $filters['sort_dir'] = 'DESC';
        $filters['sort'] = 'cate';

        if(isset($_GET['filters'])) {
            $filters_decode = unserialize(base64_decode($this->asciihex->HexToAscii($_GET['filters'])));
        }
                   
        if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset'];                 
        if(isset($_GET['country_name'])) $filters['country_name'] = $_GET['country_name'];
        if(isset($_GET['competition_name'])) $filters['competition_name'] = $_GET['competition_name'];
        if(isset($_GET['team1'])) $filters['team1'] = $_GET['team1'];
        if(isset($_GET['team2'])) $filters['team2'] = $_GET['team2'];
        if(isset($_GET['score'])) $filters['score'] = $_GET['score'];
        if(isset($_GET['min'])) {
            $filters['min'] = $_GET['min'];
            $min = $_GET['min'];
        } else {
            //default
            $filters['min'] = 10;
        }
        if(isset($_GET['match_date_start'])) $filters['match_date_start'] = $_GET['match_date_start'];
        if(isset($_GET['match_date_end'])) $filters['match_date_end'] = $_GET['match_date_end'];
        
        if(isset($filters_decode) && !empty($filters_decode)) {
           foreach($filters_decode as $key=>$val) {
                $filters[$key] = $val;
            } 
        }

        foreach($filters as $key=>$val) {
            if(in_array($val,array('filter results','start date','end date'))) {
                unset($filters[$key]);
            }
        }


        $this->dataset->columns($columns);
        $this->dataset->datasource('match_model','first_goal_stats',$filters);
        $this->dataset->base_url(site_url('admincp7/livescore/stats_first_goal'));

        $params = array('min' => $min);

        // // total rows
        // unset($filters['limit']);
        // $total_rows = $this->match_model->get_num_rows($filters);
        // $this->dataset->total_rows($total_rows);

        $this->dataset->rows_per_page(300);
        // initialize the dataset
        $this->dataset->initialize();                                   
        $this->load->view('list_matches_first_goal_stats',$params); 
       
    }

    function list_matches_first_goal_not_until($min=60)
    {
        $this->load->model('match_model');        
        $this->load->library('dataset');

        error_reporting(E_ALL);
        ini_set('display_errors',1);   

        $filters    =   array();
        $filters['parsed']  =   0;
        $unparsed   =   $this->match_model->get_num_rows($filters);

        $this->admin_navigation->module_link('Parse results : '.$unparsed,site_url('admincp5/livescore/parse_matches'));
        $this->admin_navigation->module_link('Add match',site_url('admincp4/livescore/add_match'));
        $this->admin_navigation->module_link('Stats first goal not until',site_url('admincp7/livescore/stats_first_goal_not_until'));    
        
        $columns = array(
                            array(
                                    'name' => 'COUNTRY',
                                    'width' => '10%',                                        
                                    'filter' => 'country_name',
                                    'type' => 'text',                                        
                                    'sort_column' => 'country_name',
                                    ),
                            array(
                                    'name' => 'COMPETITION',
                                    'width' => '10%',
                                    'filter' => 'competition_name',
                                    'type' => 'name',                                    
                                    'sort_column' => 'competition_name',                                        
                                    ),
                            array(
                                    'name' => 'DATE',
                                    'width' => '15%',                                     
                                    'filter' => 'match_date',
                                    'type' => 'date',
                                    'field_start_date' => '2013-01-01',
                                    'field_end_date' => '2013-12-31',
                                    'sort_column' => 'match_date',                                       
                                    ),
                            array(
                                    'name' => 'HOME',
                                    'width' => '15%',                                        
                                    'filter' => 'team1',
                                    'type' => 'text',                                        
                                    'sort_column' => 'team1',
                                    ),
                            array(
                                    'name' => 'AWAY',
                                    'width' => '15%',                                        
                                    'filter' => 'team2',
                                    'type' => 'text',                                        
                                    'sort_column' => 'team2',
                                    ),
                            array(
                                    'name' => 'SCORE',
                                    'width' => '5%',
                                    'filter'    =>  'score',                                        
                                    'type' => 'text',
                                    'sort_column'   =>  'score',
                                    ),
                            array(
                                    'name' => 'MIN',
                                    'width' => '5%',
                                    'filter'    =>  'min',                                        
                                    'type' => 'text',
                                    'sort_column'   =>  'min',
                                    ),                                                 
                           array(
                                    'name' => 'LINK COMPLETE',
                                    'width' => '15%',                                        
                                    'type' => 'text,'
                                    ),
                            array(
                                    'name' => 'View',
                                    'width' => '5%',                                        
                                    'type' => 'text,'
                                    ), 
                            array(
                                    'name' => 'Edit',
                                    'width' => '5%',                                        
                                    'type' => 'text,'
                                    ),
                                                                             
                                    
                    );
        
            $filters = array();    
            $filters['limit'] = 20;            

            if(isset($_GET['filters'])) {
                $filters_decode = unserialize(base64_decode($this->asciihex->HexToAscii($_GET['filters'])));
            }
                       
            if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset'];                 
            if(isset($_GET['country_name'])) $filters['country_name'] = $_GET['country_name'];
            if(isset($_GET['competition_name'])) $filters['competition_name'] = $_GET['competition_name'];
            if(isset($_GET['team1'])) $filters['team1'] = $_GET['team1'];
            if(isset($_GET['team2'])) $filters['team2'] = $_GET['team2'];
            if(isset($_GET['score'])) $filters['score'] = $_GET['score'];
            if(isset($_GET['min'])) {
                $filters['min'] = $_GET['min'];
            } else {
                //default
                $filters['min'] = $min;
            }
            if(isset($_GET['match_date_start'])) $filters['match_date_start'] = $_GET['match_date_start'];
            if(isset($_GET['match_date_end'])) $filters['match_date_end'] = $_GET['match_date_end'];
            
            if(isset($filters_decode) && !empty($filters_decode)) {
               foreach($filters_decode as $key=>$val) {
                    $filters[$key] = $val;
                } 
            }

            foreach($filters as $key=>$val) {
                if(in_array($val,array('filter results','start date','end date'))) {
                    unset($filters[$key]);
                }
            }

            $this->dataset->columns($columns);
            $this->dataset->datasource('match_model','first_goal_not_until',$filters);
            $this->dataset->base_url(site_url('admincp7/livescore/list_matches_first_goal_not_until'));
            $this->dataset->rows_per_page($filters['limit']);

            

            // total rows
            unset($filters['limit']);
            $filters['num_rows'] = true;
            $total_rows = $this->match_model->first_goal_not_until($filters);                    
            $this->dataset->total_rows($total_rows);
           
            // initialize the dataset
            $this->dataset->initialize();               
            // add actions
            $this->dataset->action('Delete','admincp/livescore/delete_match');                
            $this->load->view('list_matches_first_goal_not_until');
    }
}