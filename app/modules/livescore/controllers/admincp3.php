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
class Admincp3 extends Admincp_Controller {

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

        if ($this->form_validation->run() === false)
        {
            $this->notices->SetError('Required fields.');
            $error = true;
        }

        if (isset($error))
        {
            redirect('admincp3/livescore/parse_matches');
            return false;
        }

        $link = $this->input->post('link');

        if (strstr($link, '2013') || strstr($link, '2014') || strstr($link, '2015'))
        {
            $link = utf8_encode($link);
            $this->parse_info_per_date($link);
        }
        else
        {
            $link = utf8_encode($link);
            $this->parse_info_per_competition($link);
        }
        echo '<br/><div align="center"><a href="http://betz.dev/admincp3/livescore/parse_matches">Back</a></div>';
        return true;
    }

    private function parse_info_per_date($link)
    {
        $this->load->model(array('competition_pre_model', 'country_model', 'team_pre_model', 'match_pre_model', 'match_model'));
        
        // Truncate the tables to avoid any errors
        $this->competition_pre_model->clear_table();
        $this->team_pre_model->clear_table();
        $this->match_pre_model->clear_table();
        
        $link = utf8_decode($link);
        $page = $this->getUrl($link);
        print '<pre>';
        //print_r($page);
        //die;
        $countries = $teams = $score = $competitions = array();

        // echo "link = $link<br/>";
        $temp = explode('/', $link);
        // print_r($temp);
        $match_date = $temp[4];
        // echo '<b>match date = '.$match_date.'</b><br/>';
        //$pattern = '|<dt>(.*?)</dt>[\s\S]*?<dd>([\s\S]*?)</dd>|';

        echo '<div align="center" style="background-color:grey;">';

        // <span class="league"> <a href="/soccer/england/"><strong>England</strong></a> - <span><a href="/soccer/england/premier-league/">Premier League</a></span></span>
        //phpinfo();die;
        //<span class="league"> <a href="/soccer/italy/"><strong>Italy</strong></a> - <span><a href="/soccer/italy/serie-b/">Serie B</a></span> </span> <span class="date">September 2</span>
        //<div class="left"> <a href="/soccer/intl/"><strong>International</strong></a> - <a href="/soccer/intl/champions-cup-group-b/">Champions Cup:: group B</a> </div>
        //<div class="left"> <a href="/soccer/denmark/"><strong>Denmark</strong></a> - <a href="/soccer/denmark/sas-ligaen/">Superligaen</a> </div>
        //$pattern = '@<div class="left">\s*<a href="(.*)"><strong>(.*)</strong></a>\s*-\s*<a href="(.*)">([\/\á\æ\é\ø\ß\ü\w\-\#\&\;\.\s\*\:]*)</a>\s*</div>@U';
        $pattern = '@<div class="left">\s*<a href="(.*)"><strong>(.*)</strong></a>\s*-\s*<a href="(.*)">(.*)</a>\s*</div>@U';
        preg_match_all($pattern, $page, $countries);
        print '<pre>COUNTRIES';
        print_r($countries);

        //<td class="fh"> St.Kickers </td> <td class="fs"> <a href="/soccer/germany/3-liga/st-kickers-vs-unterhaching/1-1485980/" class="scorelink">2 - 3</a> </td> <td class="fa"> Unterhaching </td>
        $pattern = '@<div class="ply tright name">\s*(.*)\s*</div>@U';
        preg_match_all($pattern, $page, $teams_home);
        print '<pre>TEAMS HOME';
        print_r($teams_home);

        //<td class="fh"> Saarbrucken </td> <td class="fs"> <a href="/soccer/germany/3-liga/saarbrucken-vs-fc-heidenheim/1-1485981/" class="scorelink">2 - 3</a> </td> <td class="fa"> FC Heidenheim </td> </tr> <tr class="even"> <td class="fd"> FT </td> 
        //<td class="fh"> St.Kickers </td> <td class="fs"> <a href="/soccer/germany/3-liga/st-kickers-vs-unterhaching/1-1485980/" class="scorelink">2 - 3</a> </td> <td class="fa"> Unterhaching </td>
        $pattern = '@<div class="ply name">\s*(.*)\s*</div>@U';
        preg_match_all($pattern, $page, $teams_away);
        print '<pre>TEAMS AWAY';
        print_r($teams_away);

        //<td class="fs"> <a href="/soccer/england/jp-trophy/dagenham-redbridge-vs-colchester-united/1-1560822/" class="scorelink">4 - 1</a> </td> <td class="fa"> Colchester United </td>        
        //<td class="fd"> FT </td> <td class="fh"> Brentford </td> <td class="fs"> <a href="/soccer/england/jp-trophy/brentford-vs-afc-wimbledon/1-1560824/" class="scorelink">5 - 3</a> </td> <td class="fa"> AFC Wimbledon </td> </tr> 
        //<td class="fd"> FT </td> <td class="fh"> Dagenham &amp; Redbridge </td> <td class="fs"> <a href="/soccer/england/jp-trophy/dagenham-redbridge-vs-colchester-united/1-1560822/" class="scorelink">4 - 1</a> </td> <td class="fa"> Colchester United </td> </tr> 
        //<td class="fd"> FT </td> <td class="fh"> Gillingham </td> <td class="fs"> <a href="/soccer/england/jp-trophy/gillingham-vs-leyton-orient/1-1560823/" class="scorelink">1 - 3</a> </td>
        //$pattern = '@<td class="fd">\s*([a-zA-Z]*)\s*</td>\s*<td class="fh">\s*([a-zA-Z\s\*]*)\s*</td>\s*<td class="fs">\s*<a href="(.*)" class="scorelink">\s*(.*)\s*</a>\s*</td>\s*<td class="fa">\s*(.*)\s*</td>@U';
        //<td class="fd"> FT </td> <td class="fh"> Manchester City </td> <td class="fs"> <a href="/soccer/england/premier-league/manchester-city-vs-chelsea/1-1474952/" class="scorelink" onclick="return false;">0 - 1</a> </td> <td class="fa"> Chelsea </td>
        //$pattern = '@<td class="fd">\s*([a-zA-Z]*)\s*</td>\s*<td class="fh">\s*([\/\á\æ\é\ø\ß\ü\w\-\#\&\;\.\s\*]*)\s*</td>\s*<td class="fs">\s*<a href="(.*)" class="scorelink" onclick="return false;">\s*(.*)\s*</a>\s*</td>\s*<td class="fa">\s*(.*)\s*</td>@U';
        // <div class="ply tright name"> Preston North End </div> <div class="sco"> <a href="/soccer/england/fa-cup/preston-north-end-vs-manchester-united/1-1906622/" class="scorelink" onclick="return false;">1 - 3</a> </div> <div class="ply name"> Manchester United </div>
        $pattern = '@<div class="ply tright name">\s*(.*)\s*</div>\s*<div class="sco">\s*(.*)*(<a href="(.*)" class="scorelink" onclick="return false;">(.*)</a>)*\s*</div>\s*<div class="ply name">\s*(.*)\s*</div>@U';

        preg_match_all($pattern, $page, $scores);
        print '<pre>SCORES';
        print_r($scores);

        $competition = new stdClass();
        $competitions = array();
        $match = new stdClass();

        foreach ($countries[2] as $key => $country)
        {
            $competition->country = trim($country);
            $country_link = str_replace(array('soccer', '/'), '', $countries[1][$key]);
            $competition->country_link = $country_link;
            $competition->competition_name = trim($countries[4][$key]);
            $competition->competition_link = trim(substr(str_replace('soccer/', '', $countries[3][$key]), 1, -1));
            $competition->matches = array();
            $competitions[] = clone $competition;
        }

        // add fake world competition
        $competition->country = 'WORLD';
        $competition->country_link = 'soccer/world';
        $competition->competition_name = 'WORLD';
        $competition->competition_link = 'world';
        $competition->matches = array();
        $competitions[] = clone $competition;

        //print '<pre>COMPETITIONS';
        //print_r($competitions);

        foreach ($scores[0] as $key => $val)
        {
            //$match->team_home = trim($scores[1][$key]);
            //$match->team_away = trim($scores[6][$key]);
            //$match->score_link = trim(substr(str_replace('soccer/', '', $scores[3][$key]), 1));
            //$match->score = trim($scores[4][$key]);
            //$matches[] = clone $match;
            if (strstr($val, 'href'))
            {
                $pattern = '@<div class="ply tright name">\s*(.*)\s*</div>\s*<div class="sco">\s*<a href="(.*)" class="scorelink" onclick="return false;">(.*)</a>\s*</div>\s*<div class="ply name">\s*(.*)\s*</div>@U';
            }
            else
            {
                $pattern = '@<div class="ply tright name">\s*(.*)\s*</div>\s*<div class="sco">\s*(.*)\s*</div>\s*<div class="ply name">\s*(.*)\s*</div>@U';
            }

            preg_match_all($pattern, $val, $scoresPrecise);
            print '<pre>SCORES PRECISE';
            print_r($scoresPrecise);

            if (strstr($val, 'href'))
            {
                $match->team_home = trim(str_replace(" *", "", $scoresPrecise[1][0]));
                $match->team_away = trim(str_replace(" *", "", $scoresPrecise[4][0]));
                $match->score_link = trim(str_replace('/soccer/', '', $scoresPrecise[2][0]));
                $match->score = trim($scoresPrecise[3][0]);
                $team_links = $this->match_model->get_team_links($match->score_link);
                $match->team_home_link = $team_links[0];
                $match->team_away_link = $team_links[1];
            }
            else
            {
                $match->team_home = trim(str_replace(" *", "", $scoresPrecise[1][0]));
                $match->team_away = trim(str_replace(" *", "", $scoresPrecise[3][0]));
                $match->score_link = '';
                $match->score = trim($scoresPrecise[2][0]);
                $match->team_home_link = '';
                $match->team_away_link = '';
            }
            $matches[] = clone $match;
        }

        print '<pre>MATCHES';
        print_r($matches);

        foreach ($matches as $m)
        {
            $found = false;
            $match = clone $m;
            foreach ($competitions as $key => $c)
            {
                if (strstr($m->score_link, $c->competition_link))
                {
                    $competitions[$key]->matches[] = $match;
                    $found = true;
                    break;
                }
            }

            if (!$found)
            {
                $match->score_link = 'world';
                $competitions[count($competitions) - 1]->matches[] = $match;
            }
        }

        foreach ($competitions as $c)
        {
            $param = array();
            $c->competition_link = trim(str_replace('soccer/', '', $c->competition_link));
            $param['link'] = $c->competition_link;
            
            $country_id = $this->country_model->get_country_by_link($c->country_link);
                        
            // if competition international it results country INTERNATIONAL which we don't have
            if (!$country_id)
            {
                if (strstr($c->competition_link, 'africa'))
                {
                    $country_id = $this->country_model->get_country_by_name('AFRICA');
                }
                elseif (strstr($c->competition_link, 'concacaf') || strstr($c->competition_link, 'america'))
                {
                    $country_id = $this->country_model->get_country_by_name('AMERICA');
                }               
                elseif (strstr($c->competition_link, 'asia') || strstr($c->competition_link, 'oceania'))
                {
                    $country_id = $this->country_model->get_country_by_name('ASIA');
                }               
                elseif (strstr($c->competition_link, 'euro') || strstr($c->competition_link, 'nextgen') || strstr($c->competition_link, 'toulon'))
                {
                    $country_id = $this->country_model->get_country_by_name('EUROPE');
                }               
                else
                {
                    // default WORLD
                    $country_id = $this->country_model->get_country_by_name('WORLD');
                }
            }

            // competitions
            if (!$this->competition_pre_model->competition_exists_id($param))
            {
//                $update_fields = array(
//                    'name' => $c->competition_name,
//                    'link_complete' => 'http://www.livescore.com/soccer/' . $c->competition_link,
//                );
//                $this->competition_model->update_competition_by_link($update_fields, $c->competition_link);

                $competition_id = $this->competition_pre_model->competition_exists($param);
                // does not exist as old competition id
                if (!$competition_id)
                {
                    $insert_fields = array(
                        'country_id' => $country_id,
                        'name' => $c->competition_name,
                        'link' => $c->competition_link,
                        'link_complete' => 'http://www.livescore.com/soccer/' . $c->competition_link,
                    );
                    $competition_id = $this->competition_pre_model->new_competition($insert_fields);
                }
                else
                {
                    $insert_fields = array(
                        'competition_id' => $competition_id
                    );
                    $competition_id = $this->competition_pre_model->new_competition($insert_fields);
                }
            }
            else
            {
                $competition_id = $this->competition_pre_model->competition_exists($param);
            }
            // teams
            $teams = array();
            $param = array();
            foreach ($c->matches as $m)
            {
                $teams[0] = $m->team_home_link;
                $teams[1] = $m->team_away_link;
                
                $teams_name[0] = $m->team_home;
                $teams_name[1] = $m->team_away;
                
                foreach ($teams as $key => $t)
                {
                    if (strlen($t)) {
                        $param['link'] = $t;
                    }                    
                    $param['country_id'] = $country_id;
                    $param['name'] = $teams_name[$key];
                    $param['matches'] = 0;
                                       
                    if (!$this->team_pre_model->team_exists_id($param))
                    {                        
                        $team_id = $this->team_pre_model->team_exists($param);                                                
                        
                        // does not exist as old team id
                        if (!$team_id)
                        {
                            $this->team_pre_model->new_team($param);
                        }
                        else
                        {
                            $insert_fields = array(
                                'team_id' => $team_id
                            );
                            $this->team_pre_model->new_team($insert_fields);
                        }                                               
                    }
                }
            }

            //print("competition_id = $competition_id");
            // matches
            foreach ($c->matches as $m)
            {
                $search_team1 = array('country_id' => $country_id);
                if (strlen($m->team_home_link)) {
                    $search_team1['link'] = $m->team_home_link;
                }
                
                if (strlen($m->team_home)) {
                    $search_team1['name'] = $m->team_home;
                }
                
                $search_team2 = array('country_id' => $country_id);
                if (strlen($m->team_away_link)) {
                    $search_team2['link'] = $m->team_away_link;
                }
                
                if (strlen($m->team_away)) {
                    $search_team2['name'] = $m->team_away;
                }
                
                $team1_id = $this->team_pre_model->team_exists_id($search_team1);
                $team2_id = $this->team_pre_model->team_exists_id($search_team2);
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

                //echo 'Treating match ' . $match_data['link_complete'];

                $match_id = $this->match_pre_model->match_exists(
                        array(
                            'link' => $match_data['link'],
                            'match_date' => $match_data['match_date'],
                            'team1_pre' => $match_data['team1_pre'],
                            'team2_pre' => $match_data['team2_pre'],
                ));

                if (!$match_id)
                {
                    // echo ' New match ' . PHP_EOL;
                    $this->match_pre_model->new_match($match_data);
                }
                else
                {
                    // echo ' Old match id ' . $match_id . PHP_EOL;
                    $match_db = $this->match_pre_model->get_match($match_id);
                    // add condition parse=0
                    // if matched is not parsed yet we can still update it
                    $this->match_pre_model->update_match($match_data, $match_id);
                }
            }
        }

        print_r($competitions);
        echo '</div>';
    }

    private function parse_info_per_competition($link)
    {
        $this->load->model(array('competition_model', 'country_model', 'team_model'));

        $link = utf8_decode($link);
        $page = $this->getUrl($link);
        $countries = $teams = $score = $competitions = array();

        //$pattern = '|<dt>(.*?)</dt>[\s\S]*?<dd>([\s\S]*?)</dd>|';

        echo '<div align="center" style="background-color:grey;">';

        $pattern = '@<span class="league">\s*<a href=".*"><strong>(.*)</strong></a>@';
        preg_match_all($pattern, $page, $countries);
        print '<pre>COUNTRIES';
        print_r($countries);

        if (!isset($countries[1][0]))
        {
            $pattern = '@<span class="league">\s*<strong>(.*)</strong>@';
            preg_match_all($pattern, $page, $countries);
            print '<pre>COUNTRIES';
            print_r($countries);
            $country_name = $countries[1][0];
        }
        else
        {
            $country_name = $countries[1][0];
        }

        echo "country_name = $country_name<br/>";
        $country_id = $this->country_model->get_country_by_name($country_name);

        $pattern = '@<td class="f(h|a){1}">\s*(.*)\s*</td>@';
        preg_match_all($pattern, $page, $teams);
        print '<pre>TEAMS';
        print_r($teams);

        foreach ($teams[2] as $team)
        {
            //echo "team $team start<br/>";
            $team = str_replace('*', '', $team);
            $team = trim($team);
            $team_param = array(
                'name' => $team,
                'country_id' => $country_id,
            );

            echo "country_id = $country_id<br/>";

            if ($country_id)
            {
                if (!$this->team_model->team_exists($team_param))
                {
                    $this->team_model->new_team($team_param);
                    echo 'team NOT exists ' . $team . '<br/>';
                }
                else
                {
                    echo 'team exists ' . $team . '<br/>';
                }
            }
            else
            {
                'echo team country not found' . $team . '<br/>';
            }

            //echo "team $team ends<br/>";   
        }

        preg_match_all('@<span class="league">(\s)*<a href="(.*)"><strong>(.*)</strong></a>(\s)*- <span><a href=".*>(.*)</a>@', $page, $competitions);
        print '<pre>COMPETITIONS';
        print_r($competitions);
        if (empty($competitions[3]))
        {
            preg_match_all('@<span class="league">(\s)*<a href="(.*)"><strong>(.*)</strong></a>(\s)*- <span>(.*)</span>@', $page, $competitions);
        }
        foreach ($competitions[3] as $key => $val)
        {
            echo 'COMPETITION ' . $val . ' ' . utf8_decode($competitions[5][$key]) . '<br/>';
        }
        $leagues = array();
        preg_match_all('@<span class="league">\s*<a href="(.*)">@', $page, $leagues);
        //print '<pre>';
        //print_r($leagues);
        foreach ($leagues[1] as $key => $val)
        {
            //
        }

        preg_match_all('@<td class="fd">\s*([a-zA-Z]*)\s*</td>\s*<td class="fh">\s*([a-zA-Z\s\*]*)\s*</td>\s*<td class="fs">\s*<a href="(.*)" class="scorelink">\s*(.*)\s*</a>\s*</td>\s*<td class="fa">\s*(.*)\s*</td>@', $page, $score);
        foreach ($score[3] as $key => $val)
        {

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

        if (!strstr($link, '2013'))
        {
            $aux = str_replace('http://www.livescore.com/soccer/', '', $link);
            $aux = substr($aux, 0, -1);
            $competition_param['link'] = $aux;
            $competition_param['link_complete'] = $link;
        }

        $country_id = $this->country_model->get_country_by_name($competitions[3][0]);
        if ($country_id)
            $competition_param['country_id'] = $country_id;

        if ($competition_param['link'])
        {
            if (!$this->competition_model->competition_exists($competition_param))
            {
                $this->competition_model->new_competition($competition_param);
                echo 'competition NOT exists ' . $competitions[2][0] . '<br/>';
            }
            else
            {
                echo 'competition exists ' . $competitions[2][0] . '<br/>';
            }
        }
        else
        {
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

        $count_matches_pre = $this->match_pre_model->get_num_rows();

        $filters = array();

        $this->admin_navigation->module_link('List teams pre', site_url('admincp3/livescore/list_teams_pre'));
        $this->admin_navigation->module_link('List competitions pre', site_url('admincp3/livescore/list_competitions_pre'));
        $this->admin_navigation->module_link('Move matches pre: ' . $count_matches_pre, site_url('admincp3/livescore/move_matches_pre'));
        $this->admin_navigation->module_link('Add match pre', site_url('admincp3/livescore/add_match_pre'));
        $this->admin_navigation->module_link('Add competition pre', site_url('admincp3/livescore/add_competition_pre'));
        $this->admin_navigation->module_link('Add team pre', site_url('admincp3/livescore/add_team_pre'));

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

        if (isset($_GET['filters']))
        {
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

        if (isset($filters_decode) && !empty($filters_decode))
        {
            foreach ($filters_decode as $key => $val)
            {
                $filters[$key] = $val;
            }
        }

        foreach ($filters as $key => $val)
        {
            if (in_array($val, array('filter results', 'start date', 'end date')))
            {
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
        $this->load->library('dataset');
        $this->load->model('competition_pre_model');
        $new_competitions = $this->competition_pre_model->get_num_rows(array('competition_id' => true));
        $this->admin_navigation->module_link('Move new competitions:' . $new_competitions, site_url('admincp3/livescore/move_competitions_pre'));
        $this->admin_navigation->module_link('Add competition pre', site_url('admincp3/livescore/add_competition_pre'));

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

        $filters['new_competitions'] = $new_competitions;
        $filters['country_name_sort'] = true;
        $data = $this->competition_pre_model->get_competitions($filters);
        $filters['data'] = $data;

        $filters['limit'] = 20;

        if (isset($_GET['filters']))
        {
            $filters_decode = unserialize(base64_decode($this->asciihex->HexToAscii($_GET['filters'])));
        }

        if (isset($_GET['offset']))
        {
            $filters['offset'] = $_GET['offset'];
        }

        if (isset($_GET['country_name']))
        {
            $filters['country_name'] = $_GET['country_name'];
        }

        if (isset($filters_decode) && !empty($filters_decode))
        {
            foreach ($filters_decode as $key => $val)
            {
                $filters[$key] = $val;
            }
        }

        $this->dataset->columns($columns);
        $this->dataset->datasource('competition_pre_model', 'get_competitions_by_country_with_filters', $filters);
        $this->dataset->base_url(site_url('admincp3/livescore/list_competitions_pre'));
        $this->dataset->rows_per_page($filters['limit']);

        // total rows
        unset($filters['limit']);
        $total_rows = $this->competition_pre_model->get_num_rowz($filters);
        $this->dataset->total_rows($total_rows);

        // initialize the dataset
        $this->dataset->initialize();
        // add actions
        $this->dataset->action('Delete', 'admincp3/livescore/delete_competition_pre');
        $this->load->view('list_competitions_pre');
    }

    function list_teams_pre()
    {
        $this->load->library('dataset');
        $this->load->model('team_pre_model');
        $nr_new_teams = $this->team_pre_model->get_num_rows(array('team_id' => true));
        $this->admin_navigation->module_link('Move new teams:' . $nr_new_teams, site_url('admincp3/livescore/move_teams_pre/'));
        $this->admin_navigation->module_link('Add team pre', site_url('admincp3/livescore/add_team_pre/'));
        $this->admin_navigation->module_link('Make links', site_url('admincp3/livescore/make_links/'));

        $columns = array(
            array(
                'name' => 'NAME',
                'type' => 'name',
                'width' => '15%',
            ),
            array(
                'name' => 'LINK',
                'type' => 'link',
                'width' => '15%',
            ),
            array(
                'name' => 'SIMILAR TEAMS',
                'type' => 'id',
                'width' => '10%',
            ),
            array(
                'name' => 'ID',
                'type' => 'id',
                'width' => '5%',
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
                'width' => '10%',
            ),
            array(
                'name' => 'MATCHES',
                'type' => 'text',
                'width' => '10%',
            ),
            array(
                'name' => 'EDIT',
                'width' => '10%',
                'type' => 'text',
            ),
        );

        $filters = array();
        $filters['nr_new_teams'] = $nr_new_teams;
        $filters['country_name_sort'] = true;
        $data = $this->team_pre_model->get_teams($filters);
        $filters['data'] = $data;
        $filters['limit'] = 20;
        $filters['sort'] = 'name';

        if (isset($_GET['offset']))
        {
            $filters['offset'] = $_GET['offset'];
        }

        if (isset($_GET['country_name']))
        {
            $filters['country_name'] = $_GET['country_name'];
        }

        if (isset($_GET['filters']))
        {
            $filters_decode = unserialize(base64_decode($this->asciihex->HexToAscii($_GET['filters'])));
        }

        if (isset($filters_decode) && is_array($filters_decode))
        {
            foreach ($filters_decode as $key => $val)
            {
                $filters[$key] = $val;
            }
        }

        $this->dataset->datasource('team_pre_model', 'get_teams_by_country_with_filters', $filters);
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

    public function move_competitions_pre()
    {
        $this->load->model('competition_pre_model');
        $this->competition_pre_model->move_competitions_pre();
        redirect('admincp3/livescore/list_competitions_pre');
    }

    public function move_teams_pre()
    {
        $this->load->model('team_pre_model');
        $nr_teams_moved = $this->team_pre_model->move_teams_pre();
        $this->notices->SetNotice("$nr_teams_moved teams moved successfully.");
        redirect('admincp3/livescore/list_teams_pre');
    }

    public function list_matches_by_team_id_pre($id)
    {
        $this->load->model('match_pre_model');
        $this->load->library('dataset');

        $filters = array();

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

        if (isset($_GET['filters']))
        {
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

        if (isset($filters_decode) && !empty($filters_decode))
        {
            foreach ($filters_decode as $key => $val)
            {
                $filters[$key] = $val;
            }
        }

        foreach ($filters as $key => $val)
        {
            if (in_array($val, array('filter results', 'start date', 'end date')))
            {
                unset($filters[$key]);
            }
        }

        $filters['team_id'] = $id;

        $this->dataset->columns($columns);
        $this->dataset->datasource('match_pre_model', 'get_matches_by_team_id', $filters);
        $this->dataset->base_url(site_url('admincp3/livescore/list_matches_by_team_id_pre/' . $id . '/'));
        $this->dataset->rows_per_page($filters['limit']);

        // total rows
        unset($filters['limit']);
        $total_rows = $this->match_pre_model->get_matches_by_team_id(array('team_id' => $id, 'count' => true));
        $this->dataset->total_rows($total_rows);

        // initialize the dataset
        $this->dataset->initialize();
        // add actions
        $this->dataset->action('Delete', 'admincp3/livescore/delete_match_pre');
        $this->load->view('list_matches_pre');
    }

    function xedit_team_pre($id)
    {
        $this->load->model(array('country_model', 'team_pre_model'));
        $this->load->library('admin_form');

        $team = $this->team_pre_model->get_team($id);
//        print '<pre>';
//        print_r($team);
//        print '</pre>';
        if (empty($team))
        {
            die(show_error('No team with this ID.'));
        }
        $form = new Admin_form;
        $countries = $params = array();
        $params['dropdown'] = 1;
        $countries = $this->country_model->get_countries($params);

        $form->fieldset('Team');
        $form->text('Name', 'name', $team['name'], 'Team name to be introduced', true, 'e.g., AC Milan', true);
        $form->dropdown('Country', 'country_id', $countries, $team['country_id']);

        $data = array(
            'form' => $form->display(),
            'form_title' => 'Edit Team',
            'form_action' => site_url('admincp3/livescore/add_team_validate_pre/edit/' . $team['index']),
            'action' => 'edit',
        );

        $this->load->view('add_team', $data);
    }

    function add_team_validate_pre($action = 'new', $id = false)
    {
        $this->load->library('form_validation');
        $this->load->model('team_pre_model');
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('country_id', 'Country', 'required|trim');

        if ($this->form_validation->run() === false)
        {
            $this->notices->SetError('Required fields.');
            $error = true;
        }

        if (isset($error))
        {
            if ($action == 'new')
            {
                redirect('admincp3/livescore/list_teams_pre');
                return false;
            }
            else
            {
                redirect('admincp3/livescore/edit_team_pre/' . $id);
                return false;
            }
        }

        $fields['name'] = $this->input->post('name');
        $fields['country_id'] = $this->input->post('country_id');

        if ($action == 'new')
        {
            $this->team_pre_model->new_team($fields);
            $this->notices->SetNotice('Team pre added successfully.');
            redirect('admincp3/livescore/list_teams_pre/');
        }
        else
        {
            $this->team_pre_model->update_team($fields, $id);
            $this->notices->SetNotice('Team pre updated successfully.');
            redirect('admincp3/livescore/list_teams_pre/');
        }
        return true;
    }

    function edit_team_pre_similar($id)
    {
        $this->load->model(array('country_model', 'team_pre_model'));
        $this->load->library('admin_form');

        $params = array();
        $params['dropdown'] = 1;
        $params['team_pre_id'] = $id;

        $similar_teams = $this->team_pre_model->get_similar_teams($params);

        $form = new Admin_form;

        $form->fieldset('Similar teams');
        $form->dropdown('Teams', 'team_id', $similar_teams, false, false, true, false, true);

        $data = array(
            'form' => $form->display(),
            'form_title' => 'Edit Team',
            'form_action' => site_url('admincp3/livescore/similar_team_validate_pre/' . $id)
        );
        $this->load->view('edit_team_pre_similar', $data);
    }

    function similar_team_validate_pre($id = 0)
    {
        $this->load->library('form_validation');
        $this->load->model('team_pre_model');
        $this->form_validation->set_rules('team_id', 'Team', 'is_natural_no_zero|trim');
        if ($this->form_validation->run() === false)
        {
            $this->notices->SetError('Required fields.');
            $error = true;
        }
        if (isset($error))
        {
            redirect('admincp3/livescore/edit_team_pre_similar/' . $id);
            return false;
        }

        $fields = array(
            'team_id' => $this->input->post('team_id'),
            'name' => null,
            'country_id' => null,
            'matches' => null
        );
        $this->team_pre_model->update_team($fields, $id);
        $this->notices->SetNotice('Team pre with similar teams updated successfully.');
        redirect('admincp3/livescore/list_teams_pre/');
        
        return true;
    }

    public function move_matches_pre()
    {
        $this->load->model('match_pre_model');
        $moved = $this->match_pre_model->move_matches_pre();
        $this->notices->SetNotice($moved . ' matches successfully moved to z_matches normal table');
        redirect('admincp3/livescore/list_matches_pre/');
    }

    public function add_competition_pre()
    {
        $this->load->library('admin_form');
        $this->load->model('country_model');
        $form = new Admin_form;
        $countries = $params = $competitions = array('' => 'Country competitions');
        $params['dropdown'] = 1;
        $countries = $this->country_model->get_countries($params);

        $form->fieldset('Add Competition Pre');
        $form->dropdown('Country', 'country_id_before', $countries);
        $form->dropdown('Competitions', 'competitions_before', $competitions);
        $form->text('Competition name', 'name', '', 'Competition name to be introduced', false, 'e.g., Bundesliga', true);
        $form->text('Link', 'link', '', 'Link', false, 'e.g., russia/premier-league', true);
        $form->text('Link complete', 'link_complete', '', 'Link complete', false, 'e.g., http://www.livescore.com/soccer/russia/premier-league/', true);
        $form->dropdown('Country', 'country_id', $countries);
        $data = array(
            'form' => $form->display(),
            'form_title' => 'Add Competition Pre',
            'form_action' => site_url('admincp3/livescore/add_competition_pre_validate'),
            'action' => 'new',
        );
        $this->load->view('add_competition_pre', $data);
    }

    public function add_competition_pre_validate($action = 'new', $id = false)
    {
        $this->load->library('form_validation');
        $this->load->model(array('competition_pre_model', 'competition_model'));

        $this->form_validation->set_rules('name', 'Nume', 'trim');
        $this->form_validation->set_rules('link', 'Link', 'trim');
        $this->form_validation->set_rules('country_id', 'Country', 'trim');

        if ($this->form_validation->run() === false)
        {
            $this->notices->SetError('Required fields.');
            $error = true;
        }

        if (isset($error))
        {
            if ($action == 'new')
            {
                redirect('admincp3/livescore/list_competitions_pre');
                return false;
            }
            else
            {
                redirect('admincp3/livescore/edit_competition_pre/' . $id);
                return false;
            }
        }

        $competition_before = $this->input->post('competitions_before');

        if (strlen($competition_before))
        {
            $fields['competition_id'] = $competition_before;
        }
        else
        {
            $temp = $this->input->post('link_complete');
            if ($temp[strlen($temp) - 1] == '/')
            {
                $link_complete = substr($temp, 0, -1);
            }
            else
            {
                $link_complete = $temp;
            }

            // search first the competition by link
            $competition = $this->competition_model->get_competition_by_link_complete($link_complete);

            if (!empty($competition))
            {
                $fields['competition_id'] = $competition['competition_id'];
            }
            else
            {
                $name = $this->input->post('name');
                $link = $this->input->post('link');
                $country_id = $this->input->post('country_id');

                if (!strlen($name) || !strlen($link) || !strlen($country_id))
                {
                    $this->notices->SetError('Competition not found.Add more details like name, link and country.');
                    redirect('admincp3/livescore/list_competitions_pre');
                    return false;
                }
                else
                {
                    $fields['name'] = $this->input->post('name');
                    $fields['link'] = $this->input->post('link');
                    $fields['link_complete'] = $link_complete;
                    $fields['country_id'] = $this->input->post('country_id');
                }
            }
        }

        if ($action == 'new')
        {
            if (strlen($competition_before))
            {
                $competition_exists = $this->competition_pre_model->get_competition_by_competition_id($fields['competition_id']);
                if (!empty($competition_exists))
                {
                    $this->notices->SetNotice('Competition pre already exists.');
                    redirect('admincp3/livescore/list_competitions_pre/');
                    return false;
                }
            }
            else
            {
                $competition_exists = $this->competition_pre_model->get_competition_by_criteria($fields);
                if (!empty($competition_exists))
                {
                    $this->notices->SetNotice('Competition pre already exists.');
                    redirect('admincp3/livescore/list_competitions_pre/');
                    return false;
                }
            }

            $this->competition_pre_model->new_competition($fields);
            $this->notices->SetNotice('Competition pre added successfully.');
            redirect('admincp3/livescore/list_competitions_pre/');
        }
        else
        {
            if (strlen($competition_before))
            {
                $competition_exists = $this->competition_pre_model->get_competition_by_competition_id($fields['competition_id']);
            }
            else
            {
                $competition_exists = $this->competition_pre_model->get_competition_by_criteria($fields);
            }

            if (!empty($competition_exists))
            {
                $this->notices->SetNotice('Competition pre already exists.');                    
                redirect('admincp3/livescore/list_competitions_pre/');
                return false;                    
            }

            $this->competition_pre_model->update_competition($fields, $id);
            $this->notices->SetNotice('Competition pre updated successfully.');
            redirect('admincp3/livescore/list_competitions_pre/');
        }
        
        return true;
    }

    public function edit_competition_pre($id)
    {
        $this->load->model(array('competition_pre_model', 'competition_model', 'country_model'));
        $this->load->library('admin_form');

        $competition_pre = $this->competition_pre_model->get_competition($id);
        if (empty($competition_pre))
        {
            die(show_error('No competition pre with this ID.'));
        }
        $form = new Admin_form;

        if (isset($competition_pre['competition_id']))
        {
            $competition = $this->competition_model->get_competition($competition_pre['competition_id']);
            $competitions_all = $this->competition_model->get_competitions(array('country_id' => $competition['country_id']));
            foreach ($competitions_all as $c)
            {
                $competitions[$c['competition_id']] = $c['name'];
            }
            $competition_pre['name'] = $competition_pre['link'] = $competition_pre['link_complete'] = $competition_pre['country_id'] = '';
        }
        else
        {
            $competitions = array('' => 'Country competitions');
            $competition['country_id'] = false;
            $competitions['country_id'] = false;
        }

        $countries = $params = array();
        $params['dropdown'] = 1;
        $countries = $this->country_model->get_countries($params);

        $form->fieldset('Edit Competition Pre');
        $form->dropdown('Country', 'country_id_before', $countries, $competition['country_id']);
        $form->dropdown('Competitions', 'competitions_before', $competitions, $competition_pre['competition_id']);
        $form->text('Competition name', 'name', $competition_pre['name'], 'Competition name to be introduced', false, 'e.g., Bundesliga', true);
        $form->text('Link', 'link', $competition_pre['link'], 'Competition link', false, 'e.g., russia/premier-league', true);
        $form->text('Link complete', 'link_complete', $competition_pre['link_complete'], 'Competition link complete', false, 'e.g., http://www.livescore.com/soccer/russia/premier-league/', true);
        $form->dropdown('Country', 'country_id', $countries, $competition_pre['country_id']);

        $data = array(
            'form' => $form->display(),
            'form_title' => 'Edit Competition Pre',
            'form_action' => site_url('admincp3/livescore/add_competition_pre_validate/edit/' . $competition_pre['index']),
            'action' => 'edit',
        );

        $this->load->view('add_competition_pre', $data);
    }

    public function delete_competition_pre($contents, $return_url)
    {
        $this->load->library('asciihex');
        $this->load->model('competition_pre_model');

        $contents = unserialize(base64_decode($this->asciihex->HexToAscii($contents)));
        $return_url = base64_decode($this->asciihex->HexToAscii($return_url));

        foreach ($contents as $content)
        {
            $this->competition_pre_model->delete_competition($content);
        }
        $this->notices->SetNotice('Competition pre deleted successfully.');
        redirect($return_url);
        
        return true;
    }

    public function add_team_pre()
    {
        $this->load->model('country_model');
        $this->load->library('admin_form');

        $form = new Admin_form;
        $countries = $params = array();
        $params['dropdown'] = 1;
        $countries = $this->country_model->get_countries($params);
        $teams = array(0 => 'Choose team from country');

        $form->fieldset('Add Team Pre');
        $form->dropdown('Country', 'country_id', $countries);
        $form->dropdown('Teams', 'team_id', $teams);
        $form->text('Team name', 'name', '', 'Team name to be introduced', false, 'e.g., AC Milan', true);
        $form->dropdown('Country', 'country', $countries);
        $data = array(
            'form' => $form->display(),
            'form_title' => 'Add team pre',
            'form_action' => site_url('admincp3/livescore/add_team_pre_validate'),
            'action' => 'new',
        );
        $this->load->view('add_team_pre', $data);
    }

    public function add_team_pre_validate($action = 'new', $id = false)
    {
        $this->load->library('form_validation');
        $this->load->model('team_pre_model');

        $this->form_validation->set_rules('country_id', 'Team Country', 'trim');
        $this->form_validation->set_rules('team_id', 'Team Name', 'trim');
        $this->form_validation->set_rules('name', 'Name', 'trim');
        $this->form_validation->set_rules('country', 'Country', 'trim');

        if ($this->form_validation->run() === false)
        {
            $this->notices->SetError('Required fields.');
            $error = true;
        }

        if (isset($error))
        {
            if ($action == 'new')
            {
                redirect('admincp3/livescore/list_teams_pre');
                return false;
            }
            else
            {
                redirect('admincp3/livescore/edit_team_pre/' . $id);
                return false;
            }
        }
        $team_id = (int) $this->input->post('team_id');

        if ($action == 'new')
        {
            if ($team_id)
            {
                $team_exists = $this->team_pre_model->team_exists_team_id($team_id);

                if ($team_exists)
                {
                    $this->notices->SetError('Team id already exists.');
                    redirect('admincp3/livescore/list_teams_pre');
                    return false;
                }

                $fields = array(
                    'team_id' => $team_id,
                    'country_id' => null,
                    'name' => null,
                    'matches' => 0
                );
            }
            else
            {
                $team_exists = $this->team_pre_model->team_exists_name_country(
                        array(
                            'country_id' => (int) $this->input->post('country'),
                            'name' => $this->input->post('name')
                        )
                );

                if ($team_exists)
                {
                    $this->notices->SetError('Team with country and name already exists.');
                    redirect('admincp3/livescore/list_teams_pre');
                    return false;
                }

                $fields = array(
                    'team_id' => null,
                    'country_id' => (int) $this->input->post('country'),
                    'name' => $this->input->post('name'),
                    'matches' => 0
                );
            }
        }
        else
        {
            $team = $this->team_pre_model->get_team($id);

            if (empty($team))
            {
                $this->notices->SetError('Team not found.');
                redirect('admincp3/livescore/list_teams_pre');
                return false;
            }

            if ($team_id)
            {
                $fields = array(
                    'team_id' => $team_id,
                    'country_id' => null,
                    'name' => null,
                    'matches' => 0
                );
            }
            else
            {
                $fields = array(
                    'team_id' => null,
                    'country_id' => (int) $this->input->post('country'),
                    'name' => $this->input->post('name'),
                    'matches' => 0
                );
            }
        }

        if ($action == 'new')
        {
            $team_id = $this->team_pre_model->new_team($fields);
            $this->notices->SetNotice('Team pre added successfully.');
            redirect('admincp3/livescore/list_teams_pre/');
        }
        else
        {
            $this->team_pre_model->update_team($fields, $id);
            $this->notices->SetNotice('Team pre updated successfully.');
            redirect('admincp3/livescore/list_teams_pre/');
        }

        return true;
    }

    function edit_team_pre($id)
    {
        $this->load->model(array('country_model', 'team_model', 'team_pre_model'));
        $this->load->library('admin_form');

        $team = $this->team_pre_model->get_team($id);

        if (!$team['team_id'])
        {
            $teams[0] = 'Select team';
        }

        $all_teams = $this->team_model->get_teams(array('country_id' => $team['country_id']));
        foreach ($all_teams as $t)
        {
            $teams[$t['team_id']] = $t['name'];
        }

        if (empty($team))
        {
            die(show_error('No team pre with this ID.'));
        }
        $form = new Admin_form;
        $countries = $params = array();
        $params['dropdown'] = 1;
        $countries = $this->country_model->get_countries($params);

        $form->fieldset('Edit Team Pre');
        $form->dropdown('Country', 'country_id', $countries, $team['country_id']);
        $form->dropdown('Teams', 'team_id', $teams, $team['team_id']);
        $form->text('Team name', 'name', $team['name'], 'Team name to be introduced', false, 'e.g., AC Milan', true);
        $form->dropdown('Country', 'country', $countries, $team['country_id']);
        $data = array(
            'form' => $form->display(),
            'form_title' => 'Edit Team pre',
            'form_action' => site_url('admincp3/livescore/add_team_pre_validate/edit/' . $team['index']),
            'action' => 'edit',
        );
        $this->load->view('add_team_pre', $data);
    }

    public function delete_team_pre($contents, $return_url)
    {
        $this->load->library('asciihex');
        $this->load->model(array('competition_pre_model', 'team_pre_model'));

        $contents = unserialize(base64_decode($this->asciihex->HexToAscii($contents)));
        $return_url = base64_decode($this->asciihex->HexToAscii($return_url));

        foreach ($contents as $content)
        {
            $this->team_pre_model->delete_team($content);
        }
        $this->notices->SetNotice('Team pre deleted successfully.');
        redirect($return_url);
        
        return true;
    }

    /**
     * Add Match pre         
     *     
     */
    function add_match_pre($action = 'new')
    {
        $this->load->model(array('match_pre_model', 'competition_pre_model', 'team_pre_model', 'country_model'));        

        $competitions = array();
        $params['dropdown'] = 1;
        $competitions = $this->competition_pre_model->get_all_competitions($params);
        asort($competitions);

        $teams = array();
        $teams = $this->team_pre_model->get_teams();
        foreach ($teams as $team)
        {
            $team_name[$team['index']] = $team['country_name'] . ' - ' . $team['name'];
            $team_country[$team['country_id']] = $team['country_name'];
        }
        
        $data = array(
            'competitions' => $competitions,
            'team_name' => $team_name,            
            'form_title' => 'Add New Match Pre',
            'form_action' => site_url('admincp3/livescore/post_match_pre/new'),
            'action' => 'new',
        );
        
        $this->load->view('add_match_pre', $data);
    }

    /**
     * Edit Match
     *
     * Show the Match form, preloaded with variables
     *
     * @param int $id the ID of the bet
     */
    public function edit_match_pre($id)
    {
        $this->load->model(array('match_pre_model', 'competition_pre_model', 'team_pre_model', 'country_model'));
        $match = $this->match_pre_model->get_match_pre($id);
        
//        print '<pre>';
//        print_r($match);
//        print '</pre>';
//        die;
        
        $filters['country_id'] = $match['country_id'];

        $countries = array();
        $countries = $this->country_model->get_countries();
                
        $competition = $country_competitions = array();
        $competition = $this->competition_pre_model->get_competitions($filters);
        
        foreach ($competition as $comp)
        {
            $competition_name[$comp['index']] = $comp['country_name'] . '-' . $comp['name'];
            $country_competitions[$comp['country_id']] = $comp['country_name'];
        }
        
        // get only countries for which we have competitions pre
        $countries = array_intersect($countries, $country_competitions);
        asort($competition_name);

        $teams = array();
        $teams = $this->team_pre_model->get_teams($filters);
        foreach ($teams as $team)
        {
            $team_name[$team['index']] = $team['name'];
            $team_country[$team['country_id']] = $team['country_name'];
        }

        if (!isset($match) || empty($match))
        {
            $this->notices->SetError('No pre match found');
            redirect('admincp3/livescore/list_matches_pre');
        }

        $home = $this->team_pre_model->get_team($match['team1_pre']);
        $away = $this->team_pre_model->get_team($match['team2_pre']);

        $data = array(
            'match' => $match,
            'home' => $home,
            'away' => $away,
            'id_match' => $id,
            'id_country' => $match['country_id'],
            'country_name' => $countries,
            'id_competition' => $match['competition_id'],
            'competition_name' => $competition_name,
            'competition_id_pre' => $match['competition_id_pre'],
            'home_team_id' => $match['team1_pre'],
            'away_team_id' => $match['team2_pre'],
            'team_name' => $team_name,
            'score' => $match['score'],
            'link' => $match['link_match'],
            'livescore_link' => $match['link_match_complete'],
            'match_date' => $match['match_date'],
            'form' => $match,
            'form_title' => 'Edit Match Pre',
            'form_action' => site_url('admincp3/livescore/post_match_pre/edit/' . $id),
            'action' => 'edit',
        );

        $this->load->view('edit_match_pre', $data);
    }
    
    /**
     * Handle New/Edit Match Post
     */
    public function post_match($action, $id = false)
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

        $fields = array(            
            'competition_id' => $competition_name,
            'match_date' => $match_date,
            'team1' => $home_team,
            'team2' => $away_team,
            'score' => $score,
            'link' => $link,
            'link_complete' => $link_complete,
        );        

        if ($action == 'new')
        {
            $bet_id = $this->match_model->new_match($fields);
            $this->notices->SetNotice('Match added successfully.');
        }
        else
        {
            $bet_id = $this->match_model->update_match($fields, $ID_match);
            $this->notices->SetNotice('Match edited successfully.');
        }
        redirect('admincp3/livescore/list_matches');
        
        return true;
    }

    /**
     * Handle New/Edit Match Pre Post
     * 
     * @param $action The action to perform
     * @param $id     The match id to edit
     * 
     * @return int
     */
    public function post_match_pre($action, $id = false)
    {
        $this->load->model(array('match_pre_model', 'competition_pre_model'));

        // content
        $country = $this->input->post('country_name');
        $competition_id_pre = $this->input->post('competition_name');
        $match_date = $this->input->post('match_date');
        $team1_pre = $this->input->post('home_team');
        $team2_pre = $this->input->post('away_team');
        $score = $this->input->post('score');
        $link_complete = $this->input->post('link_complete');
        
        if ($team1_pre == $team2_pre) {
            $this->notices->SetError('Home team must be different than team away');
            redirect('admincp3/livescore/add_match_pre/');
        }

        $link = str_replace('http://www.livescore.com/soccer/', '', $link_complete);        

        $fields = array(
            'competition_id_pre' => $competition_id_pre,
            'match_date' => $match_date,
            'team1_pre' => $team1_pre,
            'team2_pre' => $team2_pre,
            'score' => $score,
            'link' => $link,
            'link_complete' => $link_complete,
        );                
        
        if ($action == 'new') {
            if ($this->match_pre_model->match_exists(array('link_complete' => $link_complete))) {
                $this->notices->SetError('Match pre already exists');
                redirect('admincp3/livescore/add_match_pre/');
            }
            
            $bet_id = $this->match_pre_model->new_match($fields);
            $this->notices->SetNotice('Match pre added successfully.');
        } else {
            $bet_id = $this->match_pre_model->update_match($fields, $id);
            $this->notices->SetNotice('Match pre edited successfully.');
        }
                
        redirect('admincp3/livescore/list_matches_pre');        
        return $bet_id;
    }
    
    public function delete_match_pre($contents, $return_url)
    {
        $this->load->library('asciihex');
        $this->load->model('match_pre_model');

        $contents = unserialize(base64_decode($this->asciihex->HexToAscii($contents)));
        $return_url = base64_decode($this->asciihex->HexToAscii($return_url));

        foreach ($contents as $content)
        {
            $this->match_pre_model->delete_match($content);
        }
        $this->notices->SetNotice('Match pre deleted successfully.');
        redirect($return_url);
        return true;
    }
    
    public function make_links()
    {
        $this->load->library('asciihex');
        $this->load->model('match_pre_model');
        
        $this->match_pre_model->make_links();
        
        //redirect('admincp3/livescore/list_matches_pre'); 
    }        
}
