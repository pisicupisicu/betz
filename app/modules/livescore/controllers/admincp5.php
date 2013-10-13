<?php if (!defined('BASEPATH')) exit('No direct script access allowed');



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



class Admincp5 extends Admincp_Controller 
{
                
    function __construct()
    {

        parent::__construct();
                
        $this->admin_navigation->parent_active('livescore');
                                        
        //error_reporting(E_ALL^E_NOTICE);
        //error_reporting(E_WARNING);
    }
        
    function index () 
    {
            redirect('admincp/livescore/list_matches');        
    }

    function parse_matches()
    {
        $this->load->model('match_model');
        $match  =   $this->match_model->get_next_match();

        if(empty($match)) {
            die('All matches are already parsed');
        }

        $this->parse_match($match['id']);

        //if($match['id'] > 3) die;   

        echo '<META http-equiv="refresh" content="2;URL=/admincp5/livescore/parse_matches/">';
    }

    function parse_match($id)
    {
        $this->load->model('match_model');
        $this->load->model('goal_model');
        $this->load->model('card_model');
        //62,49
        if(!$id)    $id = 1;
        $match = $this->match_model->get_match($id);        
        $page = $this->getUrl($match['link_match']);
        // echo $match['link_c'].'<br/>';
        // echo $page.'<br/>';    
        print '<pre>MATCH';
        print_r($match);   

        //<td class="min"> 68' </td> <td class="ply"> <div> <span class="inc yellowcard right"></span> <span class="right ml4"> </span> <span class="right name">Ugur Demirok</span> <div class="clear"></div> </div> </td> <td class="sco"> </td>
        $pattern = '@<td class="min">\s*(\d*'."'".')\s*</td>\s*<td class="ply">\s*<div>\s*<span class="inc yellowcard right"></span>\s*<span class="right ml4">\s*</span>\s*<span class="right name">(.*)</span>\s*<div class="clear"></div>@U';
        preg_match_all($pattern, $page, $yellowcard_left);
        print '<pre>YELLOWCARD LEFT';
        print_r($yellowcard_left);

        foreach($yellowcard_left[1] as $key=>$val){
            $data = array(
                    'match_id'  =>  $id,
                    'card_type' =>  'yellow',
                    'min'       =>  $yellowcard_left[1][$key],
                    'player'    =>  $yellowcard_left[2][$key],
                    'team'      =>  'home',
                );
            if(!$this->card_model->card_exists($data)) {
                $this->card_model->new_card($data);
            }
        }        
       
        //<td class="min"> 45' </td> <td class="ply"> <div> <span class=" right"></span> <span class="right ml4"> </span> <span class="right name"></span> <div class="clear"></div> </div> </td> <td class="sco"> </td> <td class="ply"> <div> <span class="inc yellowcard left"></span> <span class="left mr4"> </span> <span class="left name">Onur Recep Kivrak</span> <div class="clear"></div> </div> </td>
        $pattern = '@<td class="min">\s*(\d*'."'".')\s*</td>\s*<td class="ply">\s*<div>\s*<span class=" right"></span>\s*<span class="right ml4">\s*</span>\s*<span class="right name"></span>\s*<div class="clear"></div>\s*</div>\s*</td>\s*<td class="sco">\s*</td>\s*<td class="ply">\s*<div>\s*<span class="inc yellowcard left"></span>\s*<span class="left mr4">\s*</span>\s*<span class="left name">(.*)</span>\s*<div class="clear"></div>\s*</div>@U';        
        preg_match_all($pattern, $page, $yellowcard_right);
        print '<pre>YELLOWCARD RIGHT';
        print_r($yellowcard_right);

        foreach($yellowcard_right[1] as $key=>$val){
            $data = array(
                    'match_id'  =>  $id,
                    'card_type' =>  'yellow',
                    'min'       =>  $yellowcard_right[1][$key],
                    'player'    =>  $yellowcard_right[2][$key],
                    'team'      =>  'away',
                );
            if(!$this->card_model->card_exists($data)) {
                $this->card_model->new_card($data);
            }
        }

        
        //<td class="min"> 47' </td> <td class="ply"> <div> <span class="inc yellowcard right"></span> <span class="right ml4"> </span> <span class="right name">Fredy</span> <div class="clear"></div> </div> </td> <td class="sco"> </td> <td class="ply"> <div> <span class="inc yellowcard left"></span> <span class="left mr4"> </span> <span class="left name">Fernando Marcal</span> <div class="clear"></div> </div> </td>
        //<td class="min"> 90' </td> <td class="ply"> <div> <span class="inc yellowcard right"></span> <span class="right ml4"> </span> <span class="right name">Flori</span> <div class="clear"></div> </div> </td> <td class="sco"> </td> <td class="ply"> <div> <span class="inc yellowcard left"></span> <span class="left mr4"> </span> <span class="left name">Marcel Sabitzer</span> <div class="clear"></div> </div> </td>
        $pattern = '@<td class="min">\s*(\d*'."'".')\s*</td>\s*<td class="ply">\s*<div>\s*<span class="inc yellowcard right"></span>\s*<span class="right ml4">\s*</span>\s*<span class="right name">(.*)</span>\s*<div class="clear"></div>\s*</div>\s*</td>\s*<td class="sco">\s?</td>\s*<td class="ply">\s*<div>\s*<span class="inc yellowcard left"></span>\s*<span class="left mr4">\s*</span>\s*<span class="left name">([A-Z]+.*)</span>@U';
        preg_match_all($pattern, $page, $yellowcard_right_same_line);
        print '<pre>YELLOWCARD RIGHT SAME LINE';
        print_r($yellowcard_right_same_line);

        if(!empty($yellowcard_right_same_line[2][0])) {
            foreach($yellowcard_right_same_line[2] as $key=>$val) {
                if(!strstr($yellowcard_right_same_line[2][$key],'span')) {
                    $data = array(
                    'match_id'  =>  $id,
                    'card_type' =>  'yellow',
                    'min'       =>  $yellowcard_right_same_line[1][$key],
                    'player'    =>  $yellowcard_right_same_line[3][$key],
                    'team'      =>  'away',
                );

                    //print_r($data);
                    if(!$this->card_model->card_exists($data)) {
                        $this->card_model->new_card($data);
                    }
                }
            }
        }

        $pattern = '@<td class="min">\s*(\d*'."'".')\s*</td>\s*<td class="ply">\s*<div>\s*<span class="inc redcard right"></span>\s*<span class="right ml4"></span>\s*<span class="right name">(.*)</span>@U';
        preg_match_all($pattern, $page, $redcard_left);
        print '<pre>REDCARD LEFT';
        print_r($redcard_left);

        foreach($redcard_left[1] as $key=>$val){
            $data = array(
                    'match_id'  =>  $id,
                    'card_type' =>  'red',
                    'min'       =>  $redcard_left[1][$key],
                    'player'    =>  $redcard_left[2][$key],
                    'team'      =>  'home',
                );
            if(!$this->card_model->card_exists($data)) {
                $this->card_model->new_card($data);
            }
        }

        //<td class="min"> 54' </td> <td class="ply"> <div> <span class="inc redcard right"></span> <span class="right ml4"> </span> <span class="right name">Kristian Bråtebæk</span> <div class="clear"></div> </div> </td> <td class="sco"> </td> <td class="ply"> <div> <span class="inc yellowcard left"></span> <span class="left mr4"> </span> <span class="left name">Abdurahim Laajab</span> <div class="clear"></div> </div> </td>
        $pattern = '@<td class="min">\s*(\d*'."'".')\s*</td>\s*<td class="ply">\s*<div>\s*<span class="inc redcard right"></span>\s*<span class="right ml4">\s*</span>\s*<span class="right name">(.*)</span>\s*<div class="clear"></div>\s*</div>\s*</td>\s*<td class="sco">\s*</td>\s*<td class="ply">\s*<div>\s*<span class="inc yellowcard left"></span>\s*<span class="left mr4">\s*</span>\s*<span class="left name">(.*)</span>\s*<div class="clear"></div>\s*</div>\s*</td>@U';
        preg_match_all($pattern, $page, $red_left_yellowcard_right_same_line);
        print '<pre>RED LEFT YELLOWCARD RIGHT SAME LINE';
        print_r($red_left_yellowcard_right_same_line);

        if(!empty($red_left_yellowcard_right_same_line[2][0])) {
            foreach($red_left_yellowcard_right_same_line[2] as $key=>$val) {
                if(!strstr($red_left_yellowcard_right_same_line[2][$key],'span')) {
                    $data = array(
                    'match_id'  =>  $id,
                    'card_type' =>  'yellow',
                    'min'       =>  $red_left_yellowcard_right_same_line[1][$key],
                    'player'    =>  $red_left_yellowcard_right_same_line[3][$key],
                    'team'      =>  'away',
                );

                    //print_r($data);
                    if(!$this->card_model->card_exists($data)) {
                        $this->card_model->new_card($data);
                    }
                }
            }
        }

        $pattern = '@<td class="min">\s*(\d*'."'".')\s*</td>\s*<td class="ply">\s*<div>\s*<span class=" right"></span>\s*<span class="right ml4"></span>\s*<span class="right name"></span>\s*<div class="clear"></div>\s*</div>\s*</td>\s*<td class="sco">\s*</td>\s*<td class="ply">\s*<div>\s*<span class="inc redcard left"></span>\s*<span class="left mr4"></span>\s*<span class="left name">(.*)</span>@U';
        preg_match_all($pattern, $page, $redcard_right);
        print '<pre>REDCARD RIGHT';
        print_r($redcard_right);

        foreach($redcard_right[1] as $key=>$val){
            $data = array(
                    'match_id'  =>  $id,
                    'card_type' =>  'red',
                    'min'       =>  $redcard_right[1][$key],
                    'player'    =>  $redcard_right[2][$key],
                    'team'      =>  'away',
                );
            if(!$this->card_model->card_exists($data)) {
                $this->card_model->new_card($data);
            }
        }

        //<td class="min"> 80' </td> <td class="ply"> <div> <span class="inc redcard right"></span> <span class="right ml4"> </span> <span class="right name">Hector Acuna</span> <div class="clear"></div> </div> </td> <td class="sco"> </td> <td class="ply"> <div> <span class="inc redcard left"></span> <span class="left mr4"> </span> <span class="left name">Cristian Gonzalez</span> <div class="clear"></div> </div> </td>        
        $pattern = '@<td class="min">\s*(\d*'."'".')\s*</td>\s*<td class="ply">\s*<div>\s*<span class="inc redcard right"></span>\s*<span class="right ml4">\s*</span>\s*<span class="right name">(.*)</span>\s*<div class="clear"></div>\s*</div>\s*</td>\s*<td class="sco">\s*</td>\s*<td class="ply">\s*<div>\s*<span class="inc redcard left"></span>\s*<span class="left mr4">\s*</span>\s*<span class="left name">(.*)</span>\s*<div class="clear"></div>\s*</div>\s*</td>@U';
        preg_match_all($pattern, $page, $red_left_redcard_right_same_line);
        print '<pre>RED LEFT REDCARD RIGHT SAME LINE';
        print_r($red_left_redcard_right_same_line);

        if(!empty($red_left_redcard_right_same_line[2][0])) {
            foreach($red_left_redcard_right_same_line[2] as $key=>$val) {
                if(!strstr($red_left_redcard_right_same_line[2][$key],'span')) {
                    $data = array(
                    'match_id'  =>  $id,
                    'card_type' =>  'red',
                    'min'       =>  $red_left_redcard_right_same_line[1][$key],
                    'player'    =>  $red_left_redcard_right_same_line[3][$key],
                    'team'      =>  'away',
                );

                    //print_r($data);
                    if(!$this->card_model->card_exists($data)) {
                        $this->card_model->new_card($data);
                    }
                }
            }
        }

        $pattern = '@<td class="min">\s*(\d*'."'".')\s*</td>\s*<td class="ply">\s*<div>\s*<span class="inc redyellowcard right"></span>\s*<span class="right ml4"></span>\s*<span class="right name">(.*)</span>@U';
        preg_match_all($pattern, $page, $yellowredcard_left);
        print '<pre>YELLOW-RED CARD LEFT';
        print_r($yellowredcard_left);

        foreach($yellowredcard_left[1] as $key=>$val){
            $data = array(
                    'match_id'  =>  $id,
                    'card_type' =>  'yellow_red',
                    'min'       =>  $yellowredcard_left[1][$key],
                    'player'    =>  $yellowredcard_left[2][$key],
                    'team'      =>  'home',
                );
            if(!$this->card_model->card_exists($data)) {
                $this->card_model->new_card($data);
            }
        }
        
        $pattern = '@<td class="min">\s*(\d*'."'".')\s*</td> <td class="ply">\s*<div>\s*<span class=" right"></span>\s*<span class="right ml4"></span>\s*<span class="right name"></span>\s*<div class="clear"></div>\s*</div>\s*</td>\s*<td class="sco">\s*</td>\s*<td class="ply">\s*<div>\s*<span class="inc redyellowcard left"></span>\s*<span class="left mr4"></span>\s*<span class="left name">(.*)</span>@U';
        preg_match_all($pattern, $page, $yellowredcard_right);
        print '<pre>YELLOW-RED CARD RIGHT';
        print_r($yellowredcard_right);

        foreach($yellowredcard_right[1] as $key=>$val){
            $data = array(
                    'match_id'  =>  $id,
                    'card_type' =>  'yellow_red',
                    'min'       =>  $yellowredcard_right[1][$key],
                    'player'    =>  $yellowredcard_right[2][$key],
                    'team'      =>  'away',
                );
            if(!$this->card_model->card_exists($data)) {
                $this->card_model->new_card($data);
            }            
        }                        

        $pattern = '@<td class="min">\s*(\d*'."'".')\s*</td>\s*<td class="ply">\s*<div>\s*<span class="inc goal right"></span>\s*<span class="right ml4">(.*)</span>\s*<span class="right name">(.*)</span>\s*<div class="clear"></div>\s*</div>\s*</td>\s*<td class="sco">\s*(.*)\s*</td>@U';        
        preg_match_all($pattern, $page, $goal_left);
        print '<pre>SCORE LEFT';
        print_r($goal_left);        

        $pattern = '@<td class="min">\s*(\d*'."'".')\s*</td>\s*<td class="ply">\s*<div>\s*<span class="inc goal right"></span>\s*<span class="right ml4">(.*)</span>\s*<span class="right name">(.*)</span>\s*<div class="clear"></div>\s*</div>\s*<div class="hidden" data-type="details">\s*<span class=" right"></span>\s*<span class="assist right ml4">\s*(.*)\s*</span> <span class="assist right name">(.*)</span>@U';
        preg_match_all($pattern, $page, $goal_left_assist);
        print '<pre>SCORE LEFT ASSIST';
        print_r($goal_left_assist);

        foreach($goal_left[1] as $key=>$val){
            if(isset($goal_left_assist[5][$key]))   {
                $assist = trim(strip_tags($goal_left_assist[5][$key]));
            } else {
                $assist = '';
            }
            $player =   strip_tags($goal_left[3][$key]);
            $pos    =   strpos($player,'(assist)');
            if($pos) {
                $player = substr($player,0,$pos);
            }
            
            $data = array(                    
                    'match_id'  =>  $id,
                    'score'     =>  str_replace(" ","",$goal_left[4][$key]),
                    'min'       =>  str_replace("'","",$goal_left[1][$key]),
                    'assist'    =>  $assist,
                    'type'      =>  trim(strip_tags($goal_left[2][$key])),
                    'player'    =>  trim($player),
                    'team'      =>  'home',
                );
            //print_r($data);
            if(!$this->goal_model->goal_exists($data)) {
                $this->goal_model->new_goal($data);
            }
        }

        //<td class="min"> 58' </td> <td class="ply"> <div> <span class=" right"></span> <span class="right ml4"> </span> <span class="right name"></span> <div class="clear"></div> </div> </td> <td class="sco"> 1 - 1 </td> <td class="ply"> <div> <span class="inc goal left"></span> <span class="left mr4"> </span> <span class="left name">Michael Wessel</span> <div class="clear"></div> </div> </td>
        $pattern = '@<td class="min">\s*(\d*'."'".')\s*</td>\s*<td class="ply">\s*<div>\s*<span class=" right"></span>\s*<span class="right ml4">\s*</span>\s*<span class="right name"></span>\s*<div class="clear"></div>\s*</div>\s*</td>\s*<td class="sco">\s*(.*){5}\s*</td>\s*<td class="ply">\s*<div>\s*<span class="inc goal left"></span>\s*<span class="left mr4">(.*)</span>\s*<span class="left name">(.*)</span>\s*<div class="clear"></div>\s*</div>@U';
        preg_match_all($pattern, $page, $goal_right);
        print '<pre>SCORE RIGHT';
        print_r($goal_right);

        foreach($goal_right[0] as $key=>$val){
            if(@substr_count($goal_right[0][$key],'<td class="sco">') > 1) {
                $pos = strrpos($goal_right[0][$key],'<td class="min">');
                $substring  =   substr($goal_right[0][$key],$pos);
                preg_match_all($pattern, $substring, $goal_right2);
                print '<pre>SCORE RIGHT2';
                print_r($goal_right2);
                $goal_right[2][$key]    =   $goal_right2[2][0];
                $goal_right[1][$key]    =   $goal_right2[1][0];
                $goal_right[3][$key]    =   $goal_right2[3][0];
                $goal_right[4][$key]    =   $goal_right2[4][0];

                //echo "substring = $substring<br/>";
            }
        }

        
        $pattern = '@<td class="min">\s*(\d*'."'".')\s*</td>\s*<td class="ply">\s*<div>\s*<span class=" right"></span>\s*<span class="right ml4">\s*</span>\s*<span class="right name"></span>\s*<div class="clear"></div>\s*</div>\s*</td>\s*<td class="sco">\s*\d*\s-\s\d*\s*</td>\s*<td class="ply">\s*<div>\s*<span class="inc goal left"></span>\s*<span class="left mr4">\s*</span>\s*<span class="left name">(.*)</span>\s*<div class="clear"></div>\s*</div>\s*<div class="hidden" data-type="details">\s*<span class=" right"></span>\s*<span class="assist left mr4">(.*)</span>\s*<span class="assist left name">(.*)</span>\s*<div class="clear"></div>\s*</div>@U';        
        preg_match_all($pattern, $page, $goal_right_assist);
        print '<pre>SCORE RIGHT ASSIST';
        print_r($goal_right_assist); 

        foreach($goal_right[1] as $key=>$val){
            if(isset($goal_right_assist[4][$key])) {
                $assist = trim(strip_tags($goal_right_assist[4][$key]));
            } else {
                $assist = '';
            }
            $data = array(
                    'match_id'  =>  $id,
                    'score'     =>  str_replace(" ","",$goal_right[2][$key]),
                    'min'       =>  str_replace("'","",$goal_right[1][$key]),
                    'assist'    =>  $assist,
                    'type'      =>  trim(strip_tags($goal_right[3][$key])),
                    'player'    =>  trim(strip_tags($goal_right[4][$key])),
                    'team'      =>  'away',
                );
            if(!$this->goal_model->goal_exists($data)) {
                $this->goal_model->new_goal($data);
            }
            
        }

        $update_fields  =   array(
                'parsed'    =>  1,
            );
        $this->match_model->update_match($update_fields,$id);               

    }

    function view_match($id)
    {
        $this->load->model('match_model');
        $this->load->model('team_model');
        $this->load->model('goal_model');
        $this->load->model('card_model');
                
        $match = $this->match_model->get_match($id);
        if(!isset($match) || empty($match)) {
            $this->notices->SetError('No match found');
            redirect('admincp/livescore/list_matches');
        }

        $home   =   $this->team_model->get_team($match['team1']);
        $away   =   $this->team_model->get_team($match['team2']);
        $goals  =   $this->goal_model->get_goals_by_match($id);
        $cards  =   $this->card_model->get_cards_by_match($id);

        //print_r($cards);

        $data = array(
                        'match' =>  $match,
                        'home'  =>  $home,
                        'away'  =>  $away,
                        'goals' =>  $goals,
                        'cards' =>  $cards,
                    );
        
        $this->load->view('view_match',$data);        
        
    }
        
    private function getUrl($url)
    {
            $cUrl = curl_init();
            $headers[] = 'Connection: Keep-Alive';
            $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';               
            curl_setopt($cUrl, CURLOPT_HTTPHEADER, $headers); 
            curl_setopt($cUrl, CURLOPT_URL,$url);
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

    public function fix_star_teams()
    {
        $this->load->model('team_model');
        $this->load->model('match_model');
        $filters = array();
        $filters['name'] = '\*';
        $teams = $this->team_model->get_teams($filters);        
        print '<pre>';
        //print_r($teams);

        foreach($teams as $t) {
            print_r($t);
            $team_star_id = $t['team_id'];
            $team = str_replace(' *','',$t['name']);
            //echo "team = $team<br/>";
            $filters['equal'] = 1;            
            $filters['name'] = $team;
            $team2 = $this->team_model->get_teams($filters);
            $team_id = $team2[0]['team_id'];
            print_r($team2);

            echo "team_star_id = $team_star_id team_id = $team_id<br/>";
            $this->team_model->delete_team($team_star_id);
            $i++;

            // $matches = $this->match_model->get_matches_by_team_id($team_star_id);
            // if(!empty($matches)) {
            //     print '<pre>MATCH';
            //     print_r($matches);
            // }                        
        }

        echo "$i teams deleted<br/>";
    }

    public function delete_duplicate_teams()
    {
        $this->load->model('team_model');
        $this->load->model('match_model');
        $teams = $this->team_model->get_duplicates();
        print '<pre>';
        //print_r($teams);

        if($teams) {
                foreach($teams as $t) {
                $filters['country_id'] = $t['country_id'];
                $filters['name']       = $t['name'];
                $teamz = $this->team_model->get_team_by_country_and_name($filters);
                print_r($teamz);
                foreach($teamz as $tz) {
                    $matches = $this->match_model->get_matches_by_team_id($tz['team_id']);
                    $count = count($matches);
                    if($count) {
                       echo "COUNT = ".count($matches).'<br/>';
                       print_r($matches); 
                    }
                    
                }

                if(!$count) {
                    //delete duplicate team with higher team_id
                    //$this->team_model->delete_team($teamz[1]['team_id']);
                }
            }
        } else {
            echo 'No duplicate teams found!';
        }

        
    }

}
