<?=$this->load->view(branded_view('cp/header'));?>
<div style="width:auto;text-align:center;" align="center;clear:both;">
    <div style="width:50%;float:left;">
        <iframe src="<?php echo site_url('admincp/livescore/list_matches_by_team_id_partial/' . $team_to_keep . '/' . $team_to_remove . '/1'); ?>" style="height:2500px;"></iframe>
    </div>
    <div style="width:50%;float:left;">
        <iframe src="<?php echo site_url('admincp/livescore/list_matches_by_team_id_partial/' . $team_to_keep . '/' . $team_to_remove . '/2'); ?>" style="height:2500px;"></iframe>
    </div>
</div>

<?=$this->load->view(branded_view('cp/footer'));?>





