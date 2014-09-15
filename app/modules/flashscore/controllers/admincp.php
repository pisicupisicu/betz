<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
    
/**
 * Content Control Panel FLASHSCORE
 * 
 * Displays all control panel forms, datasets, and other displays
 *
 * @author Weblight.ro
 * @copyright Weblight.ro
 * @package BJ Tool
 *
 */
class ParsedFlag {

    const DONT_EXIST = 0;
    const PARSED = 1;
    const NOT_PARSED = 2;

}

class Admincp extends Admincp_Controller 
{

    function __construct() {

        parent::__construct();

        $this->admin_navigation->parent_active('flashscore');

        //error_reporting(E_ALL^E_NOTICE);
        //error_reporting(E_WARNING);
    }

    function index() 
    {
        $this->load->parse_form();
    }

    function parse_form() 
    {
        $this->load->library('admin_form');

        $form = new Admin_Form;

        $form->fieldset('File');
        $form->file('HTML File', 'userfile', '250px', true);

        // Compile View data
        $data = array(
            'form' => $form->display(),
            'form_action' => site_url('admincp/flashscore/get_event_ids')
        );

        $this->load->view('parse_form.php', $data);
    }

    function get_event_ids() 
    {
        $this->load->model('event_model');

        if (isset($_FILES['userfile']) 
            && isset($_FILES['userfile']['tmp_name']) 
            && is_uploaded_file($_FILES['userfile']['tmp_name'])) {
            // Read the file in

            $content = file_get_contents($_FILES['userfile']['tmp_name']);

            $pattern = '@<i></i>(.*?)</a>@';
            preg_match_all($pattern, $content, $date);

            $date_not = $date[1][0];
            $date_short = substr($date_not, 0, -3);
            $array_date = explode('/', $date_short);
            $event_date = date("Y") . "-" . $array_date[1] . "-" . $array_date[0];

//              echo $event_date;

            $pattern = '@id="g_1_(.*?)"@';

            preg_match_all($pattern, $content, $ids);
//              print '<pre> IDS: ';
//              print_r ($ids[1]); 

            $counter = $duplicate = 0;
            $insert_fields = array();

            foreach ($ids[1] as $value) {

                $flag = $this->event_model->duplicate_id($value);

                if ($flag == 1) {
                    $duplicate ++;
                } else {
                    $counter ++;
                    $insert_fields = array(
                        'event_id' => $value,
                        'event_date' => $event_date
                    );

                    $this->event_model->new_event_id($insert_fields);

//                  if ($counter = 2) break;
                }
            }

            echo "{$counter} ID-uri au fost adaugate.<br>{$duplicate} NU au fost adaugate fiind duplicat<br>";
            echo "<a href='parse_form'>Back to parse form</a>";
        } else {
            return redirect(site_url('admincp/flashscore/parse_form'));
        }
    }

    function parse_matches() 
    {
        $this->load->model('event_model');
        $match = $this->event_model->get_next_match();

        foreach ($match as $val) {

            if (empty($val)) {
                die('All matches are already parsed');
            }
//            echo "<pre>";
//print_r ($val);
            $this->match_summary($val['ID'], $val['event_id']);

            echo '<META http-equiv="refresh" content="2;URL=/admincp/flashscore/parse_matches/">';
        }
//die;
    }

    function match_summary($id, $event_id) 
    {
        $this->load->model('country_model');
        $this->load->model('team_model');
        $this->load->model('competition_model');
        $this->load->model('event_model');

        header('Content-Type: text/html;charset=utf-8');
        $content = file_get_contents("http://www.flashscore.com/match/$event_id/#match-summary");
        //$content = file_get_contents("http://www.flashscore.com/match/QwQleago/#match-summary"); // Coupe de France Probleme
        //$content = file_get_contents("http://www.flashscore.com/match/r9eYGt2E/#match-summary"); // england
        //$content = file_get_contents("http://www.flashscore.com/match/xOp7B2zr/#match-summary"); // germany
        //$content = file_get_contents("http://www.flashscore.com/match/4IAfUzmt/#match-summary"); // holland
        //$content = file_get_contents("http://www.flashscore.com/match/UNpqn8t5/#match-summary"); // probleme score


        $pattern_country = '@<div class="fleft">\s*<span class="(.*?)"></span>(.*?)\s*</div>@';
        preg_match_all($pattern_country, $content, $date);

        $array_data = explode(':', $date[2][0]);

        $country_name = $array_data[0];
        $competition_name = trim($array_data[1]);
        echo "$country_name<br>$competition_name";

        //echo $content;

        $pattern_team = '@<tr><td rowspan="3" class="tlogo-home"><img width="50" height="50" src="(.*?)" alt="(.*?)"/></td><td class="tname-home logo-enable">(.*?)</td><td class="current-result">(.*?)</td><td class="tname-away logo-enable">(.*?)</td><td rowspan="3" class="tlogo-away"><img width="50" height="50" src="(.*?)" alt="(.*?)"/></td></tr>@';

        preg_match_all($pattern_team, $content, $date);

        $home_flag = $date[1][0];
        $home_clear = strip_tags($date[3][0]);
        $home_team = str_replace("&nbsp;", '', $home_clear);
        $score = strip_tags($date[4][0]);
        $away_flag = $date[6][0];
        $away_clear = strip_tags($date[7][0]);
        $away_team = str_replace("&nbsp;", '', $away_clear);
        echo "<pre>";
        echo "home flag:{$home_flag}<br>";
        echo "home team:{$home_team}<br>";
        echo $score . "<br>";
        echo "away flag:{$away_flag}<br>";
        echo "away team:{$away_team}<br>";
        print_r($date);
//        die;
        $country_fields = array('country_name' => $country_name);
        $flags = serialize(array('goals_cards' => ParsedFlag::NOT_PARSED, 'lineups' => ParsedFlag::NOT_PARSED, 'coaches' => ParsedFlag::NOT_PARSED, 'statistics' => ParsedFlag::NOT_PARSED, 'h2h' => ParsedFlag::NOT_PARSED, '1x2full' => ParsedFlag::NOT_PARSED, '1x2first' => ParsedFlag::NOT_PARSED, '1x2second' => ParsedFlag::NOT_PARSED, 'CS' => ParsedFlag::NOT_PARSED, 'OUfull' => ParsedFlag::NOT_PARSED, 'OUfirst' => ParsedFlag::NOT_PARSED, 'OUsecond' => ParsedFlag::NOT_PARSED));

        if (!$this->country_model->country_exists($country_fields)) {

            $country_id = $this->country_model->new_country($country_fields);

            //add competition;
            $competition_fields = array('country_id' => $country_id, 'competition_name' => $competition_name);
            $competition_id = $this->competition_model->new_competition($competition_fields);

            //add teams depending on conuntry id;

            $team_fields = array('team1' => array('country_id' => $country_id, 'team_name' => $home_team, 'logo' => $home_flag),
                'team2' => array('country_id' => $country_id, 'team_name' => $away_team, 'logo' => $away_flag));

            $teams_id = $this->team_model->new_teams($team_fields);

            $team1 = $teams_id[0];
            $team2 = $teams_id[1];

            //add match summary;

            $match_fields = array('competition_id' => $competition_id,
                'team1' => $team1,
                'team2' => $team2,
                'score' => $score,
                'parsed' => 1,
                'parsed_date' => date('Y-m-d'),
                'parse_flags' => $flags);
            $match_id = $this->event_model->new_match($match_fields, $id, $event_id); // sa bage in linia cu event_id corespunzator
            print_r($match_fields);
//            die;
        } else {
            $country_id = $this->country_model->country_exists($country_fields);
            echo $country_id;
//           die;

            $competition_search = array('competition_name' => $competition_name);

            //verify if COMPETITION exists then return id of the newly added COMPETITION
            if (!$this->competition_model->competition_exists($competition_search)) {
                $competition_fields = array('country_id' => $country_id, 'competition_name' => $competition_name);
                $competition_id = $this->competition_model->new_competition($competition_fields);
                echo $competition_id;
            }

            $competition_id = $this->competition_model->competition_exists($competition_search);

            $team_fields = array('team1' => array('country_id' => $country_id, 'team_name' => $home_team, 'logo' => $home_flag),
                'team2' => array('country_id' => $country_id, 'team_name' => $away_team, 'logo' => $away_flag));

            //verify if TEAMS exists then return ids of the newly added TEAMS                           
            $teams = array();
            foreach ($team_fields as $val) {
                if (!$this->team_model->team_exists($val)) {
                    $team_id = $this->team_model->new_team($val);
                    echo "nu exista si a fost adaugat acum" . $team_id . "<br>";
                } else {
                    $team_id = $this->team_model->team_exists($val);
                    echo "exista si afisez doar id-u" . $team_id . "<br>";
                }
                $teams[] = $team_id;
            }

//           print_r ($teams);
            $team1 = $teams[0];
            $team2 = $teams[1];

            $match_fields = array('competition_id' => $competition_id,
                'team1' => $team1,
                'team2' => $team2,
                'score' => $score,
                'parsed' => 1,
                'parsed_date' => date('Y-m-d'),
                'parse_flags' => $flags);
            $match_id = $this->event_model->new_match($match_fields, $id, $event_id); // sa bage in linia cu event_id corespunzator
            print_r($match_fields);
//         die;
        }
        $i = 0;
        echo $i++;
    }

    function list_matches() 
    {
        $this->load->model('event_model');
        $this->load->library('dataset');

        $filters = array();
        $filters['parsed'] = 0;
        $unparsed = $this->event_model->get_num_rows($filters);

        $this->admin_navigation->module_link('Parse results :' . $unparsed, site_url('admincp/flashscore/parse_matches'));
        $this->admin_navigation->module_link('Add match', site_url('admincp/flashscore/parse_form'));

        $columns = array(
            array(
                'name' => 'COUNTRY',
                'width' => '10%',
                'filter' => 'country_name',
                'type' => 'text',
                'sort_column' => 'country_name',
            ),
            array(
                'name' => 'COMPETITION',
                'width' => '10%',
                'filter' => 'competition_name',
                'type' => 'name',
                'sort_column' => 'competition_name',
            ),
            array(
                'name' => 'DATE',
                'width' => '15%',
                'filter' => 'match_date',
                'type' => 'date',
                'field_start_date' => '2013-01-01',
                'field_end_date' => '2013-12-31',
                'sort_column' => 'match_date',
            ),
            array(
                'name' => 'HOME',
                'width' => '15%',
                'filter' => 'team1',
                'type' => 'text',
                'sort_column' => 'team1',
            ),
            array(
                'name' => 'AWAY',
                'width' => '15%',
                'filter' => 'team2',
                'type' => 'text',
                'sort_column' => 'team2',
            ),
            array(
                'name' => 'SCORE',
                'width' => '5%',
                'filter' => 'score',
                'type' => 'text',
                'sort_column' => 'score',
            ),
            array(
                'name' => 'LINK COMPLETE',
                'width' => '20%',
                'type' => 'text,'
            ),
            array(
                'name' => 'View',
                'width' => '5%',
                'type' => 'text,'
            ),
            array(
                'name' => 'Edit',
                'width' => '5%',
                'type' => 'text,'
            ),
        );

        $filters = array();
        $filters['limit'] = 20;

        if (isset($_GET['filters'])) {
            $filters_decode = unserialize(base64_decode($this->asciihex->HexToAscii($_GET['filters'])));
        }

        $this->dataset->columns($columns);
        $this->dataset->datasource('event_model', 'get_matches', $filters);
        $this->dataset->base_url(site_url('admincp/flashscore/list_matches'));
        $this->dataset->rows_per_page($filters['limit']);

        // total rows
        unset($filters['limit']);
        $total_rows = $this->event_model->get_num_rows($filters);
        $this->dataset->total_rows($total_rows);

        // initialize the dataset
        $this->dataset->initialize();
        // add actions
        $this->dataset->action('Delete', 'admincp/flashscore/delete_match');
        $this->load->view('list_matches');
    }

    function delete_match($contents, $return_url) 
    {

        $this->load->library('asciihex');
        $this->load->model('event_model');

        $contents = unserialize(base64_decode($this->asciihex->HexToAscii($contents)));
        $return_url = base64_decode($this->asciihex->HexToAscii($return_url));

        foreach ($contents as $content) {
            $this->event_model->delete_match($content);
        }

        $this->notices->SetNotice('Match deleted successfully.');

        redirect($return_url);

        return TRUE;
    }

    function clear_teams() 
    {
        //$this->load->model('country_model');
        $this->load->model('team_model');

        $teams = $this->team_model->get_teams();

        foreach ($teams as $key => $val) {
            echo "<pre>";
            $clear_team = str_replace("&nbsp;", '', $val['team_name']);

            $match_fields = array('team_name' => $clear_team);
            $this->team_model->clear_teams($match_fields, $val['ID'], $val['country_id']);

            print_r($val);
        }
    }

    function macro_lineups() 
    {
        $this->load->model('event_model');
//        $flags = serialize(array('goals_cards'=>ParsedFlag::NOT_PARSED,'lineups'=>ParsedFlag::NOT_PARSED,'coaches'=>ParsedFlag::NOT_PARSED,'statistics'=>ParsedFlag::NOT_PARSED,'h2h'=>ParsedFlag::NOT_PARSED,'1x2full'=>ParsedFlag::NOT_PARSED,'1x2first'=>ParsedFlag::NOT_PARSED,'1x2second'=>ParsedFlag::NOT_PARSED,'CS'=>ParsedFlag::NOT_PARSED,'OUfull'=>ParsedFlag::NOT_PARSED,'OUfirst'=>ParsedFlag::NOT_PARSED,'OUsecond'=>ParsedFlag::NOT_PARSED));

        $match = $this->event_model->get_matches();
        echo "VERSION BUILD=8810214 RECORDER=FX <br> TAB T=1 <br>";

        foreach ($match as $val) {
            echo "URL GOTO=http://www.flashscore.com/match/{$val['event_id']}/#lineups;1<br>
                  WAIT SECONDS=1<br>
                  SAVEAS TYPE=CPL FOLDER=* FILE=+_{$val['event_id']}-lineups<br>";
//            echo "<pre>";
//            print_r ($val['event_id']);
//            echo "</pre>";
        }
    }

    function reset_flags() 
    {
        $this->load->model('event_model');
        $flags = serialize(array('goals_cards' => ParsedFlag::NOT_PARSED, 'lineups' => ParsedFlag::NOT_PARSED, 'coaches' => ParsedFlag::NOT_PARSED, 'statistics' => ParsedFlag::NOT_PARSED, 'h2h' => ParsedFlag::NOT_PARSED, '1x2full' => ParsedFlag::NOT_PARSED, '1x2first' => ParsedFlag::NOT_PARSED, '1x2second' => ParsedFlag::NOT_PARSED, 'CS' => ParsedFlag::NOT_PARSED, 'OUfull' => ParsedFlag::NOT_PARSED, 'OUfirst' => ParsedFlag::NOT_PARSED, 'OUsecond' => ParsedFlag::NOT_PARSED));
        echo $flags;

        $match = $this->event_model->get_matches();

        foreach ($match as $val) {

            $reset_fields = array('parse_flags' => $flags);
            $this->event_model->reset_flags($reset_fields, $val['event_id']);
            // echo "<pre>";
            // print_r ($val['event_id']);
            // echo "</pre>";
        }
    }

    function update_flag() 
    {
        $this->load->model('event_model');
        // $flags = serialize(array('goals_cards'=>ParsedFlag::NOT_PARSED,'lineups'=>ParsedFlag::NOT_PARSED,'coaches'=>ParsedFlag::NOT_PARSED,'statistics'=>ParsedFlag::NOT_PARSED,'h2h'=>ParsedFlag::NOT_PARSED,'1x2full'=>ParsedFlag::NOT_PARSED,'1x2first'=>ParsedFlag::NOT_PARSED,'1x2second'=>ParsedFlag::NOT_PARSED,'CS'=>ParsedFlag::NOT_PARSED,'OUfull'=>ParsedFlag::NOT_PARSED,'OUfirst'=>ParsedFlag::NOT_PARSED,'OUsecond'=>ParsedFlag::NOT_PARSED));
        // echo $flags;

        $event_id = '000Prhac';

//      --------- de pus in lineups --------        
        $match_flag = $this->event_model->getflag_byevent($event_id);

        $actual_flag = unserialize($match_flag);

        print_r($actual_flag);

        // $actual_flag['lineups'] = ParsedFlag::DONT_EXIST;
        // print_r($actual_flag);
    }

    function parse_lineups() 
    {
        $this->load->model('event_model');
        $this->load->model('team_model');
        // echo ParsedFlag::PARSED; die();
//        $content = file_get_contents("http://betz.dev/app/modules/flashscore/assets/lineups/flashscore_00tRMa0f-lineups.htm"); // cu de toate
//        $content = file_get_contents("http://betz.dev/app/modules/flashscore/assets/lineups/flashscore_00Qty0Xu-lineups.htm"); // fara coache
        $content = file_get_contents("http://betz.banujos.ro/app/modules/flashscore/assets/lineups/flashscore_000Prhac-lineups.htm"); // de modificat
//        $content = file_get_contents("http://betz.banujos.ro/app/modules/flashscore/assets/lineups/flashscore_UNpqn8t5-lineups.htm"); // fara coache
//        ----------- forwarding ---------------
//        $content = file_get_contents("http://89.34.231.100/test/folder/flashscore_00tRMa0f-lineups.htm"); // cu de toate
//        $content = file_get_contents("http://89.34.231.100/test/folder/flashscore_00Qty0Xu-lineups.htm"); // fara coache
//        $content = file_get_contents("http://89.34.231.100/test/folder/flashscore_000Prhac-lineups.htm"); // de modificat
//        $content = file_get_contents("http://89.34.231.100/flashscore_downloads/flashscore_00WX8j70-lineups.htm"); // doar match summary
        $event_id = '000Prhac';

        $pattern_lineups = '@<table id="parts"><tbody><tr><td class="h-part" style="width: 35%;"><b>(.*?)</b></td><td class="h-part" style="width: 30%; white-space: nowrap;">Formation</td><td class="h-part"><b>(.*?)</b></td></tr></tbody></table>@';

        preg_match_all($pattern_lineups, $content, $date);

        $home_formation = isset($date[1][0]) ? $date[1][0] : null;
        $away_formation = isset($date[2][0]) ? $date[2][0] : null;

        echo "home lineups: $home_formation";
        echo "<br>";
        echo "away lineups: $away_formation";
        echo "<br>";

        $pattern_coaches = '@<tr><td colspan="2" class="h-part">Coaches</td></tr><tr class="odd"><td class="summary-vertical fl"><div class="time-box"> </div><span title="(.*?)" class="(.*?)"></span><div class="name">(.*?)</div></td><td class="summary-vertical fr"><div class="time-box"> </div><span title="(.*?)" class="(.*?)"></span><div class="name">(.*?)</div></td></tr>@';

        preg_match_all($pattern_coaches, $content, $date);

        $home_coach = isset($date[3][0]) ? $date[3][0] : null;
        $home_nationality = isset($date[1][0]) ? $date[1][0] : null;
        $away_coach = isset($date[6][0]) ? $date[6][0] : null;
        $away_nationality = isset($date[4][0]) ? $date[4][0] : null;

        echo "Home coach: $home_coach Nationality: $home_nationality";
        echo "<br>";
        echo "Away coach: $away_coach Nationality: $away_nationality";

        //get match_id how has the curent event_id
        $match_id = $this->event_model->getid_byevent($event_id);
        echo "<br>Match id:" . $match_id;

        //conditiile de parsare
        if (empty($home_formation) && empty($away_formation)) {
            echo"<br>nu am nimic";
            //update/add flags                
            $match_flag = $this->event_model->getflag_byevent($event_id);

            $actual_flag = unserialize($match_flag);

            $actual_flag['lineups'] = ParsedFlag::DONT_EXIST;
            $actual_flag['coaches'] = ParsedFlag::DONT_EXIST;

            $update_flag = array('parse_flags' => serialize($actual_flag));
            $this->event_model->update_flags($update_flag, $match_id);
        } else {

            echo"<br>da am lineups";
            $lineups_fields = array('match_id' => $match_id,
                'home_formation' => $home_formation,
                'away_formation' => $away_formation);
            $this->team_model->add_lineups($lineups_fields);

            if (empty($home_coach) && empty($away_coach)) {

                echo"<br>nu am coaches";
                //update/add flags                
                $match_flag = $this->event_model->getflag_byevent($event_id);

                $actual_flag = unserialize($match_flag);

                $actual_flag['lineups'] = ParsedFlag::PARSED;
                $actual_flag['coaches'] = ParsedFlag::DONT_EXIST;

                $update_flag = array('parse_flags' => serialize($actual_flag));
                $this->event_model->update_flags($update_flag, $match_id);
            } else {
//----------------Home coach -----------------
                $coaches_fields = array('coach_name' => $home_coach,
                    'nationality' => $home_nationality);

                $home_coach_id = $this->team_model->add_coaches($coaches_fields);

                // echo "<br>Home coach ID:".$home_coach_id."<br>";
                $home_coach_fields = array('coach1' => $home_coach_id);
                $this->event_model->update_home_coach($home_coach_fields, $match_id);

//----------------Away coach -----------------
                $coaches_fields = array('coach_name' => $away_coach,
                    'nationality' => $away_nationality);

                $away_coach_id = $this->team_model->add_coaches($coaches_fields);

                // echo "<br>Away coach ID:".$away_coach_id."<br>";
                $away_coach_fields = array('coach2' => $away_coach_id);
                $this->event_model->update_away_coach($away_coach_fields, $match_id);

                //update/add flags                
                $match_flag = $this->event_model->getflag_byevent($event_id);

                $actual_flag = unserialize($match_flag);

                $actual_flag['lineups'] = ParsedFlag::PARSED;
                $actual_flag['coaches'] = ParsedFlag::PARSED;

                $update_flag = array('parse_flags' => serialize($actual_flag));
                $this->event_model->update_flags($update_flag, $match_id);
                echo"<br>da am coaches";
            }
        }
        // echo '<META http-equiv="refresh" content="3;URL=/admincp/flashscore/parse_lineups/">';
    }

}
