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

    public function __construct()
    {
        parent::__construct();
        $this->CI = & get_instance();
    }        

    /**
     * Get Matches
     *
     *
     * @return array
     */
    function get_matches($filters = array()) 
    {
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
        $this->db->select('*,z_matches.link_complete AS link_match, z_matches.link AS link_match_simple');

        $result = $this->db->get('z_matches');

        foreach ($result->result_array() as $linie) {
            $temp = $this->team_model->get_team($linie['team1']);            
            $linie['team1'] = $temp['name'];
            $linie['team1_id'] = $temp['team_id'];
            $temp = $this->team_model->get_team($linie['team2']);
            $linie['team2'] = $temp['name'];
            $linie['team2_id'] = $temp['team_id'];
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

    function match_exists($match) 
    {
        if (isset($match['link'])) {
            $this->db->where('link', $match['link']);
        }
        
        if (isset($match['team1'])) {
            $this->db->where('team1', $match['team1']);
        }
        
        if (isset($match['team2'])) {
            $this->db->where('team2', $match['team2']);
        }
        
        if (isset($match['competition_id'])) {
            $this->db->where('competition_id', $match['competition_id']);
        }
        
        if (isset($match['score'])) {
            $this->db->where('score', $match['score']);
        }
               
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
    
    function get_no_of_matches_by_competition_id($competition_id) 
    {
        $this->db->or_where('competition_id', $competition_id);

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

    public function get_h2h($filters = array())
    {
        $this->load->model('team_model');
        $row = array();
        
        if (isset($filters['include_competitions'])) {
            $this->db->join('z_competitions', 'z_matches.competition_id = z_competitions.competition_id', 'inner');
            $this->db->join('z_countries', 'z_competitions.country_id = z_countries.ID', 'left');            
        }

       
        $this->db->where('team1', $filters['team1']);
        $this->db->where('team2', $filters['team2']);
        $this->db->where('match_date <', $filters['match_date']);
        
        if (isset($filters['include_competitions'])) {
            $this->db->select('*,z_competitions.name AS competition_name,z_matches.link_complete AS link_match');
        }
        
        $result_home = $this->db->get('z_matches');

        foreach ($result_home->result_array() as $line) {
            if (isset($filters['include_competitions'])) {
                $temp = $this->team_model->get_team($line['team1']);
                $line['team1_name'] = $temp['name'];
                $temp = $this->team_model->get_team($line['team2']);
                $line['team2_name'] = $temp['name'];
            }
            $row[] = $line;
        }

        if (isset($filters['include_competitions'])) {
            $this->db->join('z_competitions', 'z_matches.competition_id = z_competitions.competition_id', 'inner');
            $this->db->join('z_countries', 'z_competitions.country_id = z_countries.ID', 'left');
        }
        
        $this->db->where('team1', $filters['team2']);
        $this->db->where('team2', $filters['team1']);
        $this->db->where('match_date <', $filters['match_date']);
        
        if (isset($filters['include_competitions'])) {
           $this->db->select('*,z_competitions.name AS competition_name,z_matches.link_complete AS link_match');
        }
        
        $result_away = $this->db->get('z_matches');

        foreach ($result_away->result_array() as $line) {
            if (isset($filters['include_competitions'])) {
                $temp = $this->team_model->get_team($line['team1']);
                $line['team1_name'] = $temp['name'];
                $temp = $this->team_model->get_team($line['team2']);
                $line['team2_name'] = $temp['name'];
            }
            $row[] = $line;
        }

        if (isset($filters['count'])) {
            return count($row);
        }
        
        //print '<pre>xx ' . $filters['team1'] . ' ' . $filters['team2'];
        //print_r($row);
        //die;

        return $row;
    }
    
    protected function isHome($match, $team1, $team2)
    {
        if ($match['team1'] == $team1) {
            return true;
        }
        
        return false;
    }
    
    /**
     * 
     * @param array $match
     * @param int   $team1
     * @param int   $team2
     * 
     * @return mixed 1 for 1 0 for X 2 for 2
     */
    protected function is1X2($match, $team1, $team2)
    {
        $this->load->model('goal_model');
        
        if ($this->goal_model->isX($match['score'])) {
            return 0;
        }
        
        if ($this->isHome($match, $team1, $team2)) {
            if ($this->goal_model->isOne($match['score'])) {
                return 1;
            } elseif ($this->goal_model->isTwo($match['score'])) {
                return 2;
            }
        } else {
            if ($this->goal_model->isOne($match['score'])) {
                return 2;
            } elseif ($this->goal_model->isTwo($match['score'])) {
                return 1;
            }
        }
    }

    public function get_h2h_stats($filters = array()) 
    {
        $this->load->model('goal_model');

        $matches = $this->get_h2h($filters);
        $total = count($matches);
        $stats = array('total' => $total, '1' => 0, 'x' => 0, '2' => 0);

        foreach ($this->overs as $key => $value) {
            $stats['over_' . $key] = 0;
            $stats['under_' . $key] = 0;
        }

        foreach ($matches as $match) {
            $temp = $this->is1X2($match, $filters['team1'], $filters['team2']);
            
            if (!$temp) {
                $stats['x'] ++;
            } elseif ($temp == 1) {
                $stats['1'] ++;
            } elseif ($temp == 2) {
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

    public function get_h2h_stats_percentage($filters = array())
    {
        $stats = $this->get_h2h_stats($filters);
        
        // stats in p = percentages
        $stats['1p'] = $stats['xp'] = $stats['2p'] = 0;
        foreach ($this->overs as $key => $value) {
            $stats['over_' . $key . 'p'] = $stats['under_' . $key . 'p'] = 0;
        }
        
        if (!$stats['total']) {
            return $stats;
        }
        
        $stats['1p'] = round($stats['1'] * 100 / $stats['total'], 2);
        $stats['xp'] = round($stats['x'] * 100 / $stats['total'], 2);
        $stats['2p'] = round($stats['2'] * 100 / $stats['total'], 2);
        
        foreach ($this->overs as $key => $value) {
            $stats['over_' . $key . 'p'] = round($stats['over_' . $key] * 100 / $stats['total'], 2);
            $stats['under_' . $key . 'p'] = round($stats['under_' . $key] * 100 / $stats['total'], 2);
            
        }        

        return $stats;
    }

    public function algorithm($date, $atLeastMatches)
    {
        $stats = array();
        $filters = array();
        $filters['match_date_start'] = $filters['match_date_end'] = $date;
        $matches = $this->get_matches($filters);
                
        unset($filters['match_date_start']);
        unset($filters['match_date_end']);

        foreach ($matches as $match) {
            $filters['team1'] = $match['team1_id'];
            $filters['team2'] = $match['team2_id'];
            $filters['match_date'] = $date;
            $statsTemp = $this->get_h2h_stats_percentage($filters);
            // do not take into consideration matches without at least a certain number of previous encounters
            if ($statsTemp['total'] < $atLeastMatches) {
                continue;
            }
            
            $stats[$match['id']] = $statsTemp;
            $stats[$match['id']]['match'] = $match;
        }

        $new_stats = array();

        foreach ($stats as $match_id => $value) {
            $new_stats['1'][$match_id] = $stats[$match_id]['1p'];
            $new_stats['x'][$match_id] = $stats[$match_id]['xp'];
            $new_stats['2'][$match_id] = $stats[$match_id]['2p'];
            foreach ($this->overs as $key => $val) {
                $new_stats['over_' . $key][$match_id] = $stats[$match_id]['over_' . $key . 'p'];
                $new_stats['under_' . $key][$match_id] = $stats[$match_id]['under_' . $key . 'p'];
            }
        }
                
        arsort($new_stats['1']);
        arsort($new_stats['x']);
        arsort($new_stats['2']);
        foreach ($this->overs as $key => $val) {
            arsort($new_stats['over_' . $key]);
            arsort($new_stats['under_' . $key]);
        }
        
        $new_stats['stats'] = $stats;
        
        //print '<pre>';
        //print_r($matches);
        //print_r($stats);
        //print '--------------------------------';
        //print_r($new_stats);
        //print '</pre>';

        return $new_stats;
    }

    public function algorithm_success_all($filters) 
    {
        $this->load->model('goal_model');
        $stats = $this->algorithm($filters['date'], $filters['atLeastMatches']);
//        print '<pre>SSTATS';
//        print_r($stats);
//        print '</pre>SSTATS';
        
        $success = array('1' => array('ok' => 0, 'total' => 0), 'x' => array('ok' => 0, 'total' => 0), '2' => array('ok' => 0, 'total' => 0));

        foreach ($this->overs as $key => $value) {
            $success['over_' . $key] = array('ok' => 0, 'total' => 0);
            $success['under_' . $key] = array('ok' => 0, 'total' => 0);
        }

        foreach ($stats['1'] as $match_id => $value) {
            $score = $stats['stats'][$match_id]['match']['score'];
            if ($value > $filters['accuracy']) {
                if ($this->goal_model->isOne($score)) {
                    $success['1']['ok']++;
                }
                
                $success['1']['total']++;
            }
        }

        foreach ($stats['x'] as $match_id => $value) {
            $score = $stats['stats'][$match_id]['match']['score'];
            if ($value > $filters['accuracy']) {
                if ($this->goal_model->isX($score)) {
                    $success['x']['ok']++;
                }
                
                $success['x']['total']++;
            }
        }

        foreach ($stats['2'] as $match_id => $value) {
            $score = $stats['stats'][$match_id]['match']['score'];
            if ($value > $filters['accuracy']) {
                if ($this->goal_model->isTwo($score)) {
                    $success['2']['ok']++;
                }
                
                $success['2']['total']++;
            }
        }

        foreach ($this->overs as $key => $value) {
            foreach ($stats['over_' . $key] as $match_id => $value) {
                $score = $stats['stats'][$match_id]['match']['score'];
                if ($value > $filters['accuracy']) {
                    if ($this->goal_model->isOver($score, $key)) {
                        $success['over_' . $key]['ok']++;
                    }
                    
                    $success['over_' . $key]['total']++;
                }
            }

            foreach ($stats['under_' . $key] as $match_id => $value) {
                $score = $stats['stats'][$match_id]['match']['score'];
                if ($value > $filters['accuracy']) {
                    if ($this->goal_model->isUnder($score, $key)) {
                        $success['under_' . $key]['ok']++;
                    }
                    
                    $success['under_' . $key]['total']++;
                }
            }
        }


        $success['1']['p'] = $success['1']['total'] == 0 ? 0 : round($success['1']['ok'] * 100 / $success['1']['total'], 2);
        $success['x']['p'] = $success['x']['total'] == 0 ? 0 : round($success['x']['ok'] * 100 / $success['x']['total'], 2);
        $success['2']['p'] = $success['2']['total'] == 0 ? 0 : round($success['2']['ok'] * 100 / $success['2']['total'], 2);

        foreach ($this->overs as $key => $value) {
            $success['over_' . $key]['p'] = $success['over_' . $key]['total'] == 0 ? 0 :round($success['over_' . $key]['ok'] * 100 / $success['over_' . $key]['total'], 2);
            $success['under_' . $key]['p'] = $success['under_' . $key]['total'] == 0 ? 0 : round($success['under_' . $key]['ok'] * 100 / $success['under_' . $key]['total'], 2);
        }
        
        $stats['success'] = $success;
        
        print '<pre>';
        print_r($stats);
        print '</pre>';

        return $stats;
    }
    
    public function check_batch($id)
    {
        $this->load->model(array('goal_model'));
        $limit = 1000;
        
        $this->db->limit($limit, $id);
        $result = $this->db->get('z_matches');
       
        foreach ($result->result_array() as $linie) {
            $check_goals = $this->goal_model->get_goals_by_match($linie['id']);
            $goals = explode('-', $linie['score']);
            $total_goals = $goals[0] + $goals[1];

 //            print_r($check_goals);
 //            echo $check_goals . '/' . $total_goals;die;

            if(count($check_goals) != $total_goals) {
                // update match
                $this->update_match(array('parsed' => 0), $linie['id']);
            }
        }
        
        if (($id + $limit) > $this->get_num_rows()) {
            return 0;
        }
        
        return ($id + $limit);
    }        
    
    function get_unparsed_matches($filters = array())
    {
        
        $this->load->model(array('team_model', 'goal_model'));
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
        
        $this->db->where('parsed', 0);

        $result = $this->db->get('z_matches');
        
        foreach ($result->result_array() as $linie) {
            $temp = $this->team_model->get_team($linie['team1']);
            $linie['team1'] = $temp['name'];
            $temp = $this->team_model->get_team($linie['team2']);
            $linie['team2'] = $temp['name'];
            $linie['competition_name'] = $linie['name'];
            $check_goals = $this->goal_model->get_goals_by_match($linie['id']);
            $goals = explode('-', $linie['score']);
            $total_goals = $goals[0] + $goals[1];
            if(count($check_goals) != $total_goals) {
                $row[] = $linie;
            }
        }

        return $row;
    }
    
    function get_unparsed_num_rows($filters = array()) 
    {
        $this->load->model('goal_model');
        
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
        
        $this->db->where('parsed', 0);

        $result = $this->db->get('z_matches');

        return $result->num_rows();
    }
    
    public function getSpecialCharacters()
    {
        $string = "\À\Á\Â\Ã\Ä\Å\Æ\Ç\È\É\Ê\Ë\Ì\Í\Î\Ï\Ð\Ñ\Ò\Ó\Ô\Õ\Ö\×\Ø\Ù\Ú\Û\Ü\Ý\Þ\ß\à\á\â\ã\ä\å\æ\ç\è\é\ê\ë\ì\í\î\ï\ð\ñ\ò\ó\ô\õ\ö\÷\ø\ù\ú\û\ü\ý\þ\ÿ\Â\â\Ă\ă\Î\î\ş\Ş\ţ\Ţ\ș\Ș\ț\Ț\-\'\&#x27;\.";
        
        return $string;
    }
    
    public function get_team_links($link)
    {
        $start = $link;
        //echo $start . '<br/>';
        $pozVs = strpos($start, '-vs-');
        //echo $pozVs . '<br/>';
        $link = substr($start, 0, $pozVs);
        //echo $link. '<br/>';
        $pozFirst = strrpos($link, '/');
        //echo $pozFirst. '<br/>';
        $link = substr($start, $pozVs);
        $link = substr($link, 0, strpos($link, '/'));
        //echo $link. '<br/>';
        $pozSecond = strpos($start, $link) + strlen($link);
        //echo $pozSecond. '<br/>';
        $link = substr($start, $pozFirst + 1);
        $link = substr($link, 0, strpos($link, '/'));
        $team1 = substr($link, 0, strpos($link, '-vs-'));
        $team2 = str_replace($team1 . '-vs-', '', $link);
        
        return array($team1, $team2);
    }
    
    public function make_links()
    {
        $this->load->model('team_model');
        
        $matches = $this->get_matches(array('limit'=>500, 'parsed' => 0));
        $link = null;
        foreach ($matches as $match) {
            $start = $match['link_match_simple'];
            //echo $start . '<br/>';
            $pozVs = strpos($start, '-vs-');
            //echo $pozVs . '<br/>';
            $link = substr($start, 0, $pozVs);
            //echo $link. '<br/>';
            $pozFirst = strrpos($link, '/');
            //echo $pozFirst. '<br/>';
            $link = substr($start, $pozVs);
            $link = substr($link, 0, strpos($link, '/'));
            //echo $link. '<br/>';
            $pozSecond = strpos($start, $link) + strlen($link);
            //echo $pozSecond. '<br/>';
            $link = substr($start, $pozFirst + 1);
            $link = substr($link, 0, strpos($link, '/'));
            $team1 = substr($link, 0, strpos($link, '-vs-'));
            $team2 = str_replace($team1 . '-vs-', '', $link);
            echo $match['id'] . '=>' . $start . '=>' . $link . '=>' . $team1 . '=>' . $team2 .'<br/>';
                       
            $firstTeam = $this->team_model->get_team($match['team1_id']);
            
            if (!$firstTeam['link']) {
                $this->team_model->update_team(array('link' => $team1), $match['team1_id']);
            } else {
                if ($firstTeam['link'] != $team1) {
                    echo '<div style="color:red;">' . $match['id'] . '=>' . $match['link_match_simple'] . '=>TEAM1:' . $team1 . '=>' . $firstTeam['link'] . '</div><br/>';
                }
            }
            
            $secondTeam = $this->team_model->get_team($match['team2_id']);
            
            if (!$secondTeam['link']) {
                $this->team_model->update_team(array('link' => $team2), $match['team2_id']);
            } else {
                if ($secondTeam['link'] != $team2) {
                    echo '<div style="color:red;">' . $match['id'] . '=>' . $match['link_match_simple'] . '=>TEAM2:' . $team2 . '=>' . $secondTeam['link'] . '</div><br/>';
                }
            }
                                 
            $this->update_match(array('parsed' => 1), $match['id']);
        }               
    }
    
}

