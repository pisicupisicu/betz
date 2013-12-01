<?=$this->load->view(branded_view('cp/header'));?>

<script type="text/javascript">
    $(function ()  
				{
var dataSource = [
    { year: 1950, europe: 546, americas: 332, africa: 227 },
    { year: 1960, europe: 605, americas: 417, africa: 283 },
    { year: 1970, europe: 656, americas: 513, africa: 361 },
    { year: 1980, europe: 694, americas: 614, africa: 471 },
    { year: 1990, europe: 721, americas: 721, africa: 623 },
    { year: 2000, europe: 730, americas: 836, africa: 797 },
    { year: 2010, europe: 728, americas: 935, africa: 982 },
    { year: 2020, europe: 721, americas: 1027, africa: 1189 },
    { year: 2030, europe: 704, americas: 1110, africa: 1416 },
    { year: 2040, europe: 680, americas: 1178, africa: 1665 },
    { year: 2050, europe: 650, americas: 1231, africa: 1937 }
];

$("#chartContainer").dxChart({
    dataSource: dataSource,
    commonSeriesSettings: {
        argumentField: "year"
    },
    series: [
        { valueField: "europe", name: "Europe" },
        { valueField: "americas", name: "Americas" },
        { valueField: "africa", name: "Africa" }
    ],
    argumentAxis:{
        grid:{
            visible: true
        }
    },
    tooltip:{
        enabled: true
    },
    title: "Historic, Current and Future Population",
    legend: {
        verticalAlignment: "bottom",
        horizontalAlignment: "center"
    },
    commonPaneSettings: {
        border:{
            visible: true,
            right: false
        }       
    }
});

}

			);
		</script>

<h1><div style="text-align:center"><?=$form_title?></div></h1>
		<div class="content">

			<div class="pane">

				<div id="chartContainer" style="margin: 0 auto;"></div>

			</div>
        </div>


        
<form class="form-horizontal">
    <div class="control-group">
        <label class="control-label" for="inputEmail">Insert Score</label>
        
        <div class="controls">
        <input type="text" id="score" name ="score" placeholder="E.g. 1-2"><button type="submit" class="btn btn-success">Go</button>
        </div>
    </div>

</form>
 <!--       
<h1>ALL MATCHES FINISHED: <?=$score?> TOTAL GAMES : <?php echo $total_games['total_games']; ?></h1>        
<table class="dataset" cellpadding="0" cellspacing="0">
   		<thead>
   			<tr class="odd">
				<td style="width:20%" colspan="3">GOAL that make score:<?php echo $key; ?></td>
			</tr>
		</thead>
			<tbody>
				<tr class="">
                    <td align="center">
						Min <b><?=$row[$key]['under']['cate'];?></b>
					</td>
					<td align="center">
						Total <b><?=$row[$key]['under']['cate'];?></b>
					</td>
	                <td align="center" <?php if($row[$key]['under']['percent'] > 70) echo 'style="color:red;"'; ?>>
	                	Procent <b><?=$row[$key]['under']['percent'];?>%</b>
	                </td>
				</tr>
			</tbody>
	</table>-->
        
<?=$this->load->view(branded_view('cp/footer'));?>









