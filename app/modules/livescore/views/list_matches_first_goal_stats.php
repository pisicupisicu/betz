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
    var min = $('input[name="min"]').val();
    var country_name = $('input[name="country_name"]').val();

    $.ajax({
            type: 'post',
            url: '/admincp7/livescore/update_filters/',
            data: 'match_date_start='+match_date_start+"&match_date_end="+match_date_end+"&country_name="+country_name+"&team1="+team1+"&team2="+team2+"&score="+score+"&min="+min,
            dataType:'html',
            success: function(data, textStatus, XMLHttpRequest) {
                console.log('filters '+data);
                $('input[name="filters"]').val(data);
                document.forms['dataset_form'].submit();
                //event.preventDefault();
             }      
            });
        
	});

});    

</script>
<?php
	//print '<pre>';
	//print_r($this->dataset->data);

	//last element different by the others
	$total_games = array_pop($this->dataset->data);
	$this->dataset->total_rows = $this->dataset->total_rows - 5; 
?>
<?php 
		$temp = (string)$min;
		$length = strlen($temp);
		if($temp[$length-1] == '1') {
			$th = 'st';
		} elseif($temp[$length-1] == '2') {
			$th = 'nd';
		} elseif($temp[$length-1] == '3') {
			$th = 'rd';
		} else {
			$th = 'th';
		}	
?>

<h1>FIRST GOAL BY THE <?php echo $min.'<sup>'.$th.'</sup> minute'; ?> TOTAL GAMES : <?php echo $total_games['total_games']; ?></h1>

<div style="width:auto;text-align:center;" align="center">
	<?php		
		foreach ($this->dataset->data as $row) {
			if(!array_key_exists('score',$row)) {
				$overs = array('1.5','2.5','3.5','4.5');
			 	foreach($overs as $over) {
			 		if(array_key_exists($over,$row)) {
			 			$key = $over;
			 			break;
			 		}
			 	}			 	
	?>
	<table class="dataset" cellpadding="0" cellspacing="0">
   		<thead>
   			<tr class="odd">
				<td style="width:20%">UNDER <?php echo $key; ?> TOTAL MATCHES</td>
				<td style="width:20%">UNDER <?php echo $key; ?> PERCENT</td>
				<td style="width:20%">OVER <?php echo $key; ?> TOTAL MATCHES</td>
				<td style="width:40%">OVER <?php echo $key; ?> PERCENT</td>
			</tr>
		</thead>
			<tbody>
				<tr class="">
					<td align="center">
						<b><?=$row[$key]['under']['cate'];?></b>
					</td>
	                <td align="center" <?php if($row[$key]['under']['percent'] > 70) echo 'style="color:red;"'; ?>>
	                	<b><?=$row[$key]['under']['percent'];?>%</b>
	                </td>
	                <td align="center">
	                	<b><?=$row[$key]['over']['cate'];?></b>
	                </td>		                
	                <td align="center" <?php if($row[$key]['over']['percent'] > 70) echo 'style="color:red;"'; ?>>
	                	<b><?=$row[$key]['over']['percent'];?>%</b>
	                </td>
				</tr>
			</tbody>
	</table>

	<?php 				 
		}
	}	
	?>

	<?=$this->dataset->table_head();?>
		<br/>                        
		<ul class="module_links a" style="float:right;margin-top:333px;margin-right:314px;">			
			<li>
			<a href="#" id="search">Search</a>
			</li>
			<li>
			<a href="/admincp7/livescore/list_matches_first_goal" target="_blank">List matches first goal</a>
			</li>
		</ul>                        		
            <?		

            if (!empty($this->dataset->data)) {

                $path = site_url().'app/modules/livescore/assets/';
                $keys = count($this->dataset->data);                

				foreach ($this->dataset->data as $row) {
					if(array_key_exists('score',$row)) {                 						
				?> 
					<tr>													
		                <td align="center">&nbsp;</td>
		                <td align="center">&nbsp;</td>
		                <td align="center">&nbsp;</td>
		                <td align="center">&nbsp;</td>
		                <td align="center">&nbsp;</td>
		                <td align="center"><b><?=$row['score'];?></b></td>
		                <td align="center">&nbsp;</td>
		                <td align="center"><b><?=$row['cate'];?></b></td>
		                <td align="center"><b><?=$row['percent'];?>%</b></td>		                		               
					</tr>					

				<?
			 } 

			}

	}

	else {

	?>

	<tr>

		<td colspan="4">No matches.</td>

	</tr>

	<? } ?>                        

	<?=$this->dataset->table_close();?>

</div>

<?=$this->load->view(branded_view('cp/footer'));?>



