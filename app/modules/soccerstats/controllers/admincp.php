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



class Admincp extends Admincp_Controller {
                
	function __construct()
	{

		parent::__construct();
				
		$this->admin_navigation->parent_active('soccerstats');
                                		
		//error_reporting(E_ALL^E_NOTICE);
		//error_reporting(E_WARNING);
	}
        
        function index () 
        {
            redirect('admincp/soccerstats/list_introstats');		
	}
        
//********************************** Introstats LIST *******************************//         
    /**
	* Manage Introstats
	*
	* Lists active markets for managing
	*/
	function list_introstats()
	{
		$this->load->library('dataset');
		$this->admin_navigation->module_link('Update Values',site_url('admincp/soccerstats/parse_introstats'));

		$columns = array(
						array(
							'name' => 'ID #',
							'type' => 'id',
							'width' => '5%',
                                                    ),

						array(
							'name' => 'Name',
                                                        'type' => 'text',
							'width' => '15%',
							),
                                                array(
							'name' => 'Pld',
                                                        'type' => 'text',
							'width' => '5%',
							),
                                                array(
							'name' => '1 X 2',
                                                        'type' => 'text',
							'width' => '10%',
							),
                                                array(
							'name' => '1',
                                                        'type' => 'text',
							'width' => '5%',
							'sort_column' => 'home_wins',
							),
                                                array(
							'name' => 'X',
                                                        'type' => 'text',
							'width' => '5%',
							'sort_column' => 'draw',
							),
                                                array(
							'name' => '2',
                                                        'type' => 'text',
							'width' => '5%',
							'sort_column' => 'away_wins',
							),
                                                array(
							'name' => 'Goals',
                                                        'type' => 'text',
							'width' => '5%',
														'sort_column' => 'goals_average',
							),
                                                array(
							'name' => 'Home Goals',
                                                        'type' => 'text',
							'width' => '5%',
							),
                                                array(
							'name' => 'Away Goals',
                                                        'type' => 'text',
							'width' => '5%',
							),
                                                array(
							'name' => 'Over 1.5',
                                                        'type' => 'text',
							'width' => '10%',
							'sort_column' => 'over_1_5',
							),
                                                array(
							'name' => 'Over 2.5',
                                                        'type' => 'text',
							'width' => '10%',
							'sort_column' => 'over_2_5',
							),
                                                array(
							'name' => 'Over 3.5',
                                                        'type' => 'text',
							'width' => '10%',
							'sort_column' => 'over_3_5',
							),
						
                                                array(
							'name' => '',
							'width' => '5%',
                                                        'type' => 'text',
						),
						
					);
                
                $filters = array();    
                $filters['limit'] = 50;

                if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset'];
                
			
		$this->dataset->columns($columns);
		$this->dataset->datasource('introstats_model','get_introstats',$filters);
		$this->dataset->base_url(site_url('admincp/soccerstats/list_introstats'));
		$this->dataset->rows_per_page($filters['limit']);
		
        
		// total rows
                $this->load->model('introstats_model');
		$total_rows = $this->introstats_model->num_rows_introstats($filters);
                //die (print($total_rows));
		$this->dataset->total_rows($total_rows);
		
		// initialize the dataset
		$this->dataset->initialize();

		
		$this->load->view('introstats_view');
	}
        
 //********************************** END Introstats LIST *******************************//         
        
        private function getUrl($url)
        {
                $cUrl = curl_init();
                $headers[] = 'Connection: Keep-Alive';
                $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';				
                curl_setopt($cUrl, CURLOPT_HTTPHEADER, $headers); 
                curl_setopt($cUrl, CURLOPT_URL,$url);
                //curl_setopt($cUrl, CURLOPT_HTTPGETGET,1);
                //curl_setopt($cUrl, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.2; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0)'); 	
                //curl_setopt($cUrl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']); 	
                curl_setopt($cUrl, CURLOPT_RETURNTRANSFER, 1);
                //curl_setopt($cUrl, CURLOPT_TIMEOUT, '3');

                //$pageContent = trim(curl_exec($cUrl));
                $pageContent = curl_exec($cUrl);
                curl_close($cUrl);

                return $pageContent;
        }

	function parse_links()
        {
            $this->load->view('parse_links_view');           
        }
        
        function parse_introstats()
        {
            $this->load->library('admin_form');
            $form = new Admin_form;
            $form->fieldset('Parse Introstats link');
            $form->text('Link', 'link', '', 'link to be introduced', TRUE, 'e.g., http://www.soccerstats.com/leagues.asp', TRUE);
            $data = array(
                            'form' => $form->display(),
                            'form_title' => 'Parse Introstats link',
                            'form_action' => site_url('admincp/soccerstats/parse_introstats_validate'),
                            'action' => 'new',
                        );
            
            $this->load->view('parse_form_view',$data);
        }
        
        function parse_introstats_validate()
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('link','Link','required|trim');

            if ($this->form_validation->run() === FALSE) {
                    $this->notices->SetError('Required fields.');
                    $error = TRUE;
            }

            if (isset($error)) {                    
                            redirect('admincp/soccerstats/parse_form_view');
                            return FALSE;
		}
            $link   = $this->input->post('link');
            $link = utf8_encode($link);
            
            $this->parse_introstats_info($link);
            
            echo '<br/><div align="center"><a href="http://betz.dev/admincp/soccerstats/parse_form_view">Back</a></div>';

            return TRUE;
        }

	private function parse_introstats_info($link) 
        {                       
            $link = utf8_decode($link);
            $page = $this->getUrl($link);

            $leagues = $teams = $score = $competitions = array();
            
            $this->load->model('introstats_model');
            $this->load->model('league_model');
            
			// Prima coloana cea cu ligile	
			
            $pattern = "@<a href='latest.asp\?league\=(.*)' class='blacklink2'>.*</a>@";
            preg_match_all($pattern, $page, $clearleagues);
	
			//foreach (array_keys($countries[1], 'Stats') as $key) {
			//	unset($countries[1][$key]);
			//}

			$leagues = array_unique($clearleagues[1]);
                        $leagues = array_values($leagues);	
			//print '<pre>Leagues ';
                        //print_r ($leagues);
			
			
			// A 2-a coloana cea cu Matches Played			
			
          	$pattern = "@<font color='gray'>(.*)</font>@";
            preg_match_all($pattern, $page, $played);
			$clearplayed =  array_shift($played[0]);
			//print '<pre>Matches Played ';
		  	//print_r ($played[0]);
		    
		    
			// A Restu de coloane
			$pattern = "@<TD align='center'>(.*)</TD>\s*
<TD align='center'>(.*)</TD>\s*
<TD align='center'>(.*)</TD>\s*
<TD align='center'>.*<font color='blue'>(.*)</font>.*</TD>\s*
<TD align='center'>(.*)</TD>\s*
<TD align='center'>(.*)</TD>\s*
<TD align='center'>(.*)</TD>\s*
<TD align='center'>(.*)</TD>\s*
<TD align='center'>(.*)</TD>@";
			preg_match_all($pattern, $page, $procent_matches);
			//print '<pre>% 1 % X % 2 Matches ';
		  	//print_r ($procent_matches);
			
                         $introstats_param = array(
                                     'league_name' => $leagues,
                                     'matches_played' => $played[0],
                                     'home_wins' => $procent_matches[1],
                                     'draw' => $procent_matches[2],
                                     'away_wins' => $procent_matches[3],
                                     'goals_average' => $procent_matches[4],
                                     'home_average' => $procent_matches[5],
                                     'away_average' => $procent_matches[6],
                                     'over_1_5' => $procent_matches[7],
                                     'over_2_5' => $procent_matches[8],
                                     'over_3_5' => $procent_matches[9],
                                );
                         foreach($introstats_param as $cheie=>$param){
                             foreach($param as $key=>$val){
                                 $param[$key] = strip_tags($val);
                                 $param[$key] = str_replace('&nbsp;','',$param[$key]);
                                 $param[$key] = str_replace('%','',$param[$key]);
                             }
                             $introstats_param[$cheie] = $param;
                         }
                         
                         print '<pre>';
                         print_r($introstats_param);
                         
                         $introstats_data = array();                         
                         
                         foreach($introstats_param as $cheie=>$param){
                             foreach($param as $key=>$val){
                                $id = $this->league_model->get_league_by_name($introstats_param['league_name'][$key]); 
                                $introstats_data[$key] = array(
                                  'league_name' =>  $id,
                                  'matches_played' =>  $introstats_param['matches_played'][$key],
                                  'home_wins' =>  $introstats_param['home_wins'][$key],
                                  'draw' =>  $introstats_param['draw'][$key],
                                  'away_wins' =>  $introstats_param['away_wins'][$key],
                                  'goals_average' =>  $introstats_param['goals_average'][$key],
                                  'home_average' =>  $introstats_param['home_average'][$key],
                                  'away_average' =>  $introstats_param['away_average'][$key],
                                  'over_1_5' =>  $introstats_param['over_1_5'][$key],
                                  'over_2_5' =>  $introstats_param['over_2_5'][$key],
                                  'over_3_5' =>  $introstats_param['over_3_5'][$key],                                                              
                                );
                                
                                //print_r($introstats_data[$key]);
                                
                              if(!$this->introstats_model->introstats_exists($id)){
                                $this->introstats_model->new_introstats($introstats_data[$key]); 
                             } else {
                                 $this->introstats_model->update_introstats($introstats_data[$key],$id);
                             }
                             
                             }                                                                                                                   
                         }

                        print_r ($introstats_param);die;
                       // $this->introstats_model->new_introstats($introstats_param);

		}
        

// ************************************************** TEAMS **************************************//

		function parse_teams()
        {
            $this->load->library('admin_form');
            $form = new Admin_form;
            $form->fieldset('Parse Teams');
            $form->text('Link', 'link', '', 'link to be introduced', TRUE, 'e.g., http://www.soccerstats.com/latest.asp?league=england_2012', TRUE);
            $data = array(
                            'form' => $form->display(),
                            'form_title' => 'Parse Teams',
                            'form_action' => site_url('admincp/soccerstats/parse_teams_validate'),
                            'action' => 'new',
                        );
            
            $this->load->view('parse_form_view',$data);
        }
		
		
 		function parse_teams_validate()
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('link','Link','required|trim');

            if ($this->form_validation->run() === FALSE) {
                    $this->notices->SetError('Required fields.');
                    $error = TRUE;
            }

            if (isset($error)) {                    
                            redirect('admincp/soccerstats/parse_teams');
                            return FALSE;
		}
            $link   = $this->input->post('link');
            $link = utf8_encode($link);
            
            $this->parse_teams_info($link);
            
            echo '<br/><div align="center"><a href="http://betz.banujos.ro/admincp/soccerstats/parse_teams">Back</a></div>';

            return TRUE;
        }
		
		
		private function parse_teams_info($link) 
        {                       
            $link = utf8_decode($link);
            $page = $this->getUrl($link);

            $leagues = $teams = $year = array();
            
            $this->load->model('introstats_model');
            $this->load->model('league_model');
            
	    // League	
			
            $pattern = '@<option value="\#">\s*
				(.*)\s*			
				</option>@';
				
            preg_match_all($pattern, $page, $clearleague);
			print '<pre>League ';
            print_r ($clearleague[1][0]);

			

             // Year	
			
            $pattern2 = '@<font size=\'xx-small\' bgcolor=\'#FFCC66\'>
			
			<select name="countryLeagueSeason" maxsize="7" onChange="location.href=this.options[this.selectedIndex].value" class="leaguelist">
							
				<option value="http://www.soccerstats.com/latest.asp?league=england".*\s*>(.*)</option>
			
			</select>
			</font>@';
				
            preg_match_all($pattern2, $page, $clearyear);
			print '<br><pre>Year ';
            print_r ($clearyear);
              	     
                     
             // Teams
                     
	    $pattern3 = '@<select name="countryLeague" onChange="location\.href\=this\.options\[this.selectedIndex\]\.value" class="leaguelist">\s*
<option value=\'.*\'>(.*)</option>\s*
</select>@';
				
            preg_match_all($pattern3, $page, $clearteams);
			print '<pre>Teams ';
            print_r ($clearteams);
			
	        die;
			
			
                         $introstats_param = array(
                                     'league_name' => $leagues,
                                     'matches_played' => $played[0],
                                     'home_wins' => $procent_matches[1],
                                     'draw' => $procent_matches[2],
                                     'away_wins' => $procent_matches[3],
                                     'goals_average' => $procent_matches[4],
                                     'home_average' => $procent_matches[5],
                                     'away_average' => $procent_matches[6],
                                     'over_1_5' => $procent_matches[7],
                                     'over_2_5' => $procent_matches[8],
                                     'over_3_5' => $procent_matches[9],
                                );
                         foreach($introstats_param as $cheie=>$param){
                             foreach($param as $key=>$val){
                                 $param[$key] = strip_tags($val);
                                 $param[$key] = str_replace('&nbsp;','',$param[$key]);
                                 $param[$key] = str_replace('%','',$param[$key]);
                             }
                             $introstats_param[$cheie] = $param;
                         }
                         
                         print '<pre>';
                         print_r($introstats_param);
                         
                         $introstats_data = array();                         
                         
                         foreach($introstats_param as $cheie=>$param){
                             foreach($param as $key=>$val){
                                $id = $this->league_model->get_league_by_name($introstats_param['league_name'][$key]); 
                                $introstats_data[$key] = array(
                                  'league_name' =>  $id,
                                  'matches_played' =>  $introstats_param['matches_played'][$key],
                                  'home_wins' =>  $introstats_param['home_wins'][$key],
                                  'draw' =>  $introstats_param['draw'][$key],
                                  'away_wins' =>  $introstats_param['away_wins'][$key],
                                  'goals_average' =>  $introstats_param['goals_average'][$key],
                                  'home_average' =>  $introstats_param['home_average'][$key],
                                  'away_average' =>  $introstats_param['away_average'][$key],
                                  'over_1_5' =>  $introstats_param['over_1_5'][$key],
                                  'over_2_5' =>  $introstats_param['over_2_5'][$key],
                                  'over_3_5' =>  $introstats_param['over_3_5'][$key],                                                              
                                );
                                
                                //print_r($introstats_data[$key]);
                                
                              if(!$this->introstats_model->introstats_exists($id)){
                                $this->introstats_model->new_introstats($introstats_data[$key]); 
                             } else {
                                 $this->introstats_model->update_introstats($introstats_data[$key],$id);
                             }
                             
                             }                                                                                                                   
                         }

                        print_r ($introstats_param);die;
                       // $this->introstats_model->new_introstats($introstats_param);

		}
        
		
		
		
		
		

        function list_teams2012()
        {
            $this->admin_navigation->module_link('Add team',site_url('admincp/livescore/add_team'));            
            $this->load->library('dataset');

            $columns = array(
                                array(
                                        'name' => 'NAME',
                                        'type' => 'name',
                                        'width' => '15%',                                        
                                        ),                               
                                array(
                                        'name' => 'COUNTRY',
                                        'width' => '15%',                                        
                                        'filter' => 'country_name',
                                        'type' => 'text',                                        
                                        'sort_column' => 'country_name',
                                        ),                                                 
                                array(
                                        'name' => 'EDIT',
                                        'width' => '15%',                                        
                                        'type' => 'text',
                                        ),        
                        );
            
                $filters = array();    
                $filters['limit'] = 20;

                if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset'];                 
                if(isset($_GET['country_name'])) $filters['country_name'] = $_GET['country_name'];
						

		$this->dataset->columns($columns);
		$this->dataset->datasource('team_model','get_teams',$filters);
		$this->dataset->base_url(site_url('admincp/livescore/list_teams'));
                $this->dataset->rows_per_page($filters['limit']);
	
                // total rows
		$total_rows = $this->db->get('z_teams')->num_rows(); 
		$this->dataset->total_rows($total_rows);
               
		// initialize the dataset
		$this->dataset->initialize();               
                // add actions
		$this->dataset->action('Delete','admincp/livescore/delete_team');                
                $this->load->view('list_teams');
        }
        
         function add_team()
        {            
            $this->load->library('admin_form');
            $form = new Admin_form;
            $this->load->model('country_model');
            $countries = $params = array();
            $countries = $this->country_model->get_countries($params);
            $countries = array_merge(array('Select country'),$countries);	    	
            
            $form->fieldset('Add Team');
            $form->text('Team name', 'name', '', 'Team name to be introduced', TRUE, 'e.g., AC Milan', TRUE);            
            $form->dropdown('Country','country_id', $countries);
                                                
            $data = array(
                            'form' => $form->display(),
                            'form_title' => 'Add team',
                            'form_action' => site_url('admincp/livescore/add_team_validate'),
                            'action' => 'new',
                        );
            
            $this->load->view('add_team',$data);

        }
        
        function add_team_validate($action = 'new', $id = false) 
        {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name','Name','required|trim');                
                $this->form_validation->set_rules('country_id','Country','required|trim');                
		
		if ($this->form_validation->run() === FALSE) {
			$this->notices->SetError('Required fields.');
			$error = TRUE;
		}
		
		if (isset($error)) {
			if ($action == 'new') {
				redirect('admincp/livescore/list_teams');
				return FALSE;
			}
			else {
				redirect('admincp/livescore/edit_team/' . $id);
				return FALSE;
			}	
		}

		$this->load->model('team_model');
                
                $fields['name']             = $this->input->post('name');               
                $fields['country_id']       = $this->input->post('country_id');

		if ($action == 'new') {
			$team_id = $this->team_model->new_team($fields);											
			$this->notices->SetNotice('Team added successfully.');
			redirect('admincp/livescore/list_teams/');
		}
		else {
			$this->team_model->update_team($fields,$id);											
			$this->notices->SetNotice('Team updated successfully.');
			redirect('admincp/livescore/list_teams/');
		}
		
		return TRUE;		
	}
        
        function edit_team ($id) 
        {                

		$this->load->model('team_model');
		$team = $this->team_model->get_team($id);
		if (empty($team)) {
			die(show_error('No team with this ID.'));
		}
                $this->load->library('admin_form');
		$form = new Admin_form;
                $this->load->model('team_model');
                $countries = $params = array();
                $countries = $this->country_model->get_countries($params);
                $countries = array_merge(array('Select country'),$countries);

		$form->fieldset('Team');
                $form->text('Name', 'name', $team['name'], 'Team name to be introduced', TRUE, 'e.g., AC Milan', TRUE);                
                $form->dropdown('Country','country_id', $countries,$team['country_id']);
		
		$data = array(
					'form' => $form->display(),
					'form_title' => 'Edit Team',
					'form_action' => site_url('admincp/livescore/add_team_validate/edit/'. $team['team_id']),
					'action' => 'edit',                                        
					);
		
		$this->load->view('add_team',$data);			
	}

        
        function delete_team ($contents,$return_url) 
        {		

		$this->load->library('asciihex');
		$this->load->model('competition_model');
		
		$contents = unserialize(base64_decode($this->asciihex->HexToAscii($contents)));
		$return_url = base64_decode($this->asciihex->HexToAscii($return_url));
		
		foreach ($contents as $content) {
                        $this->load->model('team_model');
                        $this->team_model->delete_team($content);			
		}
		       			
		$this->notices->SetNotice('Team deleted successfully.');
				
		redirect($return_url);
		
		return TRUE;
	
	}
        
//********************************* Bet Against Favorite ZONE ********************************//

	function list_against_favorite()
	{
		$this->load->library('dataset');
		$this->admin_navigation->module_link('Add Stake',site_url('admincp/soccerstats/list_against_favorite'));
		$this->admin_navigation->module_link('Add Values',site_url('admincp/soccerstats/add_against_value'));
		$this->admin_navigation->module_link('Add values by Parsing',site_url('admincp/soccerstats/list_against_favorite'));

		$columns = array(
						array(
							'name' => 'ID #',
							'type' => 'id',
							'filter' => 'id_against',
							'width' => '5%',
                            ),
						
						array(
							'name' => 'ZONE',
                            'type' => 'text',
							'filter' => 'zone',
							'width' => '15%',
							),

						array(
							'name' => 'Favorite Name',
                            'type' => 'text',
							'width' => '20%',
							),
						
                        array(
							'name' => 'Favorite Odds',
                            'type' => 'text',
							'width' => '5%',
							),
						
						array(
							'name' => 'Draw Odds',
                            'type' => 'text',
							'width' => '5%',
							),
						
						array(
							'name' => 'UnderDog Name',
                            'type' => 'text',
							'width' => '20%',
							),
						
                        array(
							'name' => 'UnderDog Odds',
                            'type' => 'text',
							'width' => '5%',
							),
						
						array(
							'name' => 'Result',
                            'type' => 'text',
							'width' => '10%',
							),
	 
						  array(
							'name' => 'Date inserted',
                            'type' => 'text',
							'width' => '10%',
							),
						  
						   array(
							'name' => '',
                            'type' => 'text',
							'width' => '5%',
							),
						
					);
                
                $filters = array();    
                $filters['limit'] = 50;

                if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset'];
                
			
		$this->dataset->columns($columns);
		$this->dataset->datasource('against_model','get_against_values',$filters);
		$this->dataset->base_url(site_url('admincp/soccerstats/list_against_favorite'));
		$this->dataset->rows_per_page($filters['limit']);
		
        
		// total rows
        $this->load->model('against_model');
		$total_rows = $this->against_model->num_rows_against($filters);
        //die (print($total_rows));
		$this->dataset->total_rows($total_rows);
		
		// initialize the dataset
		$this->dataset->initialize();
		
		// add actions
		$this->dataset->action('Delete','admincp/soccerstats/delete_against_value');
		
		$this->load->view('list_against_view');
	}
	
	 function add_against_value($action = 'new', $id = false) 
        {   
            $this->load->helper('form');
            $this->load->library('admin_form');  
            
            $form = new Admin_form();
            $form->fieldset ('Add New Value');    
            $this->load->model('against_model');
          
            $data = array(      

                    //'market' => $market,
                    'form_title' => 'Add New Value',
                    'form_action' => site_url('admincp2/soccerstats/post_against/new'),
		    'action' => 'new',
				);
  
		$this->load->view('add_against_view',$data);		
	}        
	
	/**
	* Handle New/Edit Bet Post
	*/
	function post_against($action, $id = false){	       
		
		$this->load->model('against_model');
		
		// content
		$id_against = $this->input->post('id_against');
		$zone = $this->input->post('zone');
		$favorite_name = $this->input->post('favorite_name');
		$favorite_odds = $this->input->post('favorite_odds');
		//$profit = $this->input->post('profit') ? $this->input->post('profit') : null;
		//$loss = $this->input->post('loss') ? $this->input->post('loss') : null;
		$draw_odds = $this->input->post('draw_odds');
		$underdog_name = $this->input->post('underdog_name');
		$underdog_odds = $this->input->post('underdog_odds');
		$result = $this->input->post('result');
		
		if ($action == 'new') {
			$bet_id = $this->against_model->new_against(
						$zone,
						$favorite_name,
						$favorite_odds,
						$draw_odds,
						$underdog_name,
						$underdog_odds,
						$result
						);
												
			$this->notices->SetNotice('Values added successfully.');
		}
		else {
			$bet_id = $this->against_model->update_against(
                                                $id_against,
						$zone,
						$favorite_name,
						$favorite_odds,
						$draw_odds,
						$underdog_name,
						$underdog_odds,
						$result													
						);

			$this->notices->SetNotice('Values edited successfully.');
		}
		
                
		redirect('admincp2/soccerstats/list_against_favorite');
		
		return TRUE;
	}
	
	/**
	* Edit Bet
	*
	* Show the bet form, preloaded with variables
	*
	* @param int $id the ID of the bet

	*/
	function edit_against_value($id) {
		$this->load->model('against_model');
       
		$bet = $this->against_model->get_against_value($id);
		
		$data = array(
					'id_against' => $bet['id_against'],
					'zone' => $bet['zone'],
					'favorite_name' => $bet['favorite_name'],
					'favorite_odds' => $bet['favorite_odds'],
					'draw_odds' => $bet['draw_odds'],
					'underdog_name' => $bet['underdog_name'],
					'underdog_odds' => $bet['underdog_odds'],
					'result' => $bet['result'],
					'form' => $bet,
					'form_title' => 'Edit Bet',
					'form_action' => site_url('admincp2/soccerstats/post_against/edit/'.$bet['ID_bet']),
                                        'action' => 'edit',
					);
		//var_dump ($data);
        //die;		
		$this->load->view('add_bet',$data);
	} 
        
        /**
	* Delete bet
	*/
         function delete_against_value($contents,$return_url) 
        {
		
		$this->load->library('asciihex');
		$this->load->model('against_model');
		
		$contents = unserialize(base64_decode($this->asciihex->HexToAscii($contents)));
		$return_url = base64_decode($this->asciihex->HexToAscii($return_url));
		
		foreach ($contents as $content) {
                        $this->against_model->delete_against_value($content);
		}
		       			
		$this->notices->SetNotice('Values deleted successfully.');
				
		redirect($return_url);
		
		return TRUE;
	
	}

//*********************************END Bet Against Favorite ZONE ********************************//

}

