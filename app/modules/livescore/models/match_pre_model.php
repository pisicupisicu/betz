<?php

/**
 * Match Pre Model
 *
 * Manages matches
 *
 * @author Weblight.ro
 * @copyright Weblight.ro
 * @package BJ Tool
 */
class Match_pre_model extends CI_Model 
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
        $this->load->model(array('team_pre_model','competition_pre_model','competition_model','country_model'));
        $row = array();

        $order_dir = (isset($filters['sort_dir'])) ? $filters['sort_dir'] : 'ASC';
        if (isset($filters['sort'])) {
            $this->db->order_by($filters['sort'], $order_dir);
        }
            
        if (isset($filters['competition_name']) && $filters['competition_name']) {
            $this->db->like('z_competitions_pre.name', $filters['competition_name']);
        }       

        $this->db->select('*,z_matches_pre.link_complete AS link_match');

        $result = $this->db->get('z_matches_pre');

        foreach ($result->result_array() as $linie) {
            $competition_pre = $this->competition_pre_model->get_competition($linie['competition_id_pre']);
            $competition = $this->competition_model->get_competition($competition_pre['competition_id']);
            if (!isset($competition_pre['country_id'])) {
                $linie['country_name'] = $competition['country_name'];
                $linie['competition_name'] = $competition['name'];
            } else {
                $linie['country_name'] = $competition_pre['country_name'];
                $linie['competition_name'] = $competition['name'];                                
            }
            
            if (!$competition_pre['competition_id']) {
                $linie['ok_competition'] = 0;
            } else {               
                $linie['ok_competition'] = 1;
            }
            
            $temp = $this->team_pre_model->get_team($linie['team1_pre']);
            $linie['team1'] = $temp['name'];
            $linie['ok_team1'] = $temp['ok'];
            $temp = $this->team_pre_model->get_team($linie['team2_pre']);
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
//             print '<pre>';
//             print_r($temp);
//             print_r($competition_pre);
//             print_r($competition);
//             print_r($linie);
//             die;
        }
//        print '<pre>';
//        print_r($row);
//        print '</pre>';
//        die;
        
        if (isset($filters['limit'])) {
            $row = array_slice($row, isset($filters['offset']) ? (int) $filters['offset'] : 0, isset($filters['limit']) ? (int) $filters['limit'] : 20);
        }
        
        return $row;
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
        //$this->db->join('z_competitions_pre', 'z_matches_pre.competition_id_pre = z_competitions_pre.index', 'inner'); // not ok because of competition_id_pre = 0       
        $this->db->where('z_matches_pre.index', $id);
        $this->db->select('*,z_matches_pre.link AS link_match,z_matches_pre.link_complete AS link_match_complete');
        $result = $this->db->get('z_matches_pre');
        foreach ($result->result_array() as $row) {
            return $row;
        }
        
        return $row;
    }
    
    public function get_match_pre($id)
    {
        $row = array();
        $query = $this->db->select('*,zm_pre.link AS link_match,zm_pre.link_complete AS link_match_complete')
                        ->from('z_matches_pre AS zm_pre')
                        ->join('z_competitions_pre AS zc_pre', 'zm_pre.competition_id_pre = zc_pre.index', 'left')
                        ->join('z_competitions AS zc', 'zc_pre.competition_id = zc.competition_id', 'left')
                        ->where('zm_pre.index', $id)
                        ->get();
        
        foreach ($query->result_array() as $row) {
            return $row;
        }
        
        return $row;
    }

    function get_matches_by_team_id($filters) 
    {
        $this->load->model('team_pre_model');
        $row = array();

        if (!isset($filters['count'])) {
            $this->db->join('z_competitions_pre', 'z_matches_pre.competition_id_pre = z_competitions_pre.index', 'inner');
            $this->db->join('z_competitions', 'z_competitions.competition_id = z_competitions_pre.competition_id', 'left');
            $this->db->join('z_countries', 'z_competitions_pre.country_id = z_countries.ID', 'left');
        }

        $this->db->or_where('team1_pre', $filters['team_id']);
        $this->db->or_where('team2_pre', $filters['team_id']);
        if (isset($filters['count'])) {
            $this->db->select('*');
        } else {
            $this->db->select('*,z_competitions.name AS competition_name,z_matches_pre.link_complete AS link_match');
            $this->db->order_by('z_matches_pre.match_date');
        }

        if (isset($filters['limit'])) {
            $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
            $this->db->limit($filters['limit'], $offset);
        }

        $result = $this->db->get('z_matches_pre');

        if (isset($filters['count'])) {
            return $result->num_rows();
        }

        foreach ($result->result_array() as $line) {
            $temp = $this->team_pre_model->get_team($line['team1_pre']);
            $line['team1'] = $temp['name'];
            $temp = $this->team_pre_model->get_team($line['team2_pre']);
            $line['team2'] = $temp['name'];
            
            $line['competition_id'] ? $line['ok_competition'] = 1 : $line['ok_competition'] = 0;
            $line['team1'] ? $line['ok_team1'] = 1 : $line['ok_team1'] = 0;
            $line['team2'] ? $line['ok_team2'] = 1 : $line['ok_team2'] = 0;
            
           
            $row[] = $line;
        }
        
        //print '<pre>';
        //print_r($row);
        //print '</pre>';

        return $row;
    }

    function get_matches_by_team_id_partial($filters) 
    {
        $this->load->model('team_pre_model');
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
            $this->db->join('z_competitions_pre', 'z_matches_pre.competition_id = z_competitions_pre.competition_id', 'inner');
            $this->db->join('z_countries', 'z_competitions_pre.country_id = z_countries.ID', 'left');
        }

        $this->db->or_where('team1', $filters['team_id']);
        $this->db->or_where('team2', $filters['team_id']);
        if (isset($filters['count'])) {
            $this->db->select('*');
        } else {
            $this->db->select('*,z_competitions_pre.name AS competition_name,z_matches_pre.link_complete AS link_match');
            $this->db->order_by('z_matches_pre.match_date');
        }

        if (isset($filters['limit'])) {
            $offset = (isset($filters['offset'])) ? $filters['offset'] : 0;
            $this->db->limit($filters['limit'], $offset);
        }

        $result = $this->db->get('z_matches_pre');

        if (isset($filters['count'])) {
            return $result->num_rows();
        }

        foreach ($result->result_array() as $line) {
            $temp = $this->team_pre_model->get_team($line['team1']);
            $line['team1'] = $temp['name'];
            $temp = $this->team_pre_model->get_team($line['team2']);
            $line['team2'] = $temp['name'];

            $row[] = $line;
        }

        return $row;
    }

    function get_matches_by_team_id_simple($filters) 
    {
        $this->load->model('team_pre_model');

        $this->db->or_where('team1', $filters['team_id']);
        $this->db->or_where('team2', $filters['team_id']);
        $this->db->select('*');

        $result = $this->db->get('z_matches_pre');

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
        $this->db->insert('z_matches_pre', $insert_fields);
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

        $this->db->update('z_matches_pre', $update_fields, array('index' => $id));
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
        
        if (isset($match['team1_pre'])) {
            $this->db->where('team1_pre', $match['team1_pre']);
        }
        
        if (isset($match['team2_pre'])) {
            $this->db->where('team2_pre', $match['team2_pre']);
        }

        $result = $this->db->get('z_matches_pre');

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
        $this->load->model('team_pre_model');
        $match_pre = $this->get_match_pre($id);
        // delete teams pre
        $this->team_pre_model->delete_team($match_pre['team1_pre']);
        $this->team_pre_model->delete_team($match_pre['team2_pre']);
        // delete match pre
        $this->db->delete('z_matches_pre', array('index' => $id));
        return true;
    }

    function get_no_of_matches_by_team_id($team_id) 
    {
        $this->db->or_where('team1', $team_id);
        $this->db->or_where('team2', $team_id);

        $result = $this->db->get('z_matches_pre');

        return $result->num_rows();
    }

    function get_no_of_matches_by_competition_id($competition_id) 
    {
        $this->db->or_where('competition_id_pre', $competition_id);

        $result = $this->db->get('z_matches_pre');

        return $result->num_rows();
    }

    function fix_score()
    {
        $row = array();
        $result = $this->db->get('z_matches_pre');

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
    
    function move_matches_pre()
    {
        $this->load->model('competition_pre_model');
        $this->load->model('team_pre_model');
        $this->load->model('match_model');
                        
        $result = $this->db->get('z_matches_pre');
        $insert_fields = array();
        $i = 0;
        
        foreach ($result->result_array() as $linie) {
            $temp = $this->competition_pre_model->get_competition($linie['competition_id_pre']);
            $insert_fields['competition_id'] = $temp['competition_id'];
            $temp = $this->team_pre_model->get_team($linie['team1_pre']);
            $insert_fields['team1'] = $temp['team_id'];
            $temp = $this->team_pre_model->get_team($linie['team2_pre']);
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

            //print '<pre>';
            //print_r($linie);
            //print_r($insert_fields);
            //print '</pre>';
            //die;
        }
        
        // truncate pre tables !!!
        $this->db->query('truncate table z_competitions_pre');
        $this->db->query('truncate table z_teams_pre');
        $this->db->query('truncate table z_matches_pre');
        
        return $i;
    }
    
    /**
     * Truncates the table
     * 
     * @return boolean
     */
    function clear_table()
    {
        $this->db->truncate('z_matches_pre');
        return true;
    }
    
    public function make_links()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $this->load->model(array('team_model', 'team_pre_model'));
        
        $matches = $this->get_matches();
        $link = null;
        foreach ($matches as $match) {
            //print '<pre>';
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
                      
            $firstTeam = $this->team_pre_model->get_team($match['team1_pre']);
            $secondTeam = $this->team_pre_model->get_team($match['team2_pre']);
            
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
            
//            print '<pre>';
//            print_r($match);
//            print '<pre>FIRST';
//            print_r($firstTeam);
//            print '<pre>SECOND';
//            print_r($secondTeam);
            
            if (isset($firstTeam['team_id'])) {
                $this->team_pre_model->update_team(array('link' => $team1), $match['team1_pre']);
            } else {
                if (!isset($firstTeam['link'])) {
                    $foundFirstTeam = $this->team_model->get_team_by_link(array('link' => $team1));

                    if (!empty($foundFirstTeam)) {
                        $this->team_pre_model->update_team(array('link' => $team1, 'team_id' => $foundFirstTeam['team_id'], 'country_id' => null, 'name' => null), $match['team1_pre']);
                    } else {
                        $this->team_pre_model->update_team(array('link' => $team1), $match['team1_pre']);
                    }
                }
            }                        
            
            if (isset($secondTeam['team_id'])) {
                $this->team_pre_model->update_team(array('link' => $team2), $match['team2_pre']);
            } else {
                if (!isset($secondTeam['link'])) {
                    $foundSecondTeam = $this->team_model->get_team_by_link(array('link' => $team2));                    

                    if (!empty($foundSecondTeam)) {
                        $this->team_pre_model->update_team(array('link' => $team2, 'team_id' => $foundSecondTeam['team_id'], 'country_id' => null, 'name' => null), $match['team2_pre']);
                    } else {
                        $this->team_pre_model->update_team(array('link' => $team2), $match['team2_pre']);
                    }                               
                }
            }                                    
        }               
    }
}
