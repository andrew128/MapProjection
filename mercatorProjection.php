<!DOCTYPE html>
<head>
	<title>Mercator Projection</title>
	<script src="//d3js.org/d3.v3.min.js"></script>
	<script src="//d3js.org/queue.v1.min.js"></script>
	<script src="//d3js.org/topojson.v1.min.js"></script>
	<style>
		.graticule {
		  fill: none;
		  stroke: #777;
		  stroke-opacity: .5;
		  stroke-width: .5px;
		}

		#countries {
		    fill: #b8b8b8;
		    stroke: #fff;
		    stroke-width: .5px;
		    stroke-linejoin: round;
		}			
	</style>
</head>
<body>
	<h1>Visualization of Traceroute</h1>

	<form method="POST">
		Enter site url:
		<input type="text" name="site" value="https://www.google.com/"> <br/>
		<input type="submit"> <br/> 
	</form>

	<?php
		if($_SERVER["REQUEST_METHOD"] == "POST"){ 
			$site = $_POST["site"];

			// pass var into bash script

			$command = escapeshellcmd("bash traceRoute.sh");
			$output = shell_exec($command);
			print $output;

			// parse output from traceroute: find city based off of ip address

			// get lat lon city stored in sql and write to traceRoutePath.csv


			// //check each line in file to see if it contains city name, if it does parse and get lon lat vals
			//$file = fopen("cities.csv", "r");
			//print(fgetcsv($file)[0]);
			//fclose($file);

			//$i = 0;
			//while(!feof($file) && $i<count($listOfCities)){
				// for($j = 0; $j<count($listOfCities); $i++){
				// 	//if(fgetcsv($file)[2] == $listOfCities[$j]){
				// 		//print $listOfCities[2];
				// 		print fgetcsv($file)[2] . ",";
				// 	//}
				//}
			//}
			// fclose($file);

			// for($i=0; $i<count($listOfCities); $i++){

			// }
			// $length = count($listOfCities);
			// $i=0
			// while()
		}
	?>

	<script type="text/javascript">
		var width=1000, height=700;
		//var traceRoutePath = [[47.5992959,-122.3288811,"Seattle"], [40.7305993,-73.9865813,"New York"], [42.9894583,-81.2460912,"London"]];

		var svg = d3.select("body").append("svg")
		    .attr("width", width)
		    .attr("height", height);

		var projection = d3.geo.mercator()
		    .scale((width + 1) / 2 / Math.PI)
		    .translate([width / 2, height / 2])
		    .precision(.1);

		var path = d3.geo.path()
		    .projection(projection);

		//DOC: creating and appending graticule to svg element
		var graticule = d3.geo.graticule();
		svg.append("path")
		    .datum(graticule)
		    .attr("class", "graticule")
		    .attr("d", path);

		var g = svg.append("g");

		d3.json("./world-110m.json", function(error, topology){
			if(error) throw error;

			//DOC: adding lon and lat for circles representing cities
			d3.csv("./cities.csv", function(error, data){
				if(error) throw error;

				g.selectAll("circle")
					.data(data)
					.enter()
					.append("circle")
			        .attr("cx", function(d) {
			            return projection([d.lon, d.lat])[0];
			        })
			        .attr("cy", function(d) {
			            return projection([d.lon, d.lat])[1];
			        })
			        .attr("r", 1)
			        .style("fill", "red");
			});

			//DOC: adding land to map
			g.selectAll("path")
		        .data(topojson.feature(topology, topology.objects.countries)
		            .features)
		    .enter()
		        .append("path")
		        .attr("d", path)
		        .attr("id", "countries")

		    //DOC: adding lines to signify path
		    d3.csv("./traceRoutePath.csv", function(error, data){
				if(error) throw error;
				console.log(data);

				for(var i=0; i<data.length-1; i++){
					// console.log(data[i]["city"]);
					// console.log(projection([data[i].lon, data[i].lat])[0]);
					// console.log(projection([data[i].lon, data[i+1].lat])[0]);
					//console.log(projection([data[i].lon, data[i].lat])[0]);
					//console.log(projection([data[i].lon, data[i].lat])[1]);
					//svg.append("g")
					// g.append("circle")
					// 	.attr("cx", projection([data[i].lon, data[i].lat])[0])
					// 	.attr("cy", projection([data[i].lon, data[i].lat])[1])
					// 	.attr("r", 10)
					// 	.style("fill", "blue")
					g.append("line")
						.attr("x1", projection([data[i].lon, data[i].lat])[0])
						.attr("y1", projection([data[i].lon, data[i].lat])[1])
						.attr("x2", projection([data[i+1].lon, data[i+1].lat])[0])
						.attr("y2", projection([data[i+1].lon, data[i+1].lat])[1])
						.style("stroke", "black")
				}
			});
		})	

	</script>
</body>
</html>
