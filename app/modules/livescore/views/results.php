<?=$this->load->view(branded_view('cp/header'));?>

<h1>Import Bets Results</h1>

	<?php if ($inserted) : ?>
		<p><?php echo $inserted ?> bets were successfully imported.</p>
	<?php endif; ?>
        
    <?php if ($duplicates) : ?>
		<p><?php echo $duplicates ?> bets were duplicates.</p>
	<?php endif; ?>    
	
    <?php if ($duplicates) : ?>   
	<p>The following members were unable to be imported: </p>
	<?php print '<pre>'; print_r($duplicate); print '</pre>'; ?>
    <?php endif; ?>
    
	<p><?php echo ($total_imports-2); ?> bets were parsed.</p>


<?=$this->load->view(branded_view('cp/footer'));?>