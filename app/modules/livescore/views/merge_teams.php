<?=$this->load->view(branded_view('cp/header'));?>
<h1>Merge teams</h1>

<p>Change different matches, delete same matches.</p>

<form class="form validate" enctype="multipart/form-data" id="form_user" method="post" action="<?=$form_action;?>">

<?= $form ?>

<div class="submit">
	<input type="submit" class="button" name="submit" value="Merge teams" />
</div>
</form>

<?=$this->load->view(branded_view('cp/footer'));?>

