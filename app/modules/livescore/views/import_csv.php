<?=$this->load->view(branded_view('cp/header'));?>
<h1>Import Betz</h1>

<p>Import bets into your list bets by uploading a CSV file from Betfair.</p>

<p><b>To get started, select the CSV file to upload.</b></p>

<form class="form validate" enctype="multipart/form-data" id="form_user" method="post" action="<?=$form_action;?>">

<?= $form ?>

<div class="submit">
	<input type="submit" class="button" name="submit" value="Upload CSV File" />
</div>
</form>

<?=$this->load->view(branded_view('cp/footer'));?>