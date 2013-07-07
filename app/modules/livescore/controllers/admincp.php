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
    
        public $t = 100;
        //start bank amount
        public $start;
        //rate percentage from bank for growth
        public $rate;
        //average odds for betting
        public $multiply;
        //target amount
        public $stop;
        //the amount at which we re-calculate the wage
        public $intermission;
    
	function __construct()
	{
		parent::__construct();
				
		$this->admin_navigation->parent_active('livescore');
                if($this->t == 10) {
                    $this->start = 20;
                    $this->rate = 0.1;
                    $this->multiply = 1.5;
                    $this->stop = 10000;
                    $this->intermission = 10;
                }
                else {
                    $this->start = 10000;
                    $this->rate = 0.01;
                    $this->multiply = 1.5;
                    $this->stop = 1000000;
                    $this->intermission = 1000;
                }
                
		
		//error_reporting(E_ALL^E_NOTICE);
		//error_reporting(E_WARNING);
	}	
	
	function index () 
        {
            redirect('admincp/livescore/steps');		
	}
        
        function steps()
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
                $filters['limit'] = 50;

                if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset'];                 
						
		$this->dataset->columns($columns);
		$this->dataset->datasource('step_model','get_steps',$filters);
		$this->dataset->base_url(site_url('admincp/livescore/steps'));
                $this->dataset->rows_per_page(50);
		
                // total rows
		$total_rows = $this->db->get('z_steps')->num_rows(); 
		$this->dataset->total_rows($total_rows);
                
		// initialize the dataset
		$this->dataset->initialize();
                
                $this->load->view('steps');
            
        }
        
        function steps_recursive($prev,$amount,$steps)
        {                
            $this->load->model('step_model');
                
            if(!$prev)      $prev = $this->start;
            if(!$amount)    $prev = $this->start;
            if(!$steps)     $steps = 1;
            
            $diff = $amount - $prev;
            if($diff >= $this->intermission) $prev += $this->intermission;

            $stake = $prev*$this->rate;        
            $win = $prev*$this->rate*($this->multiply-1);
            $amount += $win;            
            
            $fields['steps'] = $steps;
            $fields['stake'] = $stake;
            $fields['win'] = $win;
            $fields['amount'] = $amount;            

            $this->step_model->new_step($fields);
            
            $steps++;
            
            if($amount >= $this->stop)   return;
            else $this->steps_recursive($prev,$amount,$steps);
        }
                
}
