<?php
    /**
    * Match Model
    *
    * Manages matches
    *
    * @author Weblight.ro
    * @copyright Weblight.ro
    * @package BJ Tool
    */

	class Match_model extends CI_Model
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

	function get_matches ($filters = array()) 
    {
        $this->load->model('team_model');
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

        $this->db->join('z_competitions','z_matches.competition_id = z_competitions.competition_id','inner');    
        $this->db->join('z_countries','z_competitions.country_id = z_countries.ID','left');
        $this->db->select('*,z_matches.link_complete AS link_match');

		$result = $this->db->get('z_matches');        
				
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

        $this->db->join('z_competitions','z_matches.competition_id = z_competitions.competition_id','inner');    
        $this->db->join('z_countries','z_competitions.country_id = z_countries.ID','left');

        $result = $this->db->get('z_matches');

        return $result->num_rows();        
    }

        

        /**
        * Get Match
        *
        * @param int $id	
        *
        * @return array
        */

    function get_match ($id) 
    {
        $row = array();								                    

        $this->db->join('z_competitions','z_matches.competition_id = z_competitions.competition_id','inner');    
        $this->db->join('z_countries','z_competitions.country_id = z_countries.ID','left');
        $this->db->where('z_matches.id',$id);
        $this->db->select('*,z_matches.link_complete AS link_match');
        $result = $this->db->get('z_matches');

        foreach ($result->result_array() as $row) {
                return $row;
        }
        
        return $row;                   
    }

    function get_matches_by_team_id($id)
    {
        $row = array();

        $this->db->or_where('team1',$id);
        $this->db->or_where('team2',$id);

        $result = $this->db->get('z_matches');

        foreach ($result->result_array() as $line) {
                $row[] = $line;
        }
        
        return $row;
    }

    function get_next_match()
    {
        $row = array();                                                 
        
        $this->db->where('parsed',0);
        $this->db->order_by('id','asc');
        $result = $this->db->get('z_matches');

        foreach ($result->result_array() as $row) {
                return $row;
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

    function new_match ($insert_fields) 
    {																					
        $this->db->insert('z_matches', $insert_fields);		
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

    function update_match ($update_fields,$id) 
    {		

        $this->db->update('z_matches',$update_fields,array('id' => $id));
        return TRUE;
    }

    function match_exists($match)
    {                                
        $this->db->where('link',$match['link']);        
        $result = $this->db->get('z_matches');

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

    function delete_match ($id) 
    {		

        $this->db->delete('z_matches',array('id' => $id));
        return TRUE;
    }                

}

