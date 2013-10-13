<?=$this->load->view(branded_view('cp/header'));?>
<script>
jQuery(document).ready(function(){
    $('#country_select').on('change', function(event){
   if(confirm('You will lose all your values!Are you sure?')) {
       var countryID = $("#country_select").val();
        var competitionString = "/admincp4/livescore/view_competitions_selects/"+ countryID;
        var homeString = "/admincp4/livescore/view_hometeam_selects/"+ countryID;
        var awayString = "/admincp4/livescore/view_awayteam_selects/"+ countryID;

        $.get(competitionString, function(data) {
        $("#competitionSelect").html(data);
        });

        $.get(homeString, function(data) {
        $("#homeSelect").html(data);
        });

        $.get(awayString, function(data) {
        $("#awaySelect").html(data);
        });
   }                               
 });   
    

$('#submit').on('click', function(event){alert('submit');
   if(!confirm('This will change you match values!Proceed?')) {
      event.preventDefault();
  }
});

$('#default_values').on('click', function(event){    
    if(confirm('You will lose your changes so far!Proceed?')) {
        $.ajax({
                    type: 'get',
                    url: '/admincp4/livescore/get_old_match/'+<?=$id_match;?>,
                    dataType:'html',
                    success: function(data, textStatus, XMLHttpRequest) {
                        console.log('succes '+data);
                        console.log($.parseJSON(data));
                        var matchObject = $.parseJSON(data);
                        $('#score').val(matchObject.score);
                        $('#livescore').val(matchObject.link_match);
                        
                        var competitionString = "/admincp4/livescore/view_competitions_selects_selected/"+<?=$id_country;?>+'/'+<?=$id_competition?>;
                        $.get(competitionString, function(data) {
                            $("#competitionSelect").html(data);
                        });
                        
                        var homeTeamString = "/admincp4/livescore/view_hometeam_selects_selected/"+<?=$id_country;?>+'/'+<?=$home_team_id?>;
                        $.get(homeTeamString, function(data) {
                            $('[name="home_team"]').html(data);
                        });
                        
                        var awayTeamString = "/admincp4/livescore/view_awayteam_selects_selected/"+<?=$id_country;?>+'/'+<?=$away_team_id?>;
                        $.get(awayTeamString, function(data) {
                            $('[name="away_team"]').html(data);
                        });
                        
                     }      
                });
    }
});

});    
    
</script>
<div class="row-fluid">
	<div class="span12">
	<h1><? if ($action == 'new') { ?>
                                    <?=$form_title;?>
                                    <? } else { ?>
                                    <?=$form_title;?> <span class="muted"> ID:<?=$id_match;?></span>
                                    <? } ?>
        </h1>
    
    <form class="form validate form-horizontal" enctype="multipart/form-data" id="add_league_form" method="post" action="<?=$form_action;?>">
            <input type="hidden" name="ID_match" value="<? if ($action == 'new') {  } else { echo $id_match;} ?>" />
             <div class="control-group">
                            <label for="contact-message" class="control-label">Country:</label>
                            
                            <div class="controls" id="default_country">
                               <? if ($action == 'new') { echo form_dropdown('country_name',$country_name,'','id="country_select"');} else { echo form_dropdown('country_name',$country_name,$id_country,'id="country_select"');} ?>
                            </div>
             </div>
            
            <div class="control-group">
                            <label for="competition_name" class="control-label">Competition:</label>
                            
                            <div class="controls" id="competitionSelect">
                               <? if ($action == 'new') { echo form_dropdown('competition_name',$competition_name); } else { echo form_dropdown('competition_name',$competition_name,$id_competition);} ?>
                            </div>
             </div>
            
             <div class="control-group">
                            <label for="event_date" class="control-label">Match Date:</label>
                            
                            <div class="controls">

                                          <div id="datetimepicker1" class="input-append date">
                                            <input name="match_date" value="<? if ($action == 'new') {  } else { echo $match_date;} ?>" data-format="yyyy-MM-dd" type="text"></input>
                                            <span class="add-on">
                                              <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                                              </i>
                                            </span>
                                          </div>
   
                            </div>
            </div>
            
            <div class="control-group">
                            <label for="home_team" class="control-label">Home Team:</label>
                            
                            <div class="controls" id="homeSelect">
                               <? if ($action == 'new') { echo form_dropdown('home_team',$team_name); } else { echo form_dropdown('home_team',$team_name,$home_team_id);} ?>
                            </div>
             </div>
            
            <div class="control-group">
                            <label for="away_team" class="control-label">Away Team:</label>
                            
                            <div class="controls" id="awaySelect">
                               <? if ($action == 'new') { echo form_dropdown('away_team',$team_name); } else { echo form_dropdown('away_team',$team_name,$away_team_id);} ?>
                            </div>
             </div>
            
            <div class="control-group">
                            <label for="score" class="control-label">Score:</label>
                            
                            <div class="controls">
                               <input type="text" id="score" name="score" value="<? if ($action == 'new') {  } else { echo $score;} ?>" placeholder="1-0" />
                            </div>
             </div>
            
             <div class="control-group">
                            <label for="livescore" class="control-label">Livescore Link:</label>
                            
                            <div class="controls">
                               <input type="text" style="width: 650px;" id="livescore" name="livescore" value="<? if ($action == 'new') {  } else { echo $livescore_link;} ?>" placeholder="http://www.livescore.com/soccer/england/premier-league/liverpool-vs-manchester-united/1-1474162/" />
                            </div>
             </div>
            
            <div class="control-group">
                <div class="controls">
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
             </div>
                     
            <div class="control-group">
		<div class="controls">
                                           <? if ($action == 'new') { ?>
                                    <input type="submit" class="btn btn-success btn-large" name="add" value="Add" />
                                    <? } else { ?>  
                                    <input type="button" class="btn btn-warning btn-large" id="default_values" name="default" value="Reset" />
                                    <input type="submit" id="submit" class="btn btn-info btn-large" name="edit" value="Edit" />
                                    <? } ?>
                </div>
            </div>

	  
    </form>
    </div>
</div>
 <script type="text/javascript">
      $(function() {
        $('#datetimepicker1').datetimepicker({
          collapse: true
        });
      });
    </script>

<?=$this->load->view(branded_view('cp/footer'));?>