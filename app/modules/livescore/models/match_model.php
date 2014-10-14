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
class Match_model extends CI_Model {

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
    function get_matches($filters = array()) {
        $this->load->model('team_model');
        $row = array();

        $order_dir = (isset($filters['sort_dir'])) ? $filters['sort_dir'] : 'ASC';
        if (isset($filters['sort']))
            $this->db->order_by($filters['sort'], $order_dir);

        if (isset($filters['country_name']) && $filters['country_name'])
            $this->db->like('country_name', $filters['country_name']);
        if (isset($filters['competition_name']) && $filters['competition_name'])
            $this->db->like('z_competitions.name', $filters['competition_name']);
        if (isset($filters['team1']) && $filters['team1'])
            $this->db->like('zt1.name', $filters['team1']);
        if (isset($filters['team2']) && $filters['team2'])
            $this->db->like('zt2.name', $filters['team2']);
        if (isset($filters['score']) && $filters['score'])
            $this->db->like('score', $filters['score']);
        if (isset($filters['parsed']))
            $this->db->where('parsed', $filters['parsed']);
        if (isset($filters['match_date_start']) && !empty($filters['match_date_start']))
            $this->db->where('match_date >=', $filters['match_date_start']);
        if (isset($filters['match_date_end']) && !empty($filters['match_date_end']))
            $this->db->where('match_date <=', $filters['match_date_end']);

        if (isset($filters['limit'])) {
            $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
            $this->db->limit($filters['limit'], $offset);
        }

        if (isset($filters['team1']) && $filters['team1']) {
            $this->db->join('z_teams AS zt1', 'z_matches.team1 = zt1.team_id', 'inner');
        }

        if (isset($filters['team2']) && $filters['team2']) {
            $this->db->join('z_teams AS zt2', 'z_matches.team2 = zt2.team_id', 'inner');
        }

        $this->db->join('z_competitions', 'z_matches.competition_id = z_competitions.competition_id', 'inner');
        $this->db->join('z_countries', 'z_competitions.country_id = z_countries.ID', 'left');
        $this->db->select('*,z_matches.link_complete AS link_match');

        $result = $this->db->get('z_matches');

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

    /**
     * Get Matches for the range selector
     *
     *
     * @return array
     */
    function get_matches_by_score($filters = array()) {

        if (isset($filters['score']) && $filters['score'])
            $this->db->where('z_matches.score', $filters['score']);

        $result = $this->db->get('z_matches');

        return $result->result_array();
    }

    function get_match_details($id_match) {
        $this->load->model('goal_model');

        $this->db->where('z_matches.id', $id_match);

        $this->db->join('z_goals', 'z_matches.id = z_goals.match_id', 'inner');

        $result = $this->db->get('z_matches');

//        foreach ($result->result_array() as $linie) {
//            
//           
//         $row[] = $linie;
//         // print '<pre>';
//         // print_r($goals);            
//         //print_r($cardz);
//         //die;
//
//        }

        return $result->result_array();
    }

    function get_matches_and_goals($filters = array()) {
        $this->load->model('team_model');
        $this->load->model('goal_model');
        $row = array();

        $order_dir = (isset($filters['sort_dir'])) ? $filters['sort_dir'] : 'ASC';
        if (isset($filters['sort']))
            $this->db->order_by($filters['sort'], $order_dir);
        else
            $this->db->order_by('match_date', $order_dir);

        if (isset($filters['country_name']) && $filters['country_name'])
            $this->db->like('country_name', $filters['country_name']);
        if (isset($filters['competition_name']) && $filters['competition_name'])
            $this->db->like('z_competitions.name', $filters['competition_name']);
        if (isset($filters['team1']) && $filters['team1'])
            $this->db->like('zt1.name', $filters['team1']);
        if (isset($filters['team2']) && $filters['team2'])
            $this->db->like('zt2.name', $filters['team2']);
        if (isset($filters['score']) && $filters['score'])
            $this->db->like('score', $filters['score']);
        if (isset($filters['parsed']))
            $this->db->where('parsed', $filters['parsed']);
        if (isset($filters['match_date_start']) && !empty($filters['match_date_start']))
            $this->db->where('match_date >=', $filters['match_date_start']);
        if (isset($filters['match_date_end']) && !empty($filters['match_date_end']))
            $this->db->where('match_date <=', $filters['match_date_end']);

        if (isset($filters['limit'])) {
            $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
            $this->db->limit($filters['limit'], $offset);
        }

        if (isset($filters['team1']) && $filters['team1']) {
            $this->db->join('z_teams AS zt1', 'z_matches.team1 = zt1.team_id', 'inner');
        }

        if (isset($filters['team2']) && $filters['team2']) {
            $this->db->join('z_teams AS zt2', 'z_matches.team2 = zt2.team_id', 'inner');
        }

        $this->db->join('z_competitions', 'z_matches.competition_id = z_competitions.competition_id', 'inner');
        $this->db->join('z_countries', 'z_competitions.country_id = z_countries.ID', 'left');
        $this->db->select('*,z_matches.link_complete AS link_match');

        $result = $this->db->get('z_matches');

        foreach ($result->result_array() as $linie) {
            $temp = $this->team_model->get_team($linie['team1']);
            $linie['team1'] = $temp['name'];
            $temp = $this->team_model->get_team($linie['team2']);
            $linie['team2'] = $temp['name'];
            $linie['competition_name'] = $linie['name'];

            $goalz = array();
            $cardz = array();

            $goals = $this->goal_model->get_goals_by_match($linie['id']);
            foreach ($goals as $goal) {
                $goalz[$goal['score']] = $goal['min'];
            }

            $type = array('yellow_red', 'red');
            $cards = $this->card_model->get_cards_by_match($linie['id'], $type);
            //print_r($cards);
            foreach ($cards as $card) {
                $cardz[$card['card_type']] = $card['min'];
            }

            $linie['goals'] = $goalz;
            $linie['cards'] = $cardz;

            $row[] = $linie;
            // print '<pre>';
            // print_r($goals);            
            //print_r($cardz);
            //die;
        }

        return $row;
    }

    function get_num_rows($filters = array()) {

        if (isset($filters['country_name']) && $filters['country_name'])
            $this->db->like('country_name', $filters['country_name']);
        if (isset($filters['competition_name']) && $filters['competition_name'])
            $this->db->like('z_competitions.name', $filters['competition_name']);
        if (isset($filters['team1']) && $filters['team1'])
            $this->db->like('zt1.name', $filters['team1']);
        if (isset($filters['team2']) && $filters['team2'])
            $this->db->like('zt2.name', $filters['team2']);
        if (isset($filters['score']) && $filters['score'])
            $this->db->like('score', $filters['score']);
        if (isset($filters['parsed']))
            $this->db->where('parsed', $filters['parsed']);
        if (isset($filters['match_date_start']))
            $this->db->where('match_date >=', $filters['match_date_start']);
        if (isset($filters['match_date_end']))
            $this->db->where('match_date <=', $filters['match_date_end']);

        if (isset($filters['team1']) && $filters['team1']) {
            $this->db->join('z_teams AS zt1', 'z_matches.team1 = zt1.team_id', 'inner');
        }

        if (isset($filters['team2']) && $filters['team2']) {
            $this->db->join('z_teams AS zt2', 'z_matches.team2 = zt2.team_id', 'inner');
        }

        if (isset($filters['min'])) {
            $this->db->where('min <=', $filters['min']);
            $this->db->join('z_goals', 'z_matches.id = z_goals.match_id', 'inner');
        }

        $this->db->join('z_competitions', 'z_matches.competition_id = z_competitions.competition_id', 'inner');
        $this->db->join('z_countries', 'z_competitions.country_id = z_countries.ID', 'left');

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
    function get_match($id) {
        $row = array();

        $this->db->join('z_competitions', 'z_matches.competition_id = z_competitions.competition_id', 'inner');
        $this->db->join('z_countries', 'z_competitions.country_id = z_countries.ID', 'left');
        $this->db->where('z_matches.id', $id);
        $this->db->select('*,z_matches.link AS link_match,z_matches.link_complete AS link_match_complete');
        $result = $this->db->get('z_matches');

        foreach ($result->result_array() as $row) {
            return $row;
        }

        return $row;
    }

    function get_matches_by_team_id($filters) {
        $this->load->model('team_model');
        $row = array();

        if (!isset($filters['count'])) {
            $this->db->join('z_competitions', 'z_matches.competition_id = z_competitions.competition_id', 'inner');
            $this->db->join('z_countries', 'z_competitions.country_id = z_countries.ID', 'left');
        }

        $this->db->or_where('team1', $filters['team_id']);
        $this->db->or_where('team2', $filters['team_id']);
        if (isset($filters['count'])) {
            $this->db->select('*');
        } else {
            $this->db->select('*,z_competitions.name AS competition_name,z_matches.link_complete AS link_match');
            $this->db->order_by('z_matches.match_date');
        }

        if (isset($filters['limit'])) {
            $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
            $this->db->limit($filters['limit'], $offset);
        }

        $result = $this->db->get('z_matches');

        if (isset($filters['count'])) {
            return $result->num_rows();
        }

        foreach ($result->result_array() as $line) {
            $temp = $this->team_model->get_team($line['team1']);
            $line['team1'] = $temp['name'];
            $temp = $this->team_model->get_team($line['team2']);
            $line['team2'] = $temp['name'];

            $row[] = $line;
        }

        return $row;
    }

    function get_matches_by_team_id_partial($filters) {
        $this->load->model('team_model');
        $row = array();

        if ($filters['which_team'] == 1) {
            $filters['team_id'] = $filters['team_to_keep'];
            return $this->get_matches_by_team_id($filters);
        } else {
            if (isset($filters['count'])) {
                $filters['team_id'] = $filters['team_to_remove'];
                return $this->get_matches_by_team_id($filters);
            } else {
                $filters['team_id'] = $filters['team_to_keep'];
                $first_team_results = $this->get_matches_by_team_id($filters);
                $filters['team_id'] = $filters['team_to_remove'];
                $second_team_results = $this->get_matches_by_team_id($filters);

                $links = array();

                foreach ($first_team_results as $row) {
                    $links[] = $row['link_match'];
                }

                foreach ($second_team_results as $key => $row) {
                    if (!in_array($row['link_match'], $links)) {
                        $second_team_results[$key]['status'] = 'new';
                    } else {
                        $second_team_results[$key]['status'] = 'old';
                    }
                }

//                print '<pre>';
//                print_r($links);
//                print '</pre>';

                return $second_team_results;
            }
        }

        if (!isset($filters['count'])) {
            $this->db->join('z_competitions', 'z_matches.competition_id = z_competitions.competition_id', 'inner');
            $this->db->join('z_countries', 'z_competitions.country_id = z_countries.ID', 'left');
        }

        $this->db->or_where('team1', $filters['team_id']);
        $this->db->or_where('team2', $filters['team_id']);
        if (isset($filters['count'])) {
            $this->db->select('*');
        } else {
            $this->db->select('*,z_competitions.name AS competition_name,z_matches.link_complete AS link_match');
            $this->db->order_by('z_matches.match_date');
        }

        if (isset($filters['limit'])) {
            $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
            $this->db->limit($filters['limit'], $offset);
        }

        $result = $this->db->get('z_matches');

        if (isset($filters['count'])) {
            return $result->num_rows();
        }

        foreach ($result->result_array() as $line) {
            $temp = $this->team_model->get_team($line['team1']);
            $line['team1'] = $temp['name'];
            $temp = $this->team_model->get_team($line['team2']);
            $line['team2'] = $temp['name'];

            $row[] = $line;
        }

        return $row;
    }
    
    function get_matches_by_team_id_simple($filters) 
    {
        $this->load->model('team_model');       
        
        $this->db->or_where('team1', $filters['team_id']);
        $this->db->or_where('team2', $filters['team_id']);
        $this->db->select('*');
        
        $result = $this->db->get('z_matches');

        return $result->result_array();
    }

    function get_next_match() {
        $row = array();

        $this->db->where('parsed', 0);
        $this->db->order_by('id', 'asc');
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
    function new_match($insert_fields) {
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
    function update_match($update_fields, $id) {

        $this->db->update('z_matches', $update_fields, array('id' => $id));
        return TRUE;
    }

    function match_exists($match) {
        $this->db->where('link', $match['link']);
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
    function delete_match($id) {

        $this->db->delete('z_matches', array('id' => $id));
        return TRUE;
    }

    function get_no_of_matches_by_team_id($team_id) {
        $this->db->or_where('team1', $team_id);
        $this->db->or_where('team2', $team_id);

        $result = $this->db->get('z_matches');

        return $result->num_rows();
    }

    function fix_score() {
        $row = array();
        $result = $this->db->get('z_matches');

        foreach ($result->result_array() as $linie) {
            $score = $linie['score'];
            $aux = explode('-', $score);
            $score = str_replace(' ', '', $aux[0]) . '-' . str_replace(' ', '', $aux[1]);

            $data_match = array(
                'score' => $score,
            );

            $this->update_match($data_match, $linie['id']);
        }

        return $row;
    }

    function first_goal($filters = array()) {
        $row = array();

        $this->load->model('team_model');
        $this->load->model('goal_model');
        $this->load->model('card_model');

        $order_dir = (isset($filters['sort_dir'])) ? $filters['sort_dir'] : 'ASC';
        if (isset($filters['sort'])) {
            if ($filters['sort'] == 'score') {
                $filters['sort'] = 'z_matches.score';
            }

            $this->db->order_by($filters['sort'], $order_dir);
        }

        $this->db->order_by('match_date', $order_dir);

        if (isset($filters['country_name']) && $filters['country_name'])
            $this->db->like('country_name', $filters['country_name']);
        if (isset($filters['competition_name']) && $filters['competition_name'])
            $this->db->like('z_competitions.name', $filters['competition_name']);
        if (isset($filters['team1']) && $filters['team1'])
            $this->db->like('zt1.name', $filters['team1']);
        if (isset($filters['team2']) && $filters['team2'])
            $this->db->like('zt2.name', $filters['team2']);
        if (isset($filters['score']) && $filters['score'])
            $this->db->like('z_matches.score', $filters['score']);
        if (isset($filters['parsed']))
            $this->db->where('parsed', $filters['parsed']);
        if (isset($filters['match_date_start']) && !empty($filters['match_date_start']))
            $this->db->where('match_date >=', $filters['match_date_start']);
        if (isset($filters['match_date_end']) && !empty($filters['match_date_end']))
            $this->db->where('match_date <=', $filters['match_date_end']);

        if (isset($filters['limit'])) {
            $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
            $this->db->limit($filters['limit'], $offset);
        }

        if (isset($filters['team1']) && $filters['team1']) {
            $this->db->join('z_teams AS zt1', 'z_matches.team1 = zt1.team_id', 'inner');
        }

        if (isset($filters['team2']) && $filters['team2']) {
            $this->db->join('z_teams AS zt2', 'z_matches.team2 = zt2.team_id', 'inner');
        }

        $this->db->join('z_goals', 'z_matches.id = z_goals.match_id', 'inner');
        if (!isset($filters['min'])) {
            $filters['min'] = 10;
        }
        $this->db->where('min <=', $filters['min']);

        $this->db->join('z_competitions', 'z_matches.competition_id = z_competitions.competition_id', 'inner');
        $this->db->join('z_countries', 'z_competitions.country_id = z_countries.ID', 'left');

        $this->db->select('*,z_matches.id AS super_match_id,z_matches.link_complete AS link_match');
        $this->db->group_by('super_match_id');
        $result = $this->db->get('z_matches');

        foreach ($result->result_array() as $linie) {
            $temp = $this->team_model->get_team($linie['team1']);
            $linie['team1'] = $temp['name'];
            $temp = $this->team_model->get_team($linie['team2']);
            $linie['team2'] = $temp['name'];
            $linie['competition_name'] = $linie['name'];

            $goalz = array();
            $cardz = array();

            $goals = $this->goal_model->get_goals_by_match($linie['super_match_id']);
            foreach ($goals as $goal) {
                $goalz[$goal['score']] = $goal['min'];
            }

            $type = array('yellow_red', 'red');
            $cards = $this->card_model->get_cards_by_match($linie['id'], $type);
            //print_r($cards);
            foreach ($cards as $card) {
                $cardz[$card['card_type']] = $card['min'];
            }

            $linie['goals'] = $goalz;
            $linie['cards'] = $cardz;

            $linie['score'] = array_pop(array_keys($goalz));

            $row[] = $linie;
            // print '<pre>';
            // print_r($goals);            
            //print_r($cardz);
            //die;
        }

        //print '<pre>';
        //print_r($row);

        return $row;
    }

    function first_goal_stats($filters = array()) {
        $row = array();

        $this->load->model('team_model');
        $this->load->model('goal_model');
        $this->load->model('card_model');

        $order_dir = (isset($filters['sort_dir'])) ? $filters['sort_dir'] : 'ASC';
        if (isset($filters['sort']))
            $this->db->order_by($filters['sort'], $order_dir);
        else
            $this->db->order_by('match_date', $order_dir);

        //print_r($filters);

        if (isset($filters['country_name']) && $filters['country_name'])
            $this->db->like('country_name', $filters['country_name']);
        if (isset($filters['competition_name']) && $filters['competition_name'])
            $this->db->like('z_competitions.name', $filters['competition_name']);
        if (isset($filters['team1']) && $filters['team1'])
            $this->db->like('zt1.name', $filters['team1']);
        if (isset($filters['team2']) && $filters['team2'])
            $this->db->like('zt2.name', $filters['team2']);
        if (isset($filters['score']) && $filters['score'])
            $this->db->like('z_matches.score', $filters['score']);
        if (isset($filters['parsed']))
            $this->db->where('parsed', $filters['parsed']);
        if (isset($filters['match_date_start']) && !empty($filters['match_date_start']))
            $this->db->where('match_date >=', $filters['match_date_start']);
        if (isset($filters['match_date_end']) && !empty($filters['match_date_end']))
            $this->db->where('match_date <=', $filters['match_date_end']);

        if (isset($filters['limit'])) {
            $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
            $this->db->limit($filters['limit'], $offset);
        }

        if (isset($filters['team1']) && $filters['team1']) {
            $this->db->join('z_teams AS zt1', 'z_matches.team1 = zt1.team_id', 'inner');
        }

        if (isset($filters['team2']) && $filters['team2']) {
            $this->db->join('z_teams AS zt2', 'z_matches.team2 = zt2.team_id', 'inner');
        }

        $this->db->join('z_goals', 'z_matches.id = z_goals.match_id', 'inner');
        if (!isset($filters['min'])) {
            $filters['min'] = 10;
        }
        $this->db->where('min <=', $filters['min']);

        $this->db->join('z_competitions', 'z_matches.competition_id = z_competitions.competition_id', 'inner');
        $this->db->join('z_countries', 'z_competitions.country_id = z_countries.ID', 'left');

        $this->db->group_by('z_matches.score');

        $this->db->select('z_matches.score,COUNT( DISTINCT z_matches.id ) AS cate');
        $result = $this->db->get('z_matches');

        $total_first_goal = 0;

        foreach ($result->result_array() as $linie) {
            $row[] = $linie;
            $total_first_goal += $linie['cate'];
        }

        foreach ($row as $k => $match) {
            $percent = ($match['cate'] * 100) / $total_first_goal;
            $percent = sprintf("%.2f", $percent);
            $row[$k]['percent'] = $percent;
        }

        usort($row, array('Match_model', 'cmp'));

        /*         * ******** UNDER/OVER 1.5 ******** */

        $under_1_5 = $over_1_5 = 0;
        $unders_1_5 = array('0-1', '1-0');

        foreach ($row as $match) {
            if (in_array($match['score'], $unders_1_5)) {
                $under_1_5 += $match['cate'];
            } else {
                $over_1_5 += $match['cate'];
            }
        }

        $under_percent_1_5 = $over_percent_1_5 = 0;
        $under_percent_1_5 = $total_first_goal ? ($under_1_5 * 100) / $total_first_goal : 0;
        $under_percent_1_5 = sprintf("%.2f", $under_percent_1_5);
        $over_percent_1_5 = $total_first_goal ? ($over_1_5 * 100) / $total_first_goal : 0;
        $over_percent_1_5 = sprintf("%.2f", $over_percent_1_5);

        /*         * ******** UNDER/OVER 2.5 ******** */

        $under_2_5 = $over_2_5 = 0;
        $unders_2_5 = array('0-1', '1-0', '1-1', '0-2', '2-0');

        foreach ($row as $match) {
            if (in_array($match['score'], $unders_2_5)) {
                $under_2_5 += $match['cate'];
            } else {
                $over_2_5 += $match['cate'];
            }
        }

        $under_percent_2_5 = $over_percent_2_5 = 0;
        $under_percent_2_5 = $total_first_goal ? ($under_2_5 * 100) / $total_first_goal : 0;
        $under_percent_2_5 = sprintf("%.2f", $under_percent_2_5);
        $over_percent_2_5 = $total_first_goal ? ($over_2_5 * 100) / $total_first_goal : 0;
        $over_percent_2_5 = sprintf("%.2f", $over_percent_2_5);


        /*         * ******** UNDER/OVER 3.5 ******** */

        $under_3_5 = $over_3_5 = 0;
        $unders_3_5 = array('0-1', '1-0', '1-1', '0-2', '2-0', '2-1', '1-2', '3-0', '0-3');

        foreach ($row as $match) {
            if (in_array($match['score'], $unders_3_5)) {
                $under_3_5 += $match['cate'];
            } else {
                $over_3_5 += $match['cate'];
            }
        }

        $under_percent_3_5 = $over_percent_3_5 = 0;
        $under_percent_3_5 = $total_first_goal ? ($under_3_5 * 100) / $total_first_goal : 0;
        $under_percent_3_5 = sprintf("%.2f", $under_percent_3_5);
        $over_percent_3_5 = $total_first_goal ? ($over_3_5 * 100) / $total_first_goal : 0;
        $over_percent_3_5 = sprintf("%.2f", $over_percent_3_5);


        /*         * ******** UNDER/OVER 4.5 ******** */

        $under_4_5 = $over_4_5 = 0;
        $unders_4_5 = array('0-1', '1-0', '1-1', '0-2', '2-0', '2-1', '1-2', '2-2', '3-0', '0-3', '3-1', '1-3');

        foreach ($row as $match) {
            if (in_array($match['score'], $unders_4_5)) {
                $under_4_5 += $match['cate'];
            } else {
                $over_4_5 += $match['cate'];
            }
        }

        $under_percent_4_5 = $over_percent_4_5 = 0;
        $under_percent_4_5 = $total_first_goal ? ($under_4_5 * 100) / $total_first_goal : 0;
        $under_percent_4_5 = sprintf("%.2f", $under_percent_4_5);
        $over_percent_4_5 = $total_first_goal ? ($over_4_5 * 100) / $total_first_goal : 0;
        $over_percent_4_5 = sprintf("%.2f", $over_percent_4_5);

        //echo "UNDER 2.5 = $under_2_5 OF $total_first_goal = $under_percent_2_5%<br/>";
        //echo "OVER 2.5 = $over_2_5 OF $total_first_goal = $over_percent_2_5%<br/><br/>";

        $key = count($row);

        $row[$key]['1.5']['under']['cate'] = $under_1_5;
        $row[$key]['1.5']['under']['percent'] = $under_percent_1_5;
        $row[$key]['1.5']['over']['cate'] = $over_1_5;
        $row[$key]['1.5']['over']['percent'] = $over_percent_1_5;

        $key = count($row);

        $row[$key]['2.5']['under']['cate'] = $under_2_5;
        $row[$key]['2.5']['under']['percent'] = $under_percent_2_5;
        $row[$key]['2.5']['over']['cate'] = $over_2_5;
        $row[$key]['2.5']['over']['percent'] = $over_percent_2_5;

        $key = count($row);

        $row[$key]['3.5']['under']['cate'] = $under_3_5;
        $row[$key]['3.5']['under']['percent'] = $under_percent_3_5;
        $row[$key]['3.5']['over']['cate'] = $over_3_5;
        $row[$key]['3.5']['over']['percent'] = $over_percent_3_5;

        $key = count($row);

        $row[$key]['4.5']['under']['cate'] = $under_4_5;
        $row[$key]['4.5']['under']['percent'] = $under_percent_4_5;
        $row[$key]['4.5']['over']['cate'] = $over_4_5;
        $row[$key]['4.5']['over']['percent'] = $over_percent_4_5;

        $key = count($row);
        $row[$key]['total_games'] = $total_first_goal;

        // print '<pre>';
        // print_r($row);

        return $row;
    }

    private static function cmp($a, $b) {
        if ($a['percent'] == $b['percent']) {
            return 0;
        }
        return ($a['percent'] > $b['percent']) ? -1 : 1;
    }

    function first_goal_not_until($filters = array()) {
        $row = array();

        $this->load->model('team_model');
        $this->load->model('goal_model');
        $this->load->model('card_model');

        if (!isset($filters['min'])) {
            $filters['min'] = 60;
        }

        $order_dir = (isset($filters['sort_dir'])) ? $filters['sort_dir'] : 'ASC';
        if (isset($filters['sort'])) {
            if ($filters['sort'] == 'score') {
                $filters['sort'] = 'z_matches.score';
            }

            $this->db->order_by($filters['sort'], $order_dir);
        }

        $this->db->order_by('match_date', $order_dir);

        if (isset($filters['country_name']) && $filters['country_name'])
            $this->db->like('country_name', $filters['country_name']);
        if (isset($filters['competition_name']) && $filters['competition_name'])
            $this->db->like('z_competitions.name', $filters['competition_name']);
        if (isset($filters['team1']) && $filters['team1'])
            $this->db->like('zt1.name', $filters['team1']);
        if (isset($filters['team2']) && $filters['team2'])
            $this->db->like('zt2.name', $filters['team2']);
        if (isset($filters['score']) && $filters['score'])
            $this->db->like('z_matches.score', $filters['score']);
        if (isset($filters['parsed']))
            $this->db->where('parsed', $filters['parsed']);
        if (isset($filters['match_date_start']) && !empty($filters['match_date_start']))
            $this->db->where('match_date >=', $filters['match_date_start']);
        if (isset($filters['match_date_end']) && !empty($filters['match_date_end']))
            $this->db->where('match_date <=', $filters['match_date_end']);

        if (isset($filters['limit'])) {
            $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
            $this->db->limit($filters['limit'], $offset);
        }

        if (isset($filters['team1']) && $filters['team1']) {
            $this->db->join('z_teams AS zt1', 'z_matches.team1 = zt1.team_id', 'inner');
        }

        if (isset($filters['team2']) && $filters['team2']) {
            $this->db->join('z_teams AS zt2', 'z_matches.team2 = zt2.team_id', 'inner');
        }

        $this->db->select('*,z_matches.id AS super_match_id,z_matches.link_complete AS link_match');
        $this->db->join('z_competitions', 'z_matches.competition_id = z_competitions.competition_id', 'inner');
        $this->db->join('z_countries', 'z_competitions.country_id = z_countries.ID', 'left');

        $this->db->group_by('super_match_id');
        $result = $this->db->get('z_matches');

        if (isset($filters['num_rows']) && $filters['num_rows'] == true) {
            return $result->num_rows();
        }

        foreach ($result->result_array() as $linie) {
            $temp = $this->team_model->get_team($linie['team1']);
            $linie['team1'] = $temp['name'];
            $temp = $this->team_model->get_team($linie['team2']);
            $linie['team2'] = $temp['name'];
            $linie['competition_name'] = $linie['name'];

            $goalz = array();
            $cardz = array();
            $condition = true;

            $goals = $this->goal_model->get_goals_by_match($linie['super_match_id']);
            foreach ($goals as $goal) {
                $goalz[$goal['score']] = $goal['min'];
                if ($goal['min'] < $filters['min']) {
                    $condition = false;
                }
            }

            if (!$condition) {
                continue;
            }

            $type = array('yellow_red', 'red');
            $cards = $this->card_model->get_cards_by_match($linie['id'], $type);
            //print_r($cards);
            foreach ($cards as $card) {
                $cardz[$card['card_type']] = $card['min'];
            }

            $linie['goals'] = $goalz;
            $linie['cards'] = $cardz;

            $linie['score'] = array_pop(array_keys($goalz));

            $row[] = $linie;
            //print '<pre>';
            // print_r($goals);            
            //print_r($cardz);
            //die;
            //print_r($linie);            
        }

        //print '<pre>';
        //print_r($row);die;

        return $row;
    }

    public function get_h2h($filters = array()) {
        $row = array();

        $this->db->join('z_competitions', 'z_matches.competition_id = z_competitions.competition_id', 'inner');
        $this->db->join('z_countries', 'z_competitions.country_id = z_countries.ID', 'left');
        $this->db->where('team1', $filters['team1']);
        $this->db->where('team2', $filters['team2']);
        $this->db->select('*,z_competitions.name AS competition_name,z_matches.link_complete AS link_match');
        $result_home = $this->db->get('z_matches');

        foreach ($result_home->result_array() as $line) {
            $row[] = $line;
        }

        $this->db->join('z_competitions', 'z_matches.competition_id = z_competitions.competition_id', 'inner');
        $this->db->join('z_countries', 'z_competitions.country_id = z_countries.ID', 'left');
        $this->db->where('team1', $filters['team2']);
        $this->db->where('team2', $filters['team1']);
        $this->db->select('*,z_competitions.name AS competition_name,z_matches.link_complete AS link_match');
        $result_away = $this->db->get('z_matches');

        foreach ($result_away->result_array() as $line) {
            $row[] = $line;
        }

        if (isset($filters['count'])) {
            return count($row);
        }

        return $row;
    }

    public function get_h2h_stats($filters = array()) {
        $this->load->model('goal_model');

        $matches = $this->get_h2h($filters);
        $total = count($matches);
        $stats = array('total' => $total, '1' => 0, 'x' => 0, '2' => 0);

        foreach ($this->overs as $key => $value) {
            $stats['over_' . $key] = 0;
            $stats['under_' . $key] = 0;
        }

        foreach ($matches as $match) {
            if ($this->goal_model->isOne($match['score'])) {
                $stats['1'] ++;
            } elseif ($this->goal_model->isX($match['score'])) {
                $stats['x'] ++;
            } elseif ($this->goal_model->isTwo($match['score'])) {
                $stats['2'] ++;
            }

            foreach ($this->overs as $key => $value) {
                if ($this->goal_model->isOver($match['score'], $key)) {
                    $stats['over_' . $key] ++;
                } elseif ($this->goal_model->isUnder($match['score'], $key)) {
                    $stats['under_' . $key] ++;
                }
            }
        }

        return $stats;
    }

    public function get_h2h_stats_percentage($filters = array()) {
        $stats = $this->get_h2h_stats($filters);

        $stats['1'] = round($stats['1'] * 100 / $stats['total'], 2);
        $stats['x'] = round($stats['x'] * 100 / $stats['total'], 2);
        $stats['2'] = round($stats['2'] * 100 / $stats['total'], 2);

        foreach ($this->overs as $key => $value) {
            $stats['over_' . $key] = round($stats['over_' . $key] * 100 / $stats['total'], 2);
            $stats['under_' . $key] = round($stats['under_' . $key] * 100 / $stats['total'], 2);
        }

        unset($stats['total']);

        return $stats;
    }

    public function algorithm($date) {
        $stats = array();
        $filters = array();
        $filters['match_date_start'] = $filters['match_date_end'] = $date;
        $matches = $this->get_matches($filters);

        unset($filters['match_date_start']);
        unset($filters['match_date_end']);

        foreach ($matches as $match) {
            $filters['team1'] = $match['team1'];
            $filters['team2'] = $match['team2'];
            $stats[$match['id']] = $this->get_h2h_stats_percentage($filters);
        }

        $new_stats = array();

        foreach ($stats as $match_id => $value) {
            $new_stats['1'][$match_id] = $stats[$match_id]['1'];
            $new_stats['x'][$match_id] = $stats[$match_id]['x'];
            $new_stats['2'][$match_id] = $stats[$match_id]['2'];
            foreach ($this->overs as $key => $val) {
                $new_stats['over_' . $key][$match_id] = $stats[$match_id]['over_' . $key];
                $new_stats['under_' . $key][$match_id] = $stats[$match_id]['under_' . $key];
            }
        }

        $new_stats['1'] = asort($new_stats['1']);
        $new_stats['x'] = asort($new_stats['x']);
        $new_stats['2'] = asort($new_stats['2']);
        foreach ($this->overs as $key => $val) {
            $new_stats['over_' . $key] = asort($new_stats['over_' . $key]);
            $new_stats['under_' . $key] = asort($new_stats['under_' . $key]);
        }

        return $new_stats;
    }

    public function algorithm_success_all($date) {
        $this->load->model('goal_model');
        $stats = $this->algorithm($date);
        $total_matches = count($stats['1']);

        $success = array('1' => 0, 'x' => 0, '2' => 0);

        foreach ($this->overs as $key => $value) {
            $success['over_' . $key] = 0;
            $success['under_' . $key] = 0;
        }

        foreach ($stats['1'] as $match_id => $value) {
            $match = $this->get_match($match_id);
            if ($value > 50) {
                if ($this->goal_model->isOne($match['score'])) {
                    $success['1'] ++;
                }
            } else {
                if (!$this->goal_model->isOne($match['score'])) {
                    $success['1'] ++;
                }
            }
        }

        foreach ($stats['x'] as $match_id => $value) {
            $match = $this->get_match($match_id);
            if ($value > 50) {
                if ($this->goal_model->isX($match['score'])) {
                    $success['x'] ++;
                }
            } else {
                if (!$this->goal_model->isX($match['score'])) {
                    $success['x'] ++;
                }
            }
        }

        foreach ($stats['2'] as $match_id => $value) {
            $match = $this->get_match($match_id);
            if ($value > 50) {
                if ($this->goal_model->isTwo($match['score'])) {
                    $success['2'] ++;
                }
            } else {
                if (!$this->goal_model->isTwo($match['score'])) {
                    $success['2'] ++;
                }
            }
        }

        foreach ($this->overs as $key => $value) {
            foreach ($stats['over_' . $key] as $match_id => $value) {
                $match = $this->get_match($match_id);
                if ($value > 50) {
                    if ($this->goal_model->isOver($match['score'], $key)) {
                        $success['over_' . $key] ++;
                    }
                } else {
                    if (!$this->goal_model->isOver($match['score'])) {
                        $success['over_' . $key] ++;
                    }
                }
            }

            foreach ($stats['under_' . $key] as $match_id => $value) {
                $match = $this->get_match($match_id);
                if ($value > 50) {
                    if ($this->goal_model->isUnder($match['score'], $key)) {
                        $success['under_' . $key] ++;
                    }
                } else {
                    if (!$this->goal_model->isUnder($match['score'])) {
                        $success['under_' . $key] ++;
                    }
                }
            }
        }


        $success['1'] = round($success['1'] * 100 / $total_matches, 2);
        $success['x'] = round($success['x'] * 100 / $total_matches, 2);
        $success['2'] = round($success['2'] * 100 / $total_matches, 2);

        foreach ($this->overs as $key => $value) {
            $success['over_' . $key] = round($success['over_' . $key] * 100 / $total_matches, 2);
            $success['under_' . $key] = round($success['under_' . $key] * 100 / $total_matches, 2);
        }

        return $success;
    }

}
