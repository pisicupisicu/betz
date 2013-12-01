<?=$this->load->view(branded_view('cp/header'));?>
<h1>Over country stats</h1>
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
    
    //echo "<pre>";
    //if(isset($_GET['sort_column'])); if($_GET['sort_column']='total_match' && $_GET['sort_dir']='asc'){sort($this->dataset->data[]['total']);}else{rsort($this->dataset->data[]['total']);}
    //print_r ($this->dataset->data);
    
	foreach ($this->dataset->data as $key=>$row) {

	?>
		<tr>
			<td><?=$row['country_name'];?></td>
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