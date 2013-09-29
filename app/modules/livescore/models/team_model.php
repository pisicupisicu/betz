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
        if(isset($filters['sort']))  $this->db->order_by($filters['sort'], $order_dir);
                                      
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

    function get_duplicates()
    {
        $row = array(); 

        $this->db->select('*,COUNT(*) c');
        $this->db->group_by('country_id,name');
        $this->db->having('c > 1');
        $this->db->order_by('name','asc');

        $result = $this->db->get('z_teams');

        foreach ($result->result_array() as $line) {
                $row[] = $line;
        }

        return $row;
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

