<?=$this->load->view(branded_view('cp/header'));?>

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