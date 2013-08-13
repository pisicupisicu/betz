<?=$this->load->view(branded_view('cp/header'));?>

<script>
function displayVals() 
{
	var methodID = $("#strategy_select").val();
	var queryString = "/admincp2/livescore/view_method_description/"+ methodID;
	
	$.get(queryString, function(data) {
	$("#ajaxDiv").html(data);
	});
}

function MarketsSelects() 
{
	var marketsID = $("#market_selects").val();
	var queryString = "/admincp2/livescore/view_markets_selects/"+ marketsID;
	
	$.get(queryString, function(data) {
	$("#marketsDiv").html(data);
	});
}
</script>

<h1><?=$form_title;?></h1>
	<form class="form-horizontal" name="add_bet_form" action="<?=$form_action;?>" method="post">
    <input type="hidden" name="ID_bet" value="<? if ($action == 'new') {  } else { echo $ID_bet;} ?>" />
            <div class="row-fluid">
            
                <div class="span3">
                
                         <div class="control-group">
                            <label for="event_name" class="control-label">Event Name:</label>
                            
                            <div class="controls">
                                <input type="text" id="event_name" name="event_name" value="<? if ($action == 'new') {  } else { echo $event_name;} ?>" placeholder="E.g. Chelsea - Arsenal" />
                            </div>
                        </div>
                        
                        
                         <div class="control-group">
                            <label for="event_date" class="control-label">Event Date:</label>
                            
                            <div class="controls">

                                          <div id="datetimepicker1" class="input-append date">
                                            <input name="event_date" value="<? if ($action == 'new') {  } else { echo $event_date;} ?>" data-format="yyyy-MM-dd hh:mm:ss" type="text"></input>
                                            <span class="add-on">
                                              <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                                              </i>
                                            </span>
                                          </div>
   
                            </div>
                        </div>
        
        
                         <div class="control-group">
                            <label for="stake" class="control-label">Stake:</label>
                            
                            <div class="controls">
                                <input type="text" id="stake" name="stake" value="<? if ($action == 'new') {  } else { echo $stake;} ?>" placeholder="E.g. 15 Euro" />
                            </div>
                        </div>
                        
                        
                         <div class="control-group <? if ($action == 'new') {  } else { if($profit != null && $loss == null){echo "success";} else {echo "error";} } ?>" id="profit_loss">
                                <div class="control-label" style="padding-top:0;">
                                	<div class="btn-group change-class" data-toggle="buttons-radio">
                                    <button type="button" class="btn" id="success" data-toggle="button" class-toggle="btn-success">Profit</button>
                                    <button type="button" class="btn" id="fail" data-toggle="button" class-toggle="btn-danger">Loss</button>
                                	</div>
                                </div>
                            
                                <div class="controls">
                                    <input type="text" id="input_profit_loss" class="" <? if ($action == 'new') {  } else { if($profit != null && $loss == null){echo 'value='.$profit.' name="profit"';} else {echo 'value='.$loss.' name="loss"';} }?>  placeholder="E.g. 1 Euro" />
                                </div>
                        </div>
                        
                        
                         <div class="control-group">
                            <label for="contact-message" class="control-label">Country:</label>
                            
                            <div class="controls">
                               <? if ($action == 'new') { echo form_dropdown('country_name',$country_name); } else { echo form_dropdown('country_name',$country_name,$id_country);} ?>
                            </div>
                        </div>
                        
                        
                         <div class="control-group">
                            <label for="contact-message" class="control-label">Event Type:</label>
                            
                            <div class="controls">
                                 <? echo form_dropdown('event_type',$event); ?>
                            </div>
                        </div>
              
              			
                         <div class="control-group">
                               	<label for="bet_type" class="control-label">Bet Type:</label>
                            
                                <div class="controls">
                                    
                                    <div class="btn-group" data-toggle="buttons-radio">
                                      <input type="hidden" name="bet_type" value="<? if ($action == 'new') {  } else { if ($bet_type == 'Lay'){echo "Lay";} else{echo "Back";}} ?>" id="btn-input" />
                                      <button type="button" id="back_btn" class="btn <? if ($action == 'new') {  } else { if ($bet_type == 'Back'){echo "btn-info active";} else{}} ?>" value="back" data-toggle="button" class-toggle="btn-info">Back</button>
                                      <button type="button" id="lay_btn" class="btn <? if ($action == 'new') {  } else { if ($bet_type == 'Lay'){echo "btn-danger active";} else{}} ?>" value="lay" data-toggle="button" class-toggle="btn-danger">Lay</button>
                                    </div>
         
                                </div>
                        </div>
                        
                        
                         <div class="control-group">
                            <label for="odds" class="control-label">Odds:</label>
                            
                            <div class="controls">
                                <input type="text" id="odds" name="odds" value="<? if ($action == 'new') {  } else { echo $odds;} ?>" placeholder="E.g. 1.45" />
                            </div>
                        </div>
                        
                         
                         <div class="control-group">
                            <label for="market_type" class="control-label">Market Type:</label>
                            
                            <div class="controls">
                            <? if ($action == 'new') { 
								echo form_dropdown('market_type',$market,'','id="market_selects" onChange="MarketsSelects();"');
								echo "<div id='marketsDiv'></div>";
								} 
								else { echo form_dropdown('market_type',$market,$market_id);} ?>
                            </div>
                        </div>
                        
                      
                         <div class="control-group">
                            <label for="comment" class="control-label">Comment:</label>
                            
                            <div class="controls">
                                <textarea id="comment" name="comment" cols="30" rows="3"><? if ($action == 'new') {  } else { echo $comment;} ?></textarea>
                            </div>
						</div>
              
                </div>
                
                
                
                <div class="span7 offset2">
                    <? if ($action == 'new') {echo form_dropdown('strategy',$strategy,'','id="strategy_select" onChange="displayVals();" style="width:350px;"'); } else { echo form_dropdown('strategy',$strategy,$strategy_id,'id="strategy_select" onChange="displayVals();" style="width:350px;"');}?>
                    <hr>
                    <div class="well" style="height:420px; overflow-y:scroll; overflow-x:hidden;">
                    
                   				 <div id='ajaxDiv'>No strategy just luck. This is bad for your bank!</div>
                                 
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
     <script type="text/javascript">
             $('#fail').on('click', function(e){

			 $('#profit_loss')
			 .removeClass('success')
			 .addClass('error');
			 
			 $("input:text[id='input_profit_loss']").attr('name', 'loss');
		
			});  
			 
			 $('#success').on('click', function(e){

			 $('#profit_loss')
			 .removeClass('error')
			 .addClass('success');
			 
			$("input:text[id='input_profit_loss']").attr('name', 'profit');
			 
			}); 
			 
			 // Back Lay buttons submit values in a hidden input
			 var btns = ['back_btn', 'lay_btn'];
			  var input = document.getElementById('btn-input');
			  for(var i = 0; i < btns.length; i++) {
				document.getElementById(btns[i]).addEventListener('click', function() {
				  input.value = this.value;
				});
			  } 
     </script>
                                        
	 <script type="text/javascript">
      $(function() {
        $('#datetimepicker1').datetimepicker({
          collapse: true
        });
      });
    </script>
    
    <script>
		$('.btn-group > .btn, .btn[data-toggle="button"]').click(function() {
var buttonClasses = ['btn-primary','btn-danger','btn-warning','btn-success','btn-info','btn-inverse'];
var $this = $(this);
   
    if ($(this).attr('class-toggle') != undefined && !$(this).hasClass('disabled')) {
        
        var btnGroup = $this.parent('.btn-group');
        var btnToggleClass = $this.attr('class-toggle');
        var btnCurrentClass = $this.hasAnyClass(buttonClasses);
        
        
        if (btnGroup.attr('data-toggle') == 'buttons-radio') {
                if($this.hasClass('active')) {
                    return false;
                }
            var activeButton = btnGroup.find('.btn.active');
            var activeBtnClass = activeButton.hasAnyClass(buttonClasses);
            
            activeButton.removeClass(activeBtnClass).addClass(activeButton.attr('class-toggle')).attr('class-toggle',activeBtnClass);
	   }
 
            $this.removeClass(btnCurrentClass).addClass(btnToggleClass).attr('class-toggle',btnCurrentClass);

    }

});    

$.fn.hasAnyClass = function(classesToCheck) {
        for (var i = 0; i < classesToCheck.length; i++) {
            if (this.hasClass(classesToCheck[i])) {
                return classesToCheck[i];
            }
        }
        return false;
    }
    </script>
<?=$this->load->view(branded_view('cp/footer'));?>