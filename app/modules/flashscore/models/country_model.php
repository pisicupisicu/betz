<?php

/**
* Country Model
*
* Manages flashscore events
*
* @author Octavian Lelescu
* @copyright Webligh
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
* Insert New country
*
* @param array $insert_fields	
*
* @return int $insert_id
*/


    function new_country ($insert_fields) 
    {																					
            $this->db->insert('fl_countries', $insert_fields);		
            $insert_id = $this->db->insert_id();
            return $insert_id;
    }

/**
* Insert New country
*
* @param array $insert_fields	
*
* @return int $insert_id
*/
function country_exists($match)
    {                                
        $this->db->where('country_name',$match['country_name']);;

        $result = $this->db->get('fl_countries');

        foreach ($result->result_array() as $row) {
                return $row['ID'];
            }

        return $result->num_rows();
    }
                

/**
* Search for duplicate Country
*
*
* @return array
*/

    function duplicate_country ($search) {

       $this->db->like('country_name',$search);
       $this->db->select('country_name'); 

       $query = $this->db->get('fl_countries');

        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        } 														

    }

}



