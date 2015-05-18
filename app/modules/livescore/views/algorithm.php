<?=$this->load->view(branded_view('cp/header'));?>
<script>
jQuery(document).ready(function(){

$('#search').on('click', function(event){
    var id = $(this).attr('id');
    var match_date_end = $('input[name="match_date_end"]').val();
    var match_date_start = $('input[name="match_date_start"]').val();
    var team1 = $('input[name="team1"]').val();
    var team2 = $('input[name="team2"]').val();
    var score = $('input[name="score"]').val();
    var country_name = $('input[name="country_name"]').val();

    $.ajax({
            type: 'post',
            url: '/admincp/livescore/update_filters/',
            data: 'match_date_start='+match_date_start+"&match_date_end="+match_date_end+"&country_name="+country_name+"&team1="+team1+"&team2="+team2+"&score="+score,
            dataType:'html',
            success: function(data, textStatus, XMLHttpRequest) {
                console.log('filters '+data);
                $('input[name="filters"]').val(data);
                document.forms['dataset_form'].submit();
             }      
            });
        
	});

});    

</script>

<h1>SUCCESS</h1>

<div style="width:auto;text-align:center;" align="center">

	<?=$this->dataset->table_head();?>    
        <?php
        $overs = array(
            'over_0.5' => 1,
            'under_0.5' => 1,
            'over_1.5' => 2,
            'under_1.5' => 2,
            'over_2.5' => 3,
            'under_2.5' => 3,
            'over_3.5' => 4,
            'under_3.5' => 4,            
            'over_4.5' => 5,
            'under_4.5' => 5,
            'over_5.5' => 6,
            'under_5.5' => 6
        );
        
        function getScore($score)
        {
            $temp = explode('-', $score);            
            return $temp;
        }
        
        function is1($score)
        {
            $temp = getScore($score);
            
            if ($temp[0] > $temp[1]) {
                return true;
            }
            
            return false;
        }
        
        function is2($score)
        {
            $temp = getScore($score);
            
            if ($temp[0] < $temp[1]) {
                return true;
            }
            
            return false;
        }
        
        function isX($score)
        {
            $temp = getScore($score);
            
            if ($temp[0] == $temp[1]) {
                return true;
            }
            
            return false;
        }
        
        function isOver($score, $value) 
        {
            $temp = getScore($score);
            $right = false;
            
            if (($temp[0] + $temp[1]) >= $value) {
                $right = true;
            }
            
            $path = site_url().'app/modules/livescore/assets/';
            
            if ($right) {
                $img = $path . 'money_bet.png';
            } else {
                $img = $path . 'red.png';
            }
            
            return $img;
        }
        
        function isUnder($score, $value)
        {
            $temp = getScore($score);
            $right = false;
            
            if (($temp[0] + $temp[1]) < $value) {
                $right = true;
            }
            
            $path = site_url().'app/modules/livescore/assets/';
            
            if ($right) {
                $img = $path . 'money_bet.png';
            } else {
                $img = $path . 'red.png';
            }
            
            return $img;
        }
        
        function isCorrect($is, $score)
        {            
            $right = call_user_func($is, $score);
            $path = site_url().'app/modules/livescore/assets/';
            
            if ($right) {
                $img = $path . 'money_bet.png';
            } else {
                $img = $path . 'red.png';
            }
            
            return $img;
        }
        
        if (!empty($this->dataset->data)) {

            
            //echo '--------------------VIEW' . PHP_EOL;
            //print_r($this->dataset->data);

            foreach ($this->dataset->data['success'] as $key => $row) {
        ?>
           <tr>							              
                <td align="center"><b><?=$key;?></b></td>
                <td align="center"><b><?=$row['ok'];?></b></td>
                <td align="center"><b><?=$row['total'];?></b></td>
                <td align="center"><b><?=$row['p'];?>%</b></td>                
           </tr>

        <?php }
        ?>
        <thead style="width:100px;"><th>&nbsp;</th></thead>
        <thead style="background-color:yellow;">
            <th>Criteria</th>
            <th>Percentage</th>
            <th>Country</th>
            <th>Competition</th>
            <th>Team1</th>
            <th>Team2</th>
            <th>Score</th>
            <th>Link</th>
            <th>H2h</th>
        </thead>
           
        <?php    
            foreach ($this->dataset->data['1'] as $key => $row) {
                if ($row <= 50) {
                    continue;
                }
                
                $img = isCorrect('is1', $this->dataset->data['stats'][$key]['match']['score']);
            ?>
            <tr style="background-color: lime;">							              
                <td align="center"><img src="<?=$img;?>"/><b>1</b></td> 
                <td align="center"><b><?=$row;?>%</b></td>
                <td align="center"><b><?=$this->dataset->data['stats'][$key]['match']['country_name'];?></b></td>
                <td align="center"><b><?=$this->dataset->data['stats'][$key]['match']['competition_name'];?></b></td>
                <td align="center"><b><?=$this->dataset->data['stats'][$key]['match']['team1'];?></b></td>
                <td align="center"><b><?=$this->dataset->data['stats'][$key]['match']['team2'];?></b></td>
                <td align="center"><b><?=$this->dataset->data['stats'][$key]['match']['score'];?></b></td>
                <td align="center"><b><a href="<?=$this->dataset->data['stats'][$key]['match']['link_match'];?>" target="_blank">Link</a></b></td>
                <td align="center"><b><a href="/admincp9/livescore/h2h/<?=$this->dataset->data['stats'][$key]['match']['team1_id'];?>/<?=$this->dataset->data['stats'][$key]['match']['team2_id'];?>/<?=$this->dataset->data['stats'][$key]['match']['match_date'];?>" target="_blank">H2h</a></b></td>
            </tr>

        <?php }
        
            foreach ($this->dataset->data['x'] as $key => $row) {
                if ($row <= 50) {
                    continue;
                }
                
                $img = isCorrect('isX', $this->dataset->data['stats'][$key]['match']['score']);
            ?>
            <tr style="background-color: magenta;">							              
                <td align="center"><img src="<?=$img;?>"/><b>X</b></td> 
                <td align="center"><b><?=$row;?>%</b></td>
                <td align="center"><b><?=$this->dataset->data['stats'][$key]['match']['country_name'];?></b></td>
                <td align="center"><b><?=$this->dataset->data['stats'][$key]['match']['competition_name'];?></b></td>
                <td align="center"><b><?=$this->dataset->data['stats'][$key]['match']['team1'];?></b></td>
                <td align="center"><b><?=$this->dataset->data['stats'][$key]['match']['team2'];?></b></td>
                <td align="center"><b><?=$this->dataset->data['stats'][$key]['match']['score'];?></b></td>
                <td align="center"><b><a href="<?=$this->dataset->data['stats'][$key]['match']['link_match'];?>" target="_blank">Link</a></b></td>
                <td align="center"><b><a href="/admincp9/livescore/h2h/<?=$this->dataset->data['stats'][$key]['match']['team1_id'];?>/<?=$this->dataset->data['stats'][$key]['match']['team2_id'];?>/<?=$this->dataset->data['stats'][$key]['match']['match_date'];?>" target="_blank">H2h</a></b></td>            </tr>

        <?php }
        
            foreach ($this->dataset->data['2'] as $key => $row) {
                if ($row <= 50) {
                    continue;
                }
                
                $img = isCorrect('is2', $this->dataset->data['stats'][$key]['match']['score']);
            ?>
            <tr style="background-color: cyan;">							              
                <td align="center"><img src="<?=$img;?>"/><b>2</b></td> 
                <td align="center"><b><?=$row;?>%</b></td>
                <td align="center"><b><?=$this->dataset->data['stats'][$key]['match']['country_name'];?></b></td>
                <td align="center"><b><?=$this->dataset->data['stats'][$key]['match']['competition_name'];?></b></td>
                <td align="center"><b><?=$this->dataset->data['stats'][$key]['match']['team1'];?></b></td>
                <td align="center"><b><?=$this->dataset->data['stats'][$key]['match']['team2'];?></b></td>
                <td align="center"><b><?=$this->dataset->data['stats'][$key]['match']['score'];?></b></td>
                <td align="center"><b><a href="<?=$this->dataset->data['stats'][$key]['match']['link_match'];?>" target="_blank">Link</a></b></td>
                <td align="center"><b><a href="/admincp9/livescore/h2h/<?=$this->dataset->data['stats'][$key]['match']['team1_id'];?>/<?=$this->dataset->data['stats'][$key]['match']['team2_id'];?>/<?=$this->dataset->data['stats'][$key]['match']['match_date'];?>" target="_blank">H2h</a></b></td>
            </tr>

        <?php }
            $i = 0;
            $colors = array('darkseagreen', 'orchid', 'plum', 'yellowgreen');
            foreach ($overs as $cheie => $valoare) {
                $i++;
                foreach ($this->dataset->data[$cheie] as $key => $row) {
                    if ($row <= 50) {
                        continue;
                    }
                    
                    if (strstr($cheie, 'over')) {
                        $img = isOver($this->dataset->data['stats'][$key]['match']['score'], $valoare);
                    } else {
                        $img = isUnder($this->dataset->data['stats'][$key]['match']['score'], $valoare);
                    }
                    
                ?>
                <tr style="background-color: <?php echo $colors[$i%4]; ?>">							              
                    <td align="center"><img src="<?=$img;?>"/><b><?= $cheie; ?></b></td> 
                    <td align="center"><b><?=$row;?>%</b></td>
                    <td align="center"><b><?=$this->dataset->data['stats'][$key]['match']['country_name'];?></b></td>
                    <td align="center"><b><?=$this->dataset->data['stats'][$key]['match']['competition_name'];?></b></td>
                    <td align="center"><b><?=$this->dataset->data['stats'][$key]['match']['team1'];?></b></td>
                    <td align="center"><b><?=$this->dataset->data['stats'][$key]['match']['team2'];?></b></td>
                    <td align="center"><b><?=$this->dataset->data['stats'][$key]['match']['score'];?></b></td>
                    <td align="center"><b><a href="<?=$this->dataset->data['stats'][$key]['match']['link_match'];?>" target="_blank">Link</a></b></td>
                    <td align="center"><b><a href="/admincp9/livescore/h2h/<?=$this->dataset->data['stats'][$key]['match']['team1_id'];?>/<?=$this->dataset->data['stats'][$key]['match']['team2_id'];?>/<?=$this->dataset->data['stats'][$key]['match']['match_date'];?>" target="_blank">H2h</a></b></td>
                </tr>
               
        <?php } }
    }

	else {

	?>

	<tr>

		<td colspan="10">No data.</td>

	</tr>

	<?php } ?>

	<?=$this->dataset->table_close();?>

</div>

<?=$this->load->view(branded_view('cp/footer'));?>
