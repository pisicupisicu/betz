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
class Goal_model extends CI_Model {

    private $CI;
    private $overs = array('0.5' => 1, '1.5' => 2, '2.5' => 3, '3.5' => 4, '4.5' => 5, '5.5' => 6);

    function __construct() {
        parent::__construct();
        $this->CI = & get_instance();
    }

    /**
     * Get Matches
     *
     *
     * @return array
     */
    function get_goals($filters = array()) {
        $this->load->model('goal_model');
        $row = array();

        $order_dir = (isset($filters['sort_dir'])) ? $filters['sort_dir'] : 'ASC';
        if (isset($filters['sort']))
            $this->db->order_by($filters['sort'], $order_dir);

        if (isset($filters['country_name']))
            $this->db->like('country_name', $filters['country_name']);
        if (isset($filters['competition_name']))
            $this->db->like('z_competitions.name', $filters['competition_name']);
        if (isset($filters['team1']))
            $this->db->like('z_teams.name', $filters['team1']);
        if (isset($filters['team2']))
            $this->db->like('z_teams.name', $filters['team2']);

        if (isset($filters['limit'])) {
            $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
            $this->db->limit($filters['limit'], $offset);
        }

        $this->db->join('z_competitions', 'z_goals.competition_id = z_competitions.competition_id', 'inner');
        $this->db->join('z_countries', 'z_competitions.country_id = z_countries.ID', 'left');
        $this->db->select('*,z_goals.link_complete AS link_match');

        $result = $this->db->get('z_goals');

        foreach ($result->result_array() as $linie) {
            $temp = $this->team_model->get_team($linie['team1']);
            $linie['team1'] = $temp['name'];
            $temp = $this->team_model->get_team($linie['team2']);
            $linie['team2'] = $temp['name'];
            $linie['competition_name'] = $linie['name'];
            $row[] = $linie;
            // print '<pre>';
            // print_r($linie);
            // die;
        }

        return $row;
    }

    function get_num_rows($filters) {
        if (isset($filters['country_name']))
            $this->db->like('country_name', $filters['country_name']);
        if (isset($filters['competition_name']))
            $this->db->like('z_competitions.name', $filters['competition_name']);
        if (isset($filters['team1']))
            $this->db->like('z_teams.name', $filters['team1']);
        if (isset($filters['team2']))
            $this->db->like('z_teams.name', $filters['team2']);
        if (isset($filters['parsed']))
            $this->db->where('parsed', $filters['parsed']);

        $this->db->join('z_competitions', 'z_goals.competition_id = z_competitions.competition_id', 'inner');
        $this->db->join('z_countries', 'z_competitions.country_id = z_countries.ID', 'left');

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
    function get_goals_by_match($match_id) {
        $row = array();

        $this->db->where('z_goals.match_id', $match_id);
        $this->db->order_by('min', 'asc');
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
    function new_goal($insert_fields) {
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
    function update_goal($update_fields, $id) {

        $this->db->update('z_goals', $update_fields, array('id' => $id));
        return TRUE;
    }

    function goal_exists($match) {
        $this->db->where('match_id', $match['match_id']);
        $this->db->where('score', $match['score']);
        $this->db->where('min', $match['min']);
        $this->db->where('player', $match['player']);
        $this->db->where('team', $match['team']);

        $result = $this->db->get('z_goals');

        foreach ($result->result_array() as $row) {
            return $row['id'];
        }

        return $result->num_rows();
    }

    /**
     * Count Goals 
     *
     * @param $min - minutes	
     *
     * @return int $insert_id
     */
    function count_goals($min) {
        $this->db->where('min', $min);
        $this->db->from('z_goals');
        $total_goals = $this->db->count_all_results();
        ;

        return $total_goals;
    }

    /**
     * Count Score by Minutes 
     *
     * @param $min - minutes	
     *
     * @return int $insert_id
     */
    function count_corect_score($filters = array()) {
        $this->db->where('min', $filters['min']);
        $this->db->where('score', $filters['score']);
        $this->db->from('z_goals');
        $total_scores = $this->db->count_all_results();

        return $total_scores;
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
    function delete_goal($id) {

        $this->db->delete('z_goals', array('id' => $id));
        return TRUE;
    }

    /**
     * over_stats
     *
     *
     * @return boolean TRUE
     */
    function get_unique_score() {
        $this->db->distinct('score');
        $this->db->select('score AS unique_score');
        $result = $this->db->get('z_matches');

        return $result->result_array();
    }

    function over_stats($filters = array()) {

        $this->load->model('match_model');
        $this->load->model('country_model');

        $this->db->join('z_competitions', 'z_matches.competition_id = z_competitions.competition_id', 'inner');
        $this->db->join('z_countries', 'z_competitions.country_id = z_countries.ID', 'inner');
        $this->db->group_by('z_matches.score,z_countries.country_name');


        $this->db->select('z_countries.country_name,z_countries.ID AS country_id,z_matches.score,COUNT( DISTINCT z_matches.id ) AS cate');


        if (isset($filters['country_name']) && $filters['country_name']) {

            $this->db->like('z_countries.country_name', $filters['country_name']);
            $filters['setter'] = $_GET['setter'];
        }

        $result = $this->db->get('z_matches');


        $row = array();
        $country = array();
        $over = $under = 0;

        foreach ($result->result_array() as $linie) {
            $row[] = $linie;
            $explode_score = explode('-', $linie['score']);
            $total = $explode_score[0] + $explode_score[1];

            if (!isset($country[$linie['country_name']]))
                $country[$linie['country_name']] = array();
            if (!array_key_exists('under', $country[$linie['country_name']]))
                $country[$linie['country_name']]['under'] = 0;
            if (!array_key_exists('over', $country[$linie['country_name']]))
                $country[$linie['country_name']]['over'] = 0;

            if ($total < round($filters['setter'])) {
                $country[$linie['country_name']]['under'] += $linie['cate'];
            } else {
                $country[$linie['country_name']]['over'] += $linie['cate'];
            }
            $country[$linie['country_name']]['total'] = $country[$linie['country_name']]['under'] + $country[$linie['country_name']]['over'];

            $country[$linie['country_name']]['percent_over'] = ($country[$linie['country_name']]['over'] * 100) / $country[$linie['country_name']]['total'];
            $country[$linie['country_name']]['percent_over'] = sprintf("%.2f", $country[$linie['country_name']]['percent_over']);

            $country[$linie['country_name']]['country_name'] = $linie['country_name'];
        }

        usort($country, array('Goal_model', 'cmp_over'));

        return $country;
    }

    private static function cmp_over($a, $b) {

        if ($a['percent_over'] == $b['percent_over']) {
            return 0;
        }
        return ($a['percent_over'] > $b['percent_over']) ? -1 : 1;
    }

    function over_competitions($filters = array()) {

        $this->load->model('match_model');
        $this->load->model('country_model');

        $this->db->join('z_competitions', 'z_matches.competition_id = z_competitions.competition_id', 'inner');
        $this->db->join('z_countries', 'z_competitions.country_id = z_countries.ID', 'inner');
        $this->db->group_by('z_matches.score,z_competitions.name');


        $this->db->select('z_competitions.name AS country_name,z_countries.country_name AS competition_country,z_matches.score,COUNT( DISTINCT z_matches.id ) AS cate');

        if (isset($filters['country_name']) && $filters['country_name']) {

            $this->db->like('z_countries.country_name', $filters['country_name']);
            $filters['setter'] = $_GET['setter'];
        }

        $result = $this->db->get('z_matches');

//        echo "<pre>";
//        print_r ($result->result_array());
//        echo "</pre>";
//        die;


        $row = array();
        $country = array();
        $over = $under = 0;

        foreach ($result->result_array() as $linie) {
            $row[] = $linie;
            $explode_score = explode('-', $linie['score']);
            $total = $explode_score[0] + $explode_score[1];

            if (!isset($country[$linie['country_name']]))
                $country[$linie['country_name']] = array();
            if (!array_key_exists('under', $country[$linie['country_name']]))
                $country[$linie['country_name']]['under'] = 0;
            if (!array_key_exists('over', $country[$linie['country_name']]))
                $country[$linie['country_name']]['over'] = 0;

            if ($total < round($filters['setter'])) {
                $country[$linie['country_name']]['under'] += $linie['cate'];
            } else {
                $country[$linie['country_name']]['over'] += $linie['cate'];
            }
            $country[$linie['country_name']]['total'] = $country[$linie['country_name']]['under'] + $country[$linie['country_name']]['over'];

            $country[$linie['country_name']]['percent_over'] = ($country[$linie['country_name']]['over'] * 100) / $country[$linie['country_name']]['total'];
            $country[$linie['country_name']]['percent_over'] = sprintf("%.2f", $country[$linie['country_name']]['percent_over']);

            $country[$linie['country_name']]['country_name'] = $linie['country_name'];

            $country[$linie['country_name']]['competition_country'] = $linie['competition_country'];
        }

        usort($country, array('Goal_model', 'cmp_comp_over'));

        return $country;
    }

    private static function cmp_comp_over($a, $b) {

        if ($a['percent_over'] == $b['percent_over']) {
            return 0;
        }
        return ($a['percent_over'] > $b['percent_over']) ? -1 : 1;
    }

    function under_stats($filters = array()) {

        $this->load->model('match_model');
        $this->load->model('country_model');

        $this->db->join('z_competitions', 'z_matches.competition_id = z_competitions.competition_id', 'inner');
        $this->db->join('z_countries', 'z_competitions.country_id = z_countries.ID', 'inner');
        $this->db->group_by('z_matches.score,z_countries.country_name');


        $this->db->select('z_countries.country_name,z_countries.ID AS country_id,z_matches.score,COUNT( DISTINCT z_matches.id ) AS cate');


        if (isset($filters['country_name']) && $filters['country_name']) {

            $this->db->like('z_countries.country_name', $filters['country_name']);
            $filters['setter'] = $_GET['setter'];
        }

        $result = $this->db->get('z_matches');


        $row = array();
        $country = array();
        $over = $under = 0;

        foreach ($result->result_array() as $linie) {
            $row[] = $linie;
            $explode_score = explode('-', $linie['score']);
            $total = $explode_score[0] + $explode_score[1];

            if (!isset($country[$linie['country_name']]))
                $country[$linie['country_name']] = array();
            if (!array_key_exists('under', $country[$linie['country_name']]))
                $country[$linie['country_name']]['under'] = 0;
            if (!array_key_exists('over', $country[$linie['country_name']]))
                $country[$linie['country_name']]['over'] = 0;

            if ($total < round($filters['setter'])) {
                $country[$linie['country_name']]['under'] += $linie['cate'];
            } else {
                $country[$linie['country_name']]['over'] += $linie['cate'];
            }
            $country[$linie['country_name']]['total'] = $country[$linie['country_name']]['under'] + $country[$linie['country_name']]['over'];

            $country[$linie['country_name']]['percent_under'] = ($country[$linie['country_name']]['under'] * 100) / $country[$linie['country_name']]['total'];
            $country[$linie['country_name']]['percent_under'] = sprintf("%.2f", $country[$linie['country_name']]['percent_under']);

            $country[$linie['country_name']]['country_name'] = $linie['country_name'];
        }

        usort($country, array('Goal_model', 'cmp_under'));

        return $country;
    }

    private static function cmp_under($a, $b) {

        if ($a['percent_under'] == $b['percent_under']) {
            return 0;
        }
        return ($a['percent_under'] > $b['percent_under']) ? -1 : 1;
    }

    function under_competitions($filters = array()) {

        $this->load->model('match_model');
        $this->load->model('country_model');

        $this->db->join('z_competitions', 'z_matches.competition_id = z_competitions.competition_id', 'inner');
        $this->db->join('z_countries', 'z_competitions.country_id = z_countries.ID', 'inner');
        $this->db->group_by('z_matches.score,z_competitions.name');

        $this->db->select('z_competitions.name AS country_name,z_countries.country_name AS competition_country,z_matches.score,COUNT( DISTINCT z_matches.id ) AS cate');


        if (isset($filters['country_name']) && $filters['country_name']) {

            $this->db->like('z_countries.country_name', $filters['country_name']);
            $filters['setter'] = $_GET['setter'];
        }

        $result = $this->db->get('z_matches');


        $row = array();
        $country = array();
        $over = $under = 0;

        foreach ($result->result_array() as $linie) {
            $row[] = $linie;
            $explode_score = explode('-', $linie['score']);
            $total = $explode_score[0] + $explode_score[1];

            if (!isset($country[$linie['country_name']]))
                $country[$linie['country_name']] = array();
            if (!array_key_exists('under', $country[$linie['country_name']]))
                $country[$linie['country_name']]['under'] = 0;
            if (!array_key_exists('over', $country[$linie['country_name']]))
                $country[$linie['country_name']]['over'] = 0;

            if ($total < round($filters['setter'])) {
                $country[$linie['country_name']]['under'] += $linie['cate'];
            } else {
                $country[$linie['country_name']]['over'] += $linie['cate'];
            }
            $country[$linie['country_name']]['total'] = $country[$linie['country_name']]['under'] + $country[$linie['country_name']]['over'];

            $country[$linie['country_name']]['percent_under'] = ($country[$linie['country_name']]['under'] * 100) / $country[$linie['country_name']]['total'];
            $country[$linie['country_name']]['percent_under'] = sprintf("%.2f", $country[$linie['country_name']]['percent_under']);

            $country[$linie['country_name']]['country_name'] = $linie['country_name'];

            $country[$linie['country_name']]['competition_country'] = $linie['competition_country'];
        }

        usort($country, array('Goal_model', 'cmp_comp_under'));

        return $country;
    }

    private static function cmp_comp_under($a, $b) {

        if ($a['percent_under'] == $b['percent_under']) {
            return 0;
        }
        return ($a['percent_under'] > $b['percent_under']) ? -1 : 1;
    }

    public function isOver($score, $over = '2.5') {
        $explode_score = explode('-', $score);
        $goals_home = $explode_score[0];
        $goals_away = $explode_score[1];

        $total_goals = $goals_home + $goals_away;

        return $total_goals > $this->overs[$over] ? true : false;
    }

    public function isUnder($score, $under = '2.5') {
        $explode_score = explode('-', $score);
        $goals_home = $explode_score[0];
        $goals_away = $explode_score[1];

        return !$this->isOver($score, $under);
    }

    public function isOne($score) {
        $explode_score = explode('-', $score);
        $goals_home = $explode_score[0];
        $goals_away = $explode_score[1];

        return $goals_home > $goals_away ? true : false;
    }

    public function isTwo($score) {
        $explode_score = explode('-', $score);
        $goals_home = $explode_score[0];
        $goals_away = $explode_score[1];

        return $goals_away > $goals_home ? true : false;
    }

    public function isX($score) {
        $explode_score = explode('-', $score);
        $goals_home = $explode_score[0];
        $goals_away = $explode_score[1];

        return $goals_home === $goals_away ? true : false;
    }

}
