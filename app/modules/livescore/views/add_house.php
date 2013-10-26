<?=$this->load->view(branded_view('cp/header'));?>

<div class="row-fluid">
	<div class="span12">
	<h1><? if ($action == 'new') { ?>
        <?=$form_title;?>
        <? } else { ?>
        <?=$form_title;?> <span class="muted"> ID:<?=$id_house;?></span>
        <? } ?>
    </h1>
    
    <form class="form validate form-horizontal" enctype="multipart/form-data" id="add_league_form" method="post" action="<?=$form_action;?>">
        <input type="hidden" name="id_house" value="<? if ($action == 'new') {  } else { echo $id_house;} ?>" />
        
        <div class="control-group">
            <label class="control-label">Logo</label>
            <div class="controls"><input type="text" name="logo_house" id="logo_house" value="<? if ($action == 'new') {  } else { echo $logo_house;} ?>" placeholder="E.g. bet365.png" /></div>			
	    </div>
        
        <div class="control-group">
            <label class="control-label">Bookmaker Name</label>
            <div class="controls"><input type="text" name="name_house" id="name_house" value="<? if ($action == 'new') {  } else { echo $name_house;} ?>" placeholder="E.g. Bet365" /></div>			
	    </div>
        
        <div class="control-group">
            <label class="control-label">Link</label>
            <div class="controls"><input type="text" name="link_house" id="link_house" value="<? if ($action == 'new') {  } else { echo $link_house;} ?>" placeholder="E.g. www.bet365.com" /></div>			
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