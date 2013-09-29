<?=$this->load->view(branded_view('cp/header'));?>
<h1>Introstats</h1>
<div style="text-align:center">
<?=$this->dataset->table_head();?>
<?
if (!empty($this->dataset->data)) {
	foreach ($this->dataset->data as $row) {
	?>
		<tr>
			<td><input type="checkbox" name="check_<?=$row['introstats_id'];?>" value="1" class="action_items" /></td>
			<td><?=$row['league_name'];?></td>
                        <td><?=$row['matches_played'];?></td>
                        <td>    
                            <div class="progress" style="margin-bottom: 0;">
                                <div class="bar bar-success" style="width: <?=$row['home_wins'];?>%;"></div>
                                <div class="bar bar-warning" style="width: <?=$row['draw'];?>%;"></div>
                                <div class="bar bar-danger" style="width: <?=$row['away_wins'];?>%;"></div>
                            </div>
                        </td>
                        <td><?=$row['home_wins'];?>%</td>
                        <td><?=$row['draw'];?>%</td>
                        <td><?=$row['away_wins'];?>%</td>
                        <td><?=$row['goals_average'];?></td>
                        <td><?=$row['home_average'];?></td>
                        <td><?=$row['away_average'];?></td>
                        <td><?=$row['over_1_5'];?>%</td>
                        <td><?=$row['over_2_5'];?>%</td>
                        <td><?=$row['over_3_5'];?>%</td>
                                                
			<td class="options" style="text-align:right; padding:0 5px 0 0;"><a href="<?=site_url('admincp/soccerstats/stats/' . $row['league_name']);?> " class="button action_button">stats</a></td>
		</tr>
	<?
	}
}
else {
?>
<tr><td colspan="100%">Empty data set.</td></tr>
<?
}	
?>
<?=$this->dataset->table_close();?>
</div>
<?=$this->load->view(branded_view('cp/footer'));?>