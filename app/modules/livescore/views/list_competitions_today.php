<?=$this->load->view(branded_view('cp/header'));?>

<h1>COMPETITIONS TODAY</h1>

<div style="width:auto;text-align:center;" align="center">

	<?=$this->dataset->table_head();?>                        

            <?php		

            if (!empty($this->dataset->data)) {

                $path = site_url().'app/modules/livescore/assets/';
                
		foreach ($this->dataset->data as $row) {                    

		?>

			<tr>
            <td><input type="checkbox" name="check_<?=$row['index'];?>" value="1" class="action_items" /></td>				
            <td align="center" <?php if (!$row['ok_competition'])  { echo 'style="color:red;"'; }?>><b><?=$row['name'];?></b></td>
            <td align="center"><b><?=$row['country_name'];?></b></td>
            <td align="center"><b><?=$row['matches'];?></b></td>
            <td align="center"><b><?=$row['link'];?></b></td>
            <td align="center"><b><a href="<?=$row['link_complete'];?>" target="_blank"><?=$row['link_complete'];?></a></b></td>											            
			</tr>

		<?php

		}

	}

	else {

	?>

	<tr>

		<td colspan="6">No competitions.</td>

	</tr>

	<?php } ?>                        

	<?=$this->dataset->table_close();?>

</div>

<?=$this->load->view(branded_view('cp/footer'));?>


 