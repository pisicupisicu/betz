<?=$this->load->view(branded_view('cp/header'));?>

<script type="text/javascript">
    $(function ()  
				{
var dataSource = [
    
<?php 
		foreach ($min as $key=>$val) {
				  echo "{ state: \"min ".$min[$key]."\", totalgoals: ".$goals[$key].", redcards: ".$reds[$key].", yellowcards: ".$yellows[$key]." },";
               }
?>
            
];

$("#chartContainer").dxChart({
    dataSource: dataSource,
    commonSeriesSettings: {
        argumentField: "state",
        type: "bar",
        hoverMode: "allArgumentPoints",
        selectionMode: "allArgumentPoints",
        label: {
            visible: true,
            format: "fixedPoint",
            precision: 0
        }
    },
    series: [
      { valueField: "yellowcards", name: "Total Yellow Cards", color: "#DBE001" },
      { valueField: "redcards", name: "Total Red Cards", color:"#D90000" },

      { valueField: "totalgoals",
        type: "spline",
        name: "Total Goals",
        color: "#808080"
        }
    ],
  valueAxis: {
       title: {
            text: "total from all matches"
        },
        position: "top",
		
    },
    legend: {
        verticalAlignment: "bottom",
        horizontalAlignment: "center"
    },
    pointClick: function (point) {
        this.select();
    }
});

}

			);
		</script>
    
        <h1><div style="text-align:center"><?=$form_title?></div></h1>
		<div class="content">

			<div class="pane" style="overflow-x: scroll;">

				<div id="chartContainer" style="margin: 0 auto; width: 10000px; "></div>

			</div>
            <div style="text-align:center; font-size: 16px;"><div style="background:#DBE001; height: 15px; width: 15px; display: inline-block; margin: 0 5px 0 20px;"></div> Total Yellow Cards<div style="background:#D90000; height: 15px; width: 15px; display: inline-block; margin: 0 5px 0 20px;"></div>Total Red Cards <div style="background:#808080; height: 15px; width: 15px; display: inline-block; margin: 0 5px 0 20px;"></div>Total Goals</div>
		</div>



<?=$this->load->view(branded_view('cp/footer'));?>









