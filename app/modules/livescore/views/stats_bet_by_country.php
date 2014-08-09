<?=$this->load->view(branded_view('cp/header'));?>
<h1>STATS BY COUNTRY <?php echo $this->dataset->data['country_name']; ?> AND METHOD</h1>

<?=$this->dataset->table_head();?>
<?
if (!empty($this->dataset->data)) {
    
    //echo "<pre>";
    //if(isset($_GET['sort_column'])); if($_GET['sort_column']='total_match' && $_GET['sort_dir']='asc'){sort($this->dataset->data[]['total']);}else{rsort($this->dataset->data[]['total']);}
    //print_r ($this->dataset->data);
    foreach ($this->dataset->data as $key => $row) {        
        if($key == 'strategy') {
            foreach($row as $strategy_id => $strategy) {
                $color = $strategy['balance'] > 0 ? '#BDEB78' : '#E3B6C1';
                $style = "style = 'background-color:$color;font-weight:bold;'";
            ?>
            <tr>
                <td align="center" <?php echo $style; ?>>METHOD</td>
                <?php $strategy['strategy'] = str_replace(strip_tags($strategy['strategy']), strtoupper(strip_tags($strategy['strategy'])), $strategy['strategy']); ?>
                <td align="center" <?php echo $style; ?>><?php echo $strategy['strategy']; ?></td>            
            </tr>        
            <tr>
                <td align="center">COUNT</td>
                <td align="center"><?php echo $strategy['count']; ?></td>            
            </tr>
            <tr>
                <td align="center">PROFIT</td>
                <td align="center"><?php echo $strategy['profit']; ?> &euro;</td>            
            </tr>
            <tr>
                <td align="center">PROFIT COUNT</td>
                <td align="center"><?php echo $strategy['profit_count']; ?></td>            
            </tr>
            <tr>
                <td align="center">LOSS</td>
                <td align="center"><?php echo $strategy['loss']; ?> &euro;</td>            
            </tr>
            <tr>
                <td align="center">LOSS COUNT</td>
                <td align="center"><?php echo $strategy['loss_count']; ?></td>            
            </tr>
            <tr>
                <td align="center" <?php echo $style; ?>>BALANCE</td>
                <td align="center" <?php echo $style; ?>><?php echo $strategy['balance']; ?> &euro;</td>            
            </tr>
            <tr>
                <td align="center">BALANCE COUNT</td>
                <td align="center"><?php echo $strategy['balance_count']; ?></td>            
            </tr>
            <tr>
                <td align="center" <?php echo $style; ?>>PERCENTAGE</td>
                <td align="center" <?php echo $style; ?>><?php echo $strategy['percentage']; ?>%</td>            
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