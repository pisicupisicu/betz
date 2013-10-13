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

class Admincp2 extends Admincp_Controller {
                
	function __construct()
	{
		parent::__construct();
				
		$this->admin_navigation->parent_active('livescore');
                                		
		//error_reporting(E_ALL^E_NOTICE);
		//error_reporting(E_WARNING);
	}	
	
	function index () 
        {
            redirect('admincp2/livescore/strategies');		
	}
        
        function list_strategies()
        {
            $this->admin_navigation->module_link('Add strategy',site_url('admincp2/livescore/add_strategy'));
            
            $this->load->library('dataset');
            $columns = array(
                                array(
                                        'name' => 'NAME',
                                        'type' => 'name',
                                        'width' => '15%',                                        
                                        ),
                                array(
                                        'name' => 'START',
                                        'width' => '10%',                                        
                                        'type' => 'text'
                                        ),
                                array(
                                        'name' => 'RATE',
                                        'width' => '10%',                                        
                                        'type' => 'text',

                                        ),
                                array(
                                        'name' => 'MULTIPLY',
                                        'width' => '10%',
                                        'type' => 'text',
                                        ),
                                 array(
                                        'name' => 'STOP',
                                        'width' => '15%',
                                        'type' => 'text',
                                        ),
                                  array(
                                        'name' => 'INTERMISSION',
                                        'width' => '10%',
                                        'type' => 'text',
                                        ),
                                  array(
                                        'name' => 'EDIT',
                                        'width' => '10%',
                                        'type' => 'text',
                                        ),
                                  array(
                                        'name' => 'COMPUTED',
                                        'width' => '10%',
                                        'type' => 'text',
                                        ),
                                  array(
                                        'name' => 'VIEW',
                                        'width' => '10%',
                                        'type' => 'text',
                                        ),      
                        );
            
                $filters = array();    
                $filters['limit'] = 50;

                if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset'];                 
						
		$this->dataset->columns($columns);
		$this->dataset->datasource('strategy_model','get_strategies',$filters);
		$this->dataset->base_url(site_url('admincp2/livescore/strategies'));
                $this->dataset->rows_per_page($filters['limit']);
		
                // total rows
		$total_rows = $this->db->get('z_strategies')->num_rows(); 
		$this->dataset->total_rows($total_rows);
                
		// initialize the dataset
		$this->dataset->initialize();
                
                // add actions
		$this->dataset->action('Delete','admincp2/livescore/delete_strategy');
                
                $this->load->view('strategies');
            
        }
        
        function add_strategy_form_builder_shit($action = 'new', $id = false)
        {
            $this->load->library('custom_fields/form_builder');
            $this->load->library('custom_fields/fieldtype');
            $this->load->model('strategy_model');
            
            $strategy = array(
                                
                            );
            if(isset($id)) {
                $strategy = $this->strategy_model->get_strategy($id);
            }
            
            //print '<pre>';
            //print_r($strategy);
            //die;
            
            //$this->form_builder->reset();
            //print '<pre>';
            //print_r($this->fieldtype->get_fieldtype_options());die;                        
            $this->form_builder->add_field('text')->name('name')->label('Strategy name')->validators(array('min_length[5]','trim',))->required(TRUE)->value($strategy['name']);           
            $this->form_builder->add_field('text')->name('start')->label('START')->validators(array('trim'))->required(TRUE)->value($strategy['start']);
            $this->form_builder->add_field('text')->name('rate')->label('RATE')->validators(array('trim'))->required(TRUE)->value($strategy['rate']);
            $this->form_builder->add_field('text')->name('multiply')->label('MULTIPLY')->validators(array('trim'))->required(TRUE)->value($strategy['multiply']);
            $this->form_builder->add_field('text')->name('stop')->label('STOP')->validators(array('trim'))->required(TRUE)->value($strategy['stop']);
            $this->form_builder->add_field('text')->name('intermission')->label('INTERMISSION')->validators(array('trim'))->required(TRUE)->value($strategy['intermission']);
            
            $form = $this->form_builder->output_admin();            
            $values = $this->form_builder->post_to_array();            
            
            if($values['name'] !== FALSE) {
                 if ($this->form_builder->validate_post() === FALSE) {                
                    $errors = $this->form_builder->validation_errors();     
                    // not the best style, but we'll just print the HTML-formatted errors                    
                    echo $errors;
                }
                
                if (isset($errors)) {
                        if ($action == 'new') {
                                redirect('admincp2/livescore/add_strategy/new');
                                return FALSE;
                        }
                        else {
                                redirect('admincp2/livescore/add_strategy/edit/' . $id);
                                return FALSE;
                        }	
                }		                

                if ($action == 'new') {
                        $this->strategy_model->new_strategy($values);
                        $this->notices->SetNotice('Successfully added the strategy');
                        redirect('admincp2/livescore/strategies');
                }
                else {
                        $this->strategy_model->update_strategy($values,$id);												
                        $this->notices->SetNotice('Successfully updated the strategy');

                        redirect('admincp2/livescore/strategies');
                }           
            }
                        	            
            $data = array(
                            'form' => $form,
                            'form_title' => 'Create Your Favourite Strategy',
                            'form_action' => site_url('admincp2/livescore/add_strategy/'.$action),
                            'action' => 'new'
                         );
            
            $this->load->view('add_strategy',$data);
        }
        
        function add_strategy()
        {
            $this->load->library('admin_form');
            $form = new Admin_form;
            
            $form->fieldset('Add Strategy');
            $form->text('Strategy name', 'name', '', 'Strategy name to be introduced', TRUE, 'e.g., Radu super strategy', TRUE);
            $form->text('Start', 'start', '', 'start bank amount', TRUE, 'e.g., 20 &euro;', FALSE,'100px');
            $form->text('Rate', 'rate', '', 'rate percentage from bank for growth', TRUE, 'e.g., 0.1', FALSE,'100px');
            $form->text('Multiply', 'multiply', '', 'average odds for betting', TRUE, 'e.g., 1.5', FALSE,'100px');
            $form->text('Stop', 'stop', '', 'target amount', TRUE, 'e.g.,10000 &euro;', FALSE,'100px');
            $form->text('Intermission', 'intermission', '', 'the amount at which we re-calculate the wage', TRUE, 'e.g.,10 &euro;', FALSE,'100px');
            
            $data = array(
                            'form' => $form->display(),
                            'form_title' => 'Add strategy',
                            'form_action' => site_url('admincp2/livescore/add_strategy_validate'),
                            'action' => 'new'
                        );
            
            $this->load->view('add_strategy',$data);
        }
        
        function add_strategy_validate($action = 'new', $id = false) 
        {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name','Nume','required|trim');
                $this->form_validation->set_rules('start','Start','required|trim');
                $this->form_validation->set_rules('rate','Rate','required|trim');
                $this->form_validation->set_rules('multiply','Multiply','required|trim');
                $this->form_validation->set_rules('stop','Stop','required|trim');
                $this->form_validation->set_rules('intermission','Intermission','required|trim');
		
		if ($this->form_validation->run() === FALSE) {
			$this->notices->SetError('Required fields.');
			$error = TRUE;
		}
		
		if (isset($error)) {
			if ($action == 'new') {
				redirect('admincp2/livescore/add_strategy');
				return FALSE;
			}
			else {
				redirect('admincp2/livescore/edit_strategy/' . $id);
				return FALSE;
			}	
		}
		
		$this->load->model('strategy_model');
                
                $fields['name']         = $this->input->post('name');
                $fields['start']        = $this->input->post('start');
                $fields['rate']         = $this->input->post('rate');
                $fields['multiply']     = $this->input->post('multiply');
                $fields['stop']         = $this->input->post('stop');
                $fields['intermission'] = $this->input->post('intermission');
		
		if ($action == 'new') {
			$type_id = $this->strategy_model->new_strategy($fields);
															
			$this->notices->SetNotice('Strategy added successfully.');
			
			redirect('admincp2/livescore/list_strategies/');
		}
		else {
			$this->strategy_model->update_strategy($fields,$id);												
			$this->notices->SetNotice('Strategy updated successfully.');
			
			redirect('admincp2/livescore/list_strategies/');
		}
		
		return TRUE;		
	}
        
        function edit_strategy ($id) 
        {                
		$this->load->model('strategy_model');
		$strategy = $this->strategy_model->get_strategy($id);
		
		if (empty($strategy)) {
			die(show_error('No strategy with this ID.'));
		}
                
                $this->load->library('admin_form');
		$form = new Admin_form;
		
		$form->fieldset('Strategy');
                $form->text('Strategy name', 'name', $strategy['name'], 'Strategy name to be introduced', TRUE, 'e.g., Radu super strategy', TRUE);
                $form->text('Start', 'start', $strategy['start'], 'start bank amount', TRUE, 'e.g., 20 &euro;', FALSE,'100px');
                $form->text('Rate', 'rate', $strategy['rate'], 'rate percentage from bank for growth', TRUE, 'e.g., 0.1', FALSE,'100px');
                $form->text('Multiply', 'multiply', $strategy['multiply'], 'average odds for betting', TRUE, 'e.g., 1.5', FALSE,'100px');
                $form->text('Stop', 'stop', $strategy['stop'], 'target amount', TRUE, 'e.g.,10000 &euro;', FALSE,'100px');
                $form->text('Intermission', 'intermission', $strategy['intermission'], 'the amount at which we re-calculate the wage', TRUE, 'e.g.,10 &euro;', FALSE,'100px');                                
		
		$data = array(
					'form' => $form->display(),
					'form_title' => 'Edit Strategy',
					'form_action' => site_url('admincp2/livescore/add_strategy_validate/edit/'. $strategy['id']),
					'action' => 'edit',                                        
					);
		
		$this->load->view('add_strategy',$data);			
	}
        
        function delete_strategy ($contents,$return_url) 
        {
		
		$this->load->library('asciihex');
		$this->load->model('strategy_model');
		
		$contents = unserialize(base64_decode($this->asciihex->HexToAscii($contents)));
		$return_url = base64_decode($this->asciihex->HexToAscii($return_url));
		
		foreach ($contents as $content) {
                        $this->load->model('step_model');
                        $this->step_model->delete_strategy_steps($content);
			$this->strategy_model->delete_strategy($content);
		}
		       			
		$this->notices->SetNotice('Strategy deleted successfully.');
				
		redirect($return_url);
		
		return TRUE;
	
	}
        
        function view_strategy($id)
        {
            $this->load->library('dataset');
            $columns = array(
                                array(
                                        'name' => 'STEP',
                                        'type' => 'id',
                                        'width' => '15%',                                        
                                        ),
                                array(
                                        'name' => 'STAKE',
                                        'width' => '25%',                                        
                                        'type' => 'text'
                                        ),
                                array(
                                        'name' => 'PROFIT',
                                        'width' => '20%',                                        
                                        'type' => 'text',

                                        ),
                                array(
                                        'name' => 'BANK',
                                        'width' => '40%',
                                        'type' => 'text',
                                        )
                        );
            
                $filters = array();
                $filters['strategy_id'] = $id;
                $filters['limit'] = 50;

                if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset'];                 
						
		$this->dataset->columns($columns);
		$this->dataset->datasource('step_model','get_steps',$filters);
		$this->dataset->base_url(site_url('admincp2/livescore/view_strategy/'.$id));
                $this->dataset->rows_per_page($filters['limit']);
		
                // total rows
		$this->load->model('step_model');
                // total rows
		$total_rows = $this->step_model->get_num_rows($id); 
		$this->dataset->total_rows($total_rows);
                
		// initialize the dataset
		$this->dataset->initialize();
                
                $this->load->view('steps');
        }
        
        function compute_strategy($id)
        {
            $this->load->model('strategy_model');
            $strategy = $this->strategy_model->get_strategy($id);
            
            if(empty($strategy)) {
                $this->notices->SetError('Strategy not found!');				
		redirect('admincp2/livescore/list_strategies');
            }
            
            $is_computed = $this->strategy_model->is_computed($id);            
            if($is_computed) {
                $this->notices->SetError('Strategy already computed!');				
		redirect('admincp2/livescore/view_strategy/'.$id);
            }
            
            $this->steps_recursive($strategy['id'],$strategy['start'],$strategy['start'],1,$strategy['intermission'],$strategy['rate'],$strategy['multiply'],$strategy['stop']);
            $this->notices->SetNotice('Strategy computed successfully!');				
            redirect('admincp2/livescore/list_strategies');
                       
        }
        
        function steps_recursive($strategy_id,$prev,$amount,$steps,$intermission,$rate,$multiply,$stop)
        {                
            $this->load->model('step_model');
                                        
            $diff = $amount - $prev;
            if($diff >= $intermission) $prev += $intermission;

            $stake = $prev*$rate;        
            $win = $prev*$rate*($multiply-1);
            $amount += $win;            
            
            $fields = array();
            $fields['strategy_id'] = $strategy_id;
            $fields['steps'] = $steps;
            $fields['stake'] = $stake;
            $fields['win'] = $win;
            $fields['amount'] = $amount;            

            $this->step_model->new_step($fields);
            
            $steps++;
            
            if($amount >= $stop) {
                $this->notices->SetNotice('Strategy computed successfully!');				
                redirect('admincp2/livescore/list_strategies');
            }
            else $this->steps_recursive($strategy_id,$prev,$amount,$steps,$intermission,$rate,$multiply,$stop);
        }
              
//************************************* BETS LIST*******************************//  
        
        function list_bets()
	{
		$this->load->library('dataset');
		$this->admin_navigation->module_link('Add New Bet',site_url('admincp2/livescore/add_bet'));

                
                
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
							'sort_column' => 'paper_bet',
							),

						array(
							'name' => 'Event Name',
                            'type' => 'text',
							'width' => '17%',
							'filter' => 'event_name',
							),
                       array(
							'name' => 'Country',
                            'type' => 'text',
							'width' => '10%',
							'filter' => 'country_name',
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
							'filter' => 'odds',
							'sort_column' => 'odds',
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
							'width' => '24%',
                            'type' => 'text',
							'filter' => 'strategy_name',
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
                
		$this->dataset->columns($columns);
		$this->dataset->datasource('bet_model','get_bets',$filters);
		$this->dataset->base_url(site_url('admincp2/livescore/list_bets'));
		$this->dataset->rows_per_page($filters['limit']);

		// total rows
                $this->load->model('bet_model');
		$total_rows = $this->bet_model->get_num_rows_bets($filters);

		$this->dataset->total_rows($total_rows);
		
		// initialize the dataset
		$this->dataset->initialize();

		// add actions
		$this->dataset->action('Delete','admincp2/livescore/delete_bet');
		
		$this->load->view('list_bets');
	}
        
    function add_bet($action = 'new', $id = false) 
        {   
            $this->load->helper('form');
            $this->load->library('admin_form');  
            
            $form = new Admin_form();
            $form->fieldset ('Add New Bet');    
            $this->load->model('bet_model');
			$this->load->model('method_model');
			$this->load->model('league_model');
			$this->load->model('user_model');
                        $this->load->model('market_model');
			$this->load->model('country_model');

            $countries = array();
            $countries = array_merge(array('Select country'),$countries);
	    	$countries = $this->country_model->get_name_countries();

            
	    	$methods = array();
	    	$methods = $this->method_model->get_methods(); 
            foreach($methods as $val) {
                $strategy[$val['ID_method']] = $val['method_name'];
            }
            
            $leagues = array();
	    	$leagues = $this->league_model->get_leagues(); 
            foreach($leagues as $val) {
                $event[$val['ID_league']] = $val['league_name'];
            }
            
            $markets = array();
	    $markets = $this->market_model->get_markets(); 
            array_unshift($markets,array('ID_market' => 0,'market_name'=>'Select Markets'));            
            foreach($markets as $val) {
                $market[$val['ID_market']] = $val['market_name'];
            }
		
			$markets_selects = array();
	    	$markets_selects = $this->market_model->get_markets_selects();
			foreach($markets_selects as $val) {
                $market_select[$val['market_select_id']] = $val['market_select_name'];
            }
            
            $username=$this->user_model->get('id');
            //$username = $this->user_model->get('username');
            
            $data = array(      
                   	'username' => $username,
                'strategy' => $strategy,
 					'country_name' => $countries,
                    'event' => $event,
					'market_select' => $market_select,
                    'market' => $market,
                	'form_title' => 'Add New Bet',
                    'form_action' => site_url('admincp2/livescore/post_bet/new'),
					'action' => 'new',
				);
  
		$this->load->view('add_bet',$data);		
	}        
	
	/**
	* Handle New/Edit Bet Post
	*/
	function post_bet($action, $id = false){	       
		
		$this->load->model('bet_model');
		
		// content
		$id_bet = $this->input->post('ID_bet');
		$event_name = $this->input->post('event_name');
		$event_date = $this->input->post('event_date');
		$stake = $this->input->post('stake');
		$profit = $this->input->post('profit') ? $this->input->post('profit') : null;
		$loss = $this->input->post('loss') ? $this->input->post('loss') : null;
		$country_name = $this->input->post('country_name');
		$event_type = $this->input->post('event_type');
		$bet_type = $this->input->post('bet_type');
		$odds = $this->input->post('odds');
		$market_type = $this->input->post('market_type');
        $market_select = $this->input->post('markets_selects') ? $this->input->post('markets_selects') : null;
		$comment = $this->input->post('comment');
		$strategy = $this->input->post('strategy');
		$username = $this->input->post('username');
		$paper_bet = $this->input->post('paper_bet');		
		
		if ($action == 'new') {
			$bet_id = $this->bet_model->new_bet(
						$event_name,
						$event_date,
						$stake,
						$profit,
						$loss,
						$country_name,
						$event_type,
						$bet_type,
						$odds,
						$market_type,
                                                $market_select,
						$comment,
						$strategy,
                                                $username,
												$paper_bet
						);
												
			$this->notices->SetNotice('Bet added successfully.');
		}
		else {
			$bet_id = $this->bet_model->update_bet(
                                                $id_bet,
						$event_name,
						$event_date,
						$stake,
						$profit,
						$loss,
						$country_name,
						$event_type,
						$bet_type,
						$odds,
						$market_type,
                                                $market_select,
						$comment,
						$strategy,
                                                $username,
												$paper_bet
						);

			$this->notices->SetNotice('Bet edited successfully.');
		}
		
                
		redirect('admincp2/livescore/list_bets');
		
		return TRUE;
	}
	
	/**
	* Edit Bet
	*
	* Show the bet form, preloaded with variables
	*
	* @param int $id the ID of the bet

	*/
	function edit_bet($id) {
		$this->load->model('livescore/bet_model');
		$this->load->model('bet_model');
		$this->load->model('method_model');
		$this->load->model('league_model');
		$this->load->model('market_model');
		$this->load->model('country_model');
                $this->load->model('user_model');
                
            $countries = array();
            $countries = array_merge(array('Select country'),$countries);
	    	$countries = $this->country_model->get_name_countries();

            
	    	$methods = array();
	    	$methods = $this->method_model->get_methods(); 
            foreach($methods as $val) {
                $strategy[$val['ID_method']] = $val['method_name'];
            }
            
            $leagues = array();
	    	$leagues = $this->league_model->get_leagues(); 
            foreach($leagues as $val) {
                $event[$val['ID_league']] = $val['league_name'];
            }
            
            $markets = array();
	    	$markets = $this->market_model->get_markets(); 
            foreach($markets as $val) {
                $market[$val['ID_market']] = $val['market_name'];
            }
            
			
			$bet = $this->bet_model->get_bet($id);
		
		 	$markets_selects = array();
			$bet['market_select_name'] = array();
	    	$markets_selects = $this->market_model->get_markets_selects($bet['market_type']); 
            foreach($markets_selects as $val) {
                $market_select[$val['market_select_id']] = $val['market_select_name'];
            }
		$username = $this->user_model->get('username');

		$data = array(
                                        'username' => $bet['username'],
					'ID_bet' => $bet['ID_bet'],
					'event_name' => $bet['event_name'],
					'event_date' => $bet['event_date'],
					'stake' => $bet['stake'],
					'profit' => $bet['profit'],
					'loss' => $bet['loss'],
					'id_country' => $bet['country_name'],
					'country_name' => $countries,
					'event' => $event,
					'bet_type' => $bet['bet_type'],
					'odds' => $bet['odds'],
					'market_id' => $bet['market_type'],
					'market' => $market,
                    'market_select_id' => $bet['market_select'],
                    'market_select' => $market_select,
					'comment' => $bet['comment'],
					'strategy_id' => $bet['strategy'],
					'paper_bet' => $bet['paper_bet'],
					'strategy' => $strategy,
					'form' => $bet,
					'form_title' => 'Edit Bet',
					'form_action' => site_url('admincp2/livescore/post_bet/edit/'.$bet['ID_bet']),
                    'action' => 'edit',
					);
		//print_r ($market_select);
        //die;		
		$this->load->view('add_bet',$data);
	} 
        
        /**
	* Delete bet
	*/
         function delete_bet($contents,$return_url) 
        {
		
		$this->load->library('asciihex');
		$this->load->model('bet_model');
		
		$contents = unserialize(base64_decode($this->asciihex->HexToAscii($contents)));
		$return_url = base64_decode($this->asciihex->HexToAscii($return_url));
		
		foreach ($contents as $content) {
                        $this->bet_model->delete_bet($content);
		}
		       			
		$this->notices->SetNotice('Bets deleted successfully.');
				
		redirect($return_url);
		
		return TRUE;
	
	}
	
//********************************** END ADD BETS *******************************//         
        
//********************************** Methods LIST *******************************//           
                
        function list_methods()
	{
		$this->load->library('dataset');
		$this->admin_navigation->module_link('Add New Method',site_url('admincp2/livescore/add_method/new'));
                
                
                $columns = array(
						array(
							'name' => 'ID #',
							'type' => 'id',
							'width' => '5%',
							'filter' => 'ID_method',
                                                    ),

						array(
							'name' => 'Method Name',
                                                        'type' => 'text',
							'width' => '15%',
                                                        'filter' => 'method_name',
							),
						array(
							'name' => 'Method Details',
                                                        'type' => 'text',
							'width' => '75%',
							),
						 array(
							'name' => '',
							'width' => '5%',
                                                        'type' => 'text',
						),
						
					);
                
                $filters = array(); 
                $filters['limit'] = 3;
                //$filters['id'] = $id;
                
                if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset'];
		
                if(isset($_GET['filters'])) {
                    $aux = unserialize(base64_decode($this->asciihex->HexToAscii($_GET['filters'])));
                    if(isset($aux['ID_method'])) $filters['ID_method'] = $aux['ID_method'];
                    if(isset($aux['method_name'])) $filters['method_name'] = $aux['method_name'];
                }
                
		$this->dataset->columns($columns);
		$this->dataset->datasource('method_model','get_methods',$filters);
		$this->dataset->base_url(site_url('admincp2/livescore/list_methods'));
		$this->dataset->rows_per_page($filters['limit']);
		

                // total rows
		$this->load->model('method_model');
                // total rows
		$total_rows = $this->method_model->get_num_rows_methods($filters); 
		$this->dataset->total_rows($total_rows);
                
		// initialize the dataset
		$this->dataset->initialize();

		// add actions
		$this->dataset->action('Delete','admincp2/livescore/delete_method');
		
		$this->load->view('list_methods');  
        }
        
        /**
	* Delete Method
	*
	*/
         function delete_method ($contents,$return_url) 
        {
		
		$this->load->library('asciihex');
		$this->load->model('method_model');
		
		$contents = unserialize(base64_decode($this->asciihex->HexToAscii($contents)));
		$return_url = base64_decode($this->asciihex->HexToAscii($return_url));
		
		foreach ($contents as $content) {
                        $this->method_model->delete_method($content);
		}
		       			
		$this->notices->SetNotice('Methods deleted successfully.');
				
		redirect($return_url);
		
		return TRUE;
	
	}
	/**
	*View Method description
	*
	*/
	
	function view_method_description($id) 
	{	
		$this->load->model('method_model');
	
		//$methods = array();
	    $methods = $this->method_model->get_method_by_id($id); 
		
		print_r ($methods[$id]); 
		 
	}
        
        /**
	* Add New Method
	*
	*/
	function add_method () {
		
		$this->load->model('livescore/method_model');
		
		$data = array(
					//'form' => $email,
					'form_title' => 'Add New Method',
					'form_action' => site_url('admincp2/livescore/post_method/new'),
                                        'action' => 'new'
					);
		
		$this->load->view('add_method',$data);
	}
	
	
	/**
	* Handle New/Edit Method Post
	*/
	function post_method ($action, $id = false) {	
            
		
		// content
		$id_method = $this->input->post('ID_method');
		$method_name = $this->input->post('method_name');
		$method_description = $this->input->post('method_description');
			
		$this->load->model('method_model');
		
		if ($action == 'new') {
			$method_id = $this->method_model->new_method(
						$method_name,
						$method_description													
						);
												
			$this->notices->SetNotice('Method added successfully.');
		}
		else {
			$method_id = $this->method_model->update_method(
                                                $id_method,
						$method_name,
						$method_description													
						);

			$this->notices->SetNotice('Method edited successfully.');
		}
		
                
		redirect('admincp2/livescore/list_methods');
		
		return TRUE;
	}
	
	/**
	* Edit Method
	*
	* Show the email form, preloaded with variables
	*
	* @param int $id the ID of the email
	*
	* @return string The email form view
	*/
	function edit_method($id) {
		$this->load->model('livescore/method_model');
		
		$method = $this->method_model->get_method($id);
		
		$data = array(
					'ID_method' => $method['ID_method'],
					'method_name' => $method['method_name'],
					'method_description' => $method['method_description'],
					'form' => $method,
					'form_title' => 'Edit Method',
					'form_action' => site_url('admincp2/livescore/post_method/edit/'.$method['ID_method']),
                                        'action' => 'edit',
					);
		//var_dump ($data);
                //die;		
		$this->load->view('add_method',$data);
	}
                
 //********************************** END Methods LIST *******************************//       
        
//********************************** LEAGUES LIST *******************************//         
    /**
	* Manage Leagues
	*
	* Lists active leagues for managing
	*/
	function list_leagues()
	{
		$this->load->library('dataset');
		$this->admin_navigation->module_link('Add New League',site_url('admincp2/livescore/add_league'));

		$columns = array(
						array(
							'name' => 'ID #',
							'type' => 'id',
							'width' => '5%',
                                                    ),

						array(
							'name' => 'League Name',
                                                        'type' => 'text',
							'width' => '90%',
							),
						
                                                array(
							'name' => '',
							'width' => '5%',
                                                        'type' => 'text',
						),
						
					);
                
                $filters = array();    
                $filters['limit'] = 30;

                if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset'];
                
			
		$this->dataset->columns($columns);
		$this->dataset->datasource('league_model','get_leagues',$filters);
		$this->dataset->base_url(site_url('admincp2/livescore/list_leagues'));
		$this->dataset->rows_per_page($filters['limit']);
		
        
		// total rows
                $this->load->model('league_model');
		$total_rows = $this->league_model->num_rows_leagues($filters);
                //die (print($total_rows));
		$this->dataset->total_rows($total_rows);
		
		// initialize the dataset
		$this->dataset->initialize();

		// add actions
		$this->dataset->action('Delete','admincp2/livescore/delete_league');
		
		$this->load->view('list_leagues');
	}
        
        
        /**
	* Add New League
	*
	*/
	function add_league () {
		
		$this->load->model('livescore/league_model');
		
		$data = array(
					//'form' => $email,
					'form_title' => 'Add New League',
					'form_action' => site_url('admincp2/livescore/post_league/new'),
                                        'action' => 'new'
					);
		
		$this->load->view('add_league',$data);
	}
	
	
	/**
	* Handle New/Edit League Post
	*/
	function post_league ($action, $id = false) {	
            
		
		// content
		$id_league = $this->input->post('ID_league');
		$league_name = $this->input->post('league_name');
			
		$this->load->model('league_model');
		
		if ($action == 'new') {
			$method_id = $this->league_model->new_league(
						$league_name													
						);
												
			$this->notices->SetNotice('League added successfully.');
		}
		else {
			$method_id = $this->league_model->update_league(
                                                $id_league,
						$league_name													
						);

			$this->notices->SetNotice('League edited successfully.');
		}
		
                
		redirect('admincp2/livescore/list_leagues');
		
		return TRUE;
	}
	
	/**
	* Edit League
	*
	* Show the league form, preloaded with variables
	*
	* @param int $id the ID of the league
	*
	* @return string The email form view
	*/
	function edit_league($id) {
		$this->load->model('livescore/league_model');
		
		$league = $this->league_model->get_league($id);
		
		$data = array(
					'ID_league' => $league['ID_league'],
					'league_name' => $league['league_name'],
					'form' => $league,
					'form_title' => 'Edit League',
					'form_action' => site_url('admincp2/livescore/post_league/edit/'.$league['ID_league']),
                                        'action' => 'edit',
					);
		//var_dump ($data);
                //die;		
		$this->load->view('add_league',$data);
	} 
        
        /**
	* Delete League
	*/
         function delete_league($contents,$return_url) 
        {
		
		$this->load->library('asciihex');
		$this->load->model('league_model');
		
		$contents = unserialize(base64_decode($this->asciihex->HexToAscii($contents)));
		$return_url = base64_decode($this->asciihex->HexToAscii($return_url));
		
		foreach ($contents as $content) {
                        $this->league_model->delete_league($content);
		}
		       			
		$this->notices->SetNotice('Leagues deleted successfully.');
				
		redirect($return_url);
		
		return TRUE;
	
	}
        
 //********************************** END LEAGUES LIST *******************************// 
 
//********************************** Market LIST *******************************//         
    /**
	* Manage Markets
	*
	* Lists active markets for managing
	*/
	function list_markets()
	{
		$this->load->library('dataset');
		$this->admin_navigation->module_link('Add New Market',site_url('admincp2/livescore/add_market'));

		$columns = array(
						array(
							'name' => 'ID #',
							'type' => 'id',
							'width' => '5%',
                                                    ),

						array(
							'name' => 'Market Name',
                                                        'type' => 'text',
							'width' => '90%',
							),
						
                                                array(
							'name' => '',
							'width' => '5%',
                                                        'type' => 'text',
						),
						
					);
                
                $filters = array();    
                $filters['limit'] = 30;

                if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset'];
                
			
		$this->dataset->columns($columns);
		$this->dataset->datasource('market_model','get_markets',$filters);
		$this->dataset->base_url(site_url('admincp2/livescore/list_markets'));
		$this->dataset->rows_per_page($filters['limit']);
		
        
		// total rows
                $this->load->model('market_model');
		$total_rows = $this->market_model->num_rows_markets($filters);
                //die (print($total_rows));
		$this->dataset->total_rows($total_rows);
		
		// initialize the dataset
		$this->dataset->initialize();

		// add actions
		$this->dataset->action('Delete','admincp2/livescore/delete_market');
		
		$this->load->view('list_markets');
	}
     
	 
	 /**
	*View Markets Selects description
	*
	*/
	
	function view_markets_selects($id) 
	{	
		$this->load->model('market_model');
	
		$markets_selects = array();
	    $markets_selects = $this->market_model->get_markets_selects($id);
		foreach($markets_selects as $val) {
                $market_select[$val['market_select_id']] = $val['market_select_name'];
            }
                        
                if(empty($market_select)) $market_select = array(0=>'None');              
		echo form_dropdown('markets_selects',$market_select); 
		
		
	}	 
        
        /**
	* Add New Market
	*
	*/
	function add_market () {
		
		$this->load->model('livescore/market_model');
		
		$data = array(
					//'form' => $email,
					'form_title' => 'Add New Market',
					'form_action' => site_url('admincp2/livescore/post_market/new'),
                                        'action' => 'new'
					);
		
		$this->load->view('add_market',$data);
	}
	
	
	/**
	* Handle New/Edit Market Post
	*/
	function post_market ($action, $id = false) {	
            
		
		// content
		$id_market = $this->input->post('ID_market');
		$market_name = $this->input->post('market_name');
			
		$this->load->model('market_model');
		
		if ($action == 'new') {
			$market_id = $this->market_model->new_market(
						$market_name													
						);
												
			$this->notices->SetNotice('Market added successfully.');
		}
		else {
			$market_id = $this->market_model->update_market(
                                                $id_market,
						$market_name													
						);

			$this->notices->SetNotice('Market edited successfully.');
		}
		
                
		redirect('admincp2/livescore/list_markets');
		
		return TRUE;
	}
	
	/**
	* Edit Market
	*
	* Show the market form, preloaded with variables
	*
	* @param int $id the ID of the league
	*
	* @return string The email form view
	*/
	function edit_market($id) {
		$this->load->model('livescore/market_model');
		
		$market = $this->market_model->get_market($id);
		
		$data = array(
					'ID_market' => $market['ID_market'],
					'market_name' => $market['market_name'],
					'form' => $market,
					'form_title' => 'Edit Market',
					'form_action' => site_url('admincp2/livescore/post_market/edit/'.$market['ID_market']),
                                        'action' => 'edit',
					);
	
		$this->load->view('add_market',$data);
	} 
        
        /**
	* Delete Market
	*/
         function delete_market($contents,$return_url) 
        {
		
		$this->load->library('asciihex');
		$this->load->model('market_model');
		
		$contents = unserialize(base64_decode($this->asciihex->HexToAscii($contents)));
		$return_url = base64_decode($this->asciihex->HexToAscii($return_url));
		
		foreach ($contents as $content) {
                        $this->market_model->delete_market($content);
		}
		       			
		$this->notices->SetNotice('Leagues deleted successfully.');
				
		redirect($return_url);
		
		return TRUE;
	
	}
        
 //********************************** END Market LIST *******************************// 
       
}
