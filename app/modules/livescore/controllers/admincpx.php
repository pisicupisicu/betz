<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

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
class Admincpx extends Admincp_Controller
{

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
