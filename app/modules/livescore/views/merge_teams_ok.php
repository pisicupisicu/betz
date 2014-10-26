<?=$this->load->view(branded_view('cp/header'));?>

<div align="center">
    <h1>Merged performed successfully</h1>
    <h3>The team <?php echo $team_to_remove_name; ?> has been merged into team <?php echo $team_to_keep_name; ?></h3>

    <?php
        $ok = site_url().'admincp5/livescore/merge_teams/';    
    ?>
    <h1><a href="<?php echo $ok; ?>">Ok</a></h1>
</div>

<?=$this->load->view(branded_view('cp/footer'));?>
