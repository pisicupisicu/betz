<?=$this->load->view(branded_view('cp/header'));?>
<script>
    jQuery(document).ready(function() {

        $('#submit-score').on('click', function(event) {
            this.submit(); 
        });

        $(document).on("change","#link_user", function() {
            $('#link_complete').val('http://www.livescore.com/soccer/'+$('#link_user').val());     
        });

    });
</script>

<div class="row-fluid">
    <div class="span12">
        <h1><?=$form_title;?></h1>
    
        <form class="form validate form-horizontal" enctype="multipart/form-data" id="add_league_form" method="post" action="<?=$form_action;?>">            
            <div class="control-group">
                <label for="contact-message" class="control-label">Competitions:</label>
                    <div class="controls" id="default_competition">
                        <? echo form_dropdown('competitions',$competitions,'','id="competition_select"'); ?>
                    </div>
            </div>                        

            <div class="control-group">
               <label for="event_date" class="control-label">Match Date:</label>
               <div class="controls">
                   <div id="datetimepicker1" class="input-append date" >
                     <input name="match_date" data-format="yyyy-MM-dd" type="text"></input>                
                    <span class="add-on">
                       <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
                     </span>                 
                   </div>  
               </div>
           </div>

            <div class="control-group">
                <label for="home_team" class="control-label">Home Team:</label>
                <div class="controls" id="homeSelect">
                 <? echo form_dropdown('home_team',$team_name); ?>
                </div>
             </div>

            <div class="control-group">
                <label for="away_team" class="control-label">Away Team:</label>
                <div class="controls" id="awaySelect">
                  <? echo form_dropdown('away_team',$team_name); ?>
                </div>
             </div>

            <div class="control-group">
                <label for="score" class="control-label">Score:</label>
                <div class="controls">
                    <input type="text" id="score" name="score" value="" placeholder="1-0"/>
                </div>
             </div>                         

            <div class="control-group">
               <label for="livescore" class="control-label">Livescore Link:</label>
               <div class="controls">
                  <input type="text" style="width: 650px;" id="link_complete" name="link_complete" class="" value=""  placeholder="http://www.livescore.com/soccer/england/premier-league/liverpool-vs-manchester-united/1-1474162/" />
               </div>
            </div>

            <div class="control-group">
                <div class="controls">
                    <input type="submit" class="btn btn-success btn-large" name="add" value="Add Match" />
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