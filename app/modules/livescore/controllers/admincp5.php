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
class Admincp5 extends Admincp_Controller {

    function __construct()
    {
        parent::__construct();
        $this->admin_navigation->parent_active('livescore');
        //error_reporting(E_ALL^E_NOTICE);
        //error_reporting(E_WARNING);
    }

    public function index()
    {
        redirect('admincp/livescore/list_matches');
    }

    function parse_matches()
    {
        $this->load->model('match_model');
        $match = $this->match_model->get_next_match();

        if (empty($match))
        {
            $this->notices->SetNotice('All matches successfully parsed.');
            redirect('admincp3/livescore/list_matches_pre');
        }
        $this->parse_match($match['id']);

        echo '<META http-equiv="refresh" content="1;URL=/admincp5/livescore/parse_matches/">';
    }

    function parse_match($id)
    {
        $this->load->model(array('match_model', 'goal_model', 'card_model'));
        //62,49
        if (!$id)
            $id = 1;
        $match = $this->match_model->get_match($id);
        $page = $this->getUrl($match['link_match_complete']);
        // echo $match['link_c'].'<br/>';
        // echo $page.'<br/>';    
        print '<pre>MATCH<br /><br />';
        print_r($match);
        // Special characters
        $specialchars = $this->match_model->getSpecialCharacters();
        
        // CARDS PARSING START
        $pattern = '@<div class="(even)* row-gray" data-id="details" data-type="tab">\s<div class="min">\s[0-9]*\'\s</div>\s<div class="ply tright">\s<div> <span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s<span class="ml4">\s</span>\s<span class="inc yellowcard"></span>\s</div>\s</div>\s<div class="sco"> &nbsp; </div>\s<div class="ply">\s<div>\s<span class=""></span>\s<span class="mr4">\s</span>\s<span class="name"></span>\s</div>\s</div>\s</div>@';
        preg_match_all($pattern, $page, $parsed);
        for($i = 0; $i < count($parsed[0]); $i++)
        {
            $minutes = '@<div class="min">\s[0-9]*\'\s</div>@';
            preg_match_all($minutes, $parsed[0][$i], $min);
            $names = '@<span class="name">[A-Za-z'.$specialchars.'\s]*</span>@';
            preg_match_all($names, $parsed[0][$i], $nam);
            $data[] = array_merge($min, $nam);
        }
        //print_r($data);
        if(isset($data)) {
            print '<pre>YELLOW CARD LEFT</pre>';
            for($i = 0; $i < count($data); $i++)
            {
                $info = array(
                    'match_id' => $id,
                    'card_type' => 'yellow',
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'player' => trim(strip_tags($data[$i][1][0])),
                    'team' => 'home',
                );
                print_r($info);
                if (!$this->card_model->card_exists($info))
                {
                    $this->card_model->new_card($info);
                }
            }
        }
        unset($data);
        $pattern = '@<div class="(even)* row-gray" data-id="details" data-type="tab">\s<div class="min">\s[0-9]*\'\s</div>\s<div class="ply tright">\s<div>\s<span class="name"></span>\s<span class="ml4">\s</span>\s<span class=""></span>\s</div>\s</div>\s<div class="sco"> &nbsp; </div>\s<div class="ply">\s<div>\s<span class="inc yellowcard"></span>\s<span class="mr4">\s</span>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s</div>\s</div>\s</div>@';
        preg_match_all($pattern, $page, $parsed);
        
        for($i = 0; $i < count($parsed[0]); $i++)
        {
            $minutes = '@<div class="min">\s[0-9]*\'\s</div>@';
            preg_match_all($minutes, $parsed[0][$i], $min);
            $names = '@<span class="name">[A-Za-z'.$specialchars.'\s]*</span>@';
            preg_match_all($names, $parsed[0][$i], $nam);
            $data[] = array_merge($min, $nam);
        }
        //print_r($data);
        if(isset($data)) {
            print '<pre>YELLOW CARD RIGHT</pre>';
            for($i = 0; $i < count($data); $i++)
            {
                $info = array(
                    'match_id' => $id,
                    'card_type' => 'yellow',
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'player' => trim(strip_tags($data[$i][1][1])),
                    'team' => 'away',
                );
                print_r($info);
                if (!$this->card_model->card_exists($info))
                {
                    $this->card_model->new_card($info);
                }
            }
        }
        unset($data);
        $pattern = '@<div class="(even)* row-gray" data-id="details" data-type="tab">\s<div class="min">\s[0-9]*\'\s</div>\s<div class="ply tright">\s<div>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s<span class="ml4">\s</span>\s<span class="inc yellowcard"></span>\s</div>\s</div>\s<div class="sco"> &nbsp; </div>\s<div class="ply">\s<div>\s<span class="inc yellowcard"></span>\s<span class="mr4">\s</span>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s</div>\s</div>\s</div>@';
        preg_match_all($pattern, $page, $parsed);
        
        for($i = 0; $i < count($parsed[0]); $i++)
        {
            $minutes = '@<div class="min">\s[0-9]*\'\s</div>@';
            preg_match_all($minutes, $parsed[0][$i], $min);
            $names = '@<span class="name">[A-Za-z'.$specialchars.'\s]*</span>@';
            preg_match_all($names, $parsed[0][$i], $nam);
            $data[] = array_merge($min, $nam);
        }
        //print_r($data);
        if(isset($data)) {
            print '<pre>YELLOW CARDS BOTH SIDES</pre>';
            for($i = 0; $i < count($data); $i++)
            {
                $info = array(
                    'match_id' => $id,
                    'card_type' => 'yellow',
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'player' => trim(strip_tags($data[$i][1][0])),
                    'team' => 'home',
                );
                print_r($info);
                if (!$this->card_model->card_exists($info))
                {
                    $this->card_model->new_card($info);
                }
                $info = array(
                    'match_id' => $id,
                    'card_type' => 'yellow',
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'player' => trim(strip_tags($data[$i][1][1])),
                    'team' => 'away',
                );
                print_r($info);
                if (!$this->card_model->card_exists($info))
                {
                    $this->card_model->new_card($info);
                }
            }
        }
        unset($data);
        $pattern = '@<div class="(even)* row-gray" data-id="details" data-type="tab">\s<div class="min">\s[0-9]*\'\s</div>\s<div class="ply tright">\s<div>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s<span class="ml4">\s</span>\s<span class="inc yellowcard"></span>\s</div>\s</div>\s<div class="sco"> &nbsp; </div>\s<div class="ply">\s<div>\s<span class="inc redcard"></span>\s<span class="mr4">\s</span>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s</div>\s</div>\s</div>@';
        preg_match_all($pattern, $page, $parsed);
        
        for($i = 0; $i < count($parsed[0]); $i++)
        {
            $minutes = '@<div class="min">\s[0-9]*\'\s</div>@';
            preg_match_all($minutes, $parsed[0][$i], $min);
            $names = '@<span class="name">[A-Za-z'.$specialchars.'\s]*</span>@';
            preg_match_all($names, $parsed[0][$i], $nam);
            $data[] = array_merge($min, $nam);
        }
        //print_r($data);
        if(isset($data)) {
            print '<pre>YELLOW CARD LEFT RED CARD RIGHT</pre>';
            for($i = 0; $i < count($data); $i++) 
            {
                $info = array(
                    'match_id' => $id,
                    'card_type' => 'yellow',
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'player' => trim(strip_tags($data[$i][1][0])),
                    'team' => 'home',
                );
                print_r($info);
                if (!$this->card_model->card_exists($info))
                {
                    $this->card_model->new_card($info);
                }
                $info = array(
                    'match_id' => $id,
                    'card_type' => 'red',
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'player' => trim(strip_tags($data[$i][1][1])),
                    'team' => 'away',
                );
                print_r($info);
                if (!$this->card_model->card_exists($info))
                {
                    $this->card_model->new_card($info);
                }
            }
        }
        unset($data);
        $pattern = '@<div class="(even)* row-gray" data-id="details" data-type="tab">\s<div class="min">\s[0-9]*\'\s</div>\s<div class="ply tright">\s<div>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s<span class="ml4">\s</span>\s<span class="inc yellowcard"></span>\s</div>\s</div>\s<div class="sco"> &nbsp; </div>\s<div class="ply">\s<div>\s<span class="inc redyellowcard"></span>\s<span class="mr4">\s</span>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s</div>\s</div>\s</div>@';
        preg_match_all($pattern, $page, $parsed);
        
        for($i = 0; $i < count($parsed[0]); $i++)
        {
            $minutes = '@<div class="min">\s[0-9]*\'\s</div>@';
            preg_match_all($minutes, $parsed[0][$i], $min);
            $names = '@<span class="name">[A-Za-z'.$specialchars.'\s]*</span>@';
            preg_match_all($names, $parsed[0][$i], $nam);
            $data[] = array_merge($min, $nam);
        }
        //print_r($data);
        if(isset($data)) {
            print '<pre>YELLOW CARD LEFT RED YELLOW CARD RIGHT</pre>';
            for($i = 0; $i < count($data); $i++) 
            {
                $info = array(
                    'match_id' => $id,
                    'card_type' => 'yellow',
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'player' => trim(strip_tags($data[$i][1][0])),
                    'team' => 'home',
                );
                print_r($info);
                if (!$this->card_model->card_exists($info))
                {
                    $this->card_model->new_card($info);
                }
                $info = array(
                    'match_id' => $id,
                    'card_type' => 'yellow_red',
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'player' => trim(strip_tags($data[$i][1][1])),
                    'team' => 'away',
                );
                print_r($info);
                if (!$this->card_model->card_exists($info))
                {
                    $this->card_model->new_card($info);
                }
            }
        }
        unset($data);
        $pattern = '@<div class="(even)* row-gray" data-id="details" data-type="tab">\s<div class="min">\s[0-9]*\'\s</div>\s<div class="ply tright">\s<div>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s<span class="ml4">\s</span>\s<span class="inc redcard"></span>\s</div>\s</div>\s<div class="sco"> &nbsp; </div>\s<div class="ply">\s<div> <span class=""></span>\s<span class="mr4">\s</span>\s<span class="name"></span>\s</div>\s</div>\s</div>@';
        preg_match_all($pattern, $page, $parsed);
        
        for($i = 0; $i < count($parsed[0]); $i++)
        {
            $minutes = '@<div class="min">\s[0-9]*\'\s</div>@';
            preg_match_all($minutes, $parsed[0][$i], $min);
            $names = '@<span class="name">[A-Za-z'.$specialchars.'\s]*</span>@';
            preg_match_all($names, $parsed[0][$i], $nam);
            $data[] = array_merge($min, $nam);
        }
        //print_r($data);
        if(isset($data)) {
            print '<pre>RED CARD LEFT</pre>';
            for($i = 0; $i < count($data); $i++)
            {
                $info = array(
                    'match_id' => $id,
                    'card_type' => 'red',
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'player' => trim(strip_tags($data[$i][1][0])),
                    'team' => 'home',
                );
                print_r($info);
                if (!$this->card_model->card_exists($info))
                {
                    $this->card_model->new_card($info);
                }
            }
        }
        unset($data);
        $pattern = '@<div class="(even)* row-gray" data-id="details" data-type="tab">\s<div class="min">\s[0-9]*\'\s</div>\s<div class="ply tright">\s<div>\s<span class="name"></span>\s<span class="ml4">\s</span>\s<span class=""></span>\s</div>\s</div>\s<div class="sco"> &nbsp; </div>\s<div class="ply">\s<div> <span class="inc redcard"></span>\s<span class="mr4">\s</span>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s</div>\s</div>\s</div>@';
        preg_match_all($pattern, $page, $parsed);
        
        for($i = 0; $i < count($parsed[0]); $i++)
        {
            $minutes = '@<div class="min">\s[0-9]*\'\s</div>@';
            preg_match_all($minutes, $parsed[0][$i], $min);
            $names = '@<span class="name">[A-Za-z'.$specialchars.'\s]*</span>@';
            preg_match_all($names, $parsed[0][$i], $nam);
            $data[] = array_merge($min, $nam);
        }
        //print_r($data);
        if(isset($data)) {
            print '<pre>RED CARD RIGHT</pre>';
            for($i = 0; $i < count($data); $i++)
            {
                $info = array(
                    'match_id' => $id,
                    'card_type' => 'red',
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'player' => trim(strip_tags($data[$i][1][1])),
                    'team' => 'away',
                );
                print_r($info);
                if (!$this->card_model->card_exists($info))
                {
                    $this->card_model->new_card($info);
                }
            }
        }
        unset($data);
        $pattern = '@<div class="(even)* row-gray" data-id="details" data-type="tab">\s<div class="min">\s[0-9]*\'\s</div>\s<div class="ply tright">\s<div>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s<span class="ml4">\s</span>\s<span class="inc redcard"></span>\s</div>\s</div>\s<div class="sco"> &nbsp; </div>\s<div class="ply">\s<div>\s<span class="inc redcard"></span>\s<span class="mr4">\s</span>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s</div>\s</div>\s</div>@';
        preg_match_all($pattern, $page, $parsed);
        
        for($i = 0; $i < count($parsed[0]); $i++)
        {
            $minutes = '@<div class="min">\s[0-9]*\'\s</div>@';
            preg_match_all($minutes, $parsed[0][$i], $min);
            $names = '@<span class="name">[A-Za-z'.$specialchars.'\s]*</span>@';
            preg_match_all($names, $parsed[0][$i], $nam);
            $data[] = array_merge($min, $nam);
        }
        //print_r($data);
        if(isset($data)) {
            print '<pre>RED CARDS BOTH SIDES</pre>';
            for($i = 0; $i < count($data); $i++)
            {
                $info = array(
                    'match_id' => $id,
                    'card_type' => 'red',
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'player' => trim(strip_tags($data[$i][1][0])),
                    'team' => 'home',
                );
                print_r($info);
                if (!$this->card_model->card_exists($info))
                {
                    $this->card_model->new_card($info);
                }
                $info = array(
                    'match_id' => $id,
                    'card_type' => 'red',
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'player' => trim(strip_tags($data[$i][1][1])),
                    'team' => 'away',
                );
                print_r($info);
                if (!$this->card_model->card_exists($info))
                {
                    $this->card_model->new_card($info);
                }
            }
        }
        unset($data);
        $pattern = '@<div class="(even)* row-gray" data-id="details" data-type="tab">\s<div class="min">\s[0-9]*\'\s</div>\s<div class="ply tright">\s<div>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s<span class="ml4">\s</span>\s<span class="inc redcard"></span>\s</div>\s</div>\s<div class="sco"> &nbsp; </div>\s<div class="ply">\s<div>\s<span class="inc yellowcard"></span>\s<span class="mr4">\s</span>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s</div>\s</div>\s</div>@';
        preg_match_all($pattern, $page, $parsed);
        
        for($i = 0; $i < count($parsed[0]); $i++)
        {
            $minutes = '@<div class="min">\s[0-9]*\'\s</div>@';
            preg_match_all($minutes, $parsed[0][$i], $min);
            $names = '@<span class="name">[A-Za-z'.$specialchars.'\s]*</span>@';
            preg_match_all($names, $parsed[0][$i], $nam);
            $data[] = array_merge($min, $nam);
        }
        //print_r($data);
        if(isset($data)) {
            print '<pre>RED CARD LEFT YELLOW CARD RIGHT</pre>';
            for($i = 0; $i < count($data); $i++)
            {
                $info = array(
                    'match_id' => $id,
                    'card_type' => 'red',
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'player' => trim(strip_tags($data[$i][1][0])),
                    'team' => 'home',
                );
                print_r($info);
                if (!$this->card_model->card_exists($info))
                {
                    $this->card_model->new_card($info);
                }
                $info = array(
                    'match_id' => $id,
                    'card_type' => 'yellow',
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'player' => trim(strip_tags($data[$i][1][1])),
                    'team' => 'away',
                );
                print_r($info);
                if (!$this->card_model->card_exists($info))
                {
                    $this->card_model->new_card($info);
                }
            }
        }
        unset($data);
        $pattern = '@<div class="(even)* row-gray" data-id="details" data-type="tab">\s<div class="min">\s[0-9]*\'\s</div>\s<div class="ply tright">\s<div>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s<span class="ml4">\s</span>\s<span class="inc redcard"></span>\s</div>\s</div>\s<div class="sco"> &nbsp; </div>\s<div class="ply">\s<div>\s<span class="inc redyellowcard"></span>\s<span class="mr4">\s</span>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s</div>\s</div>\s</div>@';
        preg_match_all($pattern, $page, $parsed);
        
        for($i = 0; $i < count($parsed[0]); $i++)
        {
            $minutes = '@<div class="min">\s[0-9]*\'\s</div>@';
            preg_match_all($minutes, $parsed[0][$i], $min);
            $names = '@<span class="name">[A-Za-z'.$specialchars.'\s]*</span>@';
            preg_match_all($names, $parsed[0][$i], $nam);
            $data[] = array_merge($min, $nam);
        }
        //print_r($data);
        if(isset($data)) {
            print '<pre>RED CARD LEFT RED YELLOW CARD RIGHT</pre>';
            for($i = 0; $i < count($data); $i++)
            {
                $info = array(
                    'match_id' => $id,
                    'card_type' => 'red',
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'player' => trim(strip_tags($data[$i][1][0])),
                    'team' => 'home',
                );
                print_r($info);
                if (!$this->card_model->card_exists($info))
                {
                    $this->card_model->new_card($info);
                }
                $info = array(
                    'match_id' => $id,
                    'card_type' => 'yellow_red',
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'player' => trim(strip_tags($data[$i][1][1])),
                    'team' => 'away',
                );
                print_r($info);
                if (!$this->card_model->card_exists($info))
                {
                    $this->card_model->new_card($info);
                }
            }
        }
        unset($data);
        $pattern = '@<div class="(even)* row-gray" data-id="details" data-type="tab">\s<div class="min">\s[0-9]*\'\s</div>\s<div class="ply tright">\s<div>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s<span class="ml4">\s</span>\s<span class="inc redyellowcard"></span>\s</div>\s</div>\s<div class="sco"> &nbsp; </div>\s<div class="ply">\s<div> <span class=""></span>\s<span class="mr4">\s</span>\s<span class="name"></span>\s</div>\s</div>\s</div>@';
        preg_match_all($pattern, $page, $parsed);
        
        for($i = 0; $i < count($parsed[0]); $i++)
        {
            $minutes = '@<div class="min">\s[0-9]*\'\s</div>@';
            preg_match_all($minutes, $parsed[0][$i], $min);
            $names = '@<span class="name">[A-Za-z'.$specialchars.'\s]*</span>@';
            preg_match_all($names, $parsed[0][$i], $nam);
            $data[] = array_merge($min, $nam);
        }
        //print_r($data);
        if(isset($data)) {
            print '<pre>RED YELLOW CARD LEFT</pre>';
            for($i = 0; $i < count($data); $i++)
            {
                $info = array(
                    'match_id' => $id,
                    'card_type' => 'yellow_red',
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'player' => trim(strip_tags($data[$i][1][0])),
                    'team' => 'home',
                );
                print_r($info);
                if (!$this->card_model->card_exists($info))
                {
                    $this->card_model->new_card($info);
                }
            }
        }
        unset($data);
        $pattern = '@<div class="(even)* row-gray" data-id="details" data-type="tab">\s<div class="min">\s[0-9]*\'\s</div>\s<div class="ply tright">\s<div>\s<span class="name"></span>\s<span class="ml4">\s</span>\s<span class=""></span>\s</div>\s</div>\s<div class="sco"> &nbsp; </div>\s<div class="ply">\s<div> <span class="inc redyellowcard"></span>\s<span class="mr4">\s</span>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s</div>\s</div>\s</div>@';
        preg_match_all($pattern, $page, $parsed);
        
        for($i = 0; $i < count($parsed[0]); $i++)
        {
            $minutes = '@<div class="min">\s[0-9]*\'\s</div>@';
            preg_match_all($minutes, $parsed[0][$i], $min);
            $names = '@<span class="name">[A-Za-z'.$specialchars.'\s]*</span>@';
            preg_match_all($names, $parsed[0][$i], $nam);
            $data[] = array_merge($min, $nam);
        }
        //print_r($data);
        if(isset($data)) {
            print '<pre>RED YELLOW CARD RIGHT</pre>';
            for($i = 0; $i < count($data); $i++)
            {
                $info = array(
                    'match_id' => $id,
                    'card_type' => 'yellow_red',
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'player' => trim(strip_tags($data[$i][1][1])),
                    'team' => 'away',
                );
                print_r($info);
                if (!$this->card_model->card_exists($info))
                {
                    $this->card_model->new_card($info);
                }
            }
        }
        unset($data);
        $pattern = '@<div class="(even)* row-gray" data-id="details" data-type="tab">\s<div class="min">\s[0-9]*\'\s</div>\s<div class="ply tright">\s<div>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s<span class="ml4">\s</span>\s<span class="inc redyellowcard"></span>\s</div>\s</div>\s<div class="sco"> &nbsp; </div>\s<div class="ply">\s<div>\s<span class="inc redyellowcard"></span>\s<span class="mr4">\s</span>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s</div>\s</div>\s</div>@';
        preg_match_all($pattern, $page, $parsed);
        
        for($i = 0; $i < count($parsed[0]); $i++)
        {
            $minutes = '@<div class="min">\s[0-9]*\'\s</div>@';
            preg_match_all($minutes, $parsed[0][$o], $min);
            $names = '@<span class="name">[A-Za-z'.$specialchars.'\s]*</span>@';
            preg_match_all($names, $parsed[0][$o], $nam);
            $data[] = array_merge($min, $nam);
        }
        //print_r($data);
        if(isset($data)) {
            print '<pre>RED YELLOW CARDS BOTH SIDES</pre>';
            for($i = 0; $i < count($data); $i++)
            {
                $info = array(
                    'match_id' => $id,
                    'card_type' => 'yellow_red',
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'player' => trim(strip_tags($data[$i][1][0])),
                    'team' => 'home',
                );
                print_r($info);
                if (!$this->card_model->card_exists($info))
                {
                    $this->card_model->new_card($info);
                }
                $info = array(
                    'match_id' => $id,
                    'card_type' => 'yellow_red',
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'player' => trim(strip_tags($data[$i][1][1])),
                    'team' => 'away',
                );
                print_r($info);
                if (!$this->card_model->card_exists($info))
                {
                    $this->card_model->new_card($info);
                }
            }
        }
        unset($data);
        $pattern = '@<div class="(even)* row-gray" data-id="details" data-type="tab">\s<div class="min">\s[0-9]*\'\s</div>\s<div class="ply tright">\s<div>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s<span class="ml4">\s</span>\s<span class="inc redyellowcard"></span>\s</div>\s</div>\s<div class="sco"> &nbsp; </div>\s<div class="ply">\s<div>\s<span class="inc yellowcard"></span>\s<span class="mr4">\s</span>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s</div>\s</div>\s</div>@';
        preg_match_all($pattern, $page, $parsed);
        
        for($i = 0; $i < count($parsed[0]); $i++)
        {
            $minutes = '@<div class="min">\s[0-9]*\'\s</div>@';
            preg_match_all($minutes, $parsed[0][$o], $min);
            $names = '@<span class="name">[A-Za-z'.$specialchars.'\s]*</span>@';
            preg_match_all($names, $parsed[0][$o], $nam);
            $data[] = array_merge($min, $nam);
        }
        //print_r($data);
        if(isset($data)) {
            print '<pre>RED YELLOW CARD LEFT YELLOW RIGHT</pre>';
            for($i = 0; $i < count($data); $i++)
            {
                $info = array(
                    'match_id' => $id,
                    'card_type' => 'yellow_red',
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'player' => trim(strip_tags($data[$i][1][0])),
                    'team' => 'home',
                );
                print_r($info);
                if (!$this->card_model->card_exists($info))
                {
                    $this->card_model->new_card($info);
                }
                $info = array(
                    'match_id' => $id,
                    'card_type' => 'yellow',
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'player' => trim(strip_tags($data[$i][1][1])),
                    'team' => 'away',
                );
                print_r($info);
                if (!$this->card_model->card_exists($info))
                {
                    $this->card_model->new_card($info);
                }
            }
        }
        unset($data);
        $pattern = '@<div class="(even)* row-gray" data-id="details" data-type="tab">\s<div class="min">\s[0-9]*\'\s</div>\s<div class="ply tright">\s<div>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s<span class="ml4">\s</span>\s<span class="inc redyellowcard"></span>\s</div>\s</div>\s<div class="sco"> &nbsp; </div>\s<div class="ply">\s<div>\s<span class="inc redcard"></span>\s<span class="mr4">\s</span>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s</div>\s</div>\s</div>@';
        preg_match_all($pattern, $page, $parsed);
        
        for($i = 0; $i < count($parsed[0]); $i++)
        {
            $minutes = '@<div class="min">\s[0-9]*\'\s</div>@';
            preg_match_all($minutes, $parsed[0][$i], $min);
            $names = '@<span class="name">[A-Za-z'.$specialchars.'\s]*</span>@';
            preg_match_all($names, $parsed[0][$i], $nam);
            $data[] = array_merge($min, $nam);
        }
        //print_r($data);
        if(isset($data)) {
            print '<pre>RED YELLOW CARD LEFT RED RIGHT</pre>';
            for($i = 0; $i < count($data); $i++)
            {
                $info = array(
                    'match_id' => $id,
                    'card_type' => 'yellow_red',
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'player' => trim(strip_tags($data[$i][1][0])),
                    'team' => 'home',
                );
                print_r($info);
                if (!$this->card_model->card_exists($info))
                {
                    $this->card_model->new_card($info);
                }
                $info = array(
                    'match_id' => $id,
                    'card_type' => 'red',
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'player' => trim(strip_tags($data[$i][1][1])),
                    'team' => 'away',
                );
                print_r($info);
                if (!$this->card_model->card_exists($info))
                {
                    $this->card_model->new_card($info);
                }
            }
        }
        unset($data);
        
        // GOALS PARSING START
        $pattern = '@<div class="(even)* row-gray" data-id="details" data-type="tab">\s<div class="min">\s[0-9]*\'\s</div>\s<div class="ply tright">\s<div>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s<span class="ml4">\s*([a-z\.\(\)]*)\s*</span>\s<span class="inc goal"></span>\s</div>\s(<div class="hidden" data-type="details">\s<span class="assist name">[A-Za-z'.$specialchars.'\(\)\s]*</span>\s<span class=""></span>\s</div>\s)*</div>\s<div class="sco">\s[0-9\s\-]*\s</div>\s<div class="ply">\s<div>\s<span class=""></span>\s<span class="mr4">\s</span>\s<span class="name"></span>\s</div>\s</div>\s</div>@';
        preg_match_all($pattern, $page, $parsed);
        for($i = 0; $i < count($parsed[0]); $i++)
        {
            $minutes = '@<div class="min">\s[0-9]*\'\s</div>@';
            preg_match_all($minutes, $parsed[0][$i], $min);
            $names = '@<span class="name">[A-Za-z'.$specialchars.'\s]*</span>@';
            preg_match_all($names, $parsed[0][$i], $nam);
            $types = '@<span class="ml4">\s*([a-z\.\(\)]*)\s*</span>@';
            preg_match_all($types, $parsed[0][$i], $typ);
            $scores = '@<div class="sco">\s[0-9\s\-]*\s</div>@';
            preg_match_all($scores, $parsed[0][$i], $sco);
            $assists = '@<span class="assist name">[A-Za-z'.$specialchars.'\(\)\s]*@';
            preg_match_all($assists, $parsed[0][$i], $ass);
            $data[] = array_merge($min, $nam, $typ, $sco, $ass);
        }
        //print_r($data);
        if(isset($data)) {
            print '<pre>LEFT GOAL</pre>';
            for($i = 0; $i < count($data); $i++)
            {
                $assist = (isset($data[$i][5][0])) ? trim(strip_tags(str_replace(" (assist)", "", $data[$i][5][0]))) : '';
                $info = array(
                    'match_id' => $id,
                    'score' => strip_tags(str_replace(" ", "", $data[$i][4][0])),
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'type' => trim(strip_tags($data[$i][2][0])),
                    'assist' => str_replace(" (assist)", "", $assist),
                    'player' => trim(strip_tags($data[$i][1][0])),
                    'team' => 'home',
                );
                print_r($info);
                if (!$this->goal_model->goal_exists($info))
                {
                    $this->goal_model->new_goal($info);
                }
            }
            $left_goals = count($data);
        }
        unset($data);
        $pattern = '@<div class="(even)* row-gray" data-id="details" data-type="tab">\s<div class="min">\s[0-9]*\'\s</div>\s<div class="ply tright">\s<div>\s<span class="name"></span>\s<span class="ml4">\s</span>\s<span class=""></span>\s</div>\s</div>\s<div class="sco">\s[0-9\s\-]*\s</div>\s<div class="ply">\s<div>\s<span class="inc goal"></span>\s<span class="mr4">\s*([a-z\.\(\)]*)\s*</span>\s<span class="name">[A-Za-z'.$specialchars.'\s]*</span>\s</div>\s(<div class="hidden" data-type="details">\s<span class="assist name">[A-Za-z'.$specialchars.'\(\)\s]*</span>\s</div>\s)*</div>\s</div>@U';
        preg_match_all($pattern, $page, $parsed);
        for($i = 0; $i < count($parsed[0]); $i++)
        {
            $minutes = '@<div class="min">\s[0-9]*\'\s</div>@';
            preg_match_all($minutes, $parsed[0][$i], $min);
            $names = '@<span class="name">[A-Za-z'.$specialchars.'\s]*</span>@';
            preg_match_all($names, $parsed[0][$i], $nam);
            $types = '@<span class="mr4">\s*([a-z\.\(\)]*)\s*</span>@';
            preg_match_all($types, $parsed[0][$i], $typ);
            $scores = '@<div class="sco">\s[0-9\s\-]*\s</div>@';
            preg_match_all($scores, $parsed[0][$i], $sco);
            $assists = '@<span class="assist name">[A-Za-z'.$specialchars.'\(\)\s]*</span>@';
            preg_match_all($assists, $parsed[0][$i], $ass);
            $data[] = array_merge($min, $nam, $typ, $sco, $ass);
        }
        //print_r($data);
        if(isset($data)) {
            print '<pre>RIGHT GOAL</pre>';
            for($i = 0; $i < count($data); $i++)
            {
                $assist = (isset($data[$i][5][0])) ? trim(strip_tags(str_replace("(assist) ", "", $data[$i][5][0]))) : '';
                $info = array(
                    'match_id' => $id,
                    'score' => strip_tags(str_replace(" ", "", $data[$i][4][0])),
                    'min' => strip_tags(str_replace("'", "", $data[$i][0][0])),
                    'type' => trim(strip_tags($data[$i][2][0])),
                    'assist' => str_replace("(assist) ", "", $assist),
                    'player' => trim(strip_tags($data[$i][1][1])),
                    'team' => 'away',
                );
                print_r($info);
                if (!$this->goal_model->goal_exists($info))
                {
                    $this->goal_model->new_goal($info);
                }
            }
            $right_goals = count($data);
        }
        unset($data);
        // GOALS REPARSING (IN CASE OF WRONG PARSING)
        $pattern = '@<div class="row row-tall">(.*)</div> <div class="star hidden"@';
        preg_match_all($pattern, $page, $parsed);
        for($i = 0; $i < count($parsed[0]); $i++)
        {
            $score = '@<div class="sco">\s[0-9\-\s]*\s</div>@';
            preg_match_all($score, $parsed[0][$i], $sco);
            $data[] = $sco;
        }
        //print_r($data);
        if(isset($data)) {
            print '<pre>CORRECT SCORE JUST IN CASE</pre>';
            for($i = 0; $i < count($data); $i++)
            {
                $info = array(
                    'score' => strip_tags(str_replace(" ", "", $data[$i][0][0])),
                );
                print_r($info);
                if($match['score'] != $info['score'])
                {
                    $this->match_model->update_match($info, $id);
                }
                if($match['score'] == '?-?')
                {
                    $left = (isset($left_goals)) ? $left_goals : 0;
                    $right = (isset($right_goals)) ? $right_goals : 0;
                    $total_goals = $left.'-'.$right;
                    $update_fields = array(
                      'score' => $total_goals,  
                    );
                    $this->match_model->update_match($update_fields, $id);
                }
            }
        }
        unset($data);
        
        $update_fields = array(
            'parsed' => 1,
        );
        $this->match_model->update_match($update_fields, $id);
    }

    function view_match($id)
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
    
    function view_match_pre($id)
    {
        $this->load->model(array('match_pre_model', 'team_pre_model'));

        $match = $this->match_pre_model->get_match_pre($id);
        if (!isset($match) || empty($match))
        {
            $this->notices->SetError('No match found');
            redirect('admincp/livescore/list_matches');
        }
        $home = $this->team_pre_model->get_team($match['team1_pre']);
        $away = $this->team_pre_model->get_team($match['team2_pre']);
        $data = array(
            'match' => $match,
            'home' => $home,
            'away' => $away
        );
        $this->load->view('view_match_pre', $data);
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
        curl_setopt($cUrl, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($cUrl, CURLOPT_TIMEOUT, '3');
        //$pageContent = trim(curl_exec($cUrl));
        $pageContent = curl_exec($cUrl);
        curl_close($cUrl);

        return $pageContent;
    }

    public function fix_star_teams()
    {
        $this->load->model(array('team_model', 'match_model'));
        $filters = array();
        $filters['name'] = '\*';
        $teams = $this->team_model->get_teams($filters);
        print '<pre>';
        //print_r($teams);

        foreach ($teams as $t)
        {
            print_r($t);
            $team_star_id = $t['team_id'];
            $team = str_replace(' *', '', $t['name']);
            //echo "team = $team<br/>";
            $filters['equal'] = 1;
            $filters['name'] = $team;
            $team2 = $this->team_model->get_teams($filters);
            $team_id = $team2[0]['team_id'];
            print_r($team2);

            echo "team_star_id = $team_star_id team_id = $team_id<br/>";
            $this->team_model->delete_team($team_star_id);
            $i++;

            // $matches = $this->match_model->get_matches_by_team_id(array('team_id' => $team_star_id);
            // if(!empty($matches)) {
            //     print '<pre>MATCH';
            //     print_r($matches);
            // }                        
        }
        echo "$i teams deleted<br/>";
    }

    public function delete_duplicate_teams()
    {
        $this->load->model(array('team_model', 'match_model'));
        $teams = $this->team_model->get_duplicates();
        print '<pre>';
        //print_r($teams);

        if ($teams)
        {
            foreach ($teams as $t)
            {
                $filters['country_id'] = $t['country_id'];
                $filters['name'] = $t['name'];
                $teamz = $this->team_model->get_team_by_country_and_name($filters);
                print_r($teamz);
                foreach ($teamz as $tz)
                {
                    $matches = $this->match_model->get_matches_by_team_id(array('team_id' => $tz['team_id']));
                    $count = count($matches);
                    if ($count)
                    {
                        echo "COUNT = " . count($matches) . '<br/>';
                        print_r($matches);
                    }
                }

                if (!$count)
                {
                    //delete duplicate team with higher team_id
                    //$this->team_model->delete_team($teamz[1]['team_id']);
                }
            }
        }
        else
        {
            echo 'No duplicate teams found!';
        }
    }

    function list_duplicate_teams()
    {
        $this->load->library('dataset');
        $this->load->model('team_model');

        $duplicates_count = $this->team_model->get_null_teams_num_rows();

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
                'name' => 'SIMILAR TEAMS',
                'type' => 'text',
                'width' => '15%',
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

        if (isset($filters_decode) && is_array($filters_decode))
        {
            foreach ($filters_decode as $key => $val)
            {
                $filters[$key] = $val;
            }
        }

        $this->dataset->columns($columns);
        $this->dataset->datasource('team_model', 'get_null_teams', $filters);

        $this->dataset->base_url(site_url('admincp5/livescore/list_duplicate_teams/'));
        $this->dataset->rows_per_page($filters['limit']);

        // total rows
        unset($filters['limit']);
        $total_rows = $this->team_model->get_null_teams_num_rows($filters);

        $this->dataset->total_rows($total_rows);

        // initialize the dataset
        $this->dataset->initialize();
        // add actions
        $this->dataset->action('Delete', 'admincp/livescore/delete_team');
        $this->load->view('list_teams_duplicate');
    }

    function list_similar_teams_by_team_id($id)
    {
        $this->load->library('dataset');
        $this->load->model('team_model');

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

        if (isset($filters_decode) && is_array($filters_decode))
        {
            foreach ($filters_decode as $key => $val)
            {
                $filters[$key] = $val;
            }
        }

        $filters['id'] = $id;
        $this->dataset->columns($columns);
        $this->dataset->datasource('team_model', 'get_similar_teams_by_team_id', $filters);

        $this->dataset->base_url(site_url('admincp5/livescore/list_similar_teams_by_team_id/'));
        $this->dataset->rows_per_page($filters['limit']);

        // total rows
        unset($filters['limit']);
        $total_rows = $this->team_model->get_similar_teams_by_team_id_num_rows($filters);

        $this->dataset->total_rows($total_rows);

        // initialize the dataset
        $this->dataset->initialize();
        // add actions
        $this->dataset->action('Delete', 'admincp/livescore/delete_team');
        $this->load->view('list_teams');
    }

    public function merge_teams()
    {
        $this->load->library('admin_form');

        $form = new Admin_Form;
        $form->fieldset('Team to keep');
        $form->text('Team to keep', 'team_to_keep', '', 'Team id', true, false, false);
        $form->fieldset('Team to remove');
        $form->text('Team to remove', 'team_to_remove', '', 'Team id', true, false, false);
        // Compile View data
        $data = array(
            'form' => $form->display(),
            'form_action' => site_url('admincp5/livescore/merge_teams_list')
        );
        $this->load->view('merge_teams', $data);
    }

    public function merge_teams_list()
    {
        $this->load->model('match_model');
        $this->load->library('dataset');

        $team_to_keep = $this->input->post('team_to_keep');
        $team_to_remove = $this->input->post('team_to_remove');
        //echo $team_to_keep . '-' . $team_to_remove . PHP_EOL;
        $this->admin_navigation->module_link('Merge teams', site_url('admincp5/livescore/merge_teams_validate/' . $team_to_keep . '/' . $team_to_remove));
        $data = array(
            'team_to_keep' => $team_to_keep,
            'team_to_remove' => $team_to_remove
        );
        $this->load->view('merge_teams_list', $data);
    }

    public function merge_teams_validate($team_to_keep, $team_to_remove)
    {
        $this->load->model(array('match_model', 'team_model'));
        $this->load->library('dataset');
        $columns = array(
            array(
                'name' => 'COMPETITION',
                'type' => 'name',
                'width' => '15%',
            ),
            array(
                'name' => 'DATE',
                'width' => '15%',
                'type' => 'text',
            ),
            array(
                'name' => 'HOME TEAM',
                'type' => 'text',
                'width' => '15%',
            ),
            array(
                'name' => 'AWAY TEAM',
                'type' => 'text',
                'width' => '15%',
            ),
            array(
                'name' => 'SCORE',
                'width' => '15%',
                'type' => 'text',
            ),
            array(
                'name' => 'LINK',
                'width' => '15%',
                'type' => 'text',
            ),
        );
        $this->dataset->columns($columns);
        $this->dataset->datasource('team_model', 'get_dummy', array('id' => 1));
        $this->dataset->rows_per_page(1);
        $this->dataset->total_rows(1);

        // initialize the dataset
        $this->dataset->initialize();

        $team = $this->team_model->get_team($team_to_keep);
        $team_to_keep_name = $team['name'];
        $team = $this->team_model->get_team($team_to_remove);
        $team_to_remove_name = $team['name'];

        $filters['team_to_keep'] = $team_to_keep;
        $filters['team_to_remove'] = $team_to_remove;
        $filters['which_team'] = 2;
        $old = $new = 0;

        $matches_second_team = $this->match_model->get_matches_by_team_id_partial($filters);

        foreach ($matches_second_team as $key => $value)
        {
            if ($matches_second_team[$key]['status'] == 'old')
            {
                $old++;
            }
            elseif ($matches_second_team[$key]['status'] == 'new')
            {
                $new++;
            }
        }

        $data = array(
            'team_to_keep' => $team_to_keep,
            'team_to_keep_name' => $team_to_keep_name,
            'team_to_remove' => $team_to_remove,
            'team_to_remove_name' => $team_to_remove_name,
            'old' => $old,
            'new' => $new,
            'matches_second_team' => $matches_second_team
        );

        $this->load->view('merge_teams_validate', $data);
    }

    public function merge_teams_ok($team_to_keep, $team_to_remove)
    {
        $this->load->model(array('match_model', 'team_model'));
        $team = $this->team_model->get_team($team_to_keep);
        $team_to_keep_name = $team['name'];
        $team = $this->team_model->get_team($team_to_remove);
        $team_to_remove_name = $team['name'];
        $this->team_model->merge_teams($team_to_keep, $team_to_remove);
        $data = array(
            'team_to_keep_name' => $team_to_keep_name,
            'team_to_remove_name' => $team_to_remove_name
        );
        $this->load->view('merge_teams_ok', $data);
    }

    // fk_country_name (country_id, name)
    public function trim_teams()
    {
        $this->load->model('team_model');
        $teams = $this->team_model->get_teams();
        foreach ($teams as $team)
        {
            $team = $this->team_model->get_team($team['team_id']);
            $update_fields = array('name' => trim($team['name']));
            $this->team_model->update_team($update_fields, $team['team_id']);
        }
        echo '<div align="center">';
        echo count($teams) . ' team names successfully trimmed<br/>';
        echo '<a href="' . site_url('admincp/livescore/list_teams') . '">Back</a>';
        echo '</div>';
    }

    public function multiple_teams()
    {
        $this->load->model('team_model');
        $this->team_model->get_multiple_teams();
    }
    
    public function merge_competitions()
    {
        $CI = & get_instance();
        $CI->load->library('admin_form');
        $this->load->model(array('country_model', 'competition_model', 'competition_custom_model', 'competition_merged_model'));

        $this->admin_navigation->module_link('Add custom competition', site_url('admincp5/livescore/add_custom_competition'));
        $this->admin_navigation->module_link('View custom competitions', site_url('admincp5/livescore/view_custom_competitions'));
        
        if($this->input->post('submit'))
        {
            $teams = $this->input->post('to');
            foreach($teams as $team)
            {
                $insert_fields = array(
                    'name' => $team,
                    'competition_id' => $this->input->post('country_name'),
                    'parent_id' => $this->input->post('custom_competition')
                );
                print_r($insert_fields);
                $this->competition_merged_model->new_competition($insert_fields);
            }
            $this->notices->SetNotice('Competitions merged successfully.');
            redirect('admincp5/livescore/merge_competitions');
        }
        else
        {
            $form = new Admin_form();
            $params = array();
            $params['dropdown'] = 1;
            $countries = $this->country_model->get_countries_merge_competitions($params);

            $form->fieldset('Add competition type');
            $form->dropdown('Test', 'test', $countries, FALSE, FALSE, FALSE, FALSE, FALSE);

            $query = $this->competition_model->get_competitions();

            foreach ($query as $key => $val)
            {
                $options[] = $val['country_name'].' - '.$val['name'];
            }

            $data = array(
                'user' => array(),
                'options' => $options,
                'countries' => $countries,
                'form_title' => 'Merge competitions',
                'form_action' => site_url('admincp5/livescore/merge_competitions')
            );
            $this->load->view('merge_competitions', $data);
        }
        
    }
    
    public function add_custom_competition()
    {
        $CI = & get_instance();
        $CI->load->library(array('form_validation', 'admin_form'));
        $this->load->model(array('country_model', 'competition_model', 'competition_type_model', 'competition_custom_model'));

        // Form validation rules set
        $CI->form_validation->set_rules('name', 'Name', 'required|min_length[3]|xss_clean');

        $this->admin_navigation->module_link('Merge competitions', site_url('admincp5/livescore/merge_competitions'));
        $this->admin_navigation->module_link('Add competition type', site_url('admincp5/livescore/add_competition_type'));

        // Let's check if the form is submited and check for errors
        if ($CI->form_validation->run() === FALSE)
        {
            $form = new Admin_form;
            $countries = $types = $params = array();
            $params['dropdown'] = 1;
            $countries = $this->country_model->get_countries($params);
            $types = $this->competition_type_model->get_type($params);

            $form->fieldset('Add custom competitions');
            $form->text('Competition name', 'name', '', 'Competition name to be introduced', TRUE, 'e.g., Champions League', TRUE);
            $form->dropdown('Country', 'country_id', $countries);
            $form->dropdown('Type', 'type_id', $types);
            
            $query = $this->competition_model->get_competitions();
            foreach ($query as $key => $val)
            {
                $options[] = $val['country_name'].' - '.$val['name'];
            }
            $data = array(
                'options' => $options,
                'form' => $form->display(),
                'form_title' => 'Add custom competition',
                'form_action' => site_url('admincp5/livescore/add_custom_competition'),
                'action' => 'new',
            );
            $this->load->view('add_custom_competition', $data);
        }
        else
        {
            $insert_fields = array(
                'name' => $this->input->post('name'),
                'country' => $this->input->post('country_id'),
                'type' => $this->input->post('type_id')
            );
            $this->competition_custom_model->new_competition($insert_fields);
            $this->notices->SetNotice('Custom competition added successfully.');
            redirect('admincp5/livescore/add_custom_competition');
        }

    }
    
    public function add_competition_type()
    {
        $CI = & get_instance();
        $CI->load->library(array('form_validation', 'admin_form'));
        $this->load->model('competition_type_model');

        // Form validation rules set
        $CI->form_validation->set_rules('name', 'Name', 'required|min_length[3]|xss_clean');

        // Let's check if the form is submited and check for errors
        if ($CI->form_validation->run() === FALSE)
        {
            $form = new Admin_form;
            $form->fieldset('Add competition type');
            $form->text('Competition type', 'name', '', 'Competition type to be introduced', TRUE, 'e.g., Championship', TRUE);
            $data = array(
                'form' => $form->display(),
                'form_title' => 'Add competition type',
                'form_action' => site_url('admincp5/livescore/add_competition_type'),
                'action' => 'new',
            );
            $this->load->view('add_custom_competition', $data);
        }
        else
        {
            $insert_fields = array(
                'name' => $this->input->post('name')
            );
            $this->competition_type_model->new_type($insert_fields);
            $this->notices->SetNotice('Competition type added successfully.');
            redirect('admincp5/livescore/add_competition_type');
        }
    }
    
    function view_custom_competitions_selects($id)
    {
        $this->load->model('competition_custom_model');

        $filters['country'] = $id;
        $competition_name = '<option value="0">Select competition</option>';
        
        $competition = $this->competition_custom_model->get_competitions($filters);
        
        for($i = 0; $i < count($competition); $i++)
        {
            $competition_name .= '<option value="'.$competition[$i]['id'].'">'.$competition[$i]['name'].'</option>';
        }

        echo $competition_name;
    }

}
