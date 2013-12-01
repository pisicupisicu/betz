<?php



/**

* Statistics Model For Match Results

*

* Manages markets

*

* @author Weblight.ro

* @copyright Weblight.ro

* @package BJ Tool



*/



	class Statistics_model extends CI_Model

	{

		private $CI;

	

		function __construct()

		{

			parent::__construct();

		

			$this->CI =& get_instance();

		}


	/**
	* get_profit_markets
	*
	* 
	* @return array 
	*/
        
	function sum_profit_markets($id) 
        {
		   
		   $this->db->select('profit');
		   $this->db->where('market_type',$id);
           $result = $this->db->get('z_bets');

					
		  //return $result->result_array();
          $profit = array();
		  foreach($result->result_array() as $market){
			
			$profit[] = $market['profit'];  
		  }

			return array_sum($profit);
		   
		}   
		
	
}

