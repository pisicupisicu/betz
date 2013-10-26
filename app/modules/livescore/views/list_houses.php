<?=$this->load->view(branded_view('cp/header'));?>
<h1>Bookmakers List</h1>
<?=$this->dataset->table_head();?>
<?
if (!empty($this->dataset->data)) {
	foreach ($this->dataset->data as $row) {
	?>
		<tr>
			<td><input type="checkbox" name="check_<?=$row['id_house'];?>" value="1" class="action_items" /></td>
            <td><img src="/app/modules/livescore/assets/logos/<?=$row['logo_house'];?>" /></td>
			<td><?=$row['name_house'];?></td>  
            <td><a href="<?=$row['link_house'];?>" alt="<?=$row['name_house'];?>" target="_blank"><?=$row['link_house'];?></a></td>       
			<td class="options" style="text-align:right; padding:0 5px 0 0;"><a href="<?=site_url('admincp4/livescore/edit_house/' . $row['id_house']);?> " class="button action_button">edit</a></td>
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