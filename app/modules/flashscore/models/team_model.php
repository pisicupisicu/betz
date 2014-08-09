<?php

/**
* Teams Model
*
* Manages flashscore events
*
* @author Octavian Lelescu
* @copyright Webligh
* @package BJ Tool
*/

	class Team_model extends CI_Model

	{

		private $CI;
		function __construct()
		{
			parent::__construct();
			$this->CI =& get_instance();
		}

                
/**
* Insert New teams
*
* @param array $insert_fields	
*
* @return int $insert_id
*/


    function new_teams ($insert_fields) 
    {																					

        foreach ($insert_fields as $val) {
            $this->db->insert('fl_teams', $val);		
            $insert_id[] = $this->db->insert_id();
        }
        return $insert_id;

     }

/**
* Insert New teams
*
* @param array $insert_fields	
*
* @return int $insert_id
*/


    function new_team ($insert_fields) 
    {																					
        $this->db->insert('fl_teams', $insert_fields);		
        $insert_id = $this->db->insert_id();
        return $insert_id;

     }
     
/**
* Insert Add lineups
*
* @param array $lineups_fields	
*
* @return int $insert_id
*/


    function add_lineups ($lineups_fields) 
    {																					

        $this->db->where('match_id', $lineups_fields['match_id']);
        $this->db->select('ID'); 

        $query = $this->db->get('fl_lineups');

         if ($query->num_rows() > 0) {

            $lineups_fields_update = array ( 'home_formation'=>$lineups_fields['home_formation'],
                                             'away_formation'=>$lineups_fields['away_formation']);
            $this->db->where('match_id', $lineups_fields['match_id']);
            $this->db->update('fl_lineups', $lineups_fields_update);

         } else {

            $this->db->insert('fl_lineups', $lineups_fields);      
            $insert_id = $this->db->insert_id();
            return $insert_id;
             
         } 

     }


     
/**
* Insert Add coaches
*
* @param array $coaches_fields	
*
* @return int $insert_id
*/


function add_coaches ($coaches_fields) 
{	
    // print_r ($coaches_fields);
    $coach_name = htmlentities($coaches_fields['coach_name']);

    $this->db->like('coach_name', $coach_name);
    $this->db->select('ID'); 

    $query = $this->db->get('fl_coaches');

     if ($query->num_rows() > 0) {

        foreach ($query->result_array() as $row) {
            return $row['ID'];
        }

     } else {

         $this->db->insert('fl_coaches', $coaches_fields);      
         $insert_id = $this->db->insert_id();
         return $insert_id;
         
     } 

 }

/**
* Get teams row from db
*
*
* @return array
*/   
function get_teams()
{
    $result = $this->db->get('fl_teams');
    
    return $result->result_array();
}

function clear_teams ($insert_fields,$id,$country_id) 
{	
    $this->db->where('ID',$id);
    $this->db->where('country_id',$country_id);
    $this->db->update('fl_teams', $insert_fields);		
    $insert_id = $this->db->insert_id();
    return $insert_id;
}

/**
* Get Team
*
* @param int $id	
*
* @return array
*/

function get_team ($id) 
{

    $row = array();								                    
    $this->db->join('fl_countries','fl_teams.country_id = fl_countries.ID','left');
    $this->db->where('fl_teams.ID',$id);
    $result = $this->db->get('fl_teams');

    foreach ($result->result_array() as $row) {
            return $row;
    }

    return $row;                   

}
    
/**
* Search Competition
*
* @param array $insert_fields	
*
* @return int $insert_id
*/
function team_exists($match)
{        
//            echo "<pre>";        
//            print_r ($match);
//            die;
    $this->db->where('team_name',$match['team_name']);
    $this->db->where('country_id',$match['country_id']);
    
    $result = $this->db->get('fl_teams');

    foreach ($result->result_array() as $row) {
            return $row['ID'];
        }

    return $result->num_rows();
}              


}



