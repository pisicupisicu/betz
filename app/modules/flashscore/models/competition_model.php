<?php

/**
* Competition Model
*
* Manages flashscore events
*
* @author Octavian Lelescu
* @copyright Webligh
* @package BJ Tool

*/

	class Competition_model extends CI_Model

	{

		private $CI;
		function __construct()
		{
			parent::__construct();
			$this->CI =& get_instance();
		}

                
/**
* Insert New Competition
*
* @param array $insert_fields	
*
* @return int $insert_id
*/


    function new_competition ($insert_fields) 
    {																					
            $this->db->insert('fl_competitions', $insert_fields);		
            $insert_id = $this->db->insert_id();
            return $insert_id;
    }
               
 
/**
* Search Competition
*
* @param array $insert_fields	
*
* @return int $insert_id
*/
function competition_exists($match)
    {                                
        $this->db->where('competition_name',$match['competition_name']);;

        $result = $this->db->get('fl_competitions');

        foreach ($result->result_array() as $row) {
                return $row['ID'];
            }

        return $result->num_rows();
    }
    

}



