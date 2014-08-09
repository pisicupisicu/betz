<?=$this->load->view(branded_view('cp/header'));?>
<h1><?=urldecode($country)?>: OVER by competions</h1>
<h2><a href="<?=site_url('admincp6/livescore/over_country')?>">Back to country list</a></h2>
<form class="form-horizontal" style="display:inline-block;">

            <select name="setter">  
                <option value="1.5">1.5</option>
                <option value="2.5" selected>2.5</option>
                <option value="3.5">3.5</option>
                <option value="4.5">4.5</option>
            </select>
            <button type="submit" class="btn btn-success">Go</button>
        
</form>
<?=$this->dataset->table_head();?>
<?
if (!empty($this->dataset->data)) {
    
	foreach ($this->dataset->data as $key=>$row) {

	?>
		<tr>
                    <td style="font-weight: bold;"><?=$row['country_name'];?></td>
                    <td><?=urldecode($country)?></td>
                    <td>OVER <?=$setter?></td>
		    <td align="center"><?=$row['total'];?></td>
            <td <?php if($row['percent_over'] > 70) echo 'style="color:red; font-weight:bold;"'; ?> ><?=$row['percent_over'];?>%</td>     
        </tr>
	<?
	}
    
}
else {
?>
<tr><td colspan="8">Empty data set.</td></tr>
<?
}	
?>
<?=$this->dataset->table_close();?>
<?=$this->load->view(branded_view('cp/footer'));?>