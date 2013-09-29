<?=$this->load->view(branded_view('cp/header'));?>


<h1><?=$form_title;?></h1>
	<form class="form-horizontal" name="add_against_view" action="<?=$form_action;?>" method="post">
    <input type="hidden" name="id_against" value="<? if ($action == 'new') {  } else { echo $ID_bet;} ?>" />
            <div class="row-fluid">
            
                <div class="span3">
                
                        
                        <div class="control-group">
                            <label for="favorite_name" class="control-label">Favorite Name:</label>
                            
                            <div class="controls">
                              <input type="text" id="event_name" name="favorite_name" value="<? if ($action == 'new') {  } else { echo $favorite_name;} ?>" placeholder="E.g. Arsenal" />
                            </div>
                        </div>
                        
       
                       <div class="control-group">
                            <label for="favorite_name" class="control-label">Favorite Odds:</label>
                            
                            <div class="controls">
                                <input type="text" id="stake" name="favorite_odds" value="<? if ($action == 'new') {  } else { echo $favorite_odds;} ?>" placeholder="E.g. 1.45" />
                            </div>
                        </div>
                    
                    <hr>
                    
                        <div class="control-group">
                            <label for="draw_name" class="control-label">Draw Odds:</label>
                            
                            <div class="controls">
                                <input type="text" id="stake" name="draw_odds" value="<? if ($action == 'new') {  } else { echo $draw_odds;} ?>" placeholder="E.g. 1.45" />
                            </div>
                        </div>
                    
                    <hr>
                    
                    <div class="control-group">
                            <label for="underdog_name" class="control-label">Underdog Name:</label>
                            
                            <div class="controls">
                              <input type="text" id="event_name" name="underdog_name" value="<? if ($action == 'new') {  } else { echo $underdog_name;} ?>" placeholder="E.g. Chelsea" />
                            </div>
                        </div>
                        
       
                       <div class="control-group">
                            <label for="underdog_odds" class="control-label">Underdog Odds:</label>
                            
                            <div class="controls">
                                <input type="text" id="stake" name="underdog_odds" value="<? if ($action == 'new') {  } else { echo $underdog_odds;} ?>" placeholder="E.g. 1.45" />
                            </div>
                        </div>
              
                </div>
                
                
                
                <div class="span7 offset2">
                    
                     <div class="control-group">
                            <label for="event_name" class="control-label">Zone:</label>
                            
                            <div class="controls">
                                <input type="text" id="event_name" name="zone" value="<? if ($action == 'new') {  } else { echo $zone;} ?>" placeholder="E.g. France" />
                            </div>
                        </div>
                                        
                     <div class="control-group <? if ($action == 'new') {  } else { if($profit != null && $loss == null){echo "success";} else {echo "error";} } ?>" id="profit_loss">
                                <div class="control-label" style="padding-top:0;">
                                	Result:
                                </div>
                            
                                <div class="controls">
                                   <div class="btn-group change-class" data-toggle="buttons-radio">
                                    <button type="button" class="btn" data-toggle="button" class-toggle="btn-danger">1</button>
                                    <button type="button" class="btn" data-toggle="button" class-toggle="btn-info">X</button>
                                    <button type="button" class="btn" data-toggle="button" class-toggle="btn-success">2</button>
                                	</div>
                                </div>
                        </div>
                
                </div>
            </div>
            <hr>
            
            <div class="row">
            	<div class="span4 offset1">
		           <p class="muted">* All fields must be completed for a good statistics results
                </div>

                 <? if ($action == 'new') { ?>
                                    <input type="submit" class="btn btn-success btn-large pull-right" name="add" value="Add New Bet" />
                                    <? } else { ?>
                                    <input type="submit" class="btn btn-info btn-large pull-right" name="edit" value="Edit Bet" />
                                    <? } ?>
            </div>
                
			</form>
		
	

	<!-- JavaScript -->

    <script src="js/bootstrap-datetimepicker.min.js"></script>
	<script src="js/bootstrap.js"></script>

<?=$this->load->view(branded_view('cp/footer'));?>