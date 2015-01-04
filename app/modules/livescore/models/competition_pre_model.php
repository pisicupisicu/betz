<?php

/**
 * Competition Pre Model
 *
 * Manages competitions pre
 *
 * @author Weblight.ro
 * @copyright Weblight.ro
 * @package BJ Tool
 */
class Competition_pre_model extends CI_Model
{

    private $CI;

    public function __construct()
    {
        parent::__construct();

        $this->CI = & get_instance();
    }

    /**
     * Get Competitions
     *
     *
     * @return array
     */
    function get_competitions($filters = array())
    {
        $this->load->model('match_pre_model');
        $this->load->model('country_model');
        $this->load->model('competition_model');

        $row = array();

        $order_dir = (isset($filters['sort_dir'])) ? $filters['sort_dir'] : 'ASC';
            
        if (isset($filters['limit'])) {
            $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
            $this->db->limit($filters['limit'], $offset);
        }

        $result = $this->db->get('z_competitions_pre');

        foreach ($result->result_array() as $linie) {
            $linie['matches'] = $this->match_pre_model->get_no_of_matches_by_competition_id($linie['index']);

            if (!$linie['competition_id']) {
                $country = $this->country_model->get_country($linie['country_id']);
                $linie['country_name'] = $country['country_name'];
                $linie['ok_competition'] = 0;
            } else {
                $competition = $this->competition_model->get_competition($linie['competition_id']);
                $linie['name'] = $competition['name'];
                $linie['link'] = $competition['link'];
                $linie['link_complete'] = $competition['link_complete'];
                $linie['country_name'] = $competition['country_name'];
                $linie['ok_competition'] = 1;
            }
            
            if (isset($filters['country_name'])
                && strcasecmp($filters['country_name'], $linie['country_name'])
            ) {
                continue;
            }
            
            $row[] = $linie;
//            print '<pre>';
//            print_r($linie);
//            die;
        }

        return $row;
    }

    /**
     * Get Competition
     *
     * @param int $id	
     *
     * @return array
     */
    function get_competition($id)
    {
        $row = array();

        $this->db->join('z_countries', 'z_competitions_pre.country_id = z_countries.ID', 'left');
        $this->db->where('index', $id);

        $result = $this->db->get('z_competitions_pre');

        foreach ($result->result_array() as $row) {

            return $row;
        }
        
        return $row;
    }

    /**
     * Create New Competition
     *
     * Creates a new competition
     *
     * @param array $insert_fields	
     *
     * @return int $insert_id
     */
    function new_competition($insert_fields)
    {
        $this->db->insert('z_competitions_pre', $insert_fields);
        $insert_id = $this->db->insert_id();

        return $insert_id;
    }

    /**
     * Update Competition
     *
     * Updates competition
     * 
     * @param array $update_fields
     * @param int $id	
     *
     * @return boolean TRUE
     */
    function update_competition($update_fields, $id)
    {

        $this->db->update('z_competitions_pre', $update_fields, array('index' => $id));

        return $id;
    }

    public function update_competition_by_link($update_fields, $link) 
    {
        $this->db->update('z_competitions_pre', $update_fields, array('link' => $link));

        return true;
    }

    function competition_exists($competition)
    {
        $this->load->model('competition_model');
        
        return $this->competition_model->competition_exists($competition);
    }
    
    function competition_exists_id($competition)
    {
        $this->db->where('link', $competition['link']);
        $result = $this->db->get('z_competitions_pre');
        // if competition is new competition
        if ($result->num_rows()) {
            foreach ($result->result_array() as $line) {
                return $line['index'];
            }
        }
        // if competition is old competition
        $competition_id = $this->competition_exists($competition);
        // if not found even as old $competition
        if (!$competition_id) {
            return 0;
        }
        
        $this->db->where('competition_id', $competition_id);
        $result = $this->db->get('z_competitions_pre');
        foreach ($result->result_array() as $line) {
            return $line['index'];
        }
        
        return $result->num_rows();
    }

    /**
     * Delete competition
     *
     * Deletes competition
     * 	
     * @param int $id	
     * @return boolean TRUE
     */
    function delete_competition($id)
    {
        $this->db->delete('z_competitions_pre', array('index' => $id));
        return true;
    }

    function get_num_rows($index, $filters) 
    {
        $this->db->where('index', $index);
        $result = $this->db->get('z_competitions_pre');

        return $result->num_rows();
    }

    function get_num_rowz($filters)
    {
        if (isset($filters['country_name'])) {
            $this->db->like('country_name', $filters['country_name']);
        }
        $this->db->join('z_countries', 'z_competitions_pre.country_id = z_countries.ID', 'left');
        $result = $this->db->get('z_competitions_pre');

        return $result->num_rows();
    }

    function fix_competitions()
    {
        $row = array();
        $result = $this->db->get('z_competitions_pre');

        $this->load->model('country_model');

        foreach ($result->result_array() as $linie) {
            $link = $linie['link'];
            $aux = explode('/', $link);
            $country_name = ucfirst(trim($aux[0]));
            $country_id = $this->country_model->get_country_by_name($country_name);
            $name = ucfirst(trim($aux[1]));
            $data_competition = array(
                'country_id' => $country_id,
                'name' => $name,
                'link_complete' => 'http://www.livescore.com/soccer/' . $link . '/',
            );
            if (!$linie['country_id'] || !$linie['name'] || !$linie['link_complete']) {
                $this->update_competition($data_competition, $linie['index']);
            }
        }

        return $row;
    }

    function fix_competitions_name() 
    {
        $row = array();
        $result = $this->db->get('z_competitions_pre');

        $this->load->model('competition_pre_model');

        foreach ($result->result_array() as $linie) {
            $linie['name'] = str_replace('</span>', '', $linie['name']);

            $data_competition = array(
                'name' => $linie['name'],
            );

            $this->update_competition($data_competition, $linie['index']);
        }

        return $row;
    }

    function get_competitions_by_country_with_filters($filters = array())
    {
        return array_slice($filters['data'], isset($filters['offset']) ? (int) $filters['offset'] : 0, isset($filters['limit']) ? (int) $filters['limit'] : 20);
    }
}
