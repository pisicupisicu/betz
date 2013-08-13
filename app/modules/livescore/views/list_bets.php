<?=$this->load->view(branded_view('cp/header'));?>
<h1>Bets List</h1>
<?=$this->dataset->table_head();?>
<?
$i = 0;
if (!empty($this->dataset->data)) {
	foreach ($this->dataset->data as $row) {
	$i++;
            ?>
		<tr title="<?=$row['comment'];?>">
			<td><input type="checkbox" name="check_<?=$row['ID_bet'];?>" value="1" class="action_items" /></td>
			<td><?=$i;?></td>
			<td><?=$row['event_name'];?></td>
                        <td><?=$row['country_name'];?></td>
                        <td><?=$row['event_type'];?></td>
                        <td><?=$row['stake'];?></td>
                        <td style="padding: 0 !important;">
                                    <div class="btn-success text-center"><?=$row['profit'];?></div>
                                    <div class="btn-danger text-center"><?=$row['loss'];?></div>
                        </td>
                        <td><?=$row['bet_type'];?></td>
                        <td><?=$row['odds'];?></td>
                        <td><?=$row['market_type'];?></td>
                        <td><?=$row['strategy'];?></td>                      
			<td class="options" style="text-align:right; padding:0 5px 0 0;"><a href="<?=site_url('admincp2/livescore/edit_bet/' . $row['ID_bet']);?> " class="button action_button">edit</a></td>
		</tr>
	<?
        
        
        
	}
}
else {
?>
<tr><td colspan="8">Empty data set.</td></tr>
<?
}	
?>
<?=$this->dataset->table_close();?>
<?=$this->load->view(branded_view('cp/footer'));?>