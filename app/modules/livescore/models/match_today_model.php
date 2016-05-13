<?php

/**
 * Match Today Model
 *
 * Manages matches
 *
 * @author Weblight.ro
 * @copyright Weblight.ro
 * @package BJ Tool
 */
class Match_today_model extends CI_Model 
{

    private $CI;
    private $overs = array('0.5' => 1, '1.5' => 2, '2.5' => 3, '3.5' => 4, '4.5' => 5, '5.5' => 6);
        
    function __construct() 
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
        $this->load->model(array('team_today_model','competition_today_model','competition_model','country_model'));
        $row = array();

        $order_dir = (isset($filters['sort_dir'])) ? $filters['sort_dir'] : 'ASC';
        if (isset($filters['sort'])) {
            $this->db->order_by($filters['sort'], $order_dir);
        }
            
        if (isset($filters['competition_name']) && $filters['competition_name']) {
            $this->db->like('z_competitions_today.name', $filters['competition_name']);
        }       

        $this->db->select('*,z_matches_today.link_complete AS link_match');

        $result = $this->db->get('z_matches_today');

        foreach ($result->result_array() as $linie) {
            $competition_today = $this->competition_today_model->get_competition($linie['competition_id_today']);
            $competition = $this->competition_model->get_competition($competition_today['competition_id']);
            if (!isset($competition_today['country_id'])) {
                $linie['country_name'] = $competition['country_name'];
                $linie['competition_name'] = isset($competition['name']) ?  $competition['name'] : '';
            } else {
                $linie['country_name'] = $competition_today['country_name'];
                $linie['competition_name'] = isset($competition['name']) ?  $competition['name'] : '';
            }
            
            if (!$competition_today['competition_id']) {
                $linie['ok_competition'] = 0;
            } else {               
                $linie['ok_competition'] = 1;
            }
            
            $temp = $this->team_today_model->get_team($linie['team1_today']);
            $linie['team1'] = $temp['name'];
            $linie['team1_id'] = $temp['team_id'];
            $linie['ok_team1'] = $temp['ok'];
            $temp = $this->team_today_model->get_team($linie['team2_today']);
            $linie['team2_id'] = $temp['team_id'];
            $linie['team2'] = $temp['name'];
            $linie['ok_team2'] = $temp['ok'];
            
            if ($linie['team1'] == $linie['team2']) {
                $linie['ok_team1'] = $linie['ok_team2'] = 0;
            }
            
            if (isset($filters['country_name'])
                && $filters['country_name']
                && strcasecmp($linie['country_name'], $filters['country_name'])) {
                continue;
            }
            
            if (isset($filters['match_date_start'])
                && !empty($filters['match_date_start'])
                && ($linie['match_date'] < $filters['match_date_start'])) {
                continue;
            }
            
            if (isset($filters['match_date_end'])
                && !empty($filters['match_date_end'])
                && ($linie['match_date'] > $filters['match_date_end'])) {
                continue;
            }
            
            if (isset($filters['score'])
                && (strcmp($filters['score'], $linie['score']))
            ) {
                continue;
            }
            
            if (isset($filters['team1'])
                && (strcasecmp($filters['team1'], $linie['team1']))
            ) {
                continue;
            }
            
            if (isset($filters['team2'])
                && (strcasecmp($filters['team2'], $linie['team2']))
            ) {
                continue;
            }
                        
            $row[] = $linie;
//             print '<today>';
//             print_r($temp);
//             print_r($competition_today);
//             print_r($competition);
//             print_r($linie);
//             die;
        }
//        print '<today>';
//        print_r($row);
//        print '</today>';
//        die;
        
        if (isset($filters['limit'])) {
            $row = array_slice($row, isset($filters['offset']) ? (int) $filters['offset'] : 0, isset($filters['limit']) ? (int) $filters['limit'] : 20);
        }
        
        return $row;
    }
    
    /**
     * Get Matches predict H2H
     *
     *
     * @return array
     */
    function get_matches_predict_h2h($filters = array()) 
    {
        $this->load->model(array('team_today_model','competition_today_model','competition_model','country_model', 'match_model', 'goal_model'));
        $row = array();

        $order_dir = (isset($filters['sort_dir'])) ? $filters['sort_dir'] : 'ASC';
        if (isset($filters['sort'])) {
            $this->db->order_by($filters['sort'], $order_dir);
        }
            
        if (isset($filters['competition_name']) && $filters['competition_name']) {
            $this->db->like('z_competitions_today.name', $filters['competition_name']);
        }       

        $this->db->select('*,z_matches_today.link_complete AS link_match');

        $result = $this->db->get('z_matches_today');

        foreach ($result->result_array() as $linie) {
            $competition_today = $this->competition_today_model->get_competition($linie['competition_id_today']);
            $competition = $this->competition_model->get_competition($competition_today['competition_id']);
            if (!isset($competition_today['country_id'])) {
                $linie['country_name'] = $competition['country_name'];
                $linie['competition_name'] = isset($competition['name']) ?  $competition['name'] : '';
            } else {
                $linie['country_name'] = $competition_today['country_name'];
                $linie['competition_name'] = isset($competition['name']) ?  $competition['name'] : '';
            }
            
            if (!$competition_today['competition_id']) {
                $linie['ok_competition'] = 0;
            } else {               
                $linie['ok_competition'] = 1;
            }
            
            $temp = $this->team_today_model->get_team($linie['team1_today']);
            $linie['team1'] = $temp['name'];
            $linie['team1_id'] = $temp['team_id'];
            $linie['ok_team1'] = $temp['ok'];
            $temp = $this->team_today_model->get_team($linie['team2_today']);
            $linie['team2_id'] = $temp['team_id'];
            $linie['team2'] = $temp['name'];
            $linie['ok_team2'] = $temp['ok'];
            
            // get previous matches
            $h2hFilters = array(
                'team1' =>  $linie['team1_id'],
                'team2' => $linie['team2_id'],
                'match_date' => $linie['match_date']
            );
            $previousMatches = $this->match_model->get_h2h($h2hFilters);
            
            $total = $overs = 0;
            $linie['percentage'] = 0;
            $linie['over'] = '';
            foreach ($previousMatches as $previousMatch) {
                $total++;
                $score = explode('-', $previousMatch['score']);
                $score[0] = (int) $score[0];
                $score[1] = (int) $score[1];
                if (($score[0] + $score[1]) > 2) {
                    $overs++;
                }
                $linie['percentage'] = round($overs * 100/ $total, 2);
                if ($linie['percentage'] < 50 ) {
                    $linie['percentage'] = 100 - $linie['percentage'];
                    $linie['over'] = 'UNDER';
                } else {
                    $linie['over'] = 'OVER';
                }
            }
            
            if ($total < 3) {
                $linie['percentage'] = 0;
            }                        
            
            if (strstr($linie['score'], '?')) {
                $linie['color'] = $linie['percentage'] == 0 ? 'grey' : 'orange';                                
            } else {
                $isOver = $this->goal_model->isOver($linie['score']);
                
                do {
                    if ($linie['percentage'] == 0) {
                        $linie['color'] = 'grey';
                        break;
                    }
                    
                    if ($linie['over'] === 'OVER' && $isOver) {
                        $linie['color'] = 'green';
                        break;
                    }
                    
                    if ($linie['over'] === 'UNDER' && !$isOver) {
                        $linie['color'] = 'green';
                        break;
                    }
                    
                    $linie['color'] = 'red';
                    
                } while (false);                                
            }                        
            
            if ($linie['team1'] == $linie['team2']) {
                $linie['ok_team1'] = $linie['ok_team2'] = 0;
            }
            
            if (isset($filters['country_name'])
                && $filters['country_name']
                && strcasecmp($linie['country_name'], $filters['country_name'])) {
                continue;
            }
            
            if (isset($filters['match_date_start'])
                && !empty($filters['match_date_start'])
                && ($linie['match_date'] < $filters['match_date_start'])) {
                continue;
            }
            
            if (isset($filters['match_date_end'])
                && !empty($filters['match_date_end'])
                && ($linie['match_date'] > $filters['match_date_end'])) {
                continue;
            }
            
            if (isset($filters['score'])
                && (strcmp($filters['score'], $linie['score']))
            ) {
                continue;
            }
            
            if (isset($filters['team1'])
                && (strcasecmp($filters['team1'], $linie['team1']))
            ) {
                continue;
            }
            
            if (isset($filters['team2'])
                && (strcasecmp($filters['team2'], $linie['team2']))
            ) {
                continue;
            }
                        
            $row[] = $linie;
//             print '<today>';
//             print_r($temp);
//             print_r($competition_today);
//             print_r($competition);
//             print_r($linie);
//             die;
        }
//        print '<today>';
//        print_r($row);
//        print '</today>';
//        die;
        
        uasort($row, array('Match_today_model', 'cmp'));
        
        if (isset($filters['limit'])) {
            $row = array_slice($row, isset($filters['offset']) ? (int) $filters['offset'] : 0, isset($filters['limit']) ? (int) $filters['limit'] : 20);
        }
        
        return $row;
    }
    
    private static function cmp($a, $b)
    {
        if ($a['percentage'] == $b['percentage']) {
            return 0;
        }
        return ($a['percentage'] > $b['percentage']) ? -1 : 1;
    }

    function get_num_rows($filters = array())
    {
        return count($this->get_matches($filters));
    }

    /**
     * Get Match
     *
     * @param int $id	
     *
     * @return array
     */
    function get_match($id)
    {
        $row = array();
        //$this->db->join('z_competitions_today', 'z_matches_today.competition_id_today = z_competitions_today.index', 'inner'); // not ok because of competition_id_today = 0       
        $this->db->where('z_matches_today.index', $id);
        $this->db->select('*,z_matches_today.link AS link_match,z_matches_today.link_complete AS link_match_complete');
        $result = $this->db->get('z_matches_today');
        foreach ($result->result_array() as $row) {
            return $row;
        }
        
        return $row;
    }
    
    public function get_match_today($id)
    {
        $row = array();
        $query = $this->db->select('*,zm_today.link AS link_match,zm_today.link_complete AS link_match_complete')
                        ->from('z_matches_today AS zm_today')
                        ->join('z_competitions_today AS zc_today', 'zm_today.competition_id_today = zc_today.index', 'left')
                        ->join('z_competitions AS zc', 'zc_today.competition_id = zc.competition_id', 'left')
                        ->where('zm_today.index', $id)
                        ->get();
        
        foreach ($query->result_array() as $row) {
            return $row;
        }
        
        return $row;
    }

    function get_matches_by_team_id($filters) 
    {
        $this->load->model('team_today_model');
        $row = array();

        if (!isset($filters['count'])) {
            $this->db->join('z_competitions_today', 'z_matches_today.competition_id_today = z_competitions_today.index', 'inner');
            $this->db->join('z_competitions', 'z_competitions.competition_id = z_competitions_today.competition_id', 'left');
            $this->db->join('z_countries', 'z_competitions_today.country_id = z_countries.ID', 'left');
        }

        $this->db->or_where('team1_today', $filters['team_id']);
        $this->db->or_where('team2_today', $filters['team_id']);
        if (isset($filters['count'])) {
            $this->db->select('*');
        } else {
            $this->db->select('*,z_competitions.name AS competition_name,z_matches_today.link_complete AS link_match');
            $this->db->order_by('z_matches_today.match_date');
        }

        if (isset($filters['limit'])) {
            $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
            $this->db->limit($filters['limit'], $offset);
        }

        $result = $this->db->get('z_matches_today');

        if (isset($filters['count'])) {
            return $result->num_rows();
        }

        foreach ($result->result_array() as $line) {
            $temp = $this->team_today_model->get_team($line['team1_today']);
            $line['team1'] = $temp['name'];
            $temp = $this->team_today_model->get_team($line['team2_today']);
            $line['team2'] = $temp['name'];
            
            $line['competition_id'] ? $line['ok_competition'] = 1 : $line['ok_competition'] = 0;
            $line['team1'] ? $line['ok_team1'] = 1 : $line['ok_team1'] = 0;
            $line['team2'] ? $line['ok_team2'] = 1 : $line['ok_team2'] = 0;
            
           
            $row[] = $line;
        }
        
        //print '<today>';
        //print_r($row);
        //print '</today>';

        return $row;
    }

    function get_matches_by_team_id_partial($filters) 
    {
        $this->load->model('team_today_model');
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

//                print '<today>';
//                print_r($links);
//                print '</today>';

                return $second_team_results;
            }
        }

        if (!isset($filters['count'])) {
            $this->db->join('z_competitions_today', 'z_matches_today.competition_id = z_competitions_today.competition_id', 'inner');
            $this->db->join('z_countries', 'z_competitions_today.country_id = z_countries.ID', 'left');
        }

        $this->db->or_where('team1', $filters['team_id']);
        $this->db->or_where('team2', $filters['team_id']);
        if (isset($filters['count'])) {
            $this->db->select('*');
        } else {
            $this->db->select('*,z_competitions_today.name AS competition_name,z_matches_today.link_complete AS link_match');
            $this->db->order_by('z_matches_today.match_date');
        }

        if (isset($filters['limit'])) {
            $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
            $this->db->limit($filters['limit'], $offset);
        }

        $result = $this->db->get('z_matches_today');

        if (isset($filters['count'])) {
            return $result->num_rows();
        }

        foreach ($result->result_array() as $line) {
            $temp = $this->team_today_model->get_team($line['team1']);
            $line['team1'] = $temp['name'];
            $temp = $this->team_today_model->get_team($line['team2']);
            $line['team2'] = $temp['name'];

            $row[] = $line;
        }

        return $row;
    }

    function get_matches_by_team_id_simple($filters) 
    {
        $this->load->model('team_today_model');

        $this->db->or_where('team1', $filters['team_id']);
        $this->db->or_where('team2', $filters['team_id']);
        $this->db->select('*');

        $result = $this->db->get('z_matches_today');

        return $result->result_array();
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
    function new_match($insert_fields)
    {
        $this->db->insert('z_matches_today', $insert_fields);
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
    function update_match($update_fields, $id) 
    {

        $this->db->update('z_matches_today', $update_fields, array('index' => $id));
        return TRUE;
    }
    
    // checked
    function match_exists($match)
    {
        if (isset($match['link'])) {
            $this->db->where('link', $match['link']);
        }
        
        if (isset($match['link_complete'])) {
            $this->db->where('link_complete', $match['link_complete']);
        }
        
        if (isset($match['match_date'])) {
            $this->db->where('match_date', $match['match_date']);
        }
        
        if (isset($match['team1_today'])) {
            $this->db->where('team1_today', $match['team1_today']);
        }
        
        if (isset($match['team2_today'])) {
            $this->db->where('team2_today', $match['team2_today']);
        }

        $result = $this->db->get('z_matches_today');

        foreach ($result->result_array() as $row) {
            return $row['index'];
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
    function delete_match($id) 
    {
        $this->load->model('team_today_model');
        $match_today = $this->get_match_today($id);
        // delete teams today
        $this->team_today_model->delete_team($match_today['team1_today']);
        $this->team_today_model->delete_team($match_today['team2_today']);
        // delete match today
        $this->db->delete('z_matches_today', array('index' => $id));
        return true;
    }

    function get_no_of_matches_by_team_id($team_id) 
    {
        $this->db->or_where('team1', $team_id);
        $this->db->or_where('team2', $team_id);

        $result = $this->db->get('z_matches_today');

        return $result->num_rows();
    }

    function get_no_of_matches_by_competition_id($competition_id) 
    {
        $this->db->or_where('competition_id_today', $competition_id);

        $result = $this->db->get('z_matches_today');

        return $result->num_rows();
    }

    function fix_score()
    {
        $row = array();
        $result = $this->db->get('z_matches_today');

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
    
    function move_matches_today()
    {
        $this->load->model('competition_today_model');
        $this->load->model('team_today_model');
        $this->load->model('match_model');
                        
        $result = $this->db->get('z_matches_today');
        $insert_fields = array();
        $i = 0;
        
        foreach ($result->result_array() as $linie) {
            $temp = $this->competition_today_model->get_competition($linie['competition_id_today']);
            $insert_fields['competition_id'] = $temp['competition_id'];
            $temp = $this->team_today_model->get_team($linie['team1_today']);
            $insert_fields['team1'] = $temp['team_id'];
            $temp = $this->team_today_model->get_team($linie['team2_today']);
            $insert_fields['team2'] = $temp['team_id'];
            
            $insert_fields['match_date'] = $linie['match_date'];
            $insert_fields['score'] = $linie['score'];
            $insert_fields['link'] = $linie['link'];
            if ($insert_fields['link'][strlen($insert_fields['link']) - 1] != '/') {
                $insert_fields['link'] .= '/';
            }
            $insert_fields['link_complete'] = $linie['link_complete'];
            $insert_fields['parse_date'] = $linie['parse_date'];
            $insert_fields['parsed'] = 0;
            
            // copy match if not exist, there maybe matches with same link for instance matches with 0-0 and no link
            if (!$this->match_model->match_exists(
                array(
                    'link' => str_replace('soccer/', '', $insert_fields['link']),
                    'competition_id' => $insert_fields['competition_id'],
                    'team1' => $insert_fields['team1'],
                    'team2' => $insert_fields['team2'],
                    'score' => $insert_fields['score']
                )
            )) 
            {
                $this->match_model->new_match($insert_fields);
                $i++;
            }

            //print '<today>';
            //print_r($linie);
            //print_r($insert_fields);
            //print '</today>';
            //die;
        }
        
        // truncate today tables !!!
        $this->db->query('truncate table z_competitions_today');
        $this->db->query('truncate table z_teams_today');
        $this->db->query('truncate table z_matches_today');
        
        return $i;
    }
    
    /**
     * Truncates the table
     * 
     * @return boolean
     */
    function clear_table()
    {
        $this->db->truncate('z_matches_today');
        return true;
    }
    
    public function make_links()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $this->load->model(array('team_model', 'team_today_model'));
        
        $matches = $this->get_matches();
        $link = null;
        foreach ($matches as $match) {
            //print '<today>';
            //print_r($match);
            $start = $match['link'];
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
                      
            $firstTeam = $this->team_today_model->get_team($match['team1_today']);
            $secondTeam = $this->team_today_model->get_team($match['team2_today']);
            
            if (isset($firstTeam['link']) && isset($secondTeam['link'])) {
                continue;
            }                        
            
            echo $match['index'] . '=>' . $start . '=>' . $link . '=>' . $team1 . '=>' . $team2 .'<br/>';

            if ($match['ok_team1'] && $match['ok_team2']) {
                echo 'OK<br/>';
                continue;
            } else {
                echo '<div style="color:red;">NOK</div>';
            }
            
            
            /**
             * 
             * SELECT * , COUNT( * ) AS c
FROM `z_teams`
GROUP BY link
HAVING c >1
LIMIT 0 , 30
             
             SELECT *
FROM `z_teams`
WHERE link IS NULL
LIMIT 0 , 30 
             */
            
//            print '<today>';
//            print_r($match);
//            print '<today>FIRST';
//            print_r($firstTeam);
//            print '<today>SECOND';
//            print_r($secondTeam);
            
            if (isset($firstTeam['team_id'])) {
                $this->team_today_model->update_team(array('link' => $team1), $match['team1_today']);
            } else {
                if (!isset($firstTeam['link'])) {
                    $foundFirstTeam = $this->team_model->get_team_by_link(array('link' => $team1));

                    if (!empty($foundFirstTeam)) {
                        $this->team_today_model->update_team(array('link' => $team1, 'team_id' => $foundFirstTeam['team_id'], 'country_id' => null, 'name' => null), $match['team1_today']);
                    } else {
                        $this->team_today_model->update_team(array('link' => $team1), $match['team1_today']);
                    }
                }
            }                        
            
            if (isset($secondTeam['team_id'])) {
                $this->team_today_model->update_team(array('link' => $team2), $match['team2_today']);
            } else {
                if (!isset($secondTeam['link'])) {
                    $foundSecondTeam = $this->team_model->get_team_by_link(array('link' => $team2));                    

                    if (!empty($foundSecondTeam)) {
                        $this->team_today_model->update_team(array('link' => $team2, 'team_id' => $foundSecondTeam['team_id'], 'country_id' => null, 'name' => null), $match['team2_today']);
                    } else {
                        $this->team_today_model->update_team(array('link' => $team2), $match['team2_today']);
                    }                               
                }
            }                                    
        }               
    }
} 