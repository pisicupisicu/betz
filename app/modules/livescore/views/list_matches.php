<?=$this->load->view(branded_view('cp/header'));?>

<h1>MATCHES</h1>

<div style="width:auto;text-align:center;" align="center">

	<?=$this->dataset->table_head();?>                        

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

				<?

			}

	}

	else {

	?>

	<tr>

		<td colspan="6">No competitions.</td>

	</tr>

	<? } ?>                        

	<?=$this->dataset->table_close();?>

</div>

<?=$this->load->view(branded_view('cp/footer'));?>



