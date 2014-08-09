<?=$this->load->view(branded_view('cp/header'));?>
<h1>Bets List</h1>
<?=$this->dataset->table_head();?>
<?php
$i = 0;
if (!empty($this->dataset->data)) {
	foreach ($this->dataset->data as $row) {
	    $i++;
        $id = $offset+$i;
        ?>
		<tr title="<?=$row['comment'];?>">
			<td><input type="checkbox" name="check_<?=$row['ID_bet'];?>" value="1" class="action_items" /></td>
			<td><? echo $id;?></td>
            <td><? echo $row['paper_bet']=='1' ? "<img src='".site_url('/app/modules/livescore/assets/paper_bet.png')."'/>" : "<img src='".site_url('/app/modules/livescore/assets/money_bet.png')."'/>"; ?></td>
            <td><? echo $row['live_tv']=='1' ? "<img src='".site_url('/app/modules/livescore/assets/tv_icon.png')."'/>" : "<img src='".site_url('/app/modules/livescore/assets/nontv_icon.png')."'/>"; ?></td>             
			<td><?=$row['event_name'];?></td>
            <td><?=$row['event_date'];?></td>
            <td><?=$row['country_name'];?></td>
            <td><?=$row['event_type'];?></td>
            <td><?php echo number_format($row['stake'], 2);?></td>
            <td style="padding: 0 !important;">
                <? if(empty($row['profit'])){ echo'<div class="alert alert-loss text-center oddformat">'.number_format($row['loss'], 2).'</div>';}
                   else { echo'<div class="alert alert-protif text-center oddformat">'.number_format($row['profit'], 2).'</div>';} ?>                               
            </td>
            <!--<td><?=$row['bet_type'];?></td>-->
            <td><div <? if($row['bet_type']=='Back'){echo 'class="alert alert-info oddformat"';}else{echo 'class="alert alert-danger oddformat"';}?>>
                <?php echo number_format($row['odds'], 2);?></div>
            </td>
            <td><?=$row['market_type'];?></td>
            <td><?=$row['market_select'];?></td>
            <td><?=$row['strategy'];?></td>
            <td><?=$row['final_score'];?></td>            
            <?if ($this->user_model->logged_in() and !$this->user_model->is_admin()){echo "&nbsp;";}else{echo "<td>".$row['username']."</td>";}?> 
			<td class="options" style="text-align:right; padding:0 5px 0 0;"><a href="<?=site_url('admincp2/livescore/edit_bet/' . $row['ID_bet']);?> " class="button action_button">edit</a></td>
		</tr>
	<?
                    
	}
}
else {
?>
<tr><td colspan="15">Empty data set.</td></tr>
<?
}	
?>
<?=$this->dataset->table_close();?>
<?=$this->load->view(branded_view('cp/footer'));?>