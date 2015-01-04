<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

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
class Admincp3 extends Admincp_Controller 
{

    function __construct() 
    {

        parent::__construct();

        $this->admin_navigation->parent_active('livescore');

        //error_reporting(E_ALL^E_NOTICE);
        //error_reporting(E_WARNING);
    }

    function index() 
    {
        redirect('admincp3/livescore/list_matches');
    }

    function parse_matches() 
    {
        $this->load->library('admin_form');
        $form = new Admin_form;
        $form->fieldset('Add Livescore link');
        $form->text('Link', 'link', '', 'link to be introduced', TRUE, 'e.g., http://www.livescore.com/soccer/2013-09-01/ OR http://www.livescore.com/soccer/brazil/serie-a-brasileiro/', TRUE);
        $data = array(
            'form' => $form->display(),
            'form_title' => 'Add Livescore link',
            'form_action' => site_url('admincp3/livescore/parse_matches_validate'),
            'action' => 'new',
        );

        $this->load->view('parse_matches', $data);
    }

    function parse_matches_validate() 
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('link', 'Link', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            $this->notices->SetError('Required fields.');
            $error = TRUE;
        }

        if (isset($error)) {
            redirect('admincp3/livescore/parse_matches');
            return FALSE;
        }

        $link = $this->input->post('link');

        if (strstr($link, '2013') || strstr($link, '2014')) {
            $link = utf8_encode($link);
            $this->parse_info_per_date($link);
        } else {
            $link = utf8_encode($link);
            $this->parse_info_per_competition($link);
        }


        echo '<br/><div align="center"><a href="http://betz.dev/admincp3/livescore/parse_matches">Back</a></div>';

        return TRUE;
    }

    private function parse_info_per_date($link)
    {
        $link = utf8_decode($link);
        $page = $this->getUrl($link);
        $countries = $teams = $score = $competitions = array();
        
        // echo "link = $link<br/>";
        $temp = explode('/', $link);
        // print_r($temp);
        $match_date = $temp[4];
        // echo '<b>match date = '.$match_date.'</b><br/>';        

        $this->load->model('competition_pre_model');
        $this->load->model('country_model');
        $this->load->model('team_pre_model');        
        $this->load->model('match_pre_model');

        //$pattern = '|<dt>(.*?)</dt>[\s\S]*?<dd>([\s\S]*?)</dd>|';

        echo '<div align="center" style="background-color:grey;">';


        // <span class="league"> <a href="/soccer/england/"><strong>England</strong></a> - <span><a href="/soccer/england/premier-league/">Premier League</a></span></span>
        //phpinfo();die;
        //<span class="league"> <a href="/soccer/italy/"><strong>Italy</strong></a> - <span><a href="/soccer/italy/serie-b/">Serie B</a></span> </span> <span class="date">September 2</span>
        $pattern = '@<span class="league">\s*<a href="(.*)"><strong>(.*)</strong></a>\s*-\s*<span><a href="(.*)">(.*)</a></span>\s*</span>@U';
        preg_match_all($pattern, $page, $countries);
        print '<pre>COUNTRIES';
        print_r($countries);

        //<td class="fh"> St.Kickers </td> <td class="fs"> <a href="/soccer/germany/3-liga/st-kickers-vs-unterhaching/1-1485980/" class="scorelink">2 - 3</a> </td> <td class="fa"> Unterhaching </td>
        $pattern = '@<td class="fh">\s*(.*)\s*</td>@U';
        preg_match_all($pattern, $page, $teams_home);
        print '<pre>TEAMS HOME';
        print_r($teams_home);

        //<td class="fh"> Saarbrucken </td> <td class="fs"> <a href="/soccer/germany/3-liga/saarbrucken-vs-fc-heidenheim/1-1485981/" class="scorelink">2 - 3</a> </td> <td class="fa"> FC Heidenheim </td> </tr> <tr class="even"> <td class="fd"> FT </td> 
        //<td class="fh"> St.Kickers </td> <td class="fs"> <a href="/soccer/germany/3-liga/st-kickers-vs-unterhaching/1-1485980/" class="scorelink">2 - 3</a> </td> <td class="fa"> Unterhaching </td>
        $pattern = '@<td class="fa">\s*(.*)\s*</td>@U';
        preg_match_all($pattern, $page, $teams_away);
        print '<pre>TEAMS AWAY';
        print_r($teams_away);

        //<td class="fs"> <a href="/soccer/england/jp-trophy/dagenham-redbridge-vs-colchester-united/1-1560822/" class="scorelink">4 - 1</a> </td> <td class="fa"> Colchester United </td>        
        //<td class="fd"> FT </td> <td class="fh"> Brentford </td> <td class="fs"> <a href="/soccer/england/jp-trophy/brentford-vs-afc-wimbledon/1-1560824/" class="scorelink">5 - 3</a> </td> <td class="fa"> AFC Wimbledon </td> </tr> 
        //<td class="fd"> FT </td> <td class="fh"> Dagenham &amp; Redbridge </td> <td class="fs"> <a href="/soccer/england/jp-trophy/dagenham-redbridge-vs-colchester-united/1-1560822/" class="scorelink">4 - 1</a> </td> <td class="fa"> Colchester United </td> </tr> 
        //<td class="fd"> FT </td> <td class="fh"> Gillingham </td> <td class="fs"> <a href="/soccer/england/jp-trophy/gillingham-vs-leyton-orient/1-1560823/" class="scorelink">1 - 3</a> </td>
        //$pattern = '@<td class="fd">\s*([a-zA-Z]*)\s*</td>\s*<td class="fh">\s*([a-zA-Z\s\*]*)\s*</td>\s*<td class="fs">\s*<a href="(.*)" class="scorelink">\s*(.*)\s*</a>\s*</td>\s*<td class="fa">\s*(.*)\s*</td>@U';
        //<td class="fd"> FT </td> <td class="fh"> Manchester City </td> <td class="fs"> <a href="/soccer/england/premier-league/manchester-city-vs-chelsea/1-1474952/" class="scorelink" onclick="return false;">0 - 1</a> </td> <td class="fa"> Chelsea </td>
        $pattern = '@<td class="fd">\s*([a-zA-Z]*)\s*</td>\s*<td class="fh">\s*([\/\á\æ\é\ø\ß\ü\w\-\#\&\;\.\s\*]*)\s*</td>\s*<td class="fs">\s*<a href="(.*)" class="scorelink" onclick="return false;">\s*(.*)\s*</a>\s*</td>\s*<td class="fa">\s*(.*)\s*</td>@U';

        preg_match_all($pattern, $page, $scores);
        print '<pre>SCORES';
        print_r($scores);

        $competition = new stdClass();
        $competitions = array();
        $match = new stdClass();

        foreach ($countries[2] as $key => $country) {
            $competition->country = trim($country);
            $competition->country_link = trim(substr($countries[1][$key], 1, -1));
            $competition->competition_name = trim($countries[4][$key]);
            $competition->competition_link = trim(substr(str_replace('soccer/', '', $countries[3][$key]), 1, -1));
            $competition->matches = array();
            $competitions[] = clone $competition;
        }

        foreach ($scores[2] as $key => $val) {
            $match->team_home = trim($scores[2][$key]);
            $match->team_away = trim($scores[5][$key]);
            $match->score_link = trim(substr(str_replace('soccer/', '', $scores[3][$key]), 1));
            $match->score = trim($scores[4][$key]);

            $matches[] = clone $match;
        }

        foreach ($matches as $m) {
            foreach ($competitions as $key => $c) {
                if (strstr($m->score_link, $c->competition_link)) {
                    $match = clone $m;
                    $competitions[$key]->matches[] = $match;
                    break;
                }
            }
        }

        foreach ($competitions as $c) {
            $param = array();
            $c->competition_link = trim(str_replace('soccer/', '', $c->competition_link));
            $param['link'] = $c->competition_link;
            $country_id = $this->country_model->get_country_by_name($c->country);
            // if competition international it results country INTERNATIONAL which we don't have
            if (!$country_id) {
                if (strstr($c->competition_link, 'africa')) {
                    $country_id = $this->country_model->get_country_by_name('AFRICA');
                } elseif (strstr($c->competition_link, 'concacaf')) {
                    $country_id = $this->country_model->get_country_by_name('AMERICA');
                } elseif (strstr($c->competition_link, 'america')) {
                    $country_id = $this->country_model->get_country_by_name('AMERICA');
                } elseif (strstr($c->competition_link, 'asia')) {
                    $country_id = $this->country_model->get_country_by_name('ASIA');
                } elseif (strstr($c->competition_link, 'oceania')) {
                    $country_id = $this->country_model->get_country_by_name('ASIA');
                } elseif (strstr($c->competition_link, 'euro')) {
                    $country_id = $this->country_model->get_country_by_name('EUROPE');
                } elseif (strstr($c->competition_link, 'nextgen')) {
                    $country_id = $this->country_model->get_country_by_name('EUROPE');
                } elseif (strstr($c->competition_link, 'toulon')) {
                    $country_id = $this->country_model->get_country_by_name('EUROPE');
                } else {
                    // default WORLD
                    $country_id = $this->country_model->get_country_by_name('WORLD');
                }
            }
            
            // competitions
            if (!$this->competition_pre_model->competition_exists_id($param)) {
                /*$update_fields = array(
                    'name' => $c->competition_name,
                    'link_complete' => 'http://www.livescore.com/soccer/' . $c->competition_link,
                );
                /$this->competition_model->update_competition_by_link($update_fields, $c->competition_link);*/
                
                $competition_id = $this->competition_pre_model->competition_exists($param);
                // does not exist as old competition id
                if (!$competition_id) {
                    $insert_fields = array(
                        'country_id' => $country_id,
                        'name' => $c->competition_name,
                        'link' => $c->competition_link,
                        'link_complete' => 'http://www.livescore.com/soccer/' . $c->competition_link,
                    );
                    $competition_id = $this->competition_pre_model->new_competition($insert_fields);
                } else {
                    $insert_fields = array(
                        'competition_id' => $competition_id
                    );
                    $competition_id = $this->competition_pre_model->new_competition($insert_fields);
                }
            }
            // teams
            $teams = array();
            $param = array();
            foreach ($c->matches as $m) {
                $teams[0] = $m->team_home;
                $teams[1] = $m->team_away;
                foreach ($teams as $t) {
                    $param['name'] = $t;
                    $param['country_id'] = $country_id;
                    $param['matches'] = 0;
                    
                    if (!$this->team_pre_model->team_exists_id($param)) {
                        $team_id = $this->team_pre_model->team_exists($param);
                        // does not exist as old team id
                        if (!$team_id) {
                            $this->team_pre_model->new_team($param);
                        } else {
                            $insert_fields = array(
                              'team_id' =>  $team_id
                            );
                            $this->team_pre_model->new_team($insert_fields);
                        }
                    }
                }
            }

            // matches
            foreach ($c->matches as $m) {
                $team1_id = $this->team_pre_model->team_exists_id(array('name' => $m->team_home, 'country_id' => $country_id));
                $team2_id = $this->team_pre_model->team_exists_id(array('name' => $m->team_away, 'country_id' => $country_id));
                $link_complete = 'http://www.livescore.com/soccer/' . $m->score_link;

                $match_data = array(
                    'competition_id_pre' => $competition_id,
                    'match_date' => $match_date,
                    'team1_pre' => $team1_id,
                    'team2_pre' => $team2_id,
                    'score' => str_replace(' ', '', $m->score),
                    'link' => $m->score_link,
                    'link_complete' => $link_complete                    
                );
                
//                if (strstr($m->team_home, 'AC Milan')) {
//                    echo '<br/>---------------';
//                    echo 'country_id = ' . $country_id . ' team1_id = ' . $team1_id . ' team2_id = ' . $team2_id;
//                    echo '<br/>';
//                    die;
//                }

                $match_id = $this->match_pre_model->match_exists(array('link' => $m->score_link));

                if (!$match_id) {
                    $this->match_pre_model->new_match($match_data);
                } else {
                    $match_db = $this->match_pre_model->get_match($match_id);
                    // if matched is not parsed yet we can still update it                    
                    $this->match_pre_model->update_match($match_data, $match_db['index']);
                    
                }
            }
        }

        print_r($competitions);
        echo '</div>';
    }

    private function parse_info_per_competition($link) 
    {
        $link = utf8_decode($link);
        $page = $this->getUrl($link);
        $countries = $teams = $score = $competitions = array();

        $this->load->model('competition_model');
        $this->load->model('country_model');
        $this->load->model('team_model');

        //$pattern = '|<dt>(.*?)</dt>[\s\S]*?<dd>([\s\S]*?)</dd>|';

        echo '<div align="center" style="background-color:grey;">';

        $pattern = '@<span class="league">\s*<a href=".*"><strong>(.*)</strong></a>@';
        preg_match_all($pattern, $page, $countries);
        print '<pre>COUNTRIES';
        print_r($countries);

        if (!isset($countries[1][0])) {
            $pattern = '@<span class="league">\s*<strong>(.*)</strong>@';
            preg_match_all($pattern, $page, $countries);
            print '<pre>COUNTRIES';
            print_r($countries);
            $country_name = $countries[1][0];
        } else {
            $country_name = $countries[1][0];
        }

        echo "country_name = $country_name<br/>";
        $country_id = $this->country_model->get_country_by_name($country_name);

        $pattern = '@<td class="f(h|a){1}">\s*(.*)\s*</td>@';
        preg_match_all($pattern, $page, $teams);
        print '<pre>TEAMS';
        print_r($teams);

        foreach ($teams[2] as $team) {
            //echo "team $team start<br/>";
            $team = str_replace('*', '', $team);
            $team = trim($team);
            $team_param = array(
                'name' => $team,
                'country_id' => $country_id,
            );

            echo "country_id = $country_id<br/>";

            if ($country_id) {
                if (!$this->team_model->team_exists($team_param)) {
                    $this->team_model->new_team($team_param);
                    echo 'team NOT exists ' . $team . '<br/>';
                } else {
                    echo 'team exists ' . $team . '<br/>';
                }
            } else {
                'echo team country not found' . $team . '<br/>';
            }

            //echo "team $team ends<br/>";   
        }

        preg_match_all('@<span class="league">(\s)*<a href="(.*)"><strong>(.*)</strong></a>(\s)*- <span><a href=".*>(.*)</a>@', $page, $competitions);
        print '<pre>COMPETITIONS';
        print_r($competitions);
        if (empty($competitions[3])) {
            preg_match_all('@<span class="league">(\s)*<a href="(.*)"><strong>(.*)</strong></a>(\s)*- <span>(.*)</span>@', $page, $competitions);
        }
        foreach ($competitions[3] as $key => $val) {
            echo 'COMPETITION ' . $val . ' ' . utf8_decode($competitions[5][$key]) . '<br/>';
        }
        $leagues = array();
        preg_match_all('@<span class="league">\s*<a href="(.*)">@', $page, $leagues);
        //print '<pre>';
        //print_r($leagues);
        foreach ($leagues[1] as $key => $val) {
            //
        }

        preg_match_all('@<td class="fd">\s*([a-zA-Z]*)\s*</td>\s*<td class="fh">\s*([a-zA-Z\s\*]*)\s*</td>\s*<td class="fs">\s*<a href="(.*)" class="scorelink">\s*(.*)\s*</a>\s*</td>\s*<td class="fa">\s*(.*)\s*</td>@', $page, $score);
        foreach ($score[3] as $key => $val) {

            $score[3][$key] = str_replace('/soccer/', '', $score[3][$key]);
            $aux = explode('/', $score[3][$key]);

            $competitions[$aux[0] . '/' . $aux[1]]['name'] = ucfirst($aux[1]);
            $competitions[$aux[0] . '/' . $aux[1]]['link'] = $aux[0] . '/' . $aux[1];
            $competitions[$aux[0] . '/' . $aux[1]]['link_complete'] = 'http://www.livescore.com/soccer/' . $aux[0] . '/' . $aux[1] . '/';
            $competitions[$aux[0] . '/' . $aux[1]]['country_id'] = $this->country_model->get_country_by_name(ucfirst($aux[0]));
        }
        //$competitions = array_unique($competitions);            
        print '<pre>MATCHES';
        print_r($score[3]);
        print_r($competitions);

        $competitions[2][0] = substr($competitions[2][0], 1, -1);
        $competition_param = array(
            'link' => $competitions[2][0],
            'name' => $competitions[3][0] . '-' . $competitions[5][0],
        );

        if (!strstr($link, '2013')) {
            $aux = str_replace('http://www.livescore.com/soccer/', '', $link);
            $aux = substr($aux, 0, -1);
            $competition_param['link'] = $aux;
            $competition_param['link_complete'] = $link;
        }

        $country_id = $this->country_model->get_country_by_name($competitions[3][0]);
        if ($country_id)
            $competition_param['country_id'] = $country_id;

        if ($competition_param['link']) {
            if (!$this->competition_model->competition_exists($competition_param)) {
                $this->competition_model->new_competition($competition_param);
                echo 'competition NOT exists ' . $competitions[2][0] . '<br/>';
            } else {
                echo 'competition exists ' . $competitions[2][0] . '<br/>';
            }
        } else {
            echo 'competition NO link ' . $competitions[2][0] . '<br/>';
        }

        echo '</div>';
    }

    private function getUrl($url) 
    {
        $cUrl = curl_init();
        $headers[] = 'Connection: Keep-Alive';
        $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
        curl_setopt($cUrl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($cUrl, CURLOPT_URL, $url);
        //curl_setopt($cUrl, CURLOPT_HTTPGETGET,1);
        //curl_setopt($cUrl, CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.2; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0)');  
        //curl_setopt($cUrl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);    
        curl_setopt($cUrl, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($cUrl, CURLOPT_TIMEOUT, '3');
        //$pageContent = trim(curl_exec($cUrl));
        $pageContent = curl_exec($cUrl);
        curl_close($cUrl);

        return $pageContent;
    }
    
    public function list_matches_pre()
    {
        $this->load->model('match_pre_model');
        $this->load->library('dataset');

        $filters = array();       
        
        $this->admin_navigation->module_link('List teams pre', site_url('admincp3/livescore/list_teams_pre'));
        $this->admin_navigation->module_link('List competitions pre', site_url('admincp3/livescore/list_competitions_pre'));

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

        if (isset($_GET['offset']))
            $filters['offset'] = $_GET['offset'];
        if (isset($_GET['country_name']))
            $filters['country_name'] = $_GET['country_name'];
        if (isset($_GET['competition_name']))
            $filters['competition_name'] = $_GET['competition_name'];
        if (isset($_GET['team1']))
            $filters['team1'] = $_GET['team1'];
        if (isset($_GET['team2']))
            $filters['team2'] = $_GET['team2'];
        if (isset($_GET['score']))
            $filters['score'] = $_GET['score'];
        if (isset($_GET['match_date_start']))
            $filters['match_date_start'] = $_GET['match_date_start'];
        if (isset($_GET['match_date_end']))
            $filters['match_date_end'] = $_GET['match_date_end'];

        if (isset($filters_decode) && !empty($filters_decode)) {
            foreach ($filters_decode as $key => $val) {
                $filters[$key] = $val;
            }
        }

        foreach ($filters as $key => $val) {
            if (in_array($val, array('filter results', 'start date', 'end date'))) {
                unset($filters[$key]);
            }
        }       
//unset($filters['limit']);
        $this->dataset->columns($columns);
        $this->dataset->datasource('match_pre_model', 'get_matches', $filters);
        $this->dataset->base_url(site_url('admincp3/livescore/list_matches_pre'));
        $this->dataset->rows_per_page($filters['limit']);

        // total rows
        unset($filters['limit']);
        $total_rows = $this->match_pre_model->get_num_rows($filters);
        $this->dataset->total_rows($total_rows);

        // initialize the dataset
        $this->dataset->initialize();
        // add actions
        $this->dataset->action('Delete', 'admincp3/livescore/delete_match_pre');
        $this->load->view('list_matches_pre');
    }
    
    function list_competitions_pre() 
    {
                
                ini_set('display_errors', 1);
        $this->load->model('competition_pre_model');
        $this->admin_navigation->module_link('Fix competitions', site_url('admincp/livescore/fix_competitions'));
        $this->admin_navigation->module_link('Add competition', site_url('admincp/livescore/add_competition'));
        $this->load->library('dataset');

        $columns = array(
            array(
                'name' => 'NAME',
                'type' => 'name',
                'width' => '15%',
            ),
            array(
                'name' => 'COUNTRY',
                'width' => '15%',
                'filter' => 'country_name',
                'type' => 'text',
                'sort_column' => 'country_name',
            ),
            array(
                'name' => 'MATCHES',
                'type' => 'name',
                'width' => '15%',
            ),
            array(
                'name' => 'LINK',
                'width' => '15%',
                'type' => 'text'
            ),
            array(
                'name' => 'LINK COMPLETE',
                'width' => '35%',
                'type' => 'text'
            ),            
            array(
                'name' => 'EDIT',
                'width' => '5%',
                'type' => 'text',
            ),
        );

        $filters = array();
        $filters['limit'] = 20;

        if (isset($_GET['filters'])) {
            $filters_decode = unserialize(base64_decode($this->asciihex->HexToAscii($_GET['filters'])));
        }

        if (isset($_GET['offset']))
            $filters['offset'] = $_GET['offset'];
        if (isset($_GET['country_name']))
            $filters['country_name'] = $_GET['country_name'];

        if (isset($filters_decode) && !empty($filters_decode)) {
            foreach ($filters_decode as $key => $val) {
                $filters[$key] = $val;
            }
        }

        $this->dataset->columns($columns);
        $this->dataset->datasource('competition_pre_model', 'get_competitions', $filters);
        $this->dataset->base_url(site_url('admincp3/livescore/list_competitions_pre'));
        $this->dataset->rows_per_page($filters['limit']);

        // total rows
        unset($filters['limit']);
        $total_rows = $this->competition_pre_model->get_num_rowz($filters);
        $this->dataset->total_rows($total_rows);

        // initialize the dataset
        $this->dataset->initialize();
        // add actions
        $this->dataset->action('Delete', 'admincp3/livescore/delete_competition');
        $this->load->view('list_competitions_pre');
    }
    
    function list_teams_pre() 
    {
        $this->load->library('dataset');
        $this->load->model('team_pre_model');

        $columns = array(
            array(
                'name' => 'NAME',
                'type' => 'name',
                'width' => '15%',
            ),
            array(
                'name' => 'ID',
                'type' => 'id',
                'width' => '15%',
            ),
            array(
                'name' => 'COUNTRY',
                'width' => '15%',
                'filter' => 'country_name',
                'type' => 'text',
                'sort_column' => 'country_name',
            ),
            array(
                'name' => '# OF MATCHES',
                'type' => 'text',
                'width' => '15%',
            ),
            array(
                'name' => 'MATCHES',
                'type' => 'text',
                'width' => '15%',
            ),
            array(
                'name' => 'EDIT',
                'width' => '15%',
                'type' => 'text',
            ),
        );

        $filters = array();
        $filters['limit'] = 20;
        $filters['sort'] = 'name';

        if (isset($_GET['offset']))
            $filters['offset'] = $_GET['offset'];
        if (isset($_GET['country_name']))
            $filters['country_name'] = $_GET['country_name'];

        if (isset($_GET['filters']))
            $filters_decode = unserialize(base64_decode($this->asciihex->HexToAscii($_GET['filters'])));

        if (isset($filters_decode) && is_array($filters_decode)) {
            foreach ($filters_decode as $key => $val) {
                $filters[$key] = $val;
            }
        }
        
        $this->dataset->datasource('team_pre_model', 'get_teams', $filters);

        $this->dataset->columns($columns);

        $this->dataset->base_url(site_url('admincp3/livescore/list_teams_pre/'));
        $this->dataset->rows_per_page($filters['limit']);

        // total rows
        unset($filters['limit']);
        $total_rows = $this->team_pre_model->get_num_rowz($filters);

        $this->dataset->total_rows($total_rows);

        // initialize the dataset
        $this->dataset->initialize();
        // add actions
        $this->dataset->action('Delete', 'admincp3/livescore/delete_team_pre');
        $this->load->view('list_teams_pre');
    }

}
