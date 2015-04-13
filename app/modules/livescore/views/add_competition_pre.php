<?=$this->load->view(branded_view('cp/header'));?>
<script>
jQuery(document).ready(function() {
		
    $('[name=country_id_before]').on('change', function(event){
        
       var countryID = $("[name=country_id_before]").val();       
       var competitionString = "/admincp4/livescore/view_competitions_selects/"+ countryID;        

        $.get(competitionString, function(data) {
            $("[name=competitions_before]").html(data);
        });       
                               
    });

    $(document).on("change","#link", function() {
        $('#link_complete').val('http://www.livescore.com/soccer/'+$('#link').val());     
    }); 

});    
    
</script>
<h1><?=$form_title;?></h1>
<form class="form validate" enctype="multipart/form-data" id="form_type" method="post" action="<?=$form_action;?>">

<fieldset>
        <ul class="form">
            <?=$form;?>
        </ul>
    </fieldset>

<div class="submit">
	<? if ($action == 'new') { ?>
	<input type="submit" class="button" name="add" value="Add" />
	<? } else { ?>
	<input type="submit" class="button" name="edit" value="Edit" />
	<? } ?>
</div>
</form>
<?=$this->load->view(branded_view('cp/footer'));?>
