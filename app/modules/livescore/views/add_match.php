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
    

$('#submit-score').on('click', function(event){ 
 
      this.submit();
 
});

 $( "#card_types" ).show();
 $( "#card_owner" ).show();
 $( "#score_step" ).hide();
 $( "#goal_scorer" ).hide();
 $( "#assist" ).hide();
 $( "#type" ).hide();
 $( "#team_types" ).show();
 $('#event_types').on('change', function(event){
			//alert($( "select#event_types option:selected").val())
		if($( "select#event_types option:selected").val()=="card") {
			 $( "#card_types" ).show();
			 $( "#card_owner" ).show();
			 $( "#score_step" ).hide();
			 $( "#goal_scorer" ).hide();
			 $( "#assist" ).hide();
			 $( "#type" ).hide();
			 $( "#team_types" ).show();
		}else{
			 $( "#card_types" ).hide();
			 $( "#card_owner" ).hide();
			 $( "#score_step" ).show();
			 $( "#goal_scorer" ).show();
			 $( "#assist" ).show();
			 $( "#type" ).show();
			 $( "#team_types" ).show();
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
                              <? if ($action == 'new') { echo form_dropdown('country_name',$country_name,'','id="country_select"'); } else { echo form_dropdown('country_name',$country_name,$id_country,'id="country_select" disabled="disabled"');} ?>
                            </div>
             </div>
            
            <div class="control-group">
                            <label for="competition_name" class="control-label">Competition:</label>
                            
                            <div class="controls" id="competitionSelect">
                              <? if ($action == 'new') { echo form_dropdown('competition_name',$competition_name); } else { echo form_dropdown('competition_name',$competition_name,$id_competition,'id="competition_name" disabled="disabled"');} ?>
                            </div>
             </div>
            
             <div class="control-group">
                            <label for="event_date" class="control-label">Match Date:</label>
                            
                            <div class="controls">

                                          <div id="datetimepicker1" class="input-append date" >
                                            <input name="match_date" <? if ($action == 'new') {  } else { echo 'value="'.$match_date.'" disabled="disabled"';} ?> data-format="yyyy-MM-dd" type="text"></input>
                                           <? if ($action == 'new') { ?> 
                                           <span class="add-on">
                                              <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                                              </i>
                                            </span>
                                            <? } else { } ?> 
                                          </div>
   
                            </div>
            </div>
            
            <div class="control-group">
                            <label for="home_team" class="control-label">Home Team:</label>
                            
                            <div class="controls" id="homeSelect">
                             <? if ($action == 'new') { echo form_dropdown('home_team',$team_name); } else { echo form_dropdown('home_team',$team_name,$home_team_id,'id="home_team" disabled="disabled"');} ?>
                            </div>
             </div>
            
            <div class="control-group">
                            <label for="away_team" class="control-label">Away Team:</label>
                            
                            <div class="controls" id="awaySelect">
                              <? if ($action == 'new') { echo form_dropdown('away_team',$team_name); } else { echo form_dropdown('away_team',$team_name,$away_team_id,'id="away_team" disabled="disabled"');} ?>
                            </div>
             </div>
            
            <div class="control-group">
                            <label for="score" class="control-label">Score:</label>
                            
                            <div class="controls">
                               <input type="text" id="score" name="score" value="<? if ($action == 'new') {  } else { echo $score;} ?>" <? if ($action == 'new') { } else { echo 'disabled="disabled"'; }?> placeholder="1-0" />
                            </div>
             </div>
            
             <div class="control-group">
                            <label for="livescore" class="control-label">Livescore Link:</label>
                            
                            <div class="controls">
                               <input type="text" style="width: 650px;" id="livescore" name="livescore" value="<? if ($action == 'new') {  } else { echo $livescore_link;} ?>" <? if ($action == 'new') { } else { echo 'disabled="disabled"'; }?> placeholder="http://www.livescore.com/soccer/england/premier-league/liverpool-vs-manchester-united/1-1474162/" />
                            </div>
             </div>
<? if ($action == 'new') {  } else {?>  
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
                            <label for="livescore" class="control-label">Event:</label>
                            
                            <div class="controls">
                               
                                <button class="btn btn-warning" type="button" data-toggle="modal" data-target="#myModal">Add Event</button>
                                
                                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                      <div class="modal-dialog">
                                       <div class="modal-content">
                                         <div class="modal-header">
                                             <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                             <h4 id="myModalLabel" class="modal-title" style="font-size:18px; font-weight:bold;">Add event by minute</h4>         
                                         </div>
                                         <div class="modal-body">
                                             <div class="control-group"> 
                                             <label for="minutes_select" class="control-label">Select minute: </label>
                                             <div class="controls">
                                             <select id="minutes_select" name="minutes_select" style="width:50px;">
                                                 
                                                 <? for ($i = 0; $i <= 90; $i++) {
                                                    echo '<option value="'.$i.'">'.$i.'</option>';
                                                } ?>

                                              </select>
                                              </div>
                                            </div> 
                                            
                                            <div class="control-group"> 
                                             <label for="event_types" class="control-label">Event Type: </label>
                                                 <div class="controls">
                                                  <select id="event_types" name="event_types" style="width:70px;">
                                                    <option value="card">Card</option>
                                                    <option value="goal">Goal</option>
                                                  </select>
                                                  </div>
                                             </div> 
                                             
                                             <div id="card_types" class="control-group"> 
                                                 <label for="card_types" class="control-label">Card Type: </label>
                                                 <div class="controls">
                                                 <select name="card_types">
                                                     <option value="yellow">Yellow</option>
                                                     <option value="red">Red</option>
                                                     <option value="yellow_red">Yellow-Red</option>
                                                 </select>
                                                 </div>
                                             </div> 
                                             
                                             <div id="card_owner" class="control-group"> 
                                                 <label for="card_owner" class="control-label">Card Owner: </label>
                                                 <div class="controls">
                                                       <input type="text" value="" name="card_owner">
                                                 </div>
                                             </div>
                                             
                                             <div id="score_step" class="control-group"> 
                                                 <label for="score_step" class="control-label">Score: </label>
                                                 <div class="controls">
                                                    <input type="text" value="" name="score_step">
                                                 </div>
                                             </div>
                                             
                                             <div id="goal_scorer" class="control-group"> 
                                                 <label for="goal_scorer" class="control-label">Goal Scorer: </label>
                                                 <div class="controls">
                                                    <input type="text" value="" name="goal_scorer">
                                                 </div>
                                             </div>
                                             
                                             <div id="assist" class="control-group"> 
                                                 <label for="assist" class="control-label">Assist: </label>
                                                 <div class="controls">
                                                    <input type="text" value="" name="assist">
                                                 </div>
                                             </div>
                                             
                                             <div id="type" class="control-group"> 
                                                 <label for="type" class="control-label">Goal type: </label>
                                                 <div class="controls">
                                                    <input type="text" value="" name="type">
                                                 </div>
                                             </div>
                                             
                                              <div id="team_types" class="control-group"> 
                                                 <label for="team_types" class="control-label">Team: </label>
                                                 <div class="controls">
                                                      <label style="text-align:left;">
                                                        <input type="radio" name="team_types" value="home"> Home
                                                      </label>
                                                      <label style="text-align:left;">
                                                        <input type="radio" name="team_types" value="away"> Away
                                                      </label>
                                                </div>
                                             </div>  
                                             
                                         </div>
                                         <div class="modal-footer">
                                           <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                           <button type="submit" id="submit-score" class="btn btn-primary">Save Event</button>
                                         </div>
                                       </div><!-- /.modal-content -->
                                     </div><!-- /.modal-dialog -->
                                   </div><!-- /.modal -->
                            </div>
             </div>
                            <? } ?> 
                                  
            <div class="control-group">
				<div class="controls">
                                           <? if ($action == 'new') { ?>
                                    <input type="submit" class="btn btn-success btn-large" name="add" value="Add Match" />
                                    <? } else { ?>  
                                    <input type="submit" id="submit" class="btn btn-info btn-large" name="step" value="Add Score" />
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