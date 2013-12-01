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
            url: '/admincp7/livescore/update_filters/',
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

<h1>MATCHES</h1>

<div style="width:auto;text-align:center;" align="center">

	<?=$this->dataset->table_head();?>
		<br/>                        
		<ul class="module_links a" style="float:right;margin-top:70px;margin-right:160px;">			
			<li>
			<a href="#" id="search">Search</a>
			</li>
		</ul>
            <?		

            if (!empty($this->dataset->data)) {

                $path = site_url().'app/modules/livescore/assets/';

				foreach ($this->dataset->data as $row) {
				$score = explode('-',$row['score']);
				$row['score'] = $score[0].' - '.$score[1];                    						
				?>

					<tr>							
						<td><input type="checkbox" name="check_<?=$row['id'];?>" value="1" class="action_items" /></td>
		                <td align="center"><b><?=$row['country_name'];?></b></td>
		                <td align="center"><b><?=$row['competition_name'];?></b></td>
		                <td align="center"><b><?=$row['match_date'];?></b></td>
		                <td align="center"><b><?=$row['team1'];?></b></td>
		                <td align="center"><b><?=$row['team2'];?></b></td>
		                <td align="center"><b><?=$row['score'];?></b></td>                
		                <td align="center"><b><a href="<?=$row['link_match'];?>" target="_blank"><?=$row['link_complete'];?></a></b></td>
		                <td align="center" class="options"><b><a href="/admincp5/livescore/view_match/<?=$row['id'];?>" target="_blank" >view</a></b></td>
		                <td align="center" class="options"><b><a href="/admincp4/livescore/edit_match/<?=$row['id'];?>">edit</a></b></td>
					</tr>
					<tr>
						<td colspan="10" style="background-color:#F4C2C2">
							<?php
								$minutes = range(1,120);								
								foreach($minutes as $minute) {										
										foreach($row['goals'] as $score => $min) {
											if($minute == $min) {
												echo '<span style="padding-left:10px;"><img src="'.site_url('/app/modules/livescore/assets/ball.png').'"/>'.'</span><b><span style="color:red;padding-left:5px;">'.$min.'</span></b>'
										."' ".'<b><span style="padding-left:5px;">'.$score.'</span></b>'." ";
											}
										}
										foreach($row['cards'] as $card_type => $min) {
											if($minute == $min) {
												$card_img = site_url('/app/modules/livescore/assets/'.$card_type.'.png');
												echo '<span style="padding-left:10px;"><img src="'.$card_img.'" width="16px" height="16px"/>'.'</span><b><span style="color:red;padding-left:5px;">'.$min.'</span></b>';												
											}
										}	
										
									}								
								 
							?>
						</td>
					</tr>

				<?

			}

	}

	else {

	?>

	<tr>

		<td colspan="10">No matches.</td>

	</tr>

	<? } ?>                        

	<?=$this->dataset->table_close();?>

</div>

<?=$this->load->view(branded_view('cp/footer'));?>



