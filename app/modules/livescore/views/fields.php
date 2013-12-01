<?=$this->load->view(branded_view('cp/header'));?>

<h1>Match Columns</h1>

<p><b>NOTE: Unmatched fields will NOT be imported.</b></p>

<?php if (isset($csv_data) && is_array($csv_data) && count($csv_data)) : ?>

<form class="form" action="<?php echo site_url('admincp4/livescore/do_import') ?>" method="post">

<div style="overflow: auto; height: 500px;">
	<table class="dataset" cellpadding="0" cellspacing="0">
		
     <?php $count = count( explode(',', $csv_data[0]) );	?>
		<?php $count = 0; ?>
		<?php foreach ($csv_data as $row) : ?>
			<?php
				$csv_fields = explode(',', $row);
			?>
			<?php if ($count == 1) { $count = 2; } else { $count = 1; } ?>

			<tr <?php echo $count == 1 ? 'class="odd"' : ''; ?>>
			<?php foreach ($csv_fields as $field) : ?>
				<td><?php echo str_replace('"', '', $field) ?></td>
			<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

</div>

<div class="submit">
	<br/>
	<input type="submit" class="button" name="submit" value="Import Bets" />
</div>

</form>

<?php endif; ?>


<?=$this->load->view(branded_view('cp/footer'));?>