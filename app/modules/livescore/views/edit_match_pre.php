<?=$this->load->view(branded_view('cp/header'));?>
<script>
jQuery(document).ready(function(){
    $('#submit').on('click', function(event){
       if(!confirm('This will change you match values!Proceed?')) {
            event.preventDefault();
        }
    });    
    
    $(document).on("change","#link_user", function()
    {
        $('#link_complete').val('http://www.livescore.com/soccer/'+$('#link_user').val());     
    }); 
});

</script>
<div class="row-fluid">
    <div class="span12">
        <h1>
            <?=$form_title;?> <span class="muted"> ID:<?=$id_match;?></span> 
            <span style="float:right;">In case your competition is not listed: <a href="/admincp3/livescore/add_competition_pre" style="color:red;font-weight:bold;" target="_blank">Add competition pre</a></span>
        </h1>        
        <form class="form validate form-horizontal" enctype="multipart/form-data" id="add_league_form" method="post" action="<?=$form_action;?>">
            <input type="hidden" name="ID_match" value="<? echo $id_match; ?>" />
            <div class="control-group">
                <label for="contact-message" class="control-label">Country:</label>
                <div class="controls" id="default_country">
                    <? echo form_dropdown('country_name',$country_name,$id_country,'id="country_select"'); ?>
                </div>
            </div>
            <div class="control-group">                
                <label for="competition_name" class="control-label">Competition:</label>
                <div class="controls" id="competitionSelect">
                    <?php echo form_dropdown('competition_name', $competition_name, $competition_id_pre); ?>
                </div>
             </div>
            <div class="control-group">
                <label for="event_date" class="control-label">Match Date:</label>
                <div class="controls">
                    <div id="datetimepicker1" class="input-append date">
                        <input name="match_date" value="<? echo $match_date; ?>" data-format="yyyy-MM-dd" type="text"></input>
                        <span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>
                    </div>
                </div>
            </div>
            <div class="control-group">
                <label for="home_team" class="control-label">Home Team:</label>
                <div class="controls" id="homeSelect">
                    <? echo form_dropdown('home_team',$team_name,$home_team_id); ?>
                </div>
            </div>
            <div class="control-group">
                <label for="away_team" class="control-label">Away Team:</label>

                <div class="controls" id="awaySelect">
                    <? echo form_dropdown('away_team',$team_name,$away_team_id); ?>
                </div>
            </div>
            <div class="control-group">
                <label for="score" class="control-label">Score:</label>
                <div class="controls">
                    <input type="text" id="score" name="score" value="<? echo $score; ?>" placeholder="1-0" />
                </div>
            </div>
            <div class="control-group">
                <label for="livescore" class="control-label">Livescore Link:</label>
                <div class="controls">                              
                    <input type="text" style="width: 650px;" id="link_complete" name="link_complete" value="<? echo $livescore_link; ?>" />
                </div>
            </div>
            <div class="control-group">
                <table class="table table-striped table-hover" align="center">
                    <thead>
                        <th width="160px;"></th>
                        <th width="30%" style="text-align:right;"><h3><?php echo $home['name']; ?></h3></th>
                        <th width="15%" style="text-align:center;"><h3><?php echo $match['score']; ?></h3></th>
                        <th><h3><?php echo $away['name']; ?></h3></th>
                    </thead>
                </table>
            </div>
            <div class="control-group">
                <div class="controls">                     
                     <input type="submit" id="submit" class="btn btn-info btn-large" name="edit" value="Edit" />
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