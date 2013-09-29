<?=$this->load->view(branded_view('cp/header'));?>
<?php
	// print '<pre>';
	// print_r($match);
	// print '</pre>';
?>

<h1>MATCH <?php echo $match['country_name'].' '.$match['name'].' '.$match['match_date']; ?></a></h1>

<div style="width:auto;text-align:center;" align="center">

	<table class="table table-striped table-hover" align="center">

		<thead>

			<th width="10%"><h3>Minutes</h3></th>

			<th width="30%" style="text-align:right;"><h3><?php echo $home['name']; ?></h3></th>

			<th width="15%" style="text-align:center;"><h3><?php echo $match['score']; ?></h3></th>

			<th><h3><?php echo $away['name']; ?></h3></th>

		</thead>

			<?php

				foreach($goals as $goal) {					

					echo '<tr>';

					$assist	=	$goal['assist']	?	' (assist '.$goal['assist'].')'	:	'';

					$type	=	$goal['type']	?	' '.$goal['type']				:	'';



					if($goal['team'] == 'home') {

						echo '<td>'.$goal['min'].'\'</td><td style="text-align:right;">'.$goal['player'].$type.$assist.' <img src="'.site_url('/app/modules/livescore/assets/ball.png').'"/>'.'</td><td style="text-align:center;">'.$goal['score'].'</td>';

						echo '<td>&nbsp;</td>';						

					} else {

						echo '<td>'.$goal['min'].'\'</td><td>&nbsp;</td><td style="text-align:center;">'.$goal['score'].'</td>';

						echo '<td>'.'<img src="'.site_url('/app/modules/livescore/assets/ball.png').'"/> '.$goal['player'].$type.$assist.'</td>';												

					}

					echo '</tr>';

				}



				foreach($cards as $card) {

					if($card['card_type'])				

					echo '<tr>';

					if($card['team'] == 'home') {

						echo '<td>'.$card['min'].'\'</td><td style="text-align:right">'.$card['player'].' <img src="'.site_url('/app/modules/livescore/assets/'.$card['card_type'].'.png').'"/>'.'</td><td>&nbsp;</td>';

						echo '<td>&nbsp;</td>';						

					} else {

						echo '<td>'.$card['min'].'\'</td><td>&nbsp;</td><td>&nbsp;</td>';

						echo '<td>'.'<img src="'.site_url('/app/modules/livescore/assets/'.$card['card_type'].'.png').'"/> '.$card['player'].'</td>';												

					}

					echo '</tr>';

				}

			?>

	</table>

</div>

<?=$this->load->view(branded_view('cp/footer'));?>

