<?php

/**
 * Custom Competitions Model
 *
 * Manages custom competitions
 *
 * @author Weblight.ro
 * @copyright Weblight.ro
 * @package BJ Tool
 */
class Competition_custom_model extends CI_Model
{

    private $CI;

    function __construct() 
    {
        parent::__construct();
        $this->CI = & get_instance();
    }
    
    /**
     * Get Custom Competitions
     *
     *
     * @return array
     */
    function get_competitions($filters = array()) 
    {
        $row = array();

        $order_dir = (isset($filters['sort_dir'])) ? $filters['sort_dir'] : 'ASC';

        if (isset($filters['country'])) {
            $this->db->where('country', $filters['country']);
        }
            
        if (isset($filters['country_name'])) {
            $this->db->like('country_name', $filters['country_name']);
        }
            
        if (isset($filters['limit'])) {
            $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
            $this->db->limit($filters['limit'], $offset);
        }
       
        $this->db->order_by('name', $order_dir);
        $result = $this->db->get('z_competitions_custom');

        foreach ($result->result_array() as $linie) {       
            $row[] = $linie;
        }

        return $row;
    }
    
    /**
     * Create New Custom Competition
     *
     * Creates a new custom competition
     *
     * @param array $insert_fields	
     *
     * @return int $insert_id
     */
    function new_competition($insert_fields) 
    {
        $this->db->insert('z_competitions_custom', $insert_fields);
        $insert_id = $this->db->insert_id();

        return $insert_id;
    }
    
}