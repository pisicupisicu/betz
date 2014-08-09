<?php



/**
* Team Model
*
* Manages teams
*
* @author Weblight.ro
* @copyright Weblight.ro
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
	* Get Teams
	*
	*
	* @return array
	*/

	function get_teams ($filters = array()) 
    {        
		$row = array();

        $order_dir = (isset($filters['sort_dir'])) ? $filters['sort_dir'] : 'ASC';                        
                                      
        if(isset($filters['country_id'])) $this->db->where('country_id',$filters['country_id']);
        if(isset($filters['country_name'])) $this->db->like('country_name',$filters['country_name']);
        if (isset($filters['limit'])) {
                    $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
                    $this->db->limit($filters['limit'], $offset);
            }

        if(isset($filters['equal'])) {
            if(isset($filters['name'])) $this->db->where('name',$filters['name']);
        } else {
            if(isset($filters['name'])) $this->db->like('name',$filters['name']);
        }    
        
        $this->db->join('z_countries','z_teams.country_id = z_countries.ID','left');

        $this->db->order_by('country_name,name',$order_dir);
       
        // if(isset($filters['sort']))  $this->db->order_by($filters['sort'], $order_dir);        

		$result = $this->db->get('z_teams');
				
		foreach ($result->result_array() as $linie) {            
			$row[] = $linie;
		}
                
		return $row;														

	}

    function get_duplicate_teams_helper($filters = array())
    {  
        $row = array();

        $order_dir = (isset($filters['sort_dir'])) ? $filters['sort_dir'] : 'ASC';          
                                      
        if(isset($filters['country_id'])) $this->db->where('country_id',$filters['country_id']);
        if(isset($filters['country_name'])) $this->db->like('country_name',$filters['country_name']);

        if (isset($filters['limit'])) {
                    $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
                    $this->db->limit($filters['limit'], $offset);
            }

        if(isset($filters['equal'])) {
            if(isset($filters['name'])) $this->db->where('name',$filters['name']);
        } else {
            if(isset($filters['name'])) $this->db->like('name',$filters['name']);
        }

        $this->db->select('*,COUNT(*) c');
        $this->db->group_by('country_id,name');
        $this->db->having('c > 1');        

        $this->db->join('z_countries','z_teams.country_id = z_countries.ID','left');

        $this->db->order_by('country_name,name',$order_dir);
        //if(isset($filters['sort']))  $this->db->order_by($filters['sort'], $order_dir);

        $result = $this->db->get('z_teams');

        foreach ($result->result_array() as $linie) {            
            $row[] = $linie;            

        }

        return $row;
    }

    function get_duplicate_teams_helper_num_rows($filters = array())
    {
        return count($this->get_duplicate_teams_helper($filters));
    }

    function get_duplicate_teams($filters = array())
    {             
        $row = $duplicate_teams = array();

        $duplicate_teams = $this->get_duplicate_teams_helper($filters);

        foreach($duplicate_teams as $team) {
            $this->db->where('country_id',$team['country_id']);
            $this->db->where('name',$team['name']);
            $this->db->join('z_countries','z_teams.country_id = z_countries.ID','left');
            $result = $this->db->get('z_teams');
            foreach ($result->result_array() as $linie) {                
                $row[] = $linie;
            }
        }

        return $row;
    }

    function get_similar_teams($filters = array(),$count = 0)
    {             
        $row = $teams = array();
        $filters_star = array('name' => '*');

        $filters_team = array_merge($filters, $filters_star);
        unset($filters_team['offset']);
        unset($filters_team['limit']);        
        $teams = $this->get_teams($filters_team);

        foreach($teams as $team) {
            $this->db->where('country_id',$team['country_id']);
            $this->db->like('name',$team['name']);
            $this->db->join('z_countries','z_teams.country_id = z_countries.ID','left');
            $result = $this->db->get('z_teams');
            
            if($result->num_rows > 1) {
               foreach ($result->result_array() as $linie) {                    
                    $row[] = $linie;
                } 
            }
            
        }       

        if(!$count) {
            return array_slice($row,isset($filters['offset']) ? (int)$filters['offset'] : 0,isset($filters['limit']) ? (int)$filters['limit'] : 20);
        } else {
            return $row;
        }
        
    }

    function get_star_teams($filters = array(),$count = 0)
    {             
        $row = $teams = array();

        $filters_team = array_merge($filters, array('name' => '*'));
        unset($filters_team['offset']);
        unset($filters_team['limit']);        
        $teams = $this->get_teams($filters_team);
        $this->load->model('match_model');
        //print_r($teams);die;

        foreach($teams as $team) {
            $team['name'] = str_replace('*','',$team['name']);
            $team['name'] = trim($team['name']);
            $this->db->where('country_id',$team['country_id']);
            $this->db->like('name',$team['name']);
            $this->db->join('z_countries','z_teams.country_id = z_countries.ID','left');
            $result = $this->db->get('z_teams');
            
            if($result->num_rows > 1) {
               foreach ($result->result_array() as $linie) {
                    $linie['matches'] = $this->match_model->get_no_of_matches_by_team_id($linie['team_id']);                    
                    $row[] = $linie;
                } 
            }
            
        }       

        if(!$count) {
            return array_slice($row,isset($filters['offset']) ? (int)$filters['offset'] : 0,isset($filters['limit']) ? (int)$filters['limit'] : 20);
        } else {
            return $row;
        }
        
    }

    function get_num_rowz_similar($filters = array())
    {       
        return count($this->get_similar_teams($filters,1));        
    }

    function get_num_rowz_star($filters = array())
    {       
        return count($this->get_star_teams($filters,1));        
    }

    function fix_duplicate_teams($filters = array())
    {
        $this->load->model('match_model');
        $duplicate_teams = array();

        $duplicate_teams = $this->get_duplicate_teams_helper($filters);
        $deleted = 0;

        foreach($duplicate_teams as $team) {
            $row = array();
            $no_of_matches = 0;
            $this->db->where('country_id',$team['country_id']);
            $this->db->where('name',$team['name']);
            $this->db->join('z_countries','z_teams.country_id = z_countries.ID','left');
            $result = $this->db->get('z_teams');
            foreach($result->result_array() as $linie) {
                $linie['no_of_matches'] = $this->match_model->get_no_of_matches_by_team_id($linie['team_id']);
                if ($linie['no_of_matches']) {
                    $no_of_matches++;
                }
                $row[] = $linie;
            }

            if($no_of_matches) {
                foreach($row as $linie) {
                    if(!$linie['no_of_matches']) {
                       $this->delete_team($linie['team_id']);
                       $deleted++; 
                    }
                }
            }

        }

        return $deleted;
    }

    function get_num_rowz($filters = array())
    {       
                
        $order_dir = (isset($filters['sort_dir'])) ? $filters['sort_dir'] : 'ASC';  
        if(isset($filters['sort']))  $this->db->order_by($filters['sort'], $order_dir);
                                      
        if(isset($filters['country_id'])) $this->db->where('country_id',$filters['country_id']);
        if(isset($filters['country_name'])) $this->db->like('country_name',$filters['country_name']);

        if (isset($filters['limit'])) {
                    $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
                    $this->db->limit($filters['limit'], $offset);
            }

        if(isset($filters['equal'])) {
            if(isset($filters['name'])) $this->db->where('name',$filters['name']);
        } else {
            if(isset($filters['name'])) $this->db->like('name',$filters['name']);
        }    

        
        $this->db->join('z_countries','z_teams.country_id = z_countries.ID','left');

        if(isset($filters['country_id'])) {
            $this->db->order_by('name','asc');
        }

        $result = $this->db->get('z_teams');

        return $result->num_rows();        
    }

    function get_num_rowz_duplicate($filters = array())
    {       
        return count($this->get_duplicate_teams());        
    }

    function get_null_teams_num_rows($filters = array())
    {
        return count($this->get_null_teams($filters));
    }

    function get_null_teams($filters = array())
    {             
        $row = array();

        $order_dir = (isset($filters['sort_dir'])) ? $filters['sort_dir'] : 'ASC';                        
                                      
        if(isset($filters['country_id'])) $this->db->where('country_id',$filters['country_id']);
        if(isset($filters['country_name'])) $this->db->like('country_name',$filters['country_name']);
        if (isset($filters['limit'])) {
                    $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
                    $this->db->limit($filters['limit'], $offset);
            }

        if(isset($filters['equal'])) {
            if(isset($filters['name'])) $this->db->where('name',$filters['name']);
        } else {
            if(isset($filters['name'])) $this->db->like('name',$filters['name']);
        }    
        
        $this->db->join('z_countries','z_teams.country_id = z_countries.ID','left');

        $this->db->order_by('country_name,name',$order_dir);
       
        if(isset($filters['sort']))  $this->db->order_by($filters['sort'], $order_dir);

        $this->db->where('z_teams.country_id', NULL);
        $result = $this->db->get('z_teams');
                
        foreach ($result->result_array() as $linie) {            
            $row[] = $linie;
        }
                
        return $row;
    }

    function get_similar_teams_by_team_id_num_rows($filters = array())
    {
        return count($this->get_similar_teams_by_team_id($filters));
    }

    function get_similar_teams_by_team_id($filters)
    {                                                       
        $this->db->where('team_id',$filters['id']);
        $result = $this->db->get('z_teams');

        foreach ($result->result_array() as $linie) {
                $row = $linie;
                break;                
        }

        $name = trim($row['name']);

        $row = array();

        $this->db->join('z_countries','z_teams.country_id = z_countries.ID','left');
        $this->db->like('z_teams.name',$name);
        $result = $this->db->get('z_teams');

        foreach ($result->result_array() as $linie) {
               $row[] = $linie;               
        }

        return $row;
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
        $this->db->join('z_countries','z_teams.country_id = z_countries.ID','left');
        $this->db->where('team_id',$id);
        $result = $this->db->get('z_teams');

        foreach ($result->result_array() as $row) {
                return $row;
        }

        return $row;                   

    }


    /**
    * Create New Team
    *
    * Creates a new team
    *
    * @param array $insert_fields	
    *
    * @return int $insert_id
    */

    function new_team ($insert_fields) 
    {																					
        $this->db->insert('z_teams', $insert_fields);		
        $insert_id = $this->db->insert_id();

        return $insert_id;
    }

    /**
    * Update Team
    *
    * Updates team
    * 
    * @param array $update_fields
    * @param int $id	
    *
    * @return boolean TRUE
    */

    function update_team ($update_fields,$id) 
    {		
            $this->db->update('z_teams',$update_fields,array('team_id' => $id));

            return TRUE;
    }

    function update_team_matches($id)
    {
        $this->load->model('match_model');
        $matches = count($this->match_model->get_matches_by_team_id(array('team_id' => $id)));
        $update_fields = array(
                'matches' => $matches,
            );
        $this->update_team($update_fields,$id);
    }

    function update_all_teams_matches()
    {
        $result = $this->db->get('z_teams');

        foreach ($result->result_array() as $row) {
                $this->update_team_matches($row['team_id']);
            }

        return $result->num_rows();
    }

    function team_exists($team)
    {                                
        $this->db->where('name',$team['name']);
        $this->db->where('country_id',$team['country_id']);
        $result = $this->db->get('z_teams');

        foreach ($result->result_array() as $row) {
                return $row['team_id'];
            }

        return $result->num_rows();
    }


    /**
    * Delete team
    *
    * Deletes team
    * 	
    * @param int $id	
    *
    * @return boolean TRUE
    */

    function delete_team ($id) 
    {		
        $this->db->delete('z_teams',array('team_id' => $id));

        return TRUE;
    }                        

    
    function get_num_rows($team_id)
    {
        $this->db->where('team_id',$team_id);
        $result = $this->db->get('z_teams');

        return $result->num_rows();        
    }
    
    function get_team_by_country_and_name($filters)
    {
        $row = array();

        $this->db->where('country_id',$filters['country_id']);
        $this->db->where('name',$filters['name']);
        $this->db->order_by('team_id');
        $result = $this->db->get('z_teams');

        foreach ($result->result_array() as $line) {
                $row[] = $line;
        }

        return $row;
    }

}

