<?=$this->load->view(branded_view('cp/header'));?>
<h1>STEPS</a></h1>
<div style="width:1600px;text-align:center;" align="center">
	<?=$this->dataset->table_head();?>
            <table align="center" border="1" width="600">
            <th align="center" colspan="4">Amounts are in &euro;</th>'
            <tr align="center"><td colspan="4">&nbsp;</td></tr>
            <tr><th align="center">STEP</th><th align="center">STAKE</th><th align="center">PROFIT</th><th align="center">BANK</th></tr>
            
            <?
		
            if (!empty($this->dataset->data)) {
		foreach ($this->dataset->data as $row) {
		?>
			<tr>							
				<td align="center"><b><?=$row['steps'];?></b></td>
                                <td align="center"><b><?=$row['stake'];?></b></td>
				<td align="center"><b><?=$row['win'];?></b></td>			
				<td align="center"><b><?=number_format($row['amount'],2,'.',',');?></b></td>			
			</tr>
		<?
		}
	}
	else {
	?>
	<tr>
		<td colspan="7">Nu sunt categorii.</td>
	</tr>
	<? } ?>
            
            </table>
	<?=$this->dataset->table_close();?>
</div>
<?=$this->load->view(branded_view('cp/footer'));?>
