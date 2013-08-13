<?=$this->load->view(branded_view('cp/header'));?>

<div class="row-fluid">
	<div class="span12">
	<h1><? if ($action == 'new') { ?>
                                    <?=$form_title;?>
                                    <? } else { ?>
                                    <?=$form_title;?> <span class="muted"> ID:<?=$ID_method;?></span>
                                    <? } ?>
        </h1>
    
    <form class="form validate form-horizontal" enctype="multipart/form-data" id="add_method_form" method="post" action="<?=$form_action;?>">
            <input type="hidden" name="ID_method" value="<? if ($action == 'new') {  } else { echo $ID_method;} ?>" />
            <div class="control-group">
		<label class="control-label">Method Name</label>
		<div class="controls"><input type="text" name="method_name" id="last_name" value="<? if ($action == 'new') {  } else { echo $method_name;} ?>" placeholder="Mathod Name" /></div>			
	    </div>
            
      
            <div class="control-group">
		<label for="contact-message" class="control-label">Method Details:</label>	
		<div class="controls">
			<textarea name="method_description" id="method_description" style="width:100%;" cols="30" rows="20"><? if ($action == 'new') {  } else { echo $method_description;} ?></textarea>
		</div>
	   </div>
           
          
            
            <div class="control-group">
		<div class="controls">
                                           <? if ($action == 'new') { ?>
                                    <input type="submit" class="button" name="add" value="Add" />
                                    <? } else { ?>
                                    <input type="submit" class="button" name="edit" value="Edit" />
                                    <? } ?>
                </div>
            </div>

	  
    </form>
    </div>
</div>
  
<?=$this->load->view(branded_view('cp/footer'));?>