<?=$this->load->view(branded_view('cp/header'));?>

<div class="row-fluid">
	<div class="span12">
	<h1><? if ($action == 'new') { ?>
        <?=$form_title;?>
        <? } else { ?>
        <?=$form_title;?> <span class="muted"> ID:<?=$id_currency;?></span>
        <? } ?>
    </h1>
    
    <form class="form validate form-horizontal" enctype="multipart/form-data" id="add_league_form" method="post" action="<?=$form_action;?>">
        <input type="hidden" name="id_currency" value="<? if ($action == 'new') {  } else { echo $id_currency;} ?>" />
        <div class="control-group">
                            <label class="control-label">Country:</label>             
                            <div class="controls">
                              <? if ($action == 'new') { echo form_dropdown('id_country',$id_country,'','id="country_select"'); } else { echo form_dropdown('id_country',$id_country,$country_name);} ?>
                            </div>
        </div>
        
        <div class="control-group">
            <label class="control-label">Flag</label>
            <div class="controls"><input type="text" name="flag" id="last_name" value="<? if ($action == 'new') {  } else { echo $flag;} ?>" placeholder="romania.png" /></div>			
	    </div>
        
        <div class="control-group">
            <label class="control-label">Currency Name</label>
            <div class="controls"><input type="text" name="name_currency" id="last_name" value="<? if ($action == 'new') {  } else { echo $name_currency;} ?>" placeholder="E.g. Dollar" /></div>			
	    </div>
        
        <div class="control-group">
            <label class="control-label">Code ISO</label>
            <div class="controls"><input type="text" name="code_ISO" id="last_name" value="<? if ($action == 'new') {  } else { echo $code_ISO;} ?>" placeholder="E.g. USD" /></div>			
	    </div>
        
        <div class="control-group">
            <label class="control-label">Currency Symbol</label>
            <div class="controls"><input type="text" name="symbol_currency" id="last_name" value="<? if ($action == 'new') {  } else { echo $symbol_currency;} ?>" placeholder="E.g. $" /></div>			
	    </div>
                     
        <div class="control-group">
		     <div class="controls">
                    <? if ($action == 'new') { ?>
                    <input type="submit" class="btn btn-success" name="add" value="Add" />
                    <? } else { ?>
                    <input type="submit" class="btn btn-info" name="edit" value="Edit" />
                    <? } ?>
             </div>
        </div>

	  
    </form>
    </div>
</div>
  
<?=$this->load->view(branded_view('cp/footer'));?>