<?=$this->load->view(branded_view('cp/header'));?>

<h1>MATCHES flashscore</h1>

<div style="width:auto;text-align:center;" align="center">

<?=$this->dataset->table_head();?>

<?		

if (!empty($this->dataset->data)) {

    $path = site_url().'app/modules/flashscore/assets/';

    foreach ($this->dataset->data as $row) {
        
    //print_r ($row);
    //die;
    //$score = explode('-',$row['score']); 
    //$row['score'] = $score[0].' - '.$score[1];                    						
?>

            <tr>							

    <td><input type="checkbox" name="check_<?=$row['ID'];?>" value="1" class="action_items" /></td>
    <td align="center"><b><?=$row['country_name'];?></b></td>
    <td align="center"><b><?=$row['competition_name'];?></b></td>
    <td align="center"><b><?=$row['event_date'];?></b></td>
    <td align="center"><b><?=$row['team1'];?></b></td>
    <td align="center"><b><?=$row['team2'];?></b></td>
    <td align="center"><b><?=$row['score'];?></b></td>                
    <td align="center"><b><a href="http://www.flashscore.com/match/<?=$row['event_id'];?>/#match-summary" target="_blank">http://www.flashscore.com/match/<?=$row['event_id'];?>/#match-summary</a></b></td>
    <td align="center" class="options"><b><a href="/admincp/flashscore/view_match/<?=$row['ID'];?>" target="_blank" >view</a></b></td>
    <td align="center" class="options"><b><a href="/admincp/flashscore/edit_match/<?=$row['ID'];?>">edit</a></b></td>
            </tr>

    <?

}

}

else {

?>

<tr>

        <td colspan="10">No matches.</td>

</tr>

<? } ?>                        

<?=$this->dataset->table_close();?>

</div>

<?=$this->load->view(branded_view('cp/footer'));?>



