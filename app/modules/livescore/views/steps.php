<?=$this->load->view(branded_view('cp/header'));?>
<h1>STEPS</a></h1>
<div style="width:auto;text-align:center;" align="center">
	<?=$this->dataset->table_head();?>           
            <?		
            if (!empty($this->dataset->data)) {
		foreach ($this->dataset->data as $row) {
		?>
			<tr>							
                            <td align="center"><b><?=$row['steps'];?></b></td>
                            <td align="center"><b><?=$row['stake'];?>&nbsp;&euro;</b></td>
				<td align="center"><b><?=$row['win'];?>&nbsp;&euro;</b></td>			
				<td align="center"><b><?=number_format($row['amount'],2,'.',',');?>&nbsp;&euro;</b></td>			
			</tr>
		<?
		}
	}
	else {
	?>
	<tr>
		<td colspan="7">No steps computed.</td>
	</tr>
	<? } ?>                       
	<?=$this->dataset->table_close();?>
</div>
<?=$this->load->view(branded_view('cp/footer'));?>
