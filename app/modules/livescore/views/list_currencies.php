<?=$this->load->view(branded_view('cp/header'));?>
<h1>Currencies List</h1>
<?=$this->dataset->table_head();?>
<?
if (!empty($this->dataset->data)) {
	foreach ($this->dataset->data as $row) {
	?>
		<tr>
			<td><input type="checkbox" name="check_<?=$row['id_currency'];?>" value="1" class="action_items" /></td>
            <td><img style="width:35px; height:25px;" src="/app/modules/livescore/assets/flags/<?=$row['flag'];?>" /></td>
			<td><?=$row['id_country'];?></td>
			<td><?=$row['name_currency'];?></td>
            <td><?=$row['code_ISO'];?></td>     
            <td><?=$row['symbol_currency'];?></td>         
			<td class="options" style="text-align:right; padding:0 5px 0 0;"><a href="<?=site_url('admincp4/livescore/edit_currency/' . $row['id_currency']);?> " class="button action_button">edit</a></td>
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