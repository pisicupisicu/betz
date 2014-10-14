<html>
<head>
<link media="screen" type="text/css" rel="stylesheet" href="<?php echo site_url('/branding/default/css/universal.css'); ?>">
<link media="screen" type="text/css" rel="stylesheet" href="<?php echo site_url('/branding/default/css/bootstrap.css'); ?>">
<link media="screen" type="text/css" rel="stylesheet" href="<?php echo site_url('/branding/default/css/bootstrap-responsive.css'); ?>">
<link media="screen" type="text/css" rel="stylesheet" href="<?php echo site_url('/branding/default/css/bootstrap-datetimepicker.min.css'); ?>">
<link href="<?php echo site_url('/branding/default/css/dataset.css'); ?>" type="text/css" rel="stylesheet">
</head>

<div style="width:auto;text-align:center;" align="center">
<h3><?php echo $new; ?> NEW MATCHES from team <?php echo $team_to_remove_name; ?> - they are about to be added to team <?php echo $team_to_keep_name; ?>!!!</h3>    
    <?=$this->dataset->table_head();?>	
    <?php
    $color = 'cyan';
    foreach ($matches_second_team as $row) {
        if ($row['status'] == 'new') {
            ?>
            <tr style="background-color: <?php echo $color; ?>;">
                <td align="center"><b><?=$row['competition_name'];?></b></td>
                <td align="center"><b><?=$row['match_date'];?></b></td>
                <td align="center"><b><?=$row['team1'];?></b></td>
                <td align="center"><b><?=$row['team2'];?></b></td>
                <td align="center"><b><?=$row['score'];?></b></td>
                <td align="center"><b><a href="<?=$row['link_match'];?>" target="_blank"><?=$row['link_complete'];?></a></b></td>
            </tr>
            <?php
        }
    }
    ?>
    <?=$this->dataset->table_close();?>
                
<div style="width:auto;text-align:center;" align="center">
    <h3><?php echo $old; ?> OLD MATCHES from team <?php echo $team_to_remove_name; ?> - they are about to be removed from the database along with the team <?php echo $team_to_keep_name; ?>!!!</h3>
<?=$this->dataset->table_head();?>	    
    <?php
    $color = 'red';
    foreach ($matches_second_team as $row) {
        if ($row['status'] == 'red') {
            ?>
            <tr style="background-color: <?php echo $color; ?>;">
                <td align="center"><b><?=$row['competition_name'];?></b></td>
                <td align="center"><b><?=$row['match_date'];?></b></td>
                <td align="center"><b><?=$row['team1'];?></b></td>
                <td align="center"><b><?=$row['team2'];?></b></td>
                <td align="center"><b><?=$row['score'];?></b></td>
                <td align="center"><b><a href="<?=$row['link_match'];?>" target="_blank"><?=$row['link_complete'];?></a></b></td>
            </tr>
            <?php
        }
    }
    ?>
    <?=$this->dataset->table_close();?>
                
    <?php if (!$old && !$new) echo 'No matches.'; ?>
    <br/><br/><br/>
    Are you sure you want to do this?&nbsp;&nbsp;&nbsp;
    <?php
        $no = site_url().'admincp5/livescore/merge_teams/';
        $yes = site_url().'admincp5/livescore/merge_teams_ok/' . $team_to_keep. '/' . $team_to_remove;
    ?>
    <h1><a href="<?php echo $yes; ?>">Yes</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo $no; ?>">No</a></h1>
               
</div>
<?=$this->load->view(branded_view('cp/footer'));?>


