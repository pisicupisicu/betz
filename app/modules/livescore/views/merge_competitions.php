<?=$this->load->view(branded_view('cp/header'));?>
<h1>Merge competitions</h1>

<p>Merge small competitions into big ones</p>

<form class="form validate" enctype="multipart/form-data" id="form_user" method="post" action="<?=$form_action;?>">
<fieldset>
    <legend>Competitions Information</legend>
    <ul class="form">
        <li id="row_">
            <label class="full" for="competition_name[]">Select competition</label>
        </li>
        <li>
            <select name="competition_name[]" class="required full"  multiple="multiple">
                <option value="0">-</option>
                <option value="1">-</option>
            </select>
        </li>
    </ul>
</fieldset>
    <!--<div class="submit">
            <input type="submit" class="button" name="submit" value="Merge teams" />
    </div>-->
</form>

<?=$this->load->view(branded_view('cp/footer'));?>

