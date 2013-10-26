<?php



/**

* Currency Model
*
* Manages Currency
*
* @author Weblight.ro

* @copyright Weblight.ro

* @package BJ Tool



**/



	class Currency_model extends CI_Model

	{

		private $CI;

	

		function __construct()

		{

			parent::__construct();

		

			$this->CI =& get_instance();

		}

           
      /**
	* Get Currency
	*
	* @param int $id
	* 
	* @return arraycurrency 
	*/
	function get_currency($id) 
        {
		
                    $row = array();								
                    $this->db->where('id_currency',$id);
                    $result = $this->db->get('z_currencies');

                    foreach ($result->result_array() as $row) {
                            return $row;
                    }

                    return $row;	
	}
        
         
        
	 /**
	* Get currencies
	*
	* 
	* @return array 
	*/
        
	function get_currencies($filters = array()) 
        {

	

		if (isset($filters['id_currency'])) {

		   $this->db->where('id_currency', $filters['id_currency']);

		   }

		if (isset($filters['limit'])) {
			$offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
			$this->db->limit($filters['limit'], $offset);
           }
             
        $this->db->join('z_countries','z_countries.ID = z_currencies.id_country','inner');
        $result = $this->db->get('z_currencies');      

		if ($result->num_rows() == 0) {
			return FALSE;
		}
        
		$currencies = array();
        
        foreach ($result->result_array() as $currency) {
                    
				$currencies[] = array(
				 'id_currency' => $currency['id_currency'],
				 'id_country' => $currency['country_name'],
				 'flag' => $currency['flag'],
				 'name_currency' => $currency['name_currency'],
				 'code_ISO' => $currency['code_ISO'],
				 'symbol_currency' => $currency['symbol_currency'],
				 );
		}
	
		return $currencies;

	}      
            
    function num_rows_currencies($filters = array()) {
             if (isset($filters['id'])) {

			$this->db->where('id_currency', $filters['id']);

		}

		                
                $result = $this->db->get('z_currencies');
                
                
                return $result->num_rows();
        }         
  
         /**
	* Create New currency
	*
	*
	* @return true
	*/
	function new_currency ($insert_fields) 
        {
						
		$this->db->insert('z_currencies', $insert_fields);

		return TRUE;
	}
          
        
        /**
	* Update currency
	*
	* @return true
	*/
	function update_currency ($update_fields,$id_currency) 
        {

		$this->db->update('z_currencies', $update_fields, array('id_currency' => $id_currency));
		
								
		return TRUE;
	}
        
        
        /**
	* Delete Leagues
	*
	* 
	* @return array 
	*/
            function delete_currency ($id) 
            {
                    $this->db->delete('z_currencies',array('id_currency' => $id));

                    return TRUE;
            }
        

}

