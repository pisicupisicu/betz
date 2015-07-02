<?=$this->load->view(branded_view('cp/header'));?>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#multiselect').multiselect();
        $('#country_select').on('change', function(event){
            var countryID = $("#country_select").val();
            var competitionString = "/admincp5/livescore/view_custom_competitions_selects/"+ countryID;

            $.get(competitionString, function(data) {
                $("#competitionSelect").html(data);
            });
        });
    });
</script>

<h1>Merge competitions</h1>
<p>Merge small competitions into big ones</p>

<form class="form" enctype="multipart/form-data" id="form_type" method="post" action="<?=$form_action;?>">
    
    <table style="width:100%">
        <tr>
            <td style="width:40%">
                <select name="from" id="multiselect" class="form-control" size="8" multiple="multiple" style="width:100%">
                        <?php foreach($options as $opt)
                        {
                            echo '<option value="'.$opt.'">'.$opt.'</option>';
                        }
                        ?>
                </select>
            </td>
            <td style="width:20%">
                <button type="button" id="multiselect_rightAll" class="btn btn-block"><i class="fa fa-angle-double-right fa-lg"></i></button>
                <button type="button" id="multiselect_rightSelected" class="btn btn-block"><i class="fa fa-angle-right fa-lg"></i></button>
                <button type="button" id="multiselect_leftSelected" class="btn btn-block"><i class="fa fa-angle-left fa-lg"></i></button>
                <button type="button" id="multiselect_leftAll" class="btn btn-block"><i class="fa fa-angle-double-left fa-lg"></i></button>
            </td>
            <td style="width:40%">
                <select name="to[]" id="multiselect_to" class="form-control" size="8" multiple="multiple" style="width:100%"></select>
            </td>
        </tr>
    </table>
    
    <div align="center">
        <table>
            <tr>
                <td>
                    <select name="country_name" id="country_select" class="form-control" >
                        <?php foreach($countries as $key => $value)
                        {
                            echo '<option value="'.$key.'">'.$value.'</option>';
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <select name="competition_name" id="competitionSelect" onchange="this.form.custom_competition.value=this.value">
                        <option value="0">Select Competition</option>
                    </select>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="submit">
            <input type="submit" class="button" name="submit" value="Merge competitions" />
    </div>
    <input type="hidden" name="custom_competition" id="custom_competition_id" value="" />
</form>
<?=$this->load->view(branded_view('cp/footer'));?>

