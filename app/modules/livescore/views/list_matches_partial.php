<html>
    <head>
        <link media="screen" type="text/css" rel="stylesheet" href="<?php echo site_url('/branding/default/css/universal.css'); ?>">
        <link media="screen" type="text/css" rel="stylesheet" href="<?php echo site_url('/branding/default/css/bootstrap.css'); ?>">
        <link media="screen" type="text/css" rel="stylesheet" href="<?php echo site_url('/branding/default/css/bootstrap-responsive.css'); ?>">
        <link media="screen" type="text/css" rel="stylesheet" href="<?php echo site_url('/branding/default/css/bootstrap-datetimepicker.min.css'); ?>">
        <link href="<?php echo site_url('/branding/default/css/dataset.css'); ?>" type="text/css" rel="stylesheet">
        
        <script>
        jQuery(document).ready(function(){

        $('#search').on('click', function(event){
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
                    data: 'match_date_start='+match_date_start+"&match_date_end="+match_date_end+"&country_name="+country_name+"&team1="+team1+"&team2="+team2+"&score="+score,
                    dataType:'html',
                    success: function(data, textStatus, XMLHttpRequest) {
                        console.log('filters '+data);
                        $('input[name="filters"]').val(data);
                        document.forms['dataset_form'].submit();
                     }      
                    });

           });

        });    

        </script>
    </head>
<h1>MATCHES</h1>

<div style="width:auto;text-align:center;" align="center">

	<?=$this->dataset->table_head();?>		
            <?php

            if (!empty($this->dataset->data)) {

                $path = site_url().'app/modules/livescore/assets/';

				foreach ($this->dataset->data as $row) {
				$score = explode('-',$row['score']);
				$row['score'] = $score[0].' - '.$score[1];
            
            $color = 'white';
            if (array_key_exists('status', $row)) {
                 if ($row['status'] == 'new') {
                    $color = 'cyan';
                } elseif($row['status'] == 'old') {
                    $color = 'red';
                }
            }
           
				?>

					<tr style="background-color: <?php echo $color; ?>;">						                
                    <td align="center"><b><?=$row['competition_name'];?></b></td>
                    <td align="center"><b><?=$row['match_date'];?></b></td>
                    <td align="center"><b><?=$row['team1'];?></b></td>
                    <td align="center"><b><?=$row['team2'];?></b></td>
                    <td align="center"><b><?=$row['score'];?></b></td>                
                    <td align="center"><b><a href="<?=$row['link_match'];?>" target="_blank"><?=$row['link_complete'];?></a></b></td>		                
					</tr>

				<?

			}

	}

	else {

	?>

	<tr>

		<td colspan="10">No matches.</td>

	</tr>

	<? } ?>                        

	<?=$this->dataset->table_close();?>

</div>

<?=$this->load->view(branded_view('cp/footer'));?>



