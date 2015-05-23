<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Content Control Panel
 * 
 * FLASHSCORE Parsing
 *
 * @author Weblight.ro
 * @copyright Weblight.ro
 * @package BJ Tool
 *
 */
class Admincp9 extends Admincp_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->admin_navigation->parent_active('livescore');
        //error_reporting(E_ALL^E_NOTICE);
        //error_reporting(E_WARNING);
    }

    public function index()
    {
        echo 'index admincp9';
        //redirect('admincp/livescore/list_competitions');
    }
    
    public function algorithm($date = '2013-12-30', $atLeastMatches = 2, $accuracy = 50)
    {        
        $filters['limit'] = 1000;
        
        $this->load->model('match_model');
        $this->load->library('dataset');
        
         $columns = array(
            array(
                'name' => 'CRITERIA',
                'width' => '25%',
                'type' => 'text'
            ),
            array(
                'name' => 'OK',
                'width' => '25%',
                'type' => 'text'
            ),
            array(
               'name' => 'Total',
                'width' => '25%',
                'type' => 'text'
            ),
            array(
                'name' => 'Percentage',
                'width' => '25%',
                'type' => 'text'
            )
        );
        
        //$data = $this->match_model->algorithm_success_all($date, $atLeastMatches);
        $this->dataset->columns($columns);
        $this->dataset->datasource('match_model', 'algorithm_success_all', array('date' => $date, 'atLeastMatches' => $atLeastMatches, 'accuracy' => $accuracy));
        $this->dataset->base_url(site_url('admincp9/livescore/algorithm'));
        $this->dataset->rows_per_page($filters['limit']);

        // total rows
        unset($filters['limit']);
        //$total_rows = $this->competition_model->get_num_rows($filters);
        $total_rows = 100;
        $this->dataset->total_rows($total_rows);

        // initialize the dataset
        $this->dataset->initialize();
        
        $data = array(
          'accuracy' =>  $accuracy 
        );
        
        $this->load->view('algorithm', $data);
        
//        print '<pre>';
//        print_r($data);
//        print '</pre>';
    }
    
    public function h2h($team1, $team2, $match_date)
    {
        $this->load->model('match_model');
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

        $filters = array('team1' => $team1, 'team2' => $team2, 'match_date' => $match_date, 'include_competitions' => 1);
        $filters['limit'] = 100;

        if (isset($_GET['filters']))
        {
            $filters_decode = unserialize(base64_decode($this->asciihex->HexToAscii($_GET['filters'])));
        }

        
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

        $this->dataset->columns($columns);
        $this->dataset->datasource('match_model', 'get_h2h', $filters);
        $this->dataset->base_url(site_url('admincp8/livescore/h2h'));
        $this->dataset->rows_per_page($filters['limit']);

        // total rows
        unset($filters['limit']);
        //$total_rows = $this->competition_model->get_num_rows($filters);
        $total_rows = 100;
        $this->dataset->total_rows($total_rows);

        // initialize the dataset
        $this->dataset->initialize();
        // add actions        
        $this->load->view('h2h');
    }

    private function getUrl($url)
    {
        $cUrl = curl_init();
        $headers[] = 'Request: GET / HTTP/1.1';
        $headers[] = 'Accept: text/html, application/xhtml+xml, */*';
        $headers[] = 'Accept-language: en-US';
        $headers[] = 'User-Agent: Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)';
        $headers[] = 'Accept-Encoding: gzip, deflate';
        $headers[] = 'Host: www.flashscore.com';
        $headers[] = 'Connection: Keep-Alive';
        $headers[] = 'Cookie: __utmc=175935605; __utma=175935605.257855743.1386761213.1392630161.1392637709.6; __utmz=175935605.1386761213.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); __utmb=175935605.1.10.1392637709';
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

    function flashscore_get_ids()
    {
        //$url = 'http://www.flashscore.com/';
        $url = 'http://d.flashscore.com/x/feed/f_1_0_2_en_1';
        //$opts = array(
        //	'http'=>array(
        //		'header'=>"Request: GET / HTTP/1.1"."Accept: text/html, application/xhtml+xml, */*".
        //		"Accept-language: en-US"."User-Agent: Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)"."Accept-Encoding: gzip, deflate"."Host: www.flashscore.com"."Connection: Keep-Alive"."Cookie: __utmc=175935605; __utma=175935605.257855743.1386761213.1392630161.1392637709.6; __utmz=175935605.1386761213.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); __utmb=175935605.1.10.1392637709"
        //	)
        //);
        //$context = stream_context_create($opts);
        //$file = file_get_contents($url, false, $context);
        $data = $this->getUrl($url);
        echo gzuncompress($data);
        //$c = file_get_contents($url);
        //echo gzuncompress($c);
        die;
        //<tr id="g_1_(.*)" class=" tr-first even stage-finished" style="cursor: pointer;">
        $pattern = '@<tr id="g_1_(.*)" class=" tr-first even stage-finished" style="cursor: pointer;">@'; //<tr id="g_1_8x09Q2i9" class=" tr-first even stage-finished" style="cursor: pointer;">
        preg_match_all($pattern, $page, $event_id);
        print '<pre>ID ';
        print_r($event_id);
        print '</pre>';
    }

}
