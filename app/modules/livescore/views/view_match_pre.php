<?= $this->load->view(branded_view('cp/header')); ?>
<?php
// print '<pre>';
// print_r($match);
// print '</pre>';
?>
<h1>Match Details ( <?php echo $match['match_date']; ?> )</a></h1>
<div style="width:auto;text-align:center;" align="center">
    <table class="table table-striped table-hover" align="center">
        <thead>
            <th width="30%" style="text-align:right;">Home team</th>
            <th width="15%" style="text-align:center;">Match score</th>
            <th>Away team</th>
        </thead>
        <tbody>
            <td width="30%" style="text-align:right;"><h3><?php echo $home['name']; ?></h3></td>
            <td width="15%" style="text-align:center;"><h3><?php echo $match['score']; ?></h3></td>
            <td><h3><?php echo $away['name']; ?></h3></td>
        </tbody>
    </table>
</div>
<?= $this->load->view(branded_view('cp/footer')); ?>

