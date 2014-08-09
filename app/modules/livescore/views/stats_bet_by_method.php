<?=$this->load->view(branded_view('cp/header'));?>
<h1>STATS BY METHOD <?php echo $this->dataset->data['method_name']; ?> AND COUNTRY</h1>

<?=$this->dataset->table_head();?>
<?
if (!empty($this->dataset->data)) {
    
    //echo "<pre>";
    //if(isset($_GET['sort_column'])); if($_GET['sort_column']='total_match' && $_GET['sort_dir']='asc'){sort($this->dataset->data[]['total']);}else{rsort($this->dataset->data[]['total']);}
    //print_r ($this->dataset->data);
    foreach ($this->dataset->data as $key => $row) {        
        if($key == 'country') {
            foreach($row as $country_id => $country) {
                $color = $country['balance'] > 0 ? '#BDEB78' : '#E3B6C1';
                $style = "style = 'background-color:$color;font-weight:bold;'";
            ?>
            <tr>
                <td align="center" <?php echo $style; ?>>COUNTRY</td>
                <?php $country['country'] = str_replace(strip_tags($country['country']), strtoupper(strip_tags($country['country'])), $country['country']); ?>
                <td align="center" <?php echo $style; ?>><?php echo $country['country']; ?></td>            
            </tr>        
            <tr>
                <td align="center">COUNT</td>
                <td align="center"><?php echo $country['count']; ?></td>            
            </tr>
            <tr>
                <td align="center">PROFIT</td>
                <td align="center"><?php echo $country['profit']; ?> &euro;</td>            
            </tr>
            <tr>
                <td align="center">PROFIT COUNT</td>
                <td align="center"><?php echo $country['profit_count']; ?></td>            
            </tr>
            <tr>
                <td align="center">LOSS</td>
                <td align="center"><?php echo $country['loss']; ?> &euro;</td>            
            </tr>
            <tr>
                <td align="center">LOSS COUNT</td>
                <td align="center"><?php echo $country['loss_count']; ?></td>            
            </tr>
            <tr>
                <td align="center" <?php echo $style; ?>>BALANCE</td>
                <td align="center" <?php echo $style; ?>><?php echo $country['balance']; ?> &euro;</td>            
            </tr>
            <tr>
                <td align="center">BALANCE COUNT</td>
                <td align="center"><?php echo $country['balance_count']; ?></td>            
            </tr>
            <tr>
                <td align="center" <?php echo $style; ?>>PERCENTAGE</td>
                <td align="center" <?php echo $style; ?>><?php echo $country['percentage']; ?>%</td>            
            </tr>            
            <?                    
            }            
        }
        else {
           ?>
                <tr>
                    <td align="center"><?=$key;?></td>
                    <td align="center"><?=$row;?></td>            
                </tr>
            <? 
        }
    
    }
    
}
else {
?>
<tr><td colspan="2">Empty data set.</td></tr>
<?
}	
?>
<?=$this->dataset->table_close();?>
<?=$this->load->view(branded_view('cp/footer'));?>