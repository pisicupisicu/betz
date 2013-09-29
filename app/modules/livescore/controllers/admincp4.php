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

