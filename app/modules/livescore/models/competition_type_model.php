<?php

/**
 * Competition Types Model
 *
 * Manages competition types
 *
 * @author Weblight.ro
 * @copyright Weblight.ro
 * @package BJ Tool
 */
class Competition_type_model extends CI_Model
{

    private $CI;

    function __construct() 
    {
        parent::__construct();
        $this->CI = & get_instance();
    }
    
    function get_type($params = array())
    {
        
        $row = array();

        if (isset($params['dropdown'])) {
            $row[0] = 'Select Type';
        }

        $result = $this->db->get('z_competitions_type');

        foreach ($result->result_array() as $linie) {

            $row[$linie['id']] = ucfirst($linie['name']);
        }

        return $row;
    }
    
    /**
     * Create New Competition Tyoe
     *
     * Creates a new competition type
     *
     * @param array $insert_fields	
     *
     * @return int $insert_id
     */
    function new_type($insert_fields)
    {
        $this->db->insert('z_competitions_type', $insert_fields);
        $type_id = $this->db->insert_id();
        
        return $type_id;
    }
    
}