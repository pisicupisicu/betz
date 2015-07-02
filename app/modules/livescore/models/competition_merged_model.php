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
class Competition_merged_model extends CI_Model
{

    private $CI;

    function __construct() 
    {
        parent::__construct();
        $this->CI = & get_instance();
    }
    
    /**
     * Create New Merged Competitions
     *
     * Creates a new merged competition
     *
     * @param array $insert_fields
     *
     * @return int $insert_id
     */
    function new_competition($insert_fields) 
    {
        $this->db->insert('z_competitions_merged', $insert_fields);
        $insert_id = $this->db->insert_id();

        return $insert_id;
    }
}