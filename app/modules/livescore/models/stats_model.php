<?php



/**

* Stats Model

*

* Manages markets

*

* @author Weblight.ro

* @copyright Weblight.ro

* @package BJ Tool



*/



	class Stats_model extends CI_Model

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
		
	/**
	* get_loss_markets
	*
	* 
	* @return array 
	*/
        
	function sum_loss_markets($id) 
        {
		   
		   $this->db->select('loss');
		   $this->db->where('market_type',$id);
           $result = $this->db->get('z_bets');

		   //return $result->result_array();
          $loss = array();
		  foreach($result->result_array() as $market){
			
			$loss[] = $market['loss'];  
		  }

			return array_sum($loss);
		}   
	
	/**
	* Get profit by type of bet
	* 
	* All back bets profit
	* 
	* @return array 
	*/
        
	function sum_back_profit($id) 
        {
		   
		   $this->db->select('profit');
		   $this->db->where('bet_type','Back');
		   $this->db->where('market_type',$id);
           $result = $this->db->get('z_bets');
           $profit = array();
		  foreach($result->result_array() as $market){
			
			$profit[] = $market['profit'];  
		  }

			return array_sum($profit);
		   
		}  
		
		/**
	* Get profit by type of bet
	* 
	* All Lay bets profit
	*
	* @return array 
	*/
        
	function sum_lay_profit($id) 
        {
		   
		   $this->db->select('profit');
		   $this->db->where('bet_type','Lay');
		   $this->db->where('market_type',$id);
           $result = $this->db->get('z_bets');
           $profit = array();
		  foreach($result->result_array() as $market){
			
			$profit[] = $market['profit'];  
		  }

			return array_sum($profit);
		   
		}  
}

