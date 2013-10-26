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

class Admincp extends Admincp_Controller 
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

	function list_matches()
    {                    
        $this->load->model('match_model');
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
            

            $this->dataset->columns($columns);
            $this->dataset->datasource('match_model','get_matches',$filters);
            $this->dataset->base_url(site_url('admincp/livescore/list_matches'));
            $this->dataset->rows_per_page($filters['limit']);

            

            // total rows
            unset($filters['limit']);
            $total_rows = $this->match_model->get_num_rows($filters);
            $this->dataset->total_rows($total_rows);
           
            // initialize the dataset
            $this->dataset->initialize();               
            // add actions
            $this->dataset->action('Delete','admincp/livescore/delete_match');                
            $this->load->view('list_matches');            
    }

    function delete_match ($contents,$return_url) 
    {       

        $this->load->library('asciihex');
        $this->load->model('match_model');
        
        $contents = unserialize(base64_decode($this->asciihex->HexToAscii($contents)));
        $return_url = base64_decode($this->asciihex->HexToAscii($return_url));
        
        foreach ($contents as $content) {                        
                        $this->match_model->delete_match($content);           
        }
                        
        $this->notices->SetNotice('Match deleted successfully.');
                
        redirect($return_url);
        
        return TRUE;

    }                     
        
    function list_competitions()
    {
        $this->load->model('competition_model');
        $this->admin_navigation->module_link('Fix competitions',site_url('admincp/livescore/fix_competitions'));    
        $this->admin_navigation->module_link('Add competition',site_url('admincp/livescore/add_competition'));            
        $this->load->library('dataset');

        $columns = array(
                            array(
                                    'name' => 'NAME',
                                    'type' => 'name',
                                    'width' => '15%',                                        
                                    ),
                            array(
                                    'name' => 'LINK',
                                    'width' => '15%',                                        
                                    'type' => 'text'
                                    ),               
                           array(
                                    'name' => 'LINK COMPLETE',
                                    'width' => '40%',                                        
                                    'type' => 'text'
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

    if(isset($_GET['filters'])) {
                $filters_decode = unserialize(base64_decode($this->asciihex->HexToAscii($_GET['filters'])));
    }

    if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset'];                 
    if(isset($_GET['country_name'])) $filters['country_name'] = $_GET['country_name'];

    if(isset($filters_decode) && !empty($filters_decode)) {
               foreach($filters_decode as $key=>$val) {
                    $filters[$key] = $val;
                } 
            }
					
	$this->dataset->columns($columns);
	$this->dataset->datasource('competition_model','get_competitions',$filters);
	$this->dataset->base_url(site_url('admincp/livescore/list_competitions'));
    $this->dataset->rows_per_page($filters['limit']);

    // total rows
    unset($filters['limit']);
	$total_rows = $this->competition_model->get_num_rowz($filters); 
	$this->dataset->total_rows($total_rows);
           
	// initialize the dataset
	$this->dataset->initialize();               
    // add actions
	$this->dataset->action('Delete','admincp/livescore/delete_competition');                
    $this->load->view('list_competitions');
    }
        
    function add_competition()
    {            

        $this->load->library('admin_form');
        $form = new Admin_form;
        $this->load->model('country_model');
        $countries = $params = array();
		$params['dropdown'] = 1;
        $countries = $this->country_model->get_countries($params);
        
        $form->fieldset('Add Competition');
        $form->text('Competition name', 'name', '', 'Competition name to be introduced', TRUE, 'e.g., Bundesliga', TRUE);
        $form->text('Link', 'link', '', 'Link', TRUE, 'e.g., russia/premier-league', TRUE);           
        $form->text('Link complete', 'link_complete', '', 'Link complete', TRUE, 'e.g., http://www.livescore.com/soccer/russia/premier-league/', TRUE);
        $form->dropdown('Country','country_id', $countries);
                                            
        $data = array(
                        'form' => $form->display(),
                        'form_title' => 'Add competition',
                        'form_action' => site_url('admincp/livescore/add_competition_validate'),
                        'action' => 'new',
                    );
        
        $this->load->view('add_competition',$data);

    }
        
    function add_competition_validate($action = 'new', $id = false) 
    {
    	$this->load->library('form_validation');
    	$this->form_validation->set_rules('name','Nume','required|trim');
                $this->form_validation->set_rules('link','Link','required|trim');
                $this->form_validation->set_rules('country_id','Country','required|trim');                
    	
    	if ($this->form_validation->run() === FALSE) {
    		$this->notices->SetError('Required fields.');
    		$error = TRUE;
    	}
    	
    	if (isset($error)) {
    		if ($action == 'new') {
    			redirect('admincp/livescore/list_competitions');
    			return FALSE;
    		}
    		else {
    			redirect('admincp/livescore/edit_competition/' . $id);
    			return FALSE;
    		}	
    	}

    	$this->load->model('competition_model');
                
        $fields['name']             = $this->input->post('name');
        $fields['link']             = $this->input->post('link');
        $fields['link_complete']    = $this->input->post('link_complete');
        $fields['country_id']       = $this->input->post('country_id');

    	if ($action == 'new') {
    		$type_id = $this->competition_model->new_competition($fields);											
    		$this->notices->SetNotice('Competition added successfully.');
    		redirect('admincp/livescore/list_competitions/');
    	}
    	else {
    		$this->competition_model->update_competition($fields,$id);											
    		$this->notices->SetNotice('Competition updated successfully.');
    		redirect('admincp/livescore/list_competitions/');
    	}
    	
    	return TRUE;		
    }

    function edit_competition ($id) 
    {                

		$this->load->model('competition_model');
		$competition = $this->competition_model->get_competition($id);
		if (empty($competition)) {
			die(show_error('No competition with this ID.'));
		}

        $this->load->library('admin_form');
		$form = new Admin_form;
        $this->load->model('country_model');
        $countries = $params = array();
        $params['dropdown'] = 1;
        $countries = $this->country_model->get_countries($params);        

		$form->fieldset('Competition');
        $form->text('Name', 'name', $competition['name'], 'Competition name to be introduced', TRUE, 'e.g., Bundesliga', TRUE);
        $form->text('Link', 'link', $competition['link'], 'Competition link', TRUE, 'e.g., russia/premier-league', TRUE);
        $form->text('Link complete', 'link_complete', $competition['link_complete'], 'Competition link complete', TRUE, 'e.g., http://www.livescore.com/soccer/russia/premier-league/', TRUE);
        $form->dropdown('Country','country_id', $countries,$competition['country_id']);
		
		$data = array(
					'form' => $form->display(),
					'form_title' => 'Edit Competition',
					'form_action' => site_url('admincp/livescore/add_competition_validate/edit/'. $competition['competition_id']),
					'action' => 'edit',                                        
					);
		
		$this->load->view('add_competition',$data);			
   }

        
    function delete_competition ($contents,$return_url) 
    {		

		$this->load->library('asciihex');
		$this->load->model('competition_model');
		
		$contents = unserialize(base64_decode($this->asciihex->HexToAscii($contents)));
		$return_url = base64_decode($this->asciihex->HexToAscii($return_url));
		
		foreach ($contents as $content) {
                        $this->load->model('competition_model');
                        $this->competition_model->delete_competition($content);			
		}
		       			
		$this->notices->SetNotice('Competition deleted successfully.');
				
		redirect($return_url);
		
		return TRUE;

   }

    function list_teams($duplicate = 0)
    {
        $this->load->library('dataset');
        $this->load->model('team_model');

        $this->admin_navigation->module_link('Add team',site_url('admincp/livescore/add_team'));
        $duplicates_count = $this->team_model->get_duplicate_teams_helper_num_rows();
        $this->admin_navigation->module_link('Duplicate teams : '.$duplicates_count,site_url('admincp/livescore/list_teams/1'));
        if($duplicates_count) {
            $this->admin_navigation->module_link('Delete duplicates',site_url('admincp/livescore/fix_duplicate_teams'));
        }               
                         
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
                                    'name' => '# OF MATCHES',
                                    'type' => 'no_of_matches',
                                    'width' => '15%',                                        
                                    ),                                                         
                            array(
                                    'name' => 'EDIT',
                                    'width' => '15%',                                        
                                    'type' => 'text',
                                    ),        
                    );
        
        $filters = array();    
        $filters['limit'] = 20;
        $filters['sort']  = 'name';

        if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset'];                 
        if(isset($_GET['country_name'])) $filters['country_name'] = $_GET['country_name'];

        if(isset($_GET['filters']))  $filters_decode = unserialize(base64_decode($this->asciihex->HexToAscii($_GET['filters'])));       

        if(isset($filters_decode) && is_array($filters_decode)) {
           foreach($filters_decode as $key=>$val) {
                $filters[$key] = $val;
            } 
        }

        $similar_count = $this->team_model->get_num_rowz_similar($filters);
        $this->admin_navigation->module_link('Similar teams : '.$similar_count,site_url('admincp/livescore/list_teams/2'));
        $this->admin_navigation->module_link('# of matches update',site_url('admincp/livescore/team_matches'));    
        					
    	$this->dataset->columns($columns);
        if (!$duplicate) {
            $this->dataset->datasource('team_model','get_teams',$filters);
        } elseif($duplicate == 1) {
            $this->dataset->datasource('team_model','get_duplicate_teams',$filters);    
        } elseif($duplicate == 2) {            
            $this->dataset->datasource('team_model','get_similar_teams',$filters);
        }
    	
    	$this->dataset->base_url(site_url('admincp/livescore/list_teams/'.$duplicate));
        $this->dataset->rows_per_page($filters['limit']);

        // total rows
        unset($filters['limit']);
        if (!$duplicate) {
            $total_rows = $this->team_model->get_num_rowz($filters);
        } elseif($duplicate == 1) {
           $total_rows = $this->team_model->get_num_rowz_duplicate($filters); 
        } elseif($duplicate == 2) {
            $total_rows = $similar_count;
        }
    	 
    	$this->dataset->total_rows($total_rows);                
               
    	// initialize the dataset
    	$this->dataset->initialize();               
        // add actions
    	$this->dataset->action('Delete','admincp/livescore/delete_team');                
        $this->load->view('list_teams');
    }

    function team_matches()
    {
        $this->load->model('team_model');
        $nr = $this->team_model->update_all_teams_matches();

        $this->notices->SetNotice($nr.' teams updated!');
        redirect('admincp/livescore/list_teams/');
    }

    //remove beginning and trailing spaces from team names
    function fix_teams()
    {
        $this->load->model('team_model');
        $teams = $this->team_model->get_teams();
        foreach($teams as $team){            
                echo $team['team_id'].'=>'.$team['name'].'<br/>';            
                $name = trim($team['name']);
                $update_fields = array(
                        'name' => $name,
                    );
                $this->team_model->update_team ($update_fields,$team['team_id']);                            
        }
    }

    function fix_duplicate_teams($filters = array())
    {
        $deleted = 0;
        $this->load->model('team_model');
        $deleted = $this->team_model->fix_duplicate_teams($filters);

        $this->notices->SetNotice($deleted.' duplicate teams deleted!');
        redirect('admincp/livescore/list_teams/');
        
    }
        
    function add_team()
    {            
        $this->load->library('admin_form');
        $form = new Admin_form;
        $this->load->model('country_model');
        $countries = $params = array();
 		$params['dropdown'] = 1;
        $countries = $this->country_model->get_countries($params);	    	
        
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
        $this->load->model('country_model');
        $this->load->model('team_model');
        $team = $this->team_model->get_team($id);
        if (empty($team)) {
        	die(show_error('No team with this ID.'));
        }

        $this->load->library('admin_form');
        $form = new Admin_form;
        $this->load->model('team_model');
        $countries = $params = array();
		$params['dropdown'] = 1;
        $countries = $this->country_model->get_countries($params);

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
        
    function fix_competitions()
    {
        $this->load->model('competition_model');
        $this->competition_model->fix_competitions();
        $return_url = 'admincp/livescore/list_competitions';
        $this->notices->SetNotice('Table competitions fixed successfully.');				
        redirect($return_url);
        return TRUE;
    }

    function fix_competitions_name()
    {
        $this->load->model('competition_model');
        $this->competition_model->fix_competitions_name();
        $return_url = 'admincp/livescore/list_competitions';
        $this->notices->SetNotice('Table competitions fixed successfully.');                
        redirect($return_url);
        return TRUE;
    }

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

    function fix_score()
    {
        $this->load->model('match_model');
        $this->match_model->fix_score();

        $this->notices->SetNotice('Score fixed successfully.');
        redirect('admincp/livescore/list_matches/');

    }

}