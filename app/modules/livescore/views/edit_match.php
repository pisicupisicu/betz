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
    

$('#submit').on('click', function(event){
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


$('.save-goal').on('click', function(event){
    var id = $(this).attr('id');
    var aux = id.split('-');
    var i = aux[2];
    
    var minutes_goal = $("#GoalModal"+i+" select[name=minutes_goal]").val();
    var score = $("#GoalModal"+i+" input[name=score]").val();
    var goal_scorer = $("#GoalModal"+i+" input[name=goal_scorer]").val();
    var assist = $("#GoalModal"+i+" input[name=assist]").val();
    var type = $("#GoalModal"+i+" input[name=type]").val();
    var goal_team = $("#GoalModal"+i+" select[name=goal_team]").val();
    var goal_id = $("#GoalModal"+i+" input[name=goal_name]").val();
        
    $.ajax({
            type: 'post',
            url: '/admincp4/livescore/update_goal/'+goal_id,
            data: 'score='+score+"&minutes_goal="+minutes_goal+"&goal_scorer="+goal_scorer+"&assist="+assist+"&type="+type+"&goal_team="+goal_team,
            dataType:'html',
            success: function(data, textStatus, XMLHttpRequest) {
                console.log('succes '+data);
                $("#GoalModal"+i).modal('hide')
                location.reload(true);
             }      
            });
    
    event.preventDefault();
});


$('.save-card').on('click', function(event){
    var id = $(this).attr('id');
    var aux = id.split('-');
    var i = aux[2];
    
    var minutes_card = $("#CardModal"+i+" select[name=minutes_card]").val();
    var card_type = $("#CardModal"+i+" select[name=card_type]").val();
    var card_owner = $("#CardModal"+i+" input[name=card_owner]").val();
    var card_team = $("#CardModal"+i+" select[name=card_team]").val();
    var card_id = $("#CardModal"+i+" input[name=card_name]").val();
        
    $.ajax({
            type: 'post',
            url: '/admincp4/livescore/update_card/'+card_id,
            data: 'minutes_card='+minutes_card+"&card_type="+card_type+"&card_owner="+card_owner+"&card_team="+card_team,
            dataType:'html',
            success: function(data, textStatus, XMLHttpRequest) {
                console.log('succes '+data);
                $("#CardModal"+i).modal('hide')
                location.reload(true);
             }      
            });
    
    event.preventDefault();
});


$(document).on("change","#link_user", function()
{
                $('#link_complete').val('http://www.livescore.com/soccer/'+$('#link_user').val());     
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
                            <label for="link_user" class="control-label">Link:</label>
                            
                            <div class="controls">
                               <input type="text" style="width: 650px;" id="link_user" name="link_user" value="<? if ($action == 'new') {  } else { echo $link;} ?>" placeholder="england/premier-league/liverpool-vs-manchester-united/1-1474162/" />
                            </div>
             </div>
            
             <div class="control-group">
                            <label for="livescore" class="control-label">Livescore Link:</label>
                            
                            <div class="controls">                              
                                <input type="text" style="width: 650px;" id="link_complete" name="link_complete" class="disabledinput" value="<? if ($action == 'new') {  } else { echo $livescore_link;} ?>" />
                            </div>
             </div>
            
            <div class="control-group">
                
                    <table class="table table-striped table-hover" align="center">

                        <thead>
							<th width="160px;"></th>
                            
                            <th width="10%"><h3>Minutes</h3></th>

                            <th width="30%" style="text-align:right;"><h3><?php echo $home['name']; ?></h3></th>

                            <th width="15%" style="text-align:center;"><h3><?php echo $match['score']; ?></h3></th>

                            <th><h3><?php echo $away['name']; ?></h3></th>

                        </thead>

                            <?php
                                $i = 0;
                                foreach($goals as $goal) {					
                                    $i++;
                                    echo '<tr>';

                                    $assist	=	$goal['assist']	?	' (assist '.$goal['assist'].')'	:	'';

                                    $type	=	$goal['type']	?	' '.$goal['type']				:	'';



                                    if($goal['team'] == 'home') {

                                        echo '<td><a class="btn btn-danger" href="/admincp4/livescore/delete_goal/edit/'.$id_match.'/'.$goal['id'].'" >Delete</a> <button class="btn btn-info" data-toggle="modal" data-target="#GoalModal'.$i.'">Edit</button></td><td>'.$goal['min'].'\'</td><td style="text-align:right;">'.$goal['player'].$type.$assist.' <img src="'.site_url('/app/modules/livescore/assets/ball.png').'"/>'.'</td><td style="text-align:center;">'.$goal['score'].'</td>';

                                        echo '<td>&nbsp;</td>';						

                                    } else {

                                        echo '<td><a class="btn btn-danger" href="/admincp4/livescore/delete_goal/edit/'.$id_match.'/'.$goal['id'].'" >Delete</a> <button class="btn btn-info" data-toggle="modal" data-target="#GoalModal'.$i.'">Edit</button></td><td>'.$goal['min'].'\'</td><td>&nbsp;</td><td style="text-align:center;">'.$goal['score'].'</td>';

                                        echo '<td>'.'<img src="'.site_url('/app/modules/livescore/assets/ball.png').'"/> '.$goal['player'].$type.$assist.'</td>';												

                                    }

                                    echo '</tr>'; ?>
                                    
                                    <div class="modal fade" id="GoalModal<?=$i?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                      <div class="modal-dialog">
                                       <div class="modal-content">
                                         <div class="modal-header">
                                             <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                             <h4 id="myModalLabel" class="modal-title" style="font-size:18px; font-weight:bold;">Edit Goal</h4>         
                                         </div>
                                         <div class="modal-body">
                                             <div class="control-group"> 
                                             <label for="minutes_select" class="control-label">Select minute: </label>
                                             <div class="controls">
                                                 
                                                 <? echo form_dropdown('minutes_goal',$minutes,$goal['min']); ?>

                                              </div>
                                            </div> 
                                            
                                         <div id="score_step" class="control-group"> 
                                                 <label for="score_step" class="control-label">Score: </label>
                                                 <div class="controls">
                                                    <input type="text" value="<?=$goal['score']?>" name="score">
                                                 </div>
                                             </div>
                                             
                                             <div id="goal_scorer" class="control-group"> 
                                                 <label for="goal_scorer" class="control-label">Goal Scorer: </label>
                                                 <div class="controls">
                                                    <input type="text" value="<?=$goal['player']?>" name="goal_scorer">
                                                 </div>
                                             </div>
                                             
                                             <div id="assist" class="control-group"> 
                                                 <label for="assist" class="control-label">Assist: </label>
                                                 <div class="controls">
                                                    <input type="text" value="<?=$assist?>" name="assist">
                                                 </div>
                                             </div>
                                             
                                             <div id="type" class="control-group"> 
                                                 <label for="type" class="control-label">Goal type: </label>
                                                 <div class="controls">
                                                    <input type="text" value="<?=$type?>" name="type">
                                                 </div>
                                             </div>
                                             
                                              <div id="team_types" class="control-group"> 
                                                 <label for="team_types" class="control-label">Team: </label>
                                                 <div class="controls">
                                                      <? echo form_dropdown('goal_team',$team_type,$goal['team']);?>
                                                </div>
                                             </div>  
                                             <input type="hidden" value="<?=$goal['id']?>" name="goal_name">
                                         </div>
                                         <div class="modal-footer">
                                           <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                           <button type="submit" id="submit-goal-<?=$i?>" class="btn btn-primary save-goal">Save Goal</button>
                                         </div>
                                       </div><!-- /.modal-content -->
                                     </div><!-- /.modal-dialog -->
                                   </div><!-- /.modal -->                            
                                    
<?
                                }



                                foreach($cards as $card) {
                                    $i++;
                                    if($card['card_type'])				

                                    echo '<tr>';

                                    if($card['team'] == 'home') {

                                        echo '<td><a class="btn btn-danger" href="/admincp4/livescore/delete_card/edit/'.$id_match.'/'.$card['id'].'" >Delete</a> <button class="btn btn-info" data-toggle="modal" data-target="#CardModal'.$i.'">Edit</button></td><td>'.$card['min'].'\'</td><td style="text-align:right">'.$card['player'].' <img src="'.site_url('/app/modules/livescore/assets/'.$card['card_type'].'.png').'"/>'.'</td><td>&nbsp;</td>';

                                        echo '<td>&nbsp;</td>';						

                                    } else {

                                        echo '<td><a class="btn btn-danger" href="/admincp4/livescore/delete_card/edit/'.$id_match.'/'.$card['id'].'" >Delete</a> <button class="btn btn-info" data-toggle="modal" data-target="#CardModal'.$i.'">Edit</button></td><td>'.$card['min'].'\'</td><td>&nbsp;</td><td>&nbsp;</td>';

                                        echo '<td>'.'<img src="'.site_url('/app/modules/livescore/assets/'.$card['card_type'].'.png').'"/> '.$card['player'].'</td>';												

                                    }

                                    echo '</tr>';?>
                                    <div class="modal fade" id="CardModal<?=$i?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                      <div class="modal-dialog">
                                       <div class="modal-content">
                                         <div class="modal-header">
                                             <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                             <h4 id="myModalLabel" class="modal-title" style="font-size:18px; font-weight:bold;">Edit Card</h4>         
                                         </div>
                                         <div class="modal-body">
                                             <div class="control-group"> 
                                             <label for="minutes_select" class="control-label">Select minute: </label>
                                             <div class="controls">
                                                 
											   <? echo form_dropdown('minutes_card',$minutes,$card['min']); ?>

                                              </div>
                                            </div> 
                                             
                                             <div id="card_types" class="control-group"> 
                                                 <label for="card_types" class="control-label">Card Type: </label>
                                                 <div class="controls">
                                                 <? echo form_dropdown('card_type',$card_type,$card['card_type']);?>
                                                 </div>
                                             </div> 
                                             
                                             <div id="card_owner" class="control-group"> 
                                                 <label for="card_owner" class="control-label">Card Owner: </label>
                                                 <div class="controls">
                                                       <input type="text" value="<?=$card['player']?>" name="card_owner">
                                                 </div>
                                             </div>
                                            
                                              <div id="team_types" class="control-group"> 
                                                 <label for="team_types" class="control-label">Team: </label>
                                                 <div class="controls">
                                                      <? echo form_dropdown('card_team',$team_type,$card['team']);?>
                                                </div>
                                             </div>  
                                             <input type="hidden" value="<?=$card['id']?>" name="card_name">
                                         </div>
                                         <div class="modal-footer">
                                           <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                           <button type="submit" id="submit-card-<?=$i?>" class="btn btn-primary save-card">Save Card</button>
                                         </div>
                                       </div><!-- /.modal-content -->
                                     </div><!-- /.modal-dialog -->
                                   </div><!-- /.modal -->
							<?
                                }

                            ?>

                    </table>

             </div>
            <div class="control-group">
                <div class="controls">
                <a href="<? echo site_url('admincp4/livescore/step_two').'/'.$id_match; ?>" class="btn btn-success">Add New Event </a>
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