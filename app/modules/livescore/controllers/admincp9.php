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
    
    public function form($team, $match_date)
    {        
        $this->load->model('match_model');
        $this->load->library('dataset');        

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
            )
        );

        $filters = array('team1' => $team, 'forTeam1' => 1, 'match_date' => $match_date, 'include_competitions' => 1);
        $filters['limit'] = 20;

        if (strlen($this->input->get('filters')))
        {
            $filters_decode = unserialize(base64_decode($this->asciihex->HexToAscii(strlen($this->input->get('filters')))));
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
        $this->dataset->datasource('match_model', 'get_form', $filters);
        $this->dataset->base_url(site_url('admincp9/livescore/form'));
        $this->dataset->rows_per_page($filters['limit']);

        // total rows
        unset($filters['limit']);
        $filters['count'] = 1;
        $total_rows = $this->match_model->get_form($filters);        
        $this->dataset->total_rows($total_rows);

        // initialize the dataset
        $this->dataset->initialize();
        // add actions        
        $this->load->view('form');
    }        
    
    public function predict_choose()
    {
        $this->load->model(array('match_today_model'));
        $date = $this->match_today_model->getDate();
        
        $this->load->library('admin_form');
        $form = new Admin_form;
        $form->fieldset('Date to predict for');
        $form->date('Date', 'date', $date, '', true);        
        $form->fieldset('Head to head');
        $form->checkbox('Head to head', 'h2h', '1', true);
        $form->fieldset('Teams form');
        $form->checkbox('Teams form', 'teamsForm', '1');
        $data = array(
            'form' => $form->display(),
            'form_title' => 'Choose your date and criteria',
            'form_action' => site_url('admincp9/livescore/predict_validate')
        );
        $this->load->view('predict_choose', $data);
    }
    
    public function predict_validate()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('date', 'Date', 'required|trim');        

        if ($this->form_validation->run() === false)
        {
            $this->notices->SetError('Required fields.');
            $error = true;
        }

        if (isset($error))
        {
            redirect('admincp9/livescore/predict_choose');
            return false;
        }
        
        $criteria = array();
        
        if (strlen($this->input->post('h2h'))) {
            $criteria[] = 'h2h';
        }
        
        if (strlen($this->input->post('teamsForm'))) {
            $criteria[] = 'form';
        }
        
        if (empty($criteria)) {
            $criteria[] = 'h2h';
        }

        $date = $this->input->post('date');
        
        redirect('admincp9/livescore/predict/' . implode('.', $criteria) . '/' . $date);        
    }
    
    public function predict($predictCriteria = '', $date = '')
    {
        $this->load->model(array('match_model', 'match_today_model'));
        $this->load->library('dataset');               
        
        $isToday = $this->match_today_model->isDate($date);
        
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
                'width' => '20%',
                'filter' => 'competition_name',
                'type' => 'name',
                'sort_column' => 'competition_name',
            ),
            array(
                'name' => 'DATE',
                'width' => '15%',               
            ),
            array(
                'name' => 'HOME',
                'width' => '10%',
                'filter' => 'team1',
                'type' => 'text',
                'sort_column' => 'team1',
            ),
            array(
                'name' => 'FORM',
                'width' => '5%',                
                'type' => 'text'                
            ),
            array(
                'name' => 'AWAY',
                'width' => '10%',
                'filter' => 'team2',
                'type' => 'text',
                'sort_column' => 'team2',
            ),
            array(
                'name' => 'FORM',
                'width' => '5%',                
                'type' => 'text'                
            ),
            array(
                'name' => 'SCORE',
                'width' => '5%',
                'filter' => 'score',
                'type' => 'text',
                'sort_column' => 'score',
            ),            
            array(
                'name' => 'H2H',
                'width' => '5%',
                'type' => 'text,'
            ),
            array(
                'name' => 'PERCENTAGE',
                'width' => '5%',
                'type' => 'text,'
            ),
            array(
                'name' => 'PREDICTION',
                'width' => '10%',
                'type' => 'text,'
            ),
        );

        $filters = array();
        $filters['limit'] = 20;
        
        if (strlen($this->input->get('offset'))) {
            $filters['offset'] = $this->input->get('offset');
        }       
        
        $filterPossibleValues = array(
            'offset', 
            'country_name',
            'competition_name',
            'team1',
            'team2',
            'score',
            'match_date_start',
            'match_date_end'
        );
                        
        foreach ($filterPossibleValues as $filterValue) {
            if (strlen($this->input->get($filterValue))) {
                $filters[$filterValue] = $this->input->get($filterValue);
            }
        } 
        
        if (strlen($this->input->get('filters')))
        {
            $filters_decode = unserialize(base64_decode($this->asciihex->HexToAscii($this->input->get('filters'))));
//            print '<pre>Filters decode';
//            print_r($filters_decode);
//            print '</pre>';
//            die;
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
        //unset($filters['limit']);
        $this->dataset->columns($columns);
        
        $methodName = 'get_matches_predict';
        $criteria = explode('.', $predictCriteria);
        foreach ($criteria as $crit) {
            $filters[$crit] = 1;
        }                
        $filters['date'] = $date;
                       
        if ($isToday) {
            $this->dataset->datasource('match_today_model', $methodName, $filters);
        } else {
            $this->dataset->datasource('match_model', $methodName, $filters);
        }
        
        $this->dataset->base_url(site_url('admincp9/livescore/predict/' . $predictCriteria . '/' . $date));
        $this->dataset->rows_per_page($filters['limit']);

        // total rows
        unset($filters['limit']);
         if ($isToday) {
             $total_rows = $this->match_today_model->get_num_rows($filters);
         } else {
             $filters['match_date_start'] = $date;
             $filters['match_date_end'] = $date;
             $total_rows = $this->match_model->get_num_rows($filters);
         }
        
        $this->dataset->total_rows($total_rows);

        // initialize the dataset
        $this->dataset->initialize();
        $data = array(
          'criteria' =>  $criteria
        );
        $this->load->view('predict', $data);
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
