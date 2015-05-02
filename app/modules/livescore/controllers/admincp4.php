<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Content Control Panel
 * 
 * Displays all control panel forms, datasets, and other displays
 *
 * @author Weblight.ro
 * @copyright Weblight.ro
 * @package BJ Tool
 *
 */
class Admincp4 extends Admincp_Controller {

    function __construct()
    {
        parent::__construct();
        $this->admin_navigation->parent_active('livescore');
        //error_reporting(E_ALL^E_NOTICE);
        //error_reporting(E_WARNING);
    }

    function index()
    {
        redirect('admincp/livescore/list_matches');
    }

    /**
     * Add Match
     *
     * Add new Match form, preloaded with variables
     *
     * @param int $id the ID of the bet
     */
    function add_match($action = 'new', $id = false)
    {

        $this->load->model(array('match_model', 'competition_model', 'team_model', 'country_model'));

        $match = $this->match_model->get_match($id);

        $countries = array();
        $params['dropdown'] = 1;
        $countries = $this->country_model->get_countries($params);

        $competition = array();
        $competition = $this->competition_model->get_competitions();
        foreach ($competition as $comp)
        {
            $competition_name[$comp['competition_id']] = $comp['name'];
        }

        $teams = array();
        $teams = $this->team_model->get_teams();
        foreach ($teams as $team)
        {
            $team_name[$team['team_id']] = $team['name'];
            $team_country[$team['country_id']] = $team['country_name'];
        }

        //echo"<pre>";
        //print_r ($match);
        //die;

        $data = array(
            'country_name' => $countries,
            'competition_name' => $competition_name,
            'team_name' => $team_name,
            'form' => $match,
            'form_title' => 'Add New Match',
            'form_action' => site_url('admincp4/livescore/post_match/new'),
            'action' => 'new',
        );
        $this->load->view('add_match', $data);
    }

    /**
     * Edit Match
     *
     * Show the Match form, preloaded with variables
     *
     * @param int $id the ID of the bet
     */
    function edit_match($id)
    {

        $this->load->model(array('match_model', 'competition_model', 'team_model', 'goal_model', 'card_model', 'country_model'));

        $match = $this->match_model->get_match($id);

        $filters['country_id'] = $match['country_id'];

        $countries = array();
        $countries = $this->country_model->get_countries();

        $competition = array();
        $competition = $this->competition_model->get_competitions($filters);
        foreach ($competition as $comp)
        {
            $competition_name[$comp['competition_id']] = $comp['name'];
        }

        $mins = range(1, 120);
        $minutes = array();
        foreach ($mins as $key => $val)
        {
            $minutes[$key] = $val;
        }

        $card_type = array('yellow' => 'yellow', 'red' => 'red', 'yellow_red' => 'yellow_red');
        $team_type = array('home' => 'home', 'away' => 'away');

        $teams = array();
        $teams = $this->team_model->get_teams($filters);
        foreach ($teams as $team)
        {
            $team_name[$team['team_id']] = $team['name'];
            $team_country[$team['country_id']] = $team['country_name'];
        }

        if (!isset($match) || empty($match))
        {
            $this->notices->SetError('No match found');
            redirect('admincp/livescore/list_matches');
        }

        $home = $this->team_model->get_team($match['team1']);
        $away = $this->team_model->get_team($match['team2']);
        $goals = $this->goal_model->get_goals_by_match($id);
        $cards = $this->card_model->get_cards_by_match($id);

        $data = array(
            'match' => $match,
            'home' => $home,
            'away' => $away,
            'goals' => $goals,
            'cards' => $cards,
            'minutes' => $minutes,
            'team_type' => $team_type,
            'card_type' => $card_type,
            'id_match' => $match['id'],
            'id_country' => $match['country_id'],
            'country_name' => $countries,
            'id_competition' => $match['competition_id'],
            'competition_name' => $competition_name,
            'home_team_id' => $match['team1'],
            'away_team_id' => $match['team2'],
            'team_name' => $team_name,
            'score' => $match['score'],
            'link' => $match['link_match'],
            'livescore_link' => $match['link_match_complete'],
            'match_date' => $match['match_date'],
            'form' => $match,
            'form_title' => 'Edit Match',
            'form_action' => site_url('admincp4/livescore/post_match/edit/' . $match['id']),
            'action' => 'edit',
        );
        $this->load->view('edit_match', $data);
    }

    /**
     * Handle New/Edit Match Post
     */
    function post_match($action, $id = false)
    {
        $this->load->model('match_model');

        // content
        $ID_match = $this->input->post('ID_match');
        $competition_name = $this->input->post('competition_name');
        $match_date = $this->input->post('match_date');
        $home_team = $this->input->post('home_team');
        $away_team = $this->input->post('away_team');
        $score = $this->input->post('score');
        $link = $this->input->post('link_user');
        $link_complete = $this->input->post('link_complete');

        $update_fields = array(
            //'id'            => $ID_match,
            'competition_id' => $competition_name,
            'match_date' => $match_date,
            'team1' => $home_team,
            'team2' => $away_team,
            'score' => $score,
            'link' => $link,
            'link_complete' => $link_complete,
        );

        $insert_fields = array(
            'competition_id' => $competition_name,
            'match_date' => $match_date,
            'team1' => $home_team,
            'team2' => $away_team,
            'score' => $score,
            'link' => $link,
            'link_complete' => $link_complete,
        );

        //print_r ($insert_fields);
        //die;

        if ($action == 'new')
        {
            $bet_id = $this->match_model->new_match($insert_fields);
            $this->notices->SetNotice('Match added successfully.');
            redirect('admincp4/livescore/step_two/' . $bet_id);
        }
        else
        {
            $bet_id = $this->match_model->update_match($update_fields, $ID_match);
            $this->notices->SetNotice('Match edited successfully.');
        }
        redirect('admincp/livescore/list_matches');
        return true;
    }

    /**
     * Step 2 la formularul de add
     *
     * Show the Add form, preloaded with variables
     *
     * @param int $id the ID of the bet
     */
    function step_two($id)
    {
        $this->load->model(array('match_model', 'competition_model', 'team_model', 'country_model', 'goal_model', 'card_model'));

        $match = $this->match_model->get_match($id);

        //$filters['country_id'] = $match['country_id'];
        $countries = array();
        $params['dropdown'] = 1;
        $countries = $this->country_model->get_countries($params);

        $competition = array();
        $competition = $this->competition_model->get_competitions();
        foreach ($competition as $comp)
        {
            $competition_name[$comp['competition_id']] = $comp['name'];
        }

        $teams = array();
        $teams = $this->team_model->get_teams();
        foreach ($teams as $team)
        {
            $team_name[$team['team_id']] = $team['name'];
            $team_country[$team['country_id']] = $team['country_name'];
        }

        $home = $this->team_model->get_team($match['team1']);
        $away = $this->team_model->get_team($match['team2']);
        $goals = $this->goal_model->get_goals_by_match($id);
        $cards = $this->card_model->get_cards_by_match($id);

        $data = array(
            'match' => $match,
            'home' => $home,
            'away' => $away,
            'goals' => $goals,
            'cards' => $cards,
            'id_match' => $match['id'],
            'id_country' => $match['country_id'],
            'country_name' => $countries,
            'id_competition' => $match['competition_id'],
            'competition_name' => $competition_name,
            'home_team_id' => $match['team1'],
            'away_team_id' => $match['team2'],
            'team_name' => $team_name,
            'score' => $match['score'],
            'link' => $match['link_match'],
            'livescore_link' => $match['link_match_complete'],
            'match_date' => $match['match_date'],
            'form' => $match,
            'form_title' => 'Add Score to this match',
            'form_action' => site_url('admincp4/livescore/post_step/new/' . $match['id']),
            'action' => 'step',
        );
        $this->load->view('add_match', $data);
    }

    /**
     * Handle NEW Score for Match
     */
    function post_step($id = false)
    {
        $this->load->model(array('goal_model', 'card_model', 'match_model'));
        // content
        $ID_match = $this->input->post('ID_match');
        $minutes_select = $this->input->post('minutes_select');
        $event_types = $this->input->post('event_types');
        $card_types = $this->input->post('card_types');
        $card_owner = $this->input->post('card_owner');
        $score_step = $this->input->post('score_step');
        $goal_scorer = $this->input->post('goal_scorer');
        $assist = $this->input->post('assist');
        $type = $this->input->post('type');
        $team_types = $this->input->post('team_types');

        if ($event_types == "card")
        {
            $insert_fields = array(
                'match_id' => $ID_match,
                'card_type' => $card_types,
                'min' => $minutes_select,
                'player' => $card_owner,
                'team' => $team_types,
            );
            $this->card_model->new_card($insert_fields);
            $this->notices->SetNotice('Score added successfully.');
            redirect('admincp4/livescore/step_two/' . $ID_match);
            //print_r ($card_fields);
            //die;
        }
        else
        {
            $insert_fields = array(
                'match_id' => $ID_match,
                'score' => $score_step,
                'min' => $minutes_select,
                'assist' => $assist,
                'type' => $type,
                'player' => $goal_scorer,
                'team' => $team_types,
            );
            $this->goal_model->new_goal($insert_fields);
            $this->notices->SetNotice('Score added successfully.');
            redirect('admincp4/livescore/step_two/' . $ID_match);
            //print_r ($card_fields);
            //die; 	
        }
        return true;
    }

    /**
     * Delete Goal from step two table
     *
     */
    function delete_goal($action, $ID_match, $ID_goal)
    {
        $this->load->model('goal_model');

        $this->goal_model->delete_goal($ID_goal);
        $this->notices->SetNotice('Goal deleted successfully.');
        if ($action == 'add')
        {
            redirect('admincp4/livescore/step_two/' . $ID_match);
        }
        else
        {
            redirect('admincp4/livescore/edit_match/' . $ID_match);
        }
    }

    /**
     * Delete Card from step two table
     *
     */
    function delete_card($action, $ID_match, $ID_card)
    {
        $this->load->model('card_model');

        $this->card_model->delete_card($ID_card);
        $this->notices->SetNotice('Card deleted successfully.');
        if ($action == 'add')
        {
            redirect('admincp4/livescore/step_two/' . $ID_match);
        }
        else
        {
            redirect('admincp4/livescore/edit_match/' . $ID_match);
        }
    }

    /**
     * Update Goal from score edit
     *
     */
    function update_goal($ID_goal)
    {
        $this->load->model('goal_model');

        $score = $this->input->post('score');
        $minutes_goal = $this->input->post('minutes_goal');
        $assist = $this->input->post('assist');
        $type = $this->input->post('type');
        $goal_scorer = $this->input->post('goal_scorer');
        $goal_team = $this->input->post('goal_team');
        $update_fields = array(
            'score' => $score,
            'min' => $minutes_goal,
            'assist' => $assist,
            'type' => $type,
            'player' => $goal_scorer,
            'team' => $goal_team,
        );
        $this->goal_model->update_goal($update_fields, $ID_goal);
    }

    /**
     * Update Goal from score edit
     *
     */
    function update_card($ID_card)
    {
        $this->load->model('card_model');

        $minutes_card = $this->input->post('minutes_card');
        $card_type = $this->input->post('card_type');
        $card_owner = $this->input->post('card_owner');
        $card_team = $this->input->post('card_team');
        $update_fields = array(
            'card_type' => $card_type,
            'min' => $minutes_card,
            'player' => $card_owner,
            'team' => $card_team,
        );
        $this->card_model->update_card($update_fields, $ID_card);
    }

    /**
     * View Competition Selects description
     *
     */
    function view_competitions_selects($id)
    {
        $this->load->model('competition_model');

        $filters['country_id'] = $id;

        $competition = array();
        $competition = $this->competition_model->get_competitions($filters);
        foreach ($competition as $comp)
        {

            $competition_name[$comp['competition_id']] = $comp['name'];
        }
        if (empty($competition_name))
            $competition_name = array(0 => 'None');

        echo form_dropdown('competition_name', $competition_name);
    }

    function view_competitions_selects_selected($id_country, $id_competition)
    {
        $this->load->model(array('competition_model', 'country_model'));
        $filters['country_id'] = $id_country;

        $countries = array();
        $params['dropdown'] = 1;
        $countries = $this->country_model->get_countries();

        $competition = array();
        $competition = $this->competition_model->get_competitions($filters);
        foreach ($competition as $comp)
        {
            $competition_name[$comp['competition_id']] = $comp['name'];
        }
        echo form_dropdown('competition_name', $competition_name, $id_competition);
    }

    /**
     * View Home team Selects description
     *
     */
    function view_hometeam_selects($id)
    {
        $this->load->model('team_model');
        $filters['country_id'] = $id;

        $teams = array();
        $teams = $this->team_model->get_teams($filters);

        foreach ($teams as $team)
        {
            $team_name[$team['team_id']] = $team['name'];
        }

        if (empty($team_name))
            $team_name = array(0 => 'None');
        echo form_dropdown('home_team', $team_name);
    }

    function view_hometeam_selects_selected($id, $home_team_id)
    {
        $this->load->model('team_model');
        $filters['country_id'] = $id;

        $teams = array();
        $teams = $this->team_model->get_teams($filters);

        foreach ($teams as $team)
        {
            $team_name[$team['team_id']] = $team['name'];
        }

        if (empty($team_name))
            $team_name = array(0 => 'None');
        echo form_dropdown('home_team', $team_name, $home_team_id);
    }

    /**
     * View Home team Selects description
     *
     */
    function view_awayteam_selects($id)
    {
        $this->load->model('team_model');
        $filters['country_id'] = $id;

        $teams = array();
        $teams = $this->team_model->get_teams($filters);

        foreach ($teams as $team)
        {
            $team_name[$team['team_id']] = $team['name'];
        }

        if (empty($team_name))
            $team_name = array(0 => 'None');
        echo form_dropdown('away_team', $team_name);
    }

    function view_awayteam_selects_selected($id, $away_team_id)
    {
        $this->load->model('team_model');
        $filters['country_id'] = $id;

        $teams = array();
        $teams = $this->team_model->get_teams($filters);

        foreach ($teams as $team)
        {
            $team_name[$team['team_id']] = $team['name'];
        }

        if (empty($team_name))
            $team_name = array(0 => 'None');
        echo form_dropdown('away_team', $team_name, $away_team_id);
    }

    function get_old_match($id)
    {
        $this->load->model('match_model');
        $match = $this->match_model->get_match($id);
        //iei meciu
        echo json_encode($match);
    }

    //**************************** Edit *******************//

    function edit_score($id)
    {
        $this->load->model(array('match_model', 'team_model', 'goal_model', 'card_model'));

        $match = $this->match_model->get_match($id);
        if (!isset($match) || empty($match))
        {
            $this->notices->SetError('No match found');
            redirect('admincp/livescore/list_matches');
        }

        $home = $this->team_model->get_team($match['team1']);
        $away = $this->team_model->get_team($match['team2']);
        $goals = $this->goal_model->get_goals_by_match($id);
        $cards = $this->card_model->get_cards_by_match($id);

        //print_r($cards);

        $data = array(
            'match' => $match,
            'home' => $home,
            'away' => $away,
            'goals' => $goals,
            'cards' => $cards,
        );

        $this->load->view('view_match', $data);
    }

    //********************************** CURRENCIES LIST *******************************//         
    /**
     * Manage Currencies
     *
     * Lists active currencies for managing
     */
    function list_currencies()
    {
        $this->load->library('dataset');
        $this->load->model('currency_model');

        $this->admin_navigation->module_link('Add New Currency', site_url('admincp4/livescore/add_currency'));

        $columns = array(
            array(
                'name' => 'Flag',
                'type' => 'text',
                'width' => '10%',
            ),
            array(
                'name' => 'Country',
                'type' => 'text',
                'width' => '35%',
            ),
            array(
                'name' => 'Currency',
                'type' => 'text',
                'width' => '30%',
            ),
            array(
                'name' => 'Code ISO',
                'type' => 'text',
                'width' => '15%',
            ),
            array(
                'name' => 'Symbol',
                'type' => 'text',
                'width' => '5%',
            ),
            array(
                'name' => '',
                'type' => 'text',
                'width' => '5%',
            ),
        );

        $filters = array();
        $filters['limit'] = 30;

        if (isset($_GET['offset']))
            $filters['offset'] = $_GET['offset'];


        $this->dataset->columns($columns);
        $this->dataset->datasource('currency_model', 'get_currencies', $filters);
        $this->dataset->base_url(site_url('admincp4/livescore/list_currencies'));
        $this->dataset->rows_per_page($filters['limit']);

        // total rows
        $total_rows = $this->currency_model->num_rows_currencies($filters);
        //die (print($total_rows));
        $this->dataset->total_rows($total_rows);

        // initialize the dataset
        $this->dataset->initialize();

        // add actions
        $this->dataset->action('Delete', 'admincp4/livescore/delete_currency');

        $this->load->view('list_currencies');
    }

    /**
     * Add New currency
     *
     */
    function add_currency()
    {
        $this->load->model(array('currency_model', 'country_model'));

        $countries = array();
        $params['dropdown'] = 1;
        $countries = $this->country_model->get_countries($params);
        $data = array(
            'id_country' => $countries,
            'form_title' => 'Add New Currency',
            'form_action' => site_url('admincp4/livescore/post_currency/new'),
            'action' => 'new'
        );
        $this->load->view('add_currency', $data);
    }

    /**
     * Handle New/Edit currency Post
     */
    function post_currency($action, $id = false)
    {

        $this->load->model(array('currency_model', 'country_model'));

        // content
        $id_currency = $this->input->post('id_currency');
        $flag = $this->input->post('flag');
        $id_country = $this->input->post('id_country');
        $name_currency = $this->input->post('name_currency');
        $code_ISO = $this->input->post('code_ISO');
        $symbol_currency = $this->input->post('symbol_currency');

        if ($action == 'new')
        {
            $insert_fields = array(
                'name_currency' => $name_currency,
                'id_country' => $id_country,
                'flag' => $flag,
                'code_ISO' => $code_ISO,
                'symbol_currency' => $symbol_currency,
            );
            $this->currency_model->new_currency($insert_fields);
            $this->notices->SetNotice('Currency added successfully.');
        }
        else
        {
            $update_fields = array(
                'id_currency' => $id_currency,
                'name_currency' => $name_currency,
                'id_country' => $id_country,
                'flag' => $flag,
                'code_ISO' => $code_ISO,
                'symbol_currency' => $symbol_currency,
            );
            $this->currency_model->update_currency($update_fields, $id_currency);
            $this->notices->SetNotice('Currency edited successfully.');
        }
        redirect('admincp4/livescore/list_currencies');
        return true;
    }

    /**
     * Edit Currency
     *
     * Show the currency form, preloaded with variables
     *
     * @param int $id the ID of the currency
     *
     */
    function edit_currency($id)
    {
        $this->load->model(array('currency_model', 'country_model'));

        $currency = $this->currency_model->get_currency($id);

        $countries = array();
        $params['dropdown'] = 1;
        $countries = $this->country_model->get_countries($params);

        $data = array(
            'id_currency' => $currency['id_currency'],
            'country_name' => $currency['id_country'],
            'id_country' => $countries,
            'flag' => $currency['flag'],
            'name_currency' => $currency['name_currency'],
            'code_ISO' => $currency['code_ISO'],
            'symbol_currency' => $currency['symbol_currency'],
            'form' => $currency,
            'form_title' => 'Edit Currency',
            'form_action' => site_url('admincp4/livescore/post_currency/edit/' . $currency['id_currency']),
            'action' => 'edit',
        );
        //var_dump ($data);
        //die;		
        $this->load->view('add_currency', $data);
    }

    /**
     * Delete currency
     */
    function delete_currency($contents, $return_url)
    {
        $this->load->library('asciihex');
        $this->load->model('currency_model');

        $contents = unserialize(base64_decode($this->asciihex->HexToAscii($contents)));
        $return_url = base64_decode($this->asciihex->HexToAscii($return_url));

        foreach ($contents as $content)
        {
            $this->currency_model->delete_currency($content);
        }
        $this->notices->SetNotice('Currency deleted successfully.');
        redirect($return_url);
        return true;
    }

    //********************************** END CURRENCIES LIST *******************************//
    // ********************** PARSE CURRENCIES & HOUSES*******************************************//

    private function getUrl($url)
    {
        $cUrl = curl_init();
        $headers[] = 'Connection: Keep-Alive';
        $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
        curl_setopt($cUrl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($cUrl, CURLOPT_URL, $url);
        curl_setopt($cUrl, CURLOPT_RETURNTRANSFER, 1);
        $pageContent = curl_exec($cUrl);
        curl_close($cUrl);
        return $pageContent;
    }

    function parse_currencies()
    {
        $this->load->model('currency_model');
        $link = 'http://www.currencysymbols.in/';
        $link = utf8_encode($link);
        $page = $this->getUrl($link);
        // Prima coloana cea cu ligile	
        $pattern = "@<tr>\s*
    <th>(.*)</th>\s*
    <th>(.*)</th>\s*
    <th>(.*)</th>\s*
    <th>(.*)</th>\s*
    <th>(.*)</th>\s*
    <th>(.*)</th>\s*
  </tr>@";
        preg_match_all($pattern, $page, $currencies);

        $db_currency = array(
            'all' => $currencies[0],
            'flag' => $currencies[1],
            'Country' => $currencies[2],
            'Currencies' => $currencies[3],
            'Code_iso' => $currencies[4],
            'Symbol' => $currencies[4],
        );
        $flags = array();
        foreach ($db_currency['flag'] as $val)
        {
            //<img src="flags/albania.png" alt="Albania flag" height="25" width="35" />       
            $pattern = '@<img src="flags/(.*)" alt="(.*)" height="25" width="35" />@';
            preg_match_all($pattern, $val, $flag);
            $flags[] = $flag[1][0];
        }

        $currency_arr = array(
            'id_country' => $currencies[2],
            'flag' => $flags,
            'name_currency' => $currencies[3],
            'code_ISO' => $currencies[4],
            'symbol_currency' => $currencies[5],
        );

        foreach ($currency_arr as $cheie => $param)
        {
            foreach ($param as $key => $val)
            {
                $insert_fields[$key] = array(
                    'id_country' => $currency_arr['id_country'][$key],
                    'flag' => $flags[$key],
                    'name_currency' => $currency_arr['name_currency'][$key],
                    'code_ISO' => $currency_arr['code_ISO'][$key],
                    'symbol_currency' => $currency_arr['symbol_currency'][$key],
                );
                echo "<pre>";
                print_r($insert_fields[$key]);
                //$match = $this->currency_model->new_currency($insert_fields[$key]);
                // sa nu uiti sa pui unique in DB la name ca le baga de X ori inca odata pe toate
            }
        }
    }

    function parse_houses()
    {
        $this->load->model('house_model');
        $link = 'http://www.top100bookmakers.com/rating.php';
        $link = utf8_encode($link);
        $page = $this->getUrl($link);

        $pattern = '@<img class="button" alt="(.*)" src="buttons/(.*)"></a></td>@';

        preg_match_all($pattern, $page, $houses);

        $house_arr = array(
            'name_house' => $houses[1],
            'logo_house' => $houses[2],
        );

        foreach ($house_arr as $cheie => $param)
        {
            foreach ($param as $key => $val)
            {
                $insert_fields[$key] = array(
                    'name_house' => $house_arr['name_house'][$key],
                    'logo_house' => $house_arr['logo_house'][$key],
                );
                echo "<pre>";
                print_r($insert_fields[$key]);
                //$match = $this->house_model->new_house($insert_fields[$key]);
                // sa nu uiti sa pui unique DB la name ca le baga de X ori inca odata pe toate
            }
        }
    }

    //********************************** END PARSE & Houses CURRENCIES *******************************//
    //**********************************  Bookmaker HOUSES  LIST *******************************//         
    /**
     * Manage houses
     *
     * Lists active houses for managing
     */
    function list_houses()
    {
        $this->load->library('dataset');
        $this->load->model('house_model');

        $this->admin_navigation->module_link('Add New Currency', site_url('admincp4/livescore/add_house'));

        $columns = array(
            array(
                'name' => 'Logo',
                'type' => 'text',
                'width' => '10%',
            ),
            array(
                'name' => 'Bookmakers',
                'type' => 'text',
                'width' => '45%',
            ),
            array(
                'name' => 'Web Link',
                'type' => 'text',
                'width' => '40%',
            ),
            array(
                'name' => '',
                'type' => 'text',
                'width' => '5%',
            ),
        );

        $filters = array();
        $filters['limit'] = 30;

        if (isset($_GET['offset']))
            $filters['offset'] = $_GET['offset'];


        $this->dataset->columns($columns);
        $this->dataset->datasource('house_model', 'get_houses', $filters);
        $this->dataset->base_url(site_url('admincp4/livescore/list_houses'));
        $this->dataset->rows_per_page($filters['limit']);

        $total_rows = $this->house_model->num_rows_houses($filters);
        //die (print($total_rows));
        $this->dataset->total_rows($total_rows);

        // initialize the dataset
        $this->dataset->initialize();

        // add actions
        $this->dataset->action('Delete', 'admincp4/livescore/delete_house');

        $this->load->view('list_houses');
    }

    /**
     * Add New house
     *
     */
    function add_house()
    {

        $this->load->model('house_model');
        $data = array(
            'form_title' => 'Add New Bookmaker',
            'form_action' => site_url('admincp4/livescore/post_house/new'),
            'action' => 'new'
        );
        $this->load->view('add_house', $data);
    }

    /**
     * Handle New/Edit house Post
     */
    function post_house($action, $id = false)
    {

        $this->load->model('house_model');
        // content
        $id_house = $this->input->post('id_house');
        $name_house = $this->input->post('name_house');
        $logo_house = $this->input->post('logo_house');
        $link_house = $this->input->post('link_house');

        if ($action == 'new')
        {
            $insert_fields = array(
                'name_house' => $name_house,
                'logo_house' => $logo_house,
                'link_house' => $link_house,
            );
            $this->house_model->new_house($insert_fields);
            $this->notices->SetNotice('Bookmaker added successfully.');
        }
        else
        {
            $update_fields = array(
                'id_house' => $id_house,
                'name_house' => $name_house,
                'logo_house' => $logo_house,
                'link_house' => $link_house,
            );
            $this->house_model->update_house($update_fields, $id_house);
            $this->notices->SetNotice('Bookmaker edited successfully.');
        }
        redirect('admincp4/livescore/list_houses');
        return true;
    }

    /**
     * Edit House
     *
     * Show the house form, preloaded with variables
     *
     * @param int $id the ID of the currency
     *
     */
    function edit_house($id)
    {
        $this->load->model('house_model');

        $house = $this->house_model->get_house($id);

        $data = array(
            'id_house' => $house['id_house'],
            'name_house' => $house['name_house'],
            'logo_house' => $house['logo_house'],
            'link_house' => $house['link_house'],
            'form' => $house,
            'form_title' => 'Edit House',
            'form_action' => site_url('admincp4/livescore/post_house/edit/' . $house['id_house']),
            'action' => 'edit',
        );
        //var_dump ($data);
        //die;		
        $this->load->view('add_house', $data);
    }

    /**
     * Delete house
     */
    function delete_house($contents, $return_url)
    {

        $this->load->library('asciihex');
        $this->load->model('house_model');

        $contents = unserialize(base64_decode($this->asciihex->HexToAscii($contents)));
        $return_url = base64_decode($this->asciihex->HexToAscii($return_url));

        foreach ($contents as $content)
        {
            $this->house_model->delete_house($content);
        }
        $this->notices->SetNotice('Bookmaker deleted successfully.');
        redirect($return_url);
        return true;
    }

    //********************************** END Bookmaker HOUSES LIST *******************************//
    // START IMPORT--------------------------------------------------------------------

    public function import_csv()
    {
        $this->load->library('admin_form');

        if ($this->input->post('submit'))
        {
            if (isset($_FILES['userfile']) && isset($_FILES['userfile']['tmp_name']) && is_uploaded_file($_FILES['userfile']['tmp_name']))
            {
                $this->load->library('encrypt');
                $this->load->helper('file');

                // Read the file in
                $content = read_file($_FILES['userfile']['tmp_name']);

                // is this a CSV file?
                if (strpos($content, ',') !== FALSE)
                {
                    // encrypt it and save it to /writable.
                    $content = $this->encrypt->encode($content);

                    write_file($this->config->item('path_writeable') . 'csv_upload.csv', $content, 'w+');
                    return redirect(site_url('admincp4/livescore/fields'));
                }
            }
        }

        $form = new Admin_Form;
        $form->fieldset('File');
        $form->file('CSV File', 'userfile', '250px', true);
        // Compile View data
        $data = array(
            'form' => $form->display(),
            'form_action' => site_url('admincp4/livescore/import_csv')
        );
        $this->load->view('import_csv.php', $data);
    }

    public function fields()
    {
        $this->load->helper('file');
        $data['csv_data'] = $this->read_csv_file();
        $this->load->view('fields', $data);
    }

    public function do_import()
    {
        $this->load->model('market_model');
        $this->load->model('bet_model');
        $data = $duplicate = array();

        $total_imports = 0;

        // Grab our records.
        $records = $this->read_csv_file();
        $duplicates = $inserted = 0;

        // Map each of our fields for importing.
        foreach ($records as $record)
        {
            if ($total_imports < 2)
            {
                $total_imports++;
                continue;
            }

            if (empty($record))
                break;

            //echo "record = $record<br/>";
            $param = array();
            // Split into each field
            $row_fields = explode(',', $record);

            $count = count($row_fields);

            if (!$count)
            {
                echo $record . '<br/>';
            }

            if (!isset($row_fields[2]) || !isset($row_fields[2]))
            {
                echo $record . '<br/>';
            }

            $temp = explode('/', $row_fields[1]);
            $market_type = trim($temp[2]);
            // if Over/Under the market type Over or Under is from the third column : Selection
            if (strstr($market_type, 'Over'))
            {
                $aux = explode(' ', $row_fields[2]);
                $market_type = trim($aux[0]);
                $market_select = trim($aux[1]);
            }
            elseif (strstr($market_type, 'Match'))
            {
                $teams = trim($temp[1]);
                $aux = explode(' v ', $teams);
                $home_team = trim($aux[0]);
                $away_team = trim($aux[1]);

                if ($home_team == $row_fields[2])
                {
                    $market_select = 'Home';
                }
                elseif ($away_team == $row_fields[2])
                {
                    $market_select = 'Away';
                }
                else
                {
                    $market_select = 'Draw';
                }
            }
            elseif (strstr($market_type, 'Correct'))
            {
                $market_select = trim($row_fields[2]);
                $market_select = str_replace(' ', '', $market_select);
            }

            // [2] => Over 2.5 Goals
            // $market_names = array();
            // $market_names = explode(' ',$row_fields[2]);
            // $market_type = $market_names[0];
            // $market_select = $market_names[1];

            $get_markets_id = $this->market_model->get_market_by_name($market_type);
            $get_markets_selects_id = $this->market_model->markets_selects_by_name_and_id($market_select, $get_markets_id['ID_market']);

            if (!array_key_exists('market_select_id', $get_markets_selects_id))
            {
                echo 'MISSING market_type = ' . $market_type . '<br/>';
                echo 'MISSING market_select = ' . $market_select . '<br/>';
                var_dump($get_markets_id);
                var_dump($get_markets_selects_id);
            }

            if (!array_key_exists('ID_market', $get_markets_id))
            {
                echo 'MISSING market_type = ' . $market_type . '<br/>';
                echo 'MISSING market_select = ' . $market_select . '<br/>';
                var_dump($get_markets_id);
                var_dump($get_markets_selects_id);
                echo '----------------------------------<br/>';
            }

            // [1] => Fixtures 20 October   / Goias v Atletico PR / Over/Under 2.5 Goals
            $bet_name = $market_name = array();
            $bet_name = explode('/', $row_fields[1]);
            $market_name = trim($bet_name[1]);

            // Search is (loss) or profit
            $profit_loss = preg_match('/^([a-f0-9])/', $row_fields[12]);
            if ($profit_loss == 1)
            {
                $profit = $row_fields[12];
                $loss = NULL;
            }
            else
            {
                $profit = NULL;
                $clear_loss = str_replace("(", "", $row_fields[12]);
                $clean = str_replace(")", "", $clear_loss);
                $loss = $clean;
            }

            // 20-Oct-13 21:28 
            $event_date_time = date('Y-m-d H:i:s', strtotime($row_fields[5]));
            $event_date = date('Y-m-d', strtotime($row_fields[5]));

            // username
            $username = $this->user_model->get('id');

            if (!empty($market_name))
            {
                $insert_fields = array(
                    'event_name' => $market_name,
                    'odds' => $row_fields[11],
                    'market_select' => $get_markets_selects_id['market_select_id'],
                    'market_type' => $get_markets_id['ID_market'],
                    'stake' => $row_fields[9],
                    'profit' => $profit,
                    'loss' => $loss,
                    'bet_type' => $row_fields[3],
                    'event_date_time' => $event_date_time,
                    'event_date' => $event_date,
                    'paper_bet' => 0,
                    'username' => $username,
                );

                $param['event_name'] = $market_name;
                $param['event_date_time'] = $event_date_time;
                $param['odds'] = $row_fields[11];
                $param['stake'] = $row_fields[9];
                $param['username'] = $username;

                if (!$this->bet_model->bet_exists($param))
                {
                    $this->bet_model->new_bet($insert_fields);
                    $inserted++;
                }
                else
                {
                    $duplicates++;
                    $duplicate[] = $record;
                }
                $total_imports++;
            }
        }

        //$this->notices->SetNotice('Duplicate!');
        //redirect(site_url('admincp4/livescore/import_csv'));
        $data['total_imports'] = $total_imports;
        $data['inserted'] = $inserted;
        $data['duplicates'] = $duplicates;
        $data['duplicate'] = $duplicate;
        $this->load->view('results', $data);
    }

    private function read_csv_file($limit = 0)
    {
        $this->load->helper('file');
        $return = false;

        if ($content = read_file('writeable/csv_upload.csv'))
        {
            $this->head_assets->stylesheet('css/dataset.css');
            $this->load->library('Encrypt');
            $content = explode("\n", $this->encrypt->decode($content));
            if ($limit > 0)
            {
                // Return a slice.
                $return = array_slice($content, 0, $limit);
            }
            else
            {
                // Return all of the results.
                $return = $content;
            }
        }
        return $return;
    }

    //-------------------------------------------------------------------- END IMPORT
    //********************************** Statistics *******************************// 

    /**
     * Stats Profit Loss in functie de markets
     *
     * Show Stats Profit Loss in functie de markets
     *
     * @param int $id the ID of the league
     * @return string The email form view
     */
    function profit_loss_stats()
    {
        $this->load->model(array('livescore/bet_model', 'livescore/market_model', 'livescore/stats_model'));
        
        $markets = $this->market_model->get_markets();
        
        $profit = array();

        foreach ($markets as $val)
        {
            // Afisez profitul pentru fiecare market in functie de ID-ul marketului respectiv
            $profit_markets = $this->stats_model->sum_profit_markets($val['ID_market']);
            // Afisez loss-ul pentru fiecare market in functie de ID-ul marketului respectiv
            $loss_markets = $this->stats_model->sum_loss_markets($val['ID_market']);
            // Afisez profit-ul pentru fiecare BACK / LAY bet pentru fiecare market in functie de ID-ul marketului respectiv
            $profit_back_bets = $this->stats_model->sum_back_profit($val['ID_market']);
            $profit_lay_bets = $this->stats_model->sum_lay_profit($val['ID_market']);
            // Bag valorile in array-uri
            $profit[] = $profit_markets;
            $loss[] = $loss_markets;
            $profit_back[] = $profit_back_bets;
            $profit_lay[] = $profit_lay_bets;
        }
        $data = array(
            'markets' => $markets,
            'profit' => $profit,
            'loss' => $loss,
            'profit_back' => $profit_back,
            'profit_lay' => $profit_lay,
        );
        $this->load->view('profit_loss_stats', $data);
    }

//********************************** Statistics *******************************// 
}
