<?php

/**
* Event Model
*
* Manages flashscore events
*
* @author Octavian Lelescu
* @copyright Octavian Lelescu
* @package BJ Tool
*/

	class Event_model extends CI_Model

	{

		private $CI;
		function __construct()
		{
			parent::__construct();
			$this->CI =& get_instance();
		}

                
/**
* Insert New events ids
*
* @param array $insert_fields	
*
* @return int $insert_id
*/


    function new_event_id ($insert_fields) 
    {																					

            $this->db->insert('fl_matches', $insert_fields);		
            $insert_id = $this->db->insert_id();
            return $insert_id;
    }
               

/**
* Insert New events ids
*
* @param array $insert_fields	
*
* @return int $insert_id
*/


    function new_match ($insert_fields,$id,$event_id) 
    {	
        $this->db->where('ID',$id);
        $this->db->like('event_id',$event_id);
        $this->db->update('fl_matches', $insert_fields);		
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    
/**
* Update flag in fl_match by match id
*
* @param array $insert_fields	
*
* @return int $insert_id
*/

    function update_flags($update_flag,$match_id) 
    {	
        $this->db->where('ID',$match_id);
        $this->db->update('fl_matches', $update_flag);
        return TRUE;
    }

/**
* Search for duplicate Event ID
*
*
* @return array
*/

    function duplicate_id ($search) {

       $this->db->like('event_id',$search);
       $this->db->select('event_id'); 

       $query = $this->db->get('fl_matches');

        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        } 														

    }
    
 /**
* Get match row from db
*
*
* @return array
*/   
function get_next_match()
    {
        $row = array();                                                 
        
        $this->db->where('parsed',0);
        //$this->db->order_by('ID','asc');
        $result = $this->db->get('fl_matches');
        
        return $result->result_array();
    }

/**
* Get Match_id by curent event_id
*
*
* @return array
*/

    function getid_byevent ($event_id) {

       $this->db->like('event_id',$event_id); 

       $result = $this->db->get('fl_matches');

        foreach ($result->result_array() as $linie) {
        $ID = $linie['ID'];
        }

        return $ID;
    }
    

/**
* Get parse_flags by curent event_id
*
*
* @return array
*/

    function getflag_byevent ($event_id) {

       $this->db->like('event_id',$event_id); 

       $result = $this->db->get('fl_matches');

        foreach ($result->result_array() as $linie) {
        $flag = $linie['parse_flags'];
        }

        return $flag;
    }

    
/**
* Get match list
*
*
* @return array
*/
    function get_matches ($filters = array()) 
    {        
    $this->load->model('team_model');
    $row = array();

    $order_dir = (isset($filters['sort_dir'])) ? $filters['sort_dir'] : 'ASC';  
    if(isset($filters['sort']))  $this->db->order_by($filters['sort'], $order_dir);

    if (isset($filters['limit'])) {
                $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
                $this->db->limit($filters['limit'], $offset);
        }

    $this->db->join('fl_competitions','fl_matches.competition_id = fl_competitions.ID','inner');    
    $this->db->join('fl_countries','fl_competitions.country_id = fl_countries.ID','left');

    $result = $this->db->get('fl_matches'); 
    
    foreach ($result->result_array() as $linie) {
        
            $temp = $this->team_model->get_team($linie['team1']);
            $linie['team1'] = $temp['team_name'];
            $temp = $this->team_model->get_team($linie['team2']);
            $linie['team2'] = $temp['team_name'];              
	    $row[] = $linie;
		}
                
    return $row;	


    }
    
/**
* Delete match
*
* Deletes match
* 	
* @param int $id	
*
* @return boolean TRUE
*/

    function delete_match ($id){		

        $this->db->delete('fl_matches',array('id' => $id));
        return TRUE;
    }


/**
 * [reset_flags resetez toate flag-urile la NOT_PARSED]
 * @param  [type] $reset_fields [resetez toate flag-urile la NOT_PARSED]
 * @return [type]               [description]
 */
    
    function reset_flags ($reset_fields,$event_id) 
    {                                                                                   

        $this->db->where('event_id', $event_id);

        $this->db->update('fl_matches', $reset_fields);

        return TRUE;


     }


/**
* Delete match
*
* Deletes match
* 	
* @param int $id	
*
* @return boolean TRUE
*/
    
    function get_num_rows($filters = array()){
//        if(isset($filters['country_name']) && $filters['country_name']) $this->db->like('country_name',$filters['country_name']);
//        if(isset($filters['competition_name']) && $filters['competition_name']) $this->db->like('fl_competitions.name',$filters['competition_name']);
          if(isset($filters['parsed'])){ $this->db->where('fl_matches.parsed',$filters['parsed']); } else { $this->db->where('fl_matches.parsed',1); }
//        if(isset($filters['match_date_start'])) $this->db->where('match_date >=',$filters['match_date_start']);
//        if(isset($filters['match_date_end'])) $this->db->where('match_date <=',$filters['match_date_end']);                        

//        $this->db->join('fl_competitions','fl_matches.competition_id = fl_competitions.ID','inner'); 
//        $this->db->join('fl_countries','fl_competitions.country_id = fl_countries.ID','left');
        
        $result = $this->db->get('fl_matches');

        return $result->num_rows();        
    }

/**
 * [update_home_coach description]
 * @param  [type] $home_coach_id [id-ul antrenorului din tabela de antrenori]
 * @param  [type] $match_id      [id-ul meciului in care se face update-ul id-ului antrenorului]
 * @return [type]                [description]
 */
    function update_home_coach ($home_coach_fields,$match_id){

        $this->db->where('ID',$match_id);
        $this->db->update('fl_matches', $home_coach_fields);      
        return TRUE;

    }

/**
 * [update_away_coach description]
 * @param  [type] $away_coach_id [id-ul antrenorului din tabela de antrenori]
 * @param  [type] $match_id      [id-ul meciului in care se face update-ul id-ului antrenorulu]
 * @return [type]                [description]
 */
    function update_away_coach ($away_coach_fields,$match_id){

        $this->db->where('ID',$match_id);
        $this->db->update('fl_matches', $away_coach_fields);      
        return TRUE;

    }


}



