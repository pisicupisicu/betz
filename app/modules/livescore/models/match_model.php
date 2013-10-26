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
                                      
        if(isset($filters['country_name']) && $filters['country_name']) $this->db->like('country_name',$filters['country_name']);
        if(isset($filters['competition_name']) && $filters['competition_name']) $this->db->like('z_competitions.name',$filters['competition_name']);
        if(isset($filters['team1']) && $filters['team1']) $this->db->like('z_teams.name',$filters['team1']);
        if(isset($filters['team2']) && $filters['team2']) $this->db->like('z_teams.name',$filters['team2']);
        if(isset($filters['score']) && $filters['score']) $this->db->like('score',$filters['score']);
        if(isset($filters['parsed']))   $this->db->where('parsed',$filters['parsed']);
        if(isset($filters['match_date_start']) && !empty($filters['match_date_start'])) $this->db->where('match_date >=',$filters['match_date_start']);
        if(isset($filters['match_date_end']) && !empty($filters['match_date_end'])) $this->db->where('match_date <=',$filters['match_date_end']);

        if (isset($filters['limit'])) {
                    $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
                    $this->db->limit($filters['limit'], $offset);
            }

        if(isset($filters['team1']) && $filters['team1']) {
            $this->db->join('z_teams','z_matches.team1 = z_teams.team_id','inner');
        }

        if(isset($filters['team2']) && $filters['team2']) {
            $this->db->join('z_teams','z_matches.team2 = z_teams.team_id','inner');
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

    function get_num_rows($filters = array())
    {

        if(isset($filters['country_name']) && $filters['country_name']) $this->db->like('country_name',$filters['country_name']);
        if(isset($filters['competition_name']) && $filters['competition_name']) $this->db->like('z_competitions.name',$filters['competition_name']);
        if(isset($filters['team1']) && $filters['team1']) $this->db->like('z_teams.name',$filters['team1']);
        if(isset($filters['team2']) && $filters['team2']) $this->db->like('z_teams.name',$filters['team2']);
        if(isset($filters['score']) && $filters['score']) $this->db->like('score',$filters['score']);
        if(isset($filters['parsed']))   $this->db->where('parsed',$filters['parsed']);
        if(isset($filters['match_date_start'])) $this->db->where('match_date >=',$filters['match_date_start']);
        if(isset($filters['match_date_end'])) $this->db->where('match_date <=',$filters['match_date_end']);                        

        if(isset($filters['team1']) && $filters['team1']) {
            $this->db->join('z_teams','z_matches.team1 = z_teams.team_id','inner');
        }

        if(isset($filters['team2']) && $filters['team2']) {
            $this->db->join('z_teams','z_matches.team2 = z_teams.team_id','inner');
        }

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
        $this->db->select('*,z_matches.link AS link_match,z_matches.link_complete AS link_match_complete');
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

    function get_no_of_matches_by_team_id($team_id)
    {
        $this->db->or_where('team1',$team_id);
        $this->db->or_where('team2',$team_id);
        
        $result = $this->db->get('z_matches');

        return $result->num_rows();
    }

    function fix_score () 
    {
        $row = array();                                
        $result = $this->db->get('z_matches');
                
        $this->load->model('match_model');
                
        foreach ($result->result_array() as $linie) {                        
            $score = $linie['score'];
            $aux = explode('-',$score);
            $score = str_replace(' ','',$aux[0]).'-'.str_replace(' ','',$aux[1]);

            $data_match = array(                           
                           'score' =>  $score,                                                                             
                        );
                        
            $this->update_match ($data_match,$linie['id']);
                                                                
        }
            
       return $row;                                                     

    }                       

}

