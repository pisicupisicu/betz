<?=$this->load->view(branded_view('cp/header'));?>
<script>
jQuery(document).ready(function(){
		
    $('#country_select').on('change', function(event){
        
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
                               
 });   
    

$('#submit-score').on('click', function(event){ 
 
      this.submit();
 
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
                            <label for="contact-message" class="control-label">Competitions:</label>
                            
                            <div class="controls" id="default_competition">
                              <? if ($action == 'new') { echo form_dropdown('competitions',$competitions,'','id="competition_select"'); } else { echo form_dropdown('competitions',$competitions,$id_competition,'id="competition_select" disabled="disabled"');} ?>
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
                   <input type="text" style="width: 650px;" id="link_complete" name="link_complete" class="" value="<? if ($action == 'new') {  } else { echo $livescore_link;} ?>"  placeholder="http://www.livescore.com/soccer/england/premier-league/liverpool-vs-manchester-united/1-1474162/" />
                </div>
             </div>
<? if ($action == 'new') {  } else {?>                                      
                            <? } ?> 
                                  
            <div class="control-group">
				<div class="controls">
                <? if ($action == 'new') { ?>
                    <input type="submit" class="btn btn-success btn-large" name="add" value="Add Match" />
                    <? } else { ?>  
                    <a class="btn btn-info btn-large offset4" href="/admincp/livescore/list_matches">Finalize</a>
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