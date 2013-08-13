<?=$this->load->view(branded_view('cp/header'));?>
<h1>Methods List</h1>
<?=$this->dataset->table_head();?>
<?
if (!empty($this->dataset->data)) {
	foreach ($this->dataset->data as $row) {
	?>
		<tr>
			<td><input type="checkbox" name="check_<?=$row['ID_method'];?>" value="1" class="action_items" /></td>
			<td><?=$row['ID_method'];?></td>
			<td><?=$row['method_name'];?></td>
			<td><?php echo substr($row['method_description'], 0, 700)."...";
                        //echo $row['method_description'];?></td>
                        
			<td class="options" style="text-align:right; padding:0 5px 0 0;"><a href="<?=site_url('admincp2/livescore/edit_method/' . $row['ID_method']);?> " class="button action_button">edit</a></td>
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