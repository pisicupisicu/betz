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
                                        
}
