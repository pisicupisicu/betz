<?= $this->load->view(branded_view('cp/header')); ?>
<script>
    jQuery(document).ready(function () {

        $('#search').on('click', function (event) {
            var id = $(this).attr('id');
            var match_date_end = $('input[name="match_date_end"]').val();
            var match_date_start = $('input[name="match_date_start"]').val();
            var team1 = $('input[name="team1"]').val();
            var team2 = $('input[name="team2"]').val();
            var score = $('input[name="score"]').val();
            var country_name = $('input[name="country_name"]').val();

            $.ajax({
                type: 'post',
                url: '/admincp/livescore/update_filters/',
                data: 'match_date_start=' + match_date_start + "&match_date_end=" + match_date_end + "&country_name=" + country_name + "&team1=" + team1 + "&team2=" + team2 + "&score=" + score,
                dataType: 'html',
                success: function (data, textStatus, XMLHttpRequest) {
                    console.log('filters ' + data);
                    $('input[name="filters"]').val(data);
                    document.forms['dataset_form'].submit();
                }
            });

        });

    });

</script>

<h1>MATCHES TODAY</h1>

<div style="width:auto;text-align:center;" align="center">

    <?= $this->dataset->table_head(); ?>
    <br/>                        
    <ul class="module_links a" style="float:right;margin-top:70px;margin-right:160px;">			
        <li>
            <a href="#" id="search">Search</a>
        </li>
    </ul>
    <?php
    if (!empty($this->dataset->data))
    {
        $path = site_url() . 'app/modules/livescore/assets/';

        foreach ($this->dataset->data as $row)
        {
            $score = explode('-', $row['score']);
            $row['score'] = $score[0] . ' - ' . $score[1];
            ?>
            <tr>							
                <td><input type="checkbox" name="check_<?= $row['index']; ?>" value="1" class="action_items" /></td>
                <td align="center"><b><?= $row['country_name']; ?></b></td>
                <td align="center" <?php
                if (!$row['ok_competition'])
                {
                    echo 'style="color:red;"';
                }                                              
                
                ?>><b><?= $row['competition_name']; ?></b></td>
                <td align="center"><b><?= $row['match_date']; ?></b></td>
                <td align="center" <?php
                    if (!$row['ok_team1'])
                    {
                        echo 'style="color:red;"';
                    }
                    ?>><b><?= $row['team1']; ?></b></td>
                <td align="center" <?php
            if (!$row['ok_team2'])
            {
                echo 'style="color:red;"';
            }
            ?>><b><?= $row['team2']; ?></b></td>
                <td align="center"><b><?= $row['score']; ?></b></td>                
                <td align="center"><b><a href="/admincp9/livescore/h2h/<?=$row['team1_id'];?>/<?=$row['team2_id'];?>/<?=$row['match_date'];?>" target="_blank">H2h</a></b></td>                
                <td align="center"><b><?= $row['percentage']; ?>%</b></td>
                <td align="center" style="background-color:<?php echo $row['color']; ?>"><b><?= $row['over']; ?></b></td>                
            </tr>

        <?php
    }
}
else
{
    ?>
        <tr>
            <td colspan="10">No today matches.</td>
        </tr>
<?php } ?>                        
<?= $this->dataset->table_close(); ?>
</div>
<?= $this->load->view(branded_view('cp/footer')); ?> 