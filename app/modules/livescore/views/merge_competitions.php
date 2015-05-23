<?=$this->load->view(branded_view('cp/header'));?>
<h1>Merge competitions</h1>

<p>Merge small competitions into big ones</p>

<form class="form validate" enctype="multipart/form-data" id="form_user" method="post" action="<?=$form_action;?>">
    <div class="row">
        <div class="col-lg-5">
                <select name="from" id="multiselect" class="form-control" size="8" multiple="multiple">
                        <?php foreach($options as $opt)
                        {
                            echo '<option value="'.$opt.'">'.$opt.'</option>';
                        }
?>
                </select>
        </div>

        <div class="col-lg-2">
                <button type="button" id="multiselect_rightAll" class="btn btn-block"><i class="glyphicon glyphicon-forward"></i></button>
                <button type="button" id="multiselect_rightSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
                <button type="button" id="multiselect_leftSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
                <button type="button" id="multiselect_leftAll" class="btn btn-block"><i class="glyphicon glyphicon-backward"></i></button>
        </div>

        <div class="col-lg-5">
                <select name="to" id="multiselect_to" class="form-control" size="8" multiple="multiple"></select>
        </div>
    </div>
    <!--<div class="submit">
            <input type="submit" class="button" name="submit" value="Merge teams" />
    </div>-->
</form>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#multiselect').multiselect();
    });
</script>

<?=$this->load->view(branded_view('cp/footer'));?>

