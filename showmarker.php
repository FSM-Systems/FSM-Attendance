<!DOCTYPE html>
<html>
<head>
<style type="text/css">
html, body {
	margin: 0;
	padding: 0px;
	text-align: center;
}

#mapid {
	width: 100%;
	height: 100%;
}
</style>
</head>
<body>
<button class="closebtn" onclick="$('#mapshow').fadeOut();">X</button>
<div id="mapid">

</div>
<script type="text/javascript" >
$(document).ready(function () {
	var mymap = L.map("mapid");
	
	var osmUrl='https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
	var osm = new L.TileLayer(osmUrl);
	mymap.addLayer(osm);
	
	mymap.setView([<?php echo $_REQUEST['lat']; ?>,<?php echo $_REQUEST['lng']; ?>], 16);
	// Load any saved or global geofences from DB
	$.ajax({
		url: "ajax/get_geofence_from_db.php",
		success: function(data) {
			if (data.length > 0) {
				var geo = data.split("~~~");
				var geodet;
				for (var x = 0; x<geo.length; x++) {
					geodet = geo[x].split("???");
					var shape = eval(geodet[3]).setStyle({"weight": 2}).addTo(mymap).bindTooltip(geodet[1]);
					shape.dbid = geodet[0];
				}
			}
		}
	});
	
	L.marker([<?php echo $_REQUEST['lat']; ?>,<?php echo $_REQUEST['lng']; ?>]).addTo(mymap);
})
</script>
</body>
</html>