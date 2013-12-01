<?php



/**

* Market Model

*

* Manages markets

*

* @author Weblight.ro

* @copyright Weblight.ro

* @package BJ Tool



*/



	class Market_model extends CI_Model

	{

		private $CI;

	

		function __construct()

		{

			parent::__construct();

		

			$this->CI =& get_instance();

		}

          
      /**
	* Get Market
	*
	* @param int $id
	* 
	* @return array 
	*/
	function get_market($id) 
        {
		
                    $row = array();								
                    $this->db->where('id_market',$id);
                    $result = $this->db->get('z_markets');

                    foreach ($result->result_array() as $row) {
                            return $row;
                    }

                    return $row;	
	}
        
      /**
	* Get Market by name
	*
	* @param int $id
	* 
	* @return array 
	*/   
     function get_market_by_name($market_name) 
        {
		
            $row = array();								
            $this->db->where('market_name',$market_name);
            $result = $this->db->get('z_markets');
            $row =array();
            foreach ($result->result_array() as $row) {
                    return $row;
            }

            return $row;	
	}   
	 /**
	* Get Markets
	*
	* 
	* @return array 
	*/
        
	function get_markets($filters = array()) 
        {
            

                $result = $this->db->get('z_markets');
	

                if (isset($filters['id_market'])) {

			$this->db->where('ID_market', $filters['id_market']);

		}

		if (isset($filters['limit'])) {
                            $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
                            $this->db->limit($filters['limit'], $offset);
                    }
		
                                
               
		if ($result->num_rows() == 0) {
			return FALSE;
		}
                
                
		$markets = array();

                foreach ($result->result_array() as $market) {
                    
                    $markets[] = array(
                                        'ID_market' => $market['ID_market'],
					'market_name' => $market['market_name'],
				     );
		}
	
		return $markets;

	}   
	
	/**
	* Get Markets Selects
	*
	* 
	* @return array 
	*/
        
	function get_markets_selects($id='4') 
        {
        $market = array();								
		$this->db->where('markets_id',$id); // doar de test am pus id 4
        $result = $this->db->get('z_markets_selects');
	
		$markets_selects = array();

                foreach ($result->result_array() as $market) {
                    
                    $markets_selects[] = array(
                    'market_select_id' => $market['market_select_id'],
					'markets_id' => $market['markets_id'],
					'market_select_name' => $market['market_select_name'],
				     );
		}
	
		return $markets_selects;

	}  
    
    /**
	* Get Markets Selects by name
	*
	* 
	* @return array 
	*/
        
	function markets_selects_by_name($market_select_name) 
        {							
		$this->db->where('market_select_name',$market_select_name);
        $result = $this->db->get('z_markets_selects');
	    $row =array();
		foreach ($result->result_array() as $row) {
                            return $row;
                    }

                    return $row;

	}  
	
	/**
	* Get Markets Selects
	* 
	* @return array 
	*/
            
    function num_rows_markets($filters = array()) {
             if (isset($filters['id'])) {

			$this->db->where('ID_market', $filters['id']);

		}

		                
                $result = $this->db->get('z_markets');
                
                
                return $result->num_rows();
        }         
  
         /**
	* Create New Market
	*
	*
	* @return int $market_name
	*/
	function new_market ($market_name) 
        {
		$insert_fields = array(
                                        'market_name' => $market_name,		
				);
						
		$this->db->insert('z_markets', $insert_fields);

		return TRUE;
	}
          
        
        /**
	* Update Market
	*
	* @return void
	*/
	function update_market ( $id_market,$market_name) 
        {
		$update_fields = array(
					'market_name' => $market_name,
				);

		$this->db->update('z_markets', $update_fields, array('ID_market' => $id_market));
		
								
		return TRUE;
	}
        
        
        /**
	* Delete Methods
	*
	* 
	* @return array 
	*/
            function delete_market ($id) 
            {
                    $this->db->delete('z_markets',array('id_market' => $id));

                    return TRUE;
            }


}

