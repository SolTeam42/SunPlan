<?php

	$myfile = fopen("devicesData.txt", "r") or die("Unable to open file!");
	$devicesList = array();
	while(!feof($myfile)) {
		$line = fgets($myfile);
		array_push($devicesList, $line);
	}
	fclose($myfile);
	
	$table = "";
	for ($x = 0; $x < count($devicesList); $x++){
		$table .= 	'<tr>
						<td>
							<div class="checkbox">
								<label>
									<input type="checkbox" value="dev'.$x.'" id="dev'.$x.'" data-name="'.substr($devicesList[$x], 0, strpos($devicesList[$x], ";")).'" data-consumption="'.substr($devicesList[$x], strpos($devicesList[$x], ";") + 2).'" onclick="drawConsumption('.$x.')">'
									.substr($devicesList[$x], 0, strpos($devicesList[$x], ";")).
								'</label>
							</div>
						</td>
						<td>
							<div class="form-group">
								<input type="number" name="hddev'.$x.'" id="hddev'.$x.'" min="1" max="24" step="0.5" value="2" oninput="dAdjustConsumption('.$x.')">
							</div>
						</td>
						<td>
							<div class="form-group">
								<input type="number" name="hndev'.$x.'" id="hndev'.$x.'" min="1" max="24" step="0.5" value="2" oninput="nAdjustConsumption('.$x.')">
							</div>
						</td>
						
						<td>
							<div class="form-group">
								<input type="color" name="devColor'.$x.'" id="devColor'.$x.'">
							</div>
						</td>
					</tr>';
	}
	

?>



<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>HI-SEAS Energy Consumption Calculator</title>
	
	<!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
	<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script type="text/javascript">
		
		
	</script>
	
	<style>
	
		.energyBar {
			border: solid 1px black;
			height: 400px;
			padding: 0px;
		}

		.bateryLevel {
			position: absolute;
			bottom: 0px;
			width: 100%;
			background-color: #b3b3b3;
		}

		.nDevConsumptionLevel {
			position: absolute;
			width: 80%;
			border-radius: 4px;
		}

		.dDevConsumptionLevel {
			position: absolute;
			width: 80%;
			border: solid 2px #666666;
			border-radius: 4px;
		}

		.my-label {
			padding-left: 5px;
			font-weight: normal !important;
		}

		.graphContainer {
			height: 150px;
			margin-bottom: 20px;
			margin-top: 20px;
			overflow: scroll;
			overflow-x: hidden;
		}

		img {
			height: 100%;
			width: 20%;
		}
		
		.inlineDiv {
			float: left;	
			display: inline;
		}
		
	</style>
	
	
  </head>
  <body>
    
	<nav class="navbar navbar-default navbar-fixed-top" id="navbar">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#"><p class="lead "><strong>HI-SEAS Energy Consumption Calculator</strong></p></a>
			</div>
			<div class="collapse navbar-collapse" id="navbar-collapse-1">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="#" id="loadData">Load Data</a></li>
				</ul>
			</div>
		</div>
	</nav>
	
	<div class="container">
	
		
		<div class="GraphPresentation">
			<div class="graphContainer" id="graphContainer">
				<!--<img src="./chart1.JPG">
				<img src="./chart2.JPG">
				<img src="./chart3.JPG">
				<img src="./chart4.JPG">-->
				<!--<p class="lead">This is the space for graphs</p>-->
			</div>
		</div>
		
		<div class="col-sm-4 text-center">
			<p id="totalEnergyTxt">Total energy for the day: 50000 kWh</p>
			<div class="col-sm-offset-2 col-sm-4 energyBar" id="dailyConsumption">
				
			</div>
		</div>
		
		<div class="col-sm-8">
			<form>
				<div class="checkbox">
					<label>
						<input type="checkbox" value="default">
						Use Default settings
					</label>
				</div>
				<hr></hr>
				<table class="table table-striped" id="devicesTable">
					<tr>
						<th>
							Device
						</th>
						<th>
							Day (hours)
						</th>
						<th>
							Night (hours)
						</th>
						
						<th>
							Color
						</th>
					</tr>
					<?php echo $table; ?>
				</table>
			</form>
		</div>
		
	</div>
	
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
	
	<script type="text/javascript">
		function drawBatteryLevel(level,maxL) {
			var divContainer = document.getElementById("dailyConsumption");
			height = parseInt($(divContainer).css("height"));
			width = parseInt($(divContainer).css("width"));
			var divBat = document.createElement("div");
			$(divBat).addClass("bateryLevel");
			$(divBat).css("height", (Math.round(height * (level/maxL))).toString() + "px");
			divContainer.appendChild(divBat);
		}
		
		function calculateB(consumption,dn){
			for (var x = 0; x < numberOfDevices; x++){
				var div = document.getElementById(dn + "consumptionDiv" + x.toString());
				if (div != null) {
					consumption = consumption + parseInt($(div).css("height"));
				}
			}
			return consumption;
		}
		
		function calculateBottom(dn) {
			var maxConsumption = 0;
			maxConsumption = calculateB(maxConsumption, "n");
			if (dn == "d") {
				maxConsumption = calculateB(maxConsumption, "d");
			}
			return maxConsumption;
		}
		
		function shiftAll(h, bottom, dev, devA) {
			var a = "n";
			for (var y = 0; y < 2; y++){
				if (y == 1) {
					a = "d";
				}
				for (var x = 0; x < numberOfDevices; x++){
					var div = document.getElementById(a + "consumptionDiv" + x.toString());
					if (div != null && (x != dev || a != devA)) {
						var currentBottom = parseInt($(div).css("bottom"));
						if (currentBottom >= bottom) {
							var shift = h.toString();
							if (h < 0){
								shift = shift.slice(0, 1) + "=" + shift.slice(1, shift.length);
							} else {
								shift = "+=" + shift;
							}
							$(div).animate({bottom: shift});
						}
					}
				}
			}
		}
		
		function drawC(dev,nd){
			if (nd == "day"){
				var a = "d";
			} else {
				var a = "n"
			}
			var div = document.getElementById(a + "consumptionDiv" + dev.toString());
			var hCons = $("#h" + a + "dev" + dev.toString()).val();
			if (div === null && hCons > 0) {
				var divContainer = document.getElementById("dailyConsumption");
				var devCons = parseInt($("#dev"+dev.toString()).attr("data-consumption"));
				var currentBottom = calculateBottom(a);
				div = document.createElement("div");
				div.id = a + "consumptionDiv" + dev.toString();
				var consHeight = Math.round(height * ((hCons*devCons)/totEnergy));
				$(div).css("height", consHeight.toString() + "px");
				$(div).addClass(a + "DevConsumptionLevel");
				$(div).css("bottom", currentBottom.toString() + "px");
				$(div).css("left", (Math.round(width/10)).toString() + "px");
				var color = document.getElementById("devColor" + dev.toString()).value;
				var r = parseInt(color.slice(1,3),16);
				var g = parseInt(color.slice(3,5),16);
				var b = parseInt(color.slice(5,7),16);
				var rgba = "rgba(" + r.toString() + ", " + g.toString() + ", " + b.toString() + ", 0.5)";
				$(div).css("background-color", rgba);
				divContainer.appendChild(div);
				shiftAll(consHeight, currentBottom, dev, a);
			} else if (div != null) {
				var currentDivHeight = parseInt($(div).css("height"));
				var currentBottom = parseInt($(div).css("bottom"));
				div.parentNode.removeChild(div);
				shiftAll(0 - currentDivHeight, currentBottom, dev, a);
			}
		}
		
		function drawConsumption(dev) {
			drawC(dev,"night");
			drawC(dev,"day");
		}
		
		function adjustC(dev, dn) {
			if (dn == "day"){
				var a = "d";
			} else {
				var a = "n"
			}
			var div = document.getElementById(a + "consumptionDiv" + dev.toString());
			if (div != null) {
				var hCons = $("#h" + a + "dev" + dev.toString()).val();
				var devCons = parseInt($("#dev"+dev.toString()).attr("data-consumption"));
				var newHeight = Math.round(height * ((hCons*devCons)/totEnergy));
				var currentHeight = parseInt($(div).css("height"));
				var currentBottom = parseInt($(div).css("bottom"));
				var totHeightChange = newHeight - currentHeight;
				var strTotHeightChange = totHeightChange.toString();
				if (totHeightChange < 0){
					strTotHeightChange = strTotHeightChange.slice(0, 1) + "=" + strTotHeightChange.slice(1, strTotHeightChange.length);
				} else {
					strTotHeightChange = "+=" + strTotHeightChange;
				}
				$(div).animate({height: strTotHeightChange});
				shiftAll(totHeightChange, currentBottom, dev, a);
			}
		}
		
		function dAdjustConsumption(dev){
			adjustC(dev,"day");
		}
		
		function nAdjustConsumption(dev){
			adjustC(dev,"night");
		}
		
		function setInitialColors(){
			for (var x = 0; x < numberOfDevices; x++){
				if (x < initialColors.length){
					$("#devColor" + x.toString()).val(initialColors[x]);
				} else {
					var r = (Math.floor(Math.random() * 256)).toString(16);
					var g = (Math.floor(Math.random() * 256)).toString(16);
					var b = (Math.floor(Math.random() * 256)).toString(16);
					var randColor = "#" + r + g + b;
					$("#devColor" + x.toString()).val(randColor);
				}
			}
		}
		
		/*function loadSolarData() {
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					var divChartContainer = document.getElementById("chart");
					divChartContainer.innerHTML = "Loading data...";
					var div = document.getElementById("hidden_chart");
					div.innerHTML = this.responseText;
					var solarDataText = div.innerHTML;
					while (solarDataText.length > 1){
						var solarDataRow = [];
						for (var x = 0; x < 4; x++){
							solarDataRow.push(solarDataText.slice(0, solarDataText.indexOf(",")));
							solarDataText = solarDataText.slice(solarDataText.indexOf(",") + 1, solarDataText.length);
						}
						solarData.push(solarDataRow);
					}
					divChartContainer.innerHTML = "Data Loaded";
				}
			};
			xhttp.open("POST", "solarData1.csv", true);
			xhttp.send();
		}*/
		
		function drawGoogleCharts(){
			currentChartDate = "";
			currentChartQuery = "";
			currentChart = 0;
			var divGraphContainer = document.getElementById("graphContainer");
			google.charts.load('current');
			for (var x = 0; x < solarDates.length; x++){
				currentChartDate = solarDates[x];
				currentChartQuery = 'SELECT B,C,D WHERE A = "' + currentChartDate.trim() + '"';
				div = document.createElement("div");
				div.id = "chart" + x.toString();
				$(div).addClass("inlineDiv");
				divGraphContainer.appendChild(div);
				google.charts.setOnLoadCallback(drawChart(x, currentChartDate, currentChartQuery));
			}
		}
		
		function loadDates(){
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					var solarDatesText = this.responseText;
					while (solarDatesText.length > 3){
						solarDates.push(solarDatesText.slice(0, solarDatesText.indexOf(",")));
						solarDatesText = solarDatesText.slice(solarDatesText.indexOf(",") + 1, solarDatesText.length);
					}
				drawGoogleCharts();
				}
			};
			xhttp.open("POST", "solarDates.csv", true);
			xhttp.send();
		}
		
		function drawChart(cc, ccd, ccq) {
			
			return function(){
			var wrapper = new google.visualization.ChartWrapper({
				chartType: 'LineChart',
				dataSourceUrl: 'https://docs.google.com/spreadsheets/d/1AjpBmfvO2pgYu-UXGMDtZ0fUNBVrh42ehjcpPojqUHA/edit?usp=sharing',
				query: ccq,
				options: {'title': ccd,
							'legend': {position: 'bottom'},
							'height': 150,
							'width': 500,
							'series': {
										0: {targetAxisIndex: 0},
										1: {targetAxisIndex: 1}
									},
							'vAxes': {
										0: {title: 'Solar Radiation [W/m2]'},
										1: {title: 'Cumulative Energy Collected [Wh]'}
									}},
				containerId: 'chart' + cc.toString()
			});
			wrapper.draw();
			}
		}
		
		var navbarHeight = $('#navbar').css('height');
		$('.GraphPresentation').css('margin-top',navbarHeight);
		
		var totEnergy = 50000;
		var batteryLevel = 20000;
		var numberOfDevices = <?php echo count($devicesList); ?>;
		var initialColors = ["#ff0000", "#00ff00", "#0000ff", "#ffff00", "#ff00ff", "#00ffff", "#cc0099", "#000099", "#ff9900", "#669900"];
		//var solarData = [];
		solarDates = [];
				
		drawBatteryLevel(batteryLevel, totEnergy);
		setInitialColors();
		//loadSolarData();
		loadDates();
		var currentChartDate = "";
		var currentChartQuery = "";
		var currentChart = 0;
		
		
		
		
	</script>
	
	
	
  </body>
</html>