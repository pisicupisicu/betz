<?=$this->load->view(branded_view('cp/header'));?>
<h1>Method Settings List</h1>
<?=$this->dataset->table_head();?>
<?
if (!empty($this->dataset->data)) {
	foreach ($this->dataset->data as $row) {
	?>
		<tr>			
			<td align="center"><?=$row['id_method'];?></td>
			<td align="center"><?=$row['method_name'];?></td>
			<td align="center"><?=$row['min'];?></td>			
			<td align="center"><?=$row['stake'];?></td>
			<td align="center"><?=$row['odds'];?></td>
			<td align="center"><?=$row['over'];?></td>
			<td align="center"><?=number_format($row['profit'], 2);?></td>
			<td align="center"><?=$row['percent'];?>%</td>
			<td align="center"><?=$row['total_bets'];?></td>
			<td align="center"><?=$row['alias'];?></td>
			<td align="center" class="options"><b><a href="/admincp8/livescore/simulate_method/<?=$row['id_setting'];?>" target="_blank">simulate</a></b></td>
			<td align="center" class="options"><b><a href="/admincp8/livescore/view/<?=$row['id_setting'];?>" target="_blank">view</a></b></td>                        		
		</tr>
	<?
	}
}
else {
?>
<tr><td colspan="8" align="center">Empty data set.</td></tr>
<?
}	
?>
<?=$this->dataset->table_close();?>
<?=$this->load->view(branded_view('cp/footer'));?>