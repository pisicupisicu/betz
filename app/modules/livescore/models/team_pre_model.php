<?php

/**
 * Team Pre Model
 *
 * Manages teams
 *
 * @author Weblight.ro
 * @copyright Weblight.ro
 * @package BJ Tool
 */
class Team_pre_model extends CI_Model
{

    private $CI;

    public function __construct()
    {
        parent::__construct();
        $this->CI = & get_instance();
    }

    /**
     * Get Teams
     *
     *
     * @return array
     */
    function get_teams($filters = array())
    {
        $this->load->model('country_model');
        $this->load->model('team_model');
        $row = array();

        $order_dir = (isset($filters['sort_dir'])) ? $filters['sort_dir'] : 'ASC';
       
        if (isset($filters['limit'])) {
            $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
            $this->db->limit($filters['limit'], $offset);
        }
        
        if (isset($filters['nr_new_teams'])) {
            $this->db->order_by('team_id', $order_dir);
            unset($filters['country_name_sort']);
        }

        if (isset($filters['equal'])) {
            if (isset($filters['name'])) {
                $this->db->where('name', $filters['name']);
            }
                
        } else {
            if (isset($filters['name'])) {
                $this->db->like('name', $filters['name']);
            }
        }

        $this->db->order_by('name', $order_dir);

        // if(isset($filters['sort']))  $this->db->order_by($filters['sort'], $order_dir);

        $result = $this->db->get('z_teams_pre');

        foreach ($result->result_array() as $linie) {
            if (!$linie['team_id']) {
                $country = $this->country_model->get_country($linie['country_id']);
                $linie['country_name'] = $country['country_name'];
                $linie['ok'] = 0;
            } else {
                $team = $this->team_model->get_team($linie['team_id']);
                $linie['name'] = $team['name'];
                $linie['matches'] = $team['matches'];
                $country = $this->country_model->get_country($team['country_id']);
                $linie['country_name'] = $country['country_name'];
                $linie['ok'] = 1;
            }
            
            if (isset($filters['country_name'])
                && strcasecmp($filters['country_name'], $linie['country_name'])
            ) {
                continue;
            }
            
            if (!$linie['team_id']) {
                $this->db->like('name', $linie['name']);
                $result2 = $this->db->get('z_teams');
                $linie['similar_teams'] = $result2->num_rows();
            } else {
                $linie['similar_teams'] = 0;
            }
            
            $row[] = $linie;
        }
        
        //if (isset($filters['country_name_sort'])) {
            usort($row, array('Team_pre_model', 'cmp'));
        //}
        
//        print '<pre>';
//        print_r($linie);
//        die;
        
        return $row;
    }
    
    function get_num_rowz($filters = array())
    {
        return count($this->get_teams($filters));
    }
    
    private static function cmp($a, $b) 
    {
        return strcasecmp($a['country_name'], $b['country_name']);
    }
    
    function get_teams_by_country_with_filters($filters = array())
    {
        return array_slice($filters['data'], isset($filters['offset']) ? (int) $filters['offset'] : 0, isset($filters['limit']) ? (int) $filters['limit'] : 20);
    }

    /**
     * Get Team
     *
     * @param int $id	
     *
     * @return array
     */
    function get_team($id)
    {
        $this->load->model('team_model');
        $continents = array(254, 245, 244, 243, 242); // AFRICA, ASIA, AMERICA, WORLD, EUROPE
        $row = array();
        $this->db->where('index', $id);
        $result = $this->db->get('z_teams_pre');

        foreach ($result->result_array() as $row) {
            if ($row['name']) {
                $row['ok'] = 0;
                return $row;
            }
            
            $row = $this->team_model->get_team($row['team_id']);
            if (in_array($row['country_id'], $continents)) {
                $row['ok'] = 0;
            } else {
                $row['ok'] = 1;
            }
            
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
    function new_team($insert_fields)
    {
        $this->db->insert('z_teams_pre', $insert_fields);
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
    function update_team($update_fields, $id) 
    {
        $this->db->update('z_teams_pre', $update_fields, array('index' => $id));

        return TRUE;
    }

    function update_team_matches($id) 
    {
        $this->load->model('match_model_pre');
        $matches = $this->match_model_pre->get_matches_by_team_id(array('index' => $id, 'count' => true));
        $update_fields = array(
            'matches' => $matches,
        );
        $this->update_team($update_fields, $id);
    }

    function update_all_teams_matches() 
    {
        $result = $this->db->get('z_teams_pre');

        foreach ($result->result_array() as $row) {
            $this->update_team_matches($row['index']);
        }

        return $result->num_rows();
    }

    function team_exists($team)
    {
        $this->load->model('team_model');
        
        return $this->team_model->team_exists($team);         
    }
    
    function team_exists_id($team)
    {
        $this->db->where('country_id', $team['country_id']);
        $this->db->where('name', $team['name']);
        $result = $this->db->get('z_teams_pre');
        // if team is new team
        if ($result->num_rows()) {
            foreach ($result->result_array() as $line) {
                return $line['index'];
            }
        }
        // if team is old team
        $team_id = $this->team_exists($team);
        // if not found even as old team
        if (!$team_id) {
            return 0;
        }
        
        $this->db->where('team_id', $team_id);
        $result = $this->db->get('z_teams_pre');
        foreach ($result->result_array() as $line) {
            return $line['index'];
        }
        
        return $result->num_rows();
    }

    function team_exists_team_id($team_id)
    {
        $this->db->where('team_id', $team_id);
        $result = $this->db->get('z_teams_pre');
        return $result->num_rows();
    }
    
    function team_exists_name_country($team)
    {
        $this->db->where('country_id', $team['country_id']);
        $this->db->where('name', $team['name']);
        $result = $this->db->get('z_teams_pre');
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
    function delete_team($id) 
    {
        $this->db->delete('z_teams_pre', array('index' => $id));

        return TRUE;
    }

    function get_num_rows($filters)
    {
        if (isset($filters['team_id'])) {
            $this->db->where('team_id', null);
        }
        $result = $this->db->get('z_teams_pre');
        
        return $result->num_rows();
    }

    function get_team_by_country_and_name($filters) 
    {
        $row = array();

        $this->db->where('country_id', $filters['country_id']);
        $this->db->where('name', $filters['name']);
        $this->db->order_by('team_id');
        $result = $this->db->get('z_teams_pre');

        foreach ($result->result_array() as $line) {
            $row[] = $line;
        }

        return $row;
    }    

    public function get_dummy($filters) 
    {
        return $this->get_team($filters['id']);
    }

    function get_teams_by_name($filters)
    {
        $row = array();

        //$this->db->where('country_id', $filters['country_id']);
        $this->db->join('z_countries', 'z_teams_pre.country_id = z_countries.ID', 'inner');
        $this->db->where('name', $filters['name']);
        $this->db->order_by('team_id');
        $result = $this->db->get('z_teams_pre');

        foreach ($result->result_array() as $linie) {
            $row[] = $linie;
        }

        return $row;
    }
    
    function get_similar_teams($params)
    {
        $team_pre = $this->get_team($params['team_pre_id']);
        $this->db->like('name', $team_pre['name']);
        $this->db->join('z_countries', 'z_teams.country_id = z_countries.ID', 'inner');
        $result = $this->db->get('z_teams');
        
        if (isset($params['dropdown'])) {
            $similar_teams[0] = 'Similar teams for ' . $team_pre['name'];
        }
                        
        foreach ($result->result_array() as $linie) {
            $similar_teams[$linie['team_id']] = $linie['country_name'] . ' - ' . $linie['name'];
        }
        
        return $similar_teams;
    }
    
    function move_teams_pre()
    {
        $this->load->model('team_model');
        
        $continents = array(254, 245, 244, 243, 242); // AFRICA, ASIA, AMERICA, WORLD, EUROPE
        $this->db->where('team_id', null);
        $this->db->order_by('name');
        $result = $this->db->get('z_teams_pre');
        $i = 0;
        
        foreach ($result->result_array() as $linie) {
            $this->db->like('name', $linie['name']);
            $result2 = $this->db->get('z_teams');
            $nr_similar_teams = $result2->num_rows();
            
            switch ($nr_similar_teams) {
                // new team -> we know the country just add the team in z_teams
                // if team from no country, just continent add manually
                case 0:
                    if (!in_array($linie['country_id'], $continents)) {
                        $insert_fields = array(
                          'country_id' => $linie['country_id'],
                          'name' => $linie['name'],
                          'matches' => $linie['matches']
                        );
                        
                        $team_id = $this->team_model->new_team($insert_fields);
                        
                        $update_fields = array(
                          'team_id' =>  $team_id,
                          'country_id' => null,
                          'name' => null,
                          'matches' => null
                        );
                        
                        $this->update_team($update_fields, $linie['index']);
                        $i++;                        
                    }
                    break;
                // just one old team -> it is the one, then just update it, nothing else here
                case 1:
                    foreach ($result2->result_array() as $linie2) {
                        $team_id = $linie2['team_id'];
                        $update_fields = array(
                          'team_id' =>  $team_id,
                          'country_id' => null,
                          'name' => null,
                          'matches' => null
                        );
                        
                        $this->update_team($update_fields, $linie['index']);
                        $i++;
                    }
                    break;
                // multiple results, edit manually
                default:
                    break;
            }            
            //break; // for testing purposes, one team at a time
        }
        
        return $i;
    }
}
