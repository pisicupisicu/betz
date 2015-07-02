<?=$this->load->view(branded_view('cp/header'));?>

<h1>TEAMS</h1>

<div style="width:auto;text-align:center;" align="center">

	<?=$this->dataset->table_head();?>                        

            <?php		

            if (!empty($this->dataset->data)) {

                $path = site_url().'app/modules/livescore/assets/';
                
		foreach ($this->dataset->data as $row) {                    
          if (!$row['ok']) {
              $link = '/admincp3/livescore/list_matches_by_team_id_pre/' . $row['index'];
              if (!$row['similar_teams']) {
                  $edit = '/admincp3/livescore/edit_team_pre/' . $row['index'];
              } else {
                  $edit = '/admincp3/livescore/edit_team_pre_similar/' . $row['index'];
              }
              
          } else {
              $link = '/admincp/livescore/list_matches_by_team_id/' . $row['team_id'];
              $edit = '/admincp/livescore/edit_team/' . $row['team_id'];
          }
		?>

			<tr>
				<td><input type="checkbox" name="check_<?=$row['team_id'];?>" value="1" class="action_items" /></td>
            <td align="center" <?php if (!$row['ok'])  { echo 'style="color:red;"'; }?>><b><?=$row['name'];?></b></td>
            <td align="center"><b><?=$row['link'];?></b></td>
            <td align="center"><b><?=$row['similar_teams'];?></b></td>
            <td align="center"><b><?=$row['team_id'];?></b></td>
				<td align="center"><b><?=$row['country_name'];?></b></td>
				<td align="center"><b><?=$row['matches'];?></b></td>
				<td align="center"><b><a href="<?=$link;?>" target="_blank">matches</a></b></td>							
            <td align="center"><b><a href="<?=$edit;?>">edit</a></b></td>
			</tr>

		<?php

		}

	}

	else {

	?>

	<tr>

		<td colspan="6">No teams.</td>

	</tr>

	<?php } ?>                        

	<?=$this->dataset->table_close();?>

</div>

<?=$this->load->view(branded_view('cp/footer'));?>




