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



class Admincp8 extends Admincp_Controller 
{
                
    function __construct()
    {

        parent::__construct();
                
        $this->admin_navigation->parent_active('livescore');
                                        
        //error_reporting(E_ALL^E_NOTICE);
        //error_reporting(E_WARNING);
    }

    function index($display = 1)
    {
        $this->admin_navigation->module_link('Simulate',site_url('admincp8/livescore/simulate'));  

        $this->load->model('method_setting_model');
        $this->load->library('dataset');

        $filters    =   array();        
                          
        $columns = array(
                            array(
                                    'name' => 'METHOD ID',
                                    'width' => '5%',                                                                            
                                    'type' => 'text',                                                                           
                                    ),
                            array(
                                    'name' => 'METHOD NAME',
                                    'width' => '10%',                                                                            
                                    'type' => 'text',                                                                           
                                    ),
                            array(
                                    'name' => 'MINUTE',
                                    'width' => '5%',                                    
                                    'type' => 'text',                                                                                                        
                                    ),                            
                            array(
                                    'name' => 'STAKE',
                                    'width' => '5%',                                                                            
                                    'type' => 'text',                                                                           
                                    ),
                            array(
                                    'name' => 'ODDS',
                                    'width' => '5%',                                                                            
                                    'type' => 'text',                                                                            
                                    ), 
                            array(
                                    'name' => 'OVER',
                                    'width' => '5%',                                                                            
                                    'type' => 'text',                                                                            
                                    ),
                            array(
                                    'name' => 'PROFIT',
                                    'width' => '15%',                                                                            
                                    'type' => 'text',                                                                            
                                    ),
                            array(
                                    'name' => 'PERCENTAGE',
                                    'width' => '10%',                                                                            
                                    'type' => 'text',                                                                            
                                    ),
                            array(
                                    'name' => 'TOTAL BETS',
                                    'width' => '15%',                                                                            
                                    'type' => 'text',                                                                            
                                    ),
                            array(
                                    'name' => 'ALIAS',
                                    'width' => '10%',                                                                            
                                    'type' => 'text',                                                                            
                                ),                         
                            array(
                                    'name' => 'SIMULATE',
                                    'width' => '10%',                                                                            
                                    'type' => 'text',                                                                            
                            ),
                            array(
                                    'name' => 'VIEW',
                                    'width' => '10%',                                                                            
                                    'type' => 'text',                                                                            
                            ),
                                                                                                                                        
                                    
        );
       
        $filters = array();    
        $filters['limit'] = 20;

        if(isset($_GET['filters'])) {
            $filters_decode = unserialize(base64_decode($this->asciihex->HexToAscii($_GET['filters'])));
        }
                   
        if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset'];        

        if(isset($filters_decode) && !empty($filters_decode)) {
           foreach($filters_decode as $key=>$val) {
                $filters[$key] = $val;
            } 
        }
               
        $filters['display'] = $display;
        $this->dataset->columns($columns);
        $this->dataset->datasource('method_setting_model','get_settings',$filters);
        $this->dataset->base_url(site_url('admincp8/livescore/index'));
        $this->dataset->rows_per_page($filters['limit']);

        // total rows
        unset($filters['limit']);
        $total_rows = $this->method_setting_model->get_num_rows($filters);        
        $this->dataset->total_rows($total_rows);

        // initialize the dataset
        $this->dataset->initialize();               
        // add actions                     
        $this->load->view('list_method_settings');   
    }

    function view($id_setting)
    {       
        $this->load->model('method_setting_model');
        $this->load->library('dataset');

        $filters    =   array();        
                          
        $columns = array(
                            array(
                                    'name' => 'Mo +',
                                    'width' => '4%',                                                                            
                                    'type' => 'text',                                                                           
                                    ),
                            array(
                                    'name' => 'Mo %',
                                    'width' => '4%',                                                                            
                                    'type' => 'text',                                                                           
                                    ),
                            array(
                                    'name' => 'Mo Bets',
                                    'width' => '4%',                                    
                                    'type' => 'text',                                                                                                        
                                    ), 
                            array(
                                    'name' => 'Tu +',
                                    'width' => '4%',                                                                            
                                    'type' => 'text',                                                                           
                                    ),
                            array(
                                    'name' => 'Tu %',
                                    'width' => '4%',                                                                            
                                    'type' => 'text',                                                                           
                                    ),
                            array(
                                    'name' => 'Tu Bets',
                                    'width' => '4%',                                    
                                    'type' => 'text',                                                                                                        
                                    ), 
                            array(
                                    'name' => 'We +',
                                    'width' => '4%',                                                                            
                                    'type' => 'text',                                                                           
                                    ),
                            array(
                                    'name' => 'We %',
                                    'width' => '4%',                                                                            
                                    'type' => 'text',                                                                           
                                    ),
                            array(
                                    'name' => 'We Bets',
                                    'width' => '4%',                                    
                                    'type' => 'text',                                                                                                        
                                    ),
                            array(
                                    'name' => 'Th +',
                                    'width' => '4%',                                                                            
                                    'type' => 'text',                                                                           
                                    ),
                            array(
                                    'name' => 'Th %',
                                    'width' => '4%',                                                                            
                                    'type' => 'text',                                                                           
                                    ),
                            array(
                                    'name' => 'Th Bets',
                                    'width' => '4%',                                    
                                    'type' => 'text',                                                                                                        
                                    ), 
                            array(
                                    'name' => 'Fr +',
                                    'width' => '4%',                                                                            
                                    'type' => 'text',                                                                           
                                    ),
                            array(
                                    'name' => 'Fr %',
                                    'width' => '4%',                                                                            
                                    'type' => 'text',                                                                           
                                    ),
                            array(
                                    'name' => 'Fr Bets',
                                    'width' => '4%',                                    
                                    'type' => 'text',                                                                                                        
                                    ),
                            array(
                                    'name' => 'Sa +',
                                    'width' => '4%',                                                                            
                                    'type' => 'text',                                                                           
                                    ),
                            array(
                                    'name' => 'Sa %',
                                    'width' => '4%',                                                                            
                                    'type' => 'text',                                                                           
                                    ),
                            array(
                                    'name' => 'Sa Bets',
                                    'width' => '4%',                                    
                                    'type' => 'text',                                                                                                        
                                    ),                          
                            array(
                                    'name' => 'Su +',
                                    'width' => '4%',                                                                            
                                    'type' => 'text',                                                                           
                                    ),
                            array(
                                    'name' => 'Su %',
                                    'width' => '4%',                                                                            
                                    'type' => 'text',                                                                           
                                    ),
                            array(
                                    'name' => 'Su Bets',
                                    'width' => '4%',                                    
                                    'type' => 'text',                                                                                                        
                            ),                                                                              
                                    
        );
                
        $filters = array();    
        $filters['limit'] = 20;

        if(isset($_GET['filters'])) {
            $filters_decode = unserialize(base64_decode($this->asciihex->HexToAscii($_GET['filters'])));
        }
                   
        if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset'];        

        if(isset($filters_decode) && !empty($filters_decode)) {
           foreach($filters_decode as $key=>$val) {
                $filters[$key] = $val;
            } 
        }
               
        $filters['id_setting'] = $id_setting;
        $this->dataset->columns($columns);
        $this->dataset->datasource('simulation_model','view_settings',$filters);
        $this->dataset->base_url(site_url('admincp8/livescore/index'));
        $this->dataset->rows_per_page($filters['limit']);

        // total rows
        unset($filters['limit']);
        $total_rows = $this->method_setting_model->get_num_rows($filters);        
        $this->dataset->total_rows($total_rows);

        // initialize the dataset
        $this->dataset->initialize();               
        // add actions                     
        $this->load->view('view_method_settings');   
    }

    function simulate_method($id_setting, $start = '2013-01-05', $final = '2013-12-31', $date = '')
    {               
        $this->load->model('simulation_model');

        if (!$date) {
            $date = $start;
        }        

        echo $this->simulation_model->simulate($id_setting, $date).'<br/>';

        if ($date < $final) {
            $date = date('Y-m-d', strtotime('+1 day', strtotime($date)));            
            echo '<META http-equiv="refresh" content="2;URL=/admincp8/livescore/simulate_method/'.$id_setting.'/'.$start.'/'.$final.'/'.$date.'">';
        } else {
            echo 'Simulation finished for method '.$id_setting.' from date '.$start.' to date '.$final;
        }
                          
    }

    function displayIntervals($one, $two, $three, $four, $five, $six)
    {
        $total = $one + $two + $three + $four + $five + $six;
        $onePercent = number_format($one*100/$total,2);
        $twoPercent = number_format($two*100/$total,2);
        $threePercent = number_format($three*100/$total,2);
        $fourPercent = number_format($four*100/$total,2);
        $fivePercent = number_format($five*100/$total,2);
        $sixPercent = number_format($six*100/$total,2);

        echo "1-15 = $one = $onePercent%<br/>";
        echo "16-30 = $two = $twoPercent%<br/>";
        echo "31-45 = $three = $threePercent%<br/>";
        echo "46-60 = $four  = $fourPercent%<br/>";
        echo "61-75 = $five  = $fivePercent%<br/>";
        echo "76-90 = $six   = $sixPercent%<br/>";
    }

    function count_intervals($id_match = 1, $one = 0, $two = 0, $three = 0, $four = 0, $five = 0, $six = 0)
    {
        $this->load->model('simulation_model');
        $this->load->model('match_model');

        $match = $this->match_model->get_match($id_match);

        if ($id_match > 31305) {
            $this->displayIntervals($one, $two, $three, $four, $five, $six);
            die('FINISHED');
        }

        if (empty($match)) {
            echo 'Skipping match id : '.$id_match.'<br/>';
            $this->displayIntervals($one, $two, $three, $four, $five, $six);
            $id_match++;
            echo '<META http-equiv="refresh" content="2;URL=/admincp8/livescore/count_intervals/'.$id_match.'/'.$one.'/'.$two.'/'.$three.'/'.$four.'/'.$five.'/'.$six.'">';            
        } else {
            echo 'Processing match id : '.$id_match.'<br/>';
            $this->displayIntervals($one, $two, $three, $four, $five, $six);
            $this->simulation_model->count_intervals($id_match, $one, $two, $three, $four, $five, $six);           
            echo '<META http-equiv="refresh" content="2;URL=/admincp8/livescore/count_intervals/'.$id_match.'/'.$one.'/'.$two.'/'.$three.'/'.$four.'/'.$five.'/'.$six.'">';
        }

    }

    function procedure($countryId = 1)
    {
        $this->load->model('competition_model');
        $this->admin_navigation->module_link('Fix competitions',site_url('admincp/livescore/fix_competitions'));    
        $this->admin_navigation->module_link('Add competition',site_url('admincp/livescore/add_competition'));            
        $this->load->library('dataset');

        $columns = array(
                            array(
                                    'name' => 'NAME',
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
                                    'width' => '40%',                                        
                                    'type' => 'text'
                            ),         
                            array(
                                    'name' => 'COUNTRY',
                                    'width' => '15%',                                        
                                    'filter' => 'country_name',
                                    'type' => 'text',                                        
                                    'sort_column' => 'country_name',
                            ),                                                 
                            array(
                                    'name' => 'EDIT',
                                    'width' => '15%',                                        
                                    'type' => 'text',
                            ),        
        );
        
        $filters = array();    
        $filters['limit'] = 20;

        if(isset($_GET['filters'])) {
            $filters_decode = unserialize(base64_decode($this->asciihex->HexToAscii($_GET['filters'])));
        }

        if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset'];                 
        if(isset($_GET['country_name'])) $filters['country_name'] = $_GET['country_name'];

        if(isset($filters_decode) && !empty($filters_decode)) {
           foreach($filters_decode as $key=>$val) {
                $filters[$key] = $val;
            } 
        }

        $filters['countryId'] = $countryId;

        $data = $this->competition_model->get_competitions_by_country_procedure($filters);
        $filters['data'] = $data;
                        
        $this->dataset->columns($columns);
        $this->dataset->datasource('competition_model','get_competitions_by_country_with_filters',$filters);
        $this->dataset->base_url(site_url('admincp/livescore/list_competitions'));
        $this->dataset->rows_per_page($filters['limit']);

        // total rows
        unset($filters['limit']);
        $total_rows = count($data); 
        $this->dataset->total_rows($total_rows);
               
        // initialize the dataset
        $this->dataset->initialize();               
        // add actions
        $this->dataset->action('Delete','admincp/livescore/delete_competition');                
        $this->load->view('list_competitions');
    }
        
    // http://stackoverflow.com/questions/16029729/mysql-error-commands-out-of-sync-you-cant-run-this-command-now
    // http://forums.mysql.com/read.php?98,358569
    // SHOW PROCEDURE STATUS;
    // SHOW FUNCTION STATUS;
    // SHOW CREATE PROCEDURE GetGoals;
    // CALL GetGoals();
    // http://blog.fedecarg.com/2009/02/22/mysql-split-string-function/

    /*
        DELIMITER $$
        DROP PROCEDURE IF EXISTS GetCompetitionsByCountryId$$
        CREATE PROCEDURE GetCompetitionsByCountryId(IN countryId INT(10))
        BEGIN
        SELECT *
        FROM `z_competitions`
        INNER JOIN `z_countries` ON  `z_competitions`.`country_id` = `z_countries`.`ID`
        WHERE `z_competitions`.`country_id` = countryId;
        END$$
        DELIMITER ;

        // CALL GetCompetitionsByCountryId(82);
    */


    /*
    DELIMITER //
    CREATE PROCEDURE GetGoals()
    BEGIN
    SELECT * FROM products;
    END //
    DELIMITER ; 

    DECLARE total_count INT DEFAULT 0
    SET total_count = 10;
    Beside SET statement, we can use SELECT … INTO to assign a query result to a variable.
    DECLARE total_products INT DEFAULT 0
    SELECT COUNT(*) INTO total_products
    FROM products 
    A variable with the ‘@’ at the beginning is session variable. It exists until the session end. 
    In MySQL, a parameter has one of three modes IN, OUT and INOUT. 
    IN this is the default mode. IN indicates that a parameter can be passed into stored procedures but any modification inside stored procedure does not change parameter.
    OUT this mode indicates that stored procedure can change this parameter and pass back to the calling program. 
    INOUT obviously this mode is combined of IN and OUT mode; you can pass parameter into stored procedure and get it back with the new value from calling program.

    DELIMITER //
    CREATE PROCEDURE GetOfficeByCountry(IN countryName VARCHAR(255))
    BEGIN
    SELECT city, phone
    FROM offices
    WHERE country = countryName;
    END //
    DELIMITER ;  

    DELIMITER $$
    CREATE PROCEDURE CountOrderByStatus(
    IN orderStatus VARCHAR(25),
    OUT total INT)
    BEGIN
    SELECT count(orderNumber)
    INTO total
    FROM orders
    WHERE status = orderStatus;
    END$$
    DELIMITER ;

    CALL CountOrderByStatus('Shipped',@total);
    SELECT @total AS total_shipped;
    To get number of in process we do the same as above
    CALL CountOrderByStatus('in process',@total);
    SELECT @total AS total_in_process; 

    In the third procedure, we will demonstrate the INOUT parameter. The stored procedure capitalizes all words in a string and returns it back to the calling program. The stored procedure source code is as follows:
    DELIMITER $$
    CREATE PROCEDURE `Capitalize`(INOUT str VARCHAR(1024))
    BEGIN
    DECLARE i INT DEFAULT 1;
    DECLARE myc, pc CHAR(1);
    DECLARE outstr VARCHAR(1000) DEFAULT str;
    WHILE i <= CHAR_LENGTH(str) DO
    SET myc = SUBSTRING(str, i, 1);
    SET pc = CASE WHEN i = 1 THEN ' '
    ELSE SUBSTRING(str, i - 1, 1)
    END;
    IF pc IN (' ', '&', '''', '_', '?', ';', ':', '!', ',', '-', '/', '(', '.') THEN
    SET outstr = INSERT(outstr, i, 1, UPPER(myc));
    END IF;
    SET i = i + 1;
    END WHILE;
    SET str = outstr;
    END$$
    DELIMITER ;
    Here is the usage of the Capitalize stored procedure
    SET @str = 'mysql stored procedure tutorial';
    CALL Capitalize(@str);
    SELECT @str; 

    The IF Statement
    The syntax of IF statement is simple as follows:
    IF expression THEN commands
    [ELSEIF expression THEN commands]
    [ELSE commands]
    END IF;  
    

    The CASE Statement
    When multiple conditions are used with IF statement the code is not easy to read. At this time, the CASE can be used to make the code clearer. The syntax of  the CASE statement is as follows:
    CASE
    WHEN expression THEN commands
    …
    WHEN expression THEN commands
    ELSE commands
    END CASE; 

    WHILE loop
    The syntax of while loop is as follows:
    WHILE expression DO
    Statements
    END WHILE 

    DELIMITER $$
    DROP PROCEDURE IF EXISTS WhileLoopProc$$
    CREATE PROCEDURE WhileLoopProc()
    BEGIN
    DECLARE x INT;
    DECLARE str VARCHAR(255);
    SET x = 1;
    SET str = '';
    WHILE x <= 5 DO
    SET str = CONCAT(str,x,',');
    SET x = x + 1;
    END WHILE;
    SELECT str;
    END$$
    DELIMITER ; 

    REPEAT loop
    The syntax of repeat loop is as follows:
    REPEAT
    Statements;
    UNTIL expression
    END REPEAT 

    DELIMITER $$
    DROP PROCEDURE IF EXISTS RepeatLoopProc$$
    CREATE PROCEDURE RepeatLoopProc()
    BEGIN
    DECLARE x INT;
    DECLARE str VARCHAR(255);
    SET x = 1;
    SET str = '';
    REPEAT
    SET str = CONCAT(str,x,',');
    SET x = x + 1;
    UNTIL x > 5
    END REPEAT;
    SELECT str;
    END$$
    DELIMITER ;

    MySQL also support a LOOP loop which allows you to execute statements repeatedly more flexible. Here is an example of using LOOP loop.

    DELIMITER $$
    DROP PROCEDURE IF EXISTS LOOPLoopProc$$
    CREATE PROCEDURE LOOPLoopProc()
    BEGIN
    DECLARE x INT;
    DECLARE str VARCHAR(255);
    SET x = 1;
    SET str = '';
    loop_label: LOOP
    IF x > 10 THEN
    LEAVE loop_label;
    END IF;
    SET x = x + 1;
    IF (x mod 2) THEN
    ITERATE loop_label;
    ELSE
    SET str = CONCAT(str,x,',');
    END IF;

    END LOOP;
    SELECT str;
    END$$
    DELIMITER ;
        
    */

    /*
    

    */

    /*
    DELIMITER $$
    DROP PROCEDURE IF EXISTS EAST19$$
    CREATE PROCEDURE EAST19(OUT toate INT,OUT total INT,OUT correct VARCHAR(255),IN minute INT,OUT mins VARCHAR(255))
    begin
        declare no_more_rows boolean default false;
        declare v_col1       int;
        declare v_col2       int;
        declare v_col3       int;
        DECLARE over TINYINT(1);        
        DECLARE home INT;
        DECLARE away INT;
        DECLARE goals INT;        

        declare cursor1 cursor for select id,score from z_matches ORDER BY id ASC LIMIT 0,8;
        declare cursor2 cursor for select min from z_goals where  match_id = v_col1;
        declare continue handler for not found set no_more_rows := true;
        
        SET toate = 0;
        SET total = 0;
        SET correct = 0;
        SET mins = '';
                
        open cursor1;
        LOOP1: loop
            fetch cursor1 into v_col1,v_col2;
                if no_more_rows then
                  close cursor1;
                  leave LOOP1;                 
                end if;
            
            SET home = SPLIT_STR(v_col2,'-',1);
            SET home = CONVERT(home,UNSIGNED INTEGER);
            SET away = SPLIT_STR(v_col2,'-',2);
            SET away = CONVERT(away,UNSIGNED INTEGER);
            SET goals = home + away;
                     
            IF goals >= 3 THEN SET over = 1;
            ELSE SET over = 0;
            END IF;

            SET toate = toate + 1;

            open cursor2;
            LOOP2: loop
                fetch cursor2 into v_col3;                
                if no_more_rows then
                    set no_more_rows := false;
                    close cursor2;
                    leave LOOP2;
                end if;                           
                SET v_col3 = CONVERT(v_col3,UNSIGNED INTEGER);
                
                IF v_col3 < minute THEN                    
                    SET total = total + 1;                    
                    SET mins = CONCAT(CONCAT(v_col3,'-'), mins);
                    IF over = 1 THEN
                        SET correct = CONCAT(CONCAT(goals,'-'), correct);
                    END IF;
                    close cursor2;
                    leave LOOP2;                                                    
                END IF;
            end loop LOOP2;
        end loop LOOP1;
    end$$

    DELIMITER ;

    SET @toate = 0;
    SET @minute = 35;
    SET @correct = '';
    SET @mins = '';
    CALL EAST19(@toate,@total,@correct,@minute,@mins);
    SELECT @toate AS toate,@total AS total_east19,@correct AS correct,@minute as min_east19,@mins as mins_east_19;
    */


    /*
        // MIN 85

DELIMITER $$
    DROP PROCEDURE IF EXISTS MIN85$$
    CREATE PROCEDURE MIN85(OUT toate INT,OUT total INT,OUT overs INT,OUT unders INT,IN minstart INT)
    begin
        declare no_more_rows boolean default false;
        declare v_col1       int;
        declare v_col2       VARCHAR(12);
        declare v_col3       int;
        DECLARE over TINYINT(1);       
        DECLARE home INT;
        DECLARE away INT;
        DECLARE goals INT;       

        declare cursor1 cursor for select id,score from z_matches ORDER BY id ASC;
        declare cursor2 cursor for select min from z_goals where  match_id = v_col1 ORDER BY min DESC;
        declare continue handler for not found set no_more_rows := true;
       
        SET toate = 0;
        SET total = 0;
        SET overs = 0;
        SET unders = 0;       
               
        open cursor1;
        LOOP1: loop
            fetch cursor1 into v_col1,v_col2;
                if no_more_rows then
                  close cursor1;
                  leave LOOP1;                
                end if;
           
            SET home = SPLIT_STR(v_col2,'-',1);
            SET home = CONVERT(home,UNSIGNED INTEGER);
            SET away = SPLIT_STR(v_col2,'-',2);
            SET away = CONVERT(away,UNSIGNED INTEGER);
            SET goals = home + away;

            SET goals = goals - 1;
                    
            IF goals >= 3 THEN SET over = 1;
            ELSE SET over = 0;
            END IF;

            SET toate = toate + 1;

            open cursor2;
            LOOP2: loop
                fetch cursor2 into v_col3;               
                if no_more_rows then
                    set no_more_rows := false;
                    close cursor2;
                    leave LOOP2;
                end if;                          
                SET v_col3 = CONVERT(v_col3,UNSIGNED INTEGER);

                IF v_col3 < minstart THEN
                    close cursor2;
                    leave LOOP2;                                                   
                END IF;
               
                SET total = total + 1;                                   
                IF over = 1 THEN
                    SET overs = overs + 1;
                ELSE
                    SET unders = unders + 1;
                END IF;
                close cursor2;
                leave LOOP2;                 
                                                                       
              
            end loop LOOP2;
        end loop LOOP1;
    end$$

    DELIMITER ;

    SET @toate = 0;
    SET @total = 0;
    SET @minstart = 85;
    SET @overs = 0;
    SET @unders = 0;
    CALL MIN85(@toate,@total,@overs,@unders,@minstart);
    SELECT @toate AS toate85,@total AS total_min85,@overs AS overs85,@unders AS unders85,@minstart as minstart85;
    
    */

    /*
        // MIN 85

DELIMITER $$
    DROP PROCEDURE IF EXISTS MIN85$$
    CREATE PROCEDURE MIN85(OUT toate INT,OUT total INT,OUT correct INT,IN minstart INT)
    begin
        declare no_more_rows boolean default false;
        declare v_col1       int;
        declare v_col2       VARCHAR(12);
        declare v_col3       int;

        DECLARE over TINYINT(1);
        DECLARE goal INT;            

        declare cursor1 cursor for select id,score from z_matches ORDER BY id ASC;
        declare cursor2 cursor for select min from z_goals where  match_id = v_col1 ORDER BY min ASC;
        declare continue handler for not found set no_more_rows := true;
       
        SET toate = 0;
        SET total = 0;
        SET correct = 0;        
               
        open cursor1;
        LOOP1: loop
            fetch cursor1 into v_col1,v_col2;
                if no_more_rows then
                  close cursor1;
                  leave LOOP1;                
                end if;
                      
            SET toate = toate + 1;
            SET goal = 0;
            SET over = 0;

            open cursor2;
            LOOP2: loop
                fetch cursor2 into v_col3;               
                if no_more_rows then
                    set no_more_rows := false;
                    close cursor2;
                    leave LOOP2;
                end if;

                SET v_col3 = CONVERT(v_col3,UNSIGNED INTEGER);

                SET goal = goal + 1;
                IF goal > 3 THEN
                    SET over = 1;
                    SET total = total + 1;
                END IF;

                IF over = 1 AND v_col3 > minstart THEN
                    SET correct = correct + 1;
                END IF;
                                            
                close cursor2;
                leave LOOP2;                 
                                                                       
              
            end loop LOOP2;
        end loop LOOP1;
    end$$

    DELIMITER ;

    SET @toate = 0;
    SET @total = 0;
    SET @correct = 0;
    SET @minstart = 85;
    
    CALL MIN85(@toate,@total,@correct,@minstart);
    SELECT @toate AS toate85,@total AS total85,@correct AS correct85,@minstart as minstart85;






    */

    /*
        find duplicate teams with country_id,name

        SELECT `country_id` , `name` , COUNT( * ) AS c
        FROM z_teams
        GROUP BY `country_id` , `name`
        HAVING c >1
        LIMIT 0 , 30


    */
    

    function list_matches($data)
    {
        $this->load->model('competition_model');
        $this->load->library('dataset');

        $filters    =   array();        
                          
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
                                    'filter'    =>  'score',                                        
                                    'type' => 'text',
                                    'sort_column'   =>  'score',
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

        if(isset($_GET['filters'])) {
            $filters_decode = unserialize(base64_decode($this->asciihex->HexToAscii($_GET['filters'])));
        }
                   
        if(isset($_GET['offset'])) $filters['offset'] = $_GET['offset'];                 
        if(isset($_GET['country_name'])) $filters['country_name'] = $_GET['country_name'];
        if(isset($_GET['competition_name'])) $filters['competition_name'] = $_GET['competition_name'];
        if(isset($_GET['team1'])) $filters['team1'] = $_GET['team1'];
        if(isset($_GET['team2'])) $filters['team2'] = $_GET['team2'];
        if(isset($_GET['score'])) $filters['score'] = $_GET['score'];
        //if(isset($_GET['match_date_start'])) $filters['match_date_start'] = $_GET['match_date_start'];
        if(isset($_GET['match_date_end'])) $filters['match_date_end'] = $_GET['match_date_end'];
        $filters['match_date_start'] = $data;

        if(isset($filters_decode) && !empty($filters_decode)) {
           foreach($filters_decode as $key=>$val) {
                $filters[$key] = $val;
            } 
        }

        foreach($filters as $key=>$val) {
            if(in_array($val,array('filter results','start date','end date'))) {
                unset($filters[$key]);
            }
        }
                    
        $this->dataset->columns($columns);
        $this->dataset->datasource('competition_model','get_matches_with_score',$filters);
        $this->dataset->base_url(site_url('admincp8/livescore/list_matches'));
        $this->dataset->rows_per_page($filters['limit']);



        // total rows
        unset($filters['limit']);
        //$total_rows = $this->competition_model->get_num_rows($filters);
        $total_rows = 10;
        $this->dataset->total_rows($total_rows);

        // initialize the dataset
        $this->dataset->initialize();               
        // add actions
        $this->dataset->action('Delete','admincp8/livescore/delete_match');                
        $this->load->view('list_algoritm');          

    }
        
            
}

