<?php



/**

* Country Model

*

* Manages steps

*

* @author Weblight.ro

* @copyright Weblight.ro

* @package BJ Tool



*/



	class Country_model extends CI_Model

	{

            private $CI;



            function __construct()

            {

                    parent::__construct();



                    $this->CI =& get_instance();

            }

	

            /**

            * Get countries

            * @param array $params

            *

            * @return array

            */

            function get_countries ($params = array()) 

            {

                    $row = array();	

                    if(isset($params['country_id'])) $this->db->where('ID',$params['country_id']);

                    if (isset($params['limit'])) {

                                $offset = (isset($params['offset'])) ? $params['offset'] : 0;

                                $this->db->limit($params['limit'], $offset);

                        }
						
					if(isset($params['dropdown'])) {
								$row[0] = 'Select Country';
					}
					
                    $result = $this->db->get('z_countries');



                    foreach ($result->result_array() as $linie) {

                            $row[$linie['ID']] = $linie['country_name'];

                    }

                    return $row;														

            }

            
   
	 /**
	* Get Countries
	*
	* 
	* @return array 
	*/
        
	function get_countries_list ($filters = array())  {

        if (isset($filters['ID'])) {
     		$this->db->where('ID', $filters['ID_country']);
		}
 
 		if (isset($filters['country_name'])) {
			$this->db->like('country_name', $filters['country_name']);
		}
            
		if (isset($filters['limit'])) {
                            $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
                            $this->db->limit($filters['limit'], $offset);
                    }
		
       $result = $this->db->get('z_countries');              
               
		if ($result->num_rows() == 0) {
			return FALSE;
		}
 
	  $countries = array();

      foreach ($result->result_array() as $country) {
                   
         $countries[] = array(
         'ID' => $country['ID'],
		 'country_name' => $country['country_name'] );
		}
	
		return $countries;

	}
			

      /**

     * Get country

     * @param array $params

     *

     * @return array

     */ 


        function get_country_by_name($name)

        {

            $this->db->like('country_name',$name);

            $result = $this->db->get('z_countries');

            foreach ($result->result_array() as $row) {

                return $row['ID'];

            }

        }

			 

            /**

            * Get Country

            *

            * @param int $id	

            *

            * @return array

            */

            function get_country ($id) 

            {

                    $row = array();								

                    $this->db->where('ID',$id);

                    $result = $this->db->get('z_countries');

                    foreach ($result->result_array() as $row) {

                            return $row;

                    }

                    return $row;														

            }



            /**

            * Create New Countries

            *

            * Creates a new step

            *

            * @param array $insert_fields	

            *

            * @return int $insert_id

            */

            function new_country ($insert_fields){																					

                    $this->db->insert('z_countries', $insert_fields);		

                    $insert_id = $this->db->insert_id();

                    return $insert_id;

            }



            /**

            * Update Country

            * 

            * @param array $update_fields

            * @param int $id	

            *

            * @return boolean TRUE

            */

            function update_country ($update_fields,$id) 

            {		
                    $this->db->update('z_countries',$update_fields,array('ID' => $id));
                    return TRUE;
            }



            /**

            * Delete step

            *

            * Deletes step

            * 	

            * @param int $id	

            *

            * @return boolean TRUE

            */

            function delete_country ($id) 

            {		

                $this->db->delete('z_countries',array('ID' => $id));



                return TRUE;

            }

            

            function delete_countries_ids($country_id)

            {

                $this->db->delete('z_countries',array('country_id' => $country_id));

                

                return TRUE;

            }

            

            function get_num_rows($country_id)

            {

                $this->db->where('ID',$country_id);

                $result = $this->db->get('z_countries');

                return $result->num_rows();        

            }

}

