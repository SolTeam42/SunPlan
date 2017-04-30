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
								<input type="number" name="hdev'.$x.'" id="hdev'.$x.'" min="1" max="24" step="0.5" value="2">
							</div>
						</td>
						<td>
							<div class="form-group">
								<input type="number" name="hndev'.$x.'" id="hndev'.$x.'" min="1" max="24" step="0.5" value="2">
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
			background-color: #8c8c8c;
		}

		.nDevConsumptionLevel {
			position: absolute;
			width: 80%;
		}

		.dDevConsumptionLevel {
			position: absolute;
			width: 80%;
			border: solid 1px black;
		}

		.my-label {
			padding-left: 5px;
			font-weight: normal !important;
		}

		.graphContainer {
			height: 100px;
			margin-bottom: 20px;
			margin-top: 20px;
		}

		img {
			height: 100%;
			width: 20%;
		}
		
	</style>
	
	<link rel="stylesheet" type="text/css" href="./css/HI-SEAS.css" />
	<script type="text/javascript" src="js/HI-SEAS.js"></script>
	
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
			<div class="graphContainer">
				<img src="./chart1.JPG">
				<img src="./chart2.JPG">
				<img src="./chart3.JPG">
				<img src="./chart4.JPG">
				<!--<p class="lead">This is the space for graphs</p>-->
			</div>
		</div>
		
		<div class="col-sm-6">
			<p id="totalEnergyTxt">Total energy for the day: 50000 kWh</p>
			<div class="col-sm-4 energyBar" id="dailyConsumption">
				
			</div>
		</div>
		
		<div class="col-sm-6">
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
	<script type="text/javascript" src="js/HI-SEAS.js"></script>
	
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
		
		function calculateBottom(dn) {
			var maxConsumption = 0;
			for (var x = 0; x < numberOfDevices; x++){
				var div = document.getElementById("nconsumptionDiv" + x.toString());
				if (div != null) {
					maxConsumption = maxConsumption + parseInt($(div).css("height"));
				}
			}
			if (dn == "day") {
				for (var x = 0; x < numberOfDevices; x++){
					var div = document.getElementById("consumptionDiv" + x.toString());
					if (div != null) {
						maxConsumption = maxConsumption + parseInt($(div).css("height"));
					}
				}
			}
			return maxConsumption;
		}
		
		function shiftDayConsumption(h) {
			for (var x = 0; x < numberOfDevices; x++){
				var div = document.getElementById("consumptionDiv" + x.toString());
				if (div != null) {
					currentBottom = parseInt($(div).css("bottom"));
					newBottom = currentBottom + h;
					$(div).animate({bottom: newBottom});
				}
			}
		}
		
		function drawConsumption(dev) {
			//Draw night consumption for the element
			var div = document.getElementById("nconsumptionDiv" + dev.toString());
			nhCons = $("#hndev" + dev.toString()).val();
			if (div === null && nhCons > 0) {
				var divContainer = document.getElementById("dailyConsumption");
				devCons = parseInt($("#dev"+dev.toString()).attr("data-consumption"));
				div = document.createElement("div");
				div.id = "nconsumptionDiv" + dev.toString();
				var consHeight = Math.round(height * ((nhCons*devCons)/totEnergy));
				$(div).css("height", consHeight.toString() + "px");
				$(div).addClass("nDevConsumptionLevel");
				$(div).css("bottom", calculateBottom("night").toString() + "px");
				$(div).css("left", (Math.round(width/10)).toString() + "px");
				var color = document.getElementById("devColor" + dev.toString()).value;
				var r = parseInt(color.slice(1,3),16);
				var g = parseInt(color.slice(3,5),16);
				var b = parseInt(color.slice(5,7),16);
				var rgba = "rgba(" + r.toString() + ", " + g.toString() + ", " + b.toString() + ", 0.5)";
				$(div).css("background-color", rgba);
				divContainer.appendChild(div);
				shiftDayConsumption(consHeight);
			}
			//Draw day consumption for the element
			var div = document.getElementById("consumptionDiv" + dev.toString());
			hCons = $("#hdev" + dev.toString()).val();
			if (div === null && hCons > 0) {
				var divContainer = document.getElementById("dailyConsumption");
				devCons = parseInt($("#dev"+dev.toString()).attr("data-consumption"));
				div = document.createElement("div");
				div.id = "consumptionDiv" + dev.toString();
				var consHeight = Math.round(height * ((hCons*devCons)/totEnergy));
				$(div).css("height", consHeight.toString() + "px");
				$(div).addClass("dDevConsumptionLevel");
				$(div).css("bottom", calculateBottom("day").toString() + "px");
				$(div).css("left", (Math.round(width/10)).toString() + "px");
				var color = document.getElementById("devColor" + dev.toString()).value;
				var r = parseInt(color.slice(1,3),16);
				var g = parseInt(color.slice(3,5),16);
				var b = parseInt(color.slice(5,7),16);
				var rgba = "rgba(" + r.toString() + ", " + g.toString() + ", " + b.toString() + ", 0.5)";
				$(div).css("background-color", rgba);
				divContainer.appendChild(div);
			}
		}
		
		var navbarHeight = $('#navbar').css('height');
		$('.GraphPresentation').css('margin-top',navbarHeight);
		
		var totEnergy = 50000;
		var batteryLevel = 20000;
		var numberOfDevices = <?php echo count($devicesList); ?>;
		
		drawBatteryLevel(batteryLevel, totEnergy);
		
		
		
	</script>
	
	
	
  </body>
</html>