<?=$this->load->view(branded_view('cp/header'));?>
<h1>Parse Form</h1>

<p>Import event ids from flashscore.</p>

<p><b>To get started, select the HTML file to upload.</b></p>

<form class="form validate" enctype="multipart/form-data" id="form_user" method="post" action="<?=$form_action;?>">

<?= $form ?>

<div class="submit">
	<input type="submit" class="button" name="submit" value="Upload HTML File" />
</div>
</form>

<?=$this->load->view(branded_view('cp/footer'));?>