<?=$this->load->view(branded_view('cp/header'));?>
<h1><?=$form_title;?></h1>
<form class="form validate" enctype="multipart/form-data" id="form_type" method="post" action="<?=$form_action;?>">

<fieldset>
        <ul class="form">
            <?=$form;?>
        </ul>
    </fieldset>
    
    <br /><label class="" for="from" style="width:100%;text-align:left">Existing competitions with countries</label><br />
    <select name="from" id="multiselect" class="form-control" size="12" multiple="multiple" style="width:100%">
        <?php foreach($options as $opt)
        {
            echo '<option value="'.$opt.'">'.$opt.'</option>';
        }
        ?>
    </select>

<div class="submit">
	<? if ($action == 'new') { ?>
	<input type="submit" class="button" name="add" value="Add" />
	<? } else { ?>
	<input type="submit" class="button" name="edit" value="Edit" />
	<? } ?>
</div>
</form>
<?=$this->load->view(branded_view('cp/footer'));?>