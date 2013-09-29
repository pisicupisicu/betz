<?=$this->load->view(branded_view('cp/header'));?>
<h1>Bet vs Favorite</h1>
<div style="text-align:center">
<?=$this->dataset->table_head();?>
<?
if (!empty($this->dataset->data)) {
	foreach ($this->dataset->data as $row) {
	?>
		<tr>
				<td><input type="checkbox" name="check_<?=$row['id_against'];?>" value="1" class="action_items" /></td>
				<td><?=$row['zone'];?></td>
                <td><?=$row['favorite_name'];?></td>
                <td><?=$row['favorite_odds'];?></td>
                <td><?=$row['draw_odds'];?></td>
                <td><?=$row['underdog_name'];?></td>
                <td><?=$row['underdog_odds'];?></td>
                <td><?=$row['result'];?></td>
                <td><?=$row['date_inserted'];?></td>                                                                      
				<td class="options" style="text-align:right; padding:0 5px 0 0;"><a href="<?=site_url('admincp/soccerstats/edit_against/' . $row['id_against']);?> " class="button action_button">edit</a></td>
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