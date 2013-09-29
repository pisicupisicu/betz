<?=$this->load->view(branded_view('cp/header'));?>

        <script type="text/javascript">
			$(function ()  
				{
   var dataSource = [
					 
<?php 
		foreach ($markets as $key=>$market) {
				  echo "{ state: \"".$market['market_name']."\", laybet: ".$profit_lay[$key].", backbet: ".$profit_back[$key]." },";
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
            format: "currency",
            precision: 0
        }
    },
    series: [
        { valueField: "backbet", name: "Back" },
        { valueField: "laybet", name: "Lay" }
    ],
    title: "Stats Profit by Back / Lay",
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

<script type="text/javascript">

			$(function ()  

				{

   var dataSource = [

<?php 
		foreach ($markets as $key=>$market) {
				  
				  echo "{ market: \"".$market['market_name']."\", profit: ".$profit[$key].", loss: -".$loss[$key]." },";
                }
?>

];

$("#chartContainer2").dxChart({

    rotated: true,

    dataSource: dataSource,

    commonSeriesSettings: {

        argumentField: "market",

        type: "bar",

        hoverMode: "allArgumentPoints",

        selectionMode: "allArgumentPoints",

        label: {

            visible: true,

            format: "currency",

            precision: 1

        }

    },

    valueAxis: {

        label: {

            format: "currency",

            precision: 1

        }

    },

    series: [

        { valueField: "profit", name: "Profit" },

        { valueField: "loss", name: "Loss" }

    ],

    title: {

        text: "Stats Profit / Loss by Markets"

    },

    legend: {

        verticalAlignment: "bottom",

        horizontalAlignment: "center"

    },

    pointClick: function(point) {

        point.select();

    }

});

}
		);

		</script>
        
		<div class="content">

			<div class="pane">

				<div id="chartContainer" style="width: 40%; margin: 0 auto; height: 440px;"></div>

			</div>
            
            <div class="pane">

				<div id="chartContainer2" style="width: 70%; margin: 0 auto; height: 440px;"></div>

			</div>

		</div>



<?=$this->load->view(branded_view('cp/footer'));?>









