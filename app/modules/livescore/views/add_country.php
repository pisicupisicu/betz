<?=$this->load->view(branded_view('cp/header'));?>

<div class="row-fluid">
	<div class="span12">
	<h1><? if ($action == 'new') { ?>
                                    <?=$form_title;?>
                                    <? } else { ?>
                                    <?=$form_title;?> <span class="muted"> ID:<?=$ID_country;?></span>
                                    <? } ?>
        </h1>
    
    <form class="form validate form-horizontal" enctype="multipart/form-data" id="add_country_form" method="post" action="<?=$form_action;?>">
            <input type="hidden" name="ID_country" value="<? if ($action == 'new') {  } else { echo $ID_country;} ?>" />
            <div class="control-group">
		<label class="control-label">Country Name</label>
		<div class="controls"><input type="text" name="country_name" id="country_name" style="width:50%;" value="<? if ($action == 'new') {  } else { echo $country_name;} ?>" placeholder="Country Name" /></div>			
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