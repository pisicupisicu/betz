<?php
    /**
    * Match Goals
    *
    * Manages Goals
    *
    * @author Weblight.ro
    * @copyright Weblight.ro
    * @package BJ Tool
    */

	class Goal_model extends CI_Model
	{
		private $CI;
	
		function __construct()

		{
			parent::__construct();		
			$this->CI =& get_instance();
		}
	
    /**
	* Get Matches
	*
	*
	* @return array
	*/

	function get_goals ($filters = array()) 
    {
        $this->load->model('goal_model');
		$row = array();
                
        $order_dir = (isset($filters['sort_dir'])) ? $filters['sort_dir'] : 'ASC';  
        if(isset($filters['sort']))  $this->db->order_by($filters['sort'], $order_dir);
                                      
        if(isset($filters['country_name'])) $this->db->like('country_name',$filters['country_name']);
        if(isset($filters['competition_name'])) $this->db->like('z_competitions.name',$filters['competition_name']);
        if(isset($filters['team1'])) $this->db->like('z_teams.name',$filters['team1']);
        if(isset($filters['team2'])) $this->db->like('z_teams.name',$filters['team2']);

        if (isset($filters['limit'])) {
                    $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
                    $this->db->limit($filters['limit'], $offset);
            }

        $this->db->join('z_competitions','z_goals.competition_id = z_competitions.competition_id','inner');    
        $this->db->join('z_countries','z_competitions.country_id = z_countries.ID','left');
        $this->db->select('*,z_goals.link_complete AS link_match');

		$result = $this->db->get('z_goals');        
				
		foreach ($result->result_array() as $linie) {
            $temp = $this->team_model->get_team($linie['team1']);
            $linie['team1'] =   $temp['name'];
            $temp =  $this->team_model->get_team($linie['team2']);
            $linie['team2'] =   $temp['name'];
            $linie['competition_name']  =   $linie['name'];              
			$row[] = $linie;
            // print '<pre>';
            // print_r($linie);
            // die;

		}
                
		return $row;														

	}

    function get_num_rows($filters)
    {
        if(isset($filters['country_name'])) $this->db->like('country_name',$filters['country_name']);
        if(isset($filters['competition_name'])) $this->db->like('z_competitions.name',$filters['competition_name']);
        if(isset($filters['team1'])) $this->db->like('z_teams.name',$filters['team1']);
        if(isset($filters['team2'])) $this->db->like('z_teams.name',$filters['team2']);
        if(isset($filters['parsed']))   $this->db->where('parsed',$filters['parsed']);

        $this->db->join('z_competitions','z_goals.competition_id = z_competitions.competition_id','inner');    
        $this->db->join('z_countries','z_competitions.country_id = z_countries.ID','left');

        $result = $this->db->get('z_goals');

        return $result->num_rows();        
    }

        

        /**
        * Get Match
        *
        * @param int $id	
        *
        * @return array
        */

    function get_goals_by_match ($match_id) 
    {
            $row = array();								                    
            
            $this->db->where('z_goals.match_id',$match_id); 
            $this->db->order_by('min','asc');          
            $result = $this->db->get('z_goals');

            foreach ($result->result_array() as $linie) {
                    $row[] = $linie;
            }
            
            return $row;                   
    }

    /**
    * Create New Match
    *
    * Creates a new match
    *
    * @param array $insert_fields	
    *
    * @return int $insert_id
    */

    function new_goal ($insert_fields) 
    {																					
        $this->db->insert('z_goals', $insert_fields);		
        $insert_id = $this->db->insert_id();

        return $insert_id;
    }



    /**
    * Update Match
    *
    * Updates match
    * 
    * @param array $update_fields
    * @param int $id	
    *
    * @return boolean TRUE
    */

    function update_goal ($update_fields,$id) 
    {		

        $this->db->update('z_goals',$update_fields,array('id' => $id));
        return TRUE;
    }

    function goal_exists($match)
    {                                        
        $this->db->where('match_id',$match['match_id']);
        $this->db->where('score',$match['score']);
        $this->db->where('min',$match['min']);       
        $this->db->where('player',$match['player']);
        $this->db->where('team',$match['team']);     

        $result = $this->db->get('z_goals');

        foreach ($result->result_array() as $row) {
                return $row['id'];
            }

        return $result->num_rows();
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

    function delete_goal ($id) 
    {		

        $this->db->delete('z_goals',array('id' => $id));
        return TRUE;
    }                

}

