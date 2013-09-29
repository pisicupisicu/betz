<?=$this->load->view(branded_view('cp/header'));?>
<h1>STRATEGIES : Amounts are in &euro;</h1>
<div style="width:auto;text-align:center;" align="center">
	<?=$this->dataset->table_head();?>                        
            <?		
            if (!empty($this->dataset->data)) {
                $path = site_url().'app/modules/livescore/assets/';
			foreach ($this->dataset->data as $row) {
                if($row['is_computed'] == TRUE) $img = '<img src="'.$path.'publie.png"/>'; else $img = '<img src="'.$path.'cache.png"/>';
                $link1 = '<a href="'.site_url('admincp2/livescore/compute_strategy/' . $row['id']).'">'.$img.'</a>';
			?>
			<tr>							
				<td><input type="checkbox" name="check_<?=$row['id'];?>" value="1" class="action_items" /></td>
                <td align="center"><b><?=$row['name'];?></b></td>
                <td align="center"><b><?=$row['start'];?></b></td>
				<td align="center"><b><?=$row['rate'];?></b></td>			
				<td align="center"><b><?=$row['multiply'];?></b></td>
                <td align="center"><b><?=$row['stop'];?></b></td>
                <td align="center"><b><?=$row['intermission'];?></b></td>
                <td align="center"><b><a href="/admincp2/livescore/edit_strategy/<?=$row['id'];?>">edit</a></b></td>
                <td align="center"><b><?=$link1;?></b></td>
                <td align="center"><b><a href="/admincp2/livescore/view_strategy/<?=$row['id'];?>">view</a></b></td>
			</tr>
		<?
		}
	}
	else {
	?>
	<tr>
		<td colspan="6">No strategies.</td>
	</tr>
	<? } ?>                        
	<?=$this->dataset->table_close();?>
</div>
<?=$this->load->view(branded_view('cp/footer'));?>
