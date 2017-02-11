<!DOCTYPE html>
<html>
<head>
<style type="text/css">
	html, head {
		height: 100%;
		width: 100%;
	}
	#mapid {
		height: 100%;
	}
</style>
</head> 
<body>
<div id="mapid">

</div>
<script type="text/javascript" >
$(document).ready(function () {
	var mymap = L.map('mapid', {closePopupOnClick: false	});
	
	var osmUrl='https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
	var osm = new L.TileLayer(osmUrl);
	mymap.addLayer(osm);
	
	var bing = new L.tileLayer.bing("AiK7ISOnjpXFqwNdphlVeE3g0LoHD4x2KKc30zj5AOFsbGVTKPlJvv5YhnPVcjnq");
	
	L.control.layers({
		OSM: osm,
		Bing: bing
	}, {}, {
		//collapsed: false
	}).addTo(mymap);
			
	//mymap.locate({setView: true, maxZoom: 16});
	
	var drawnItems = new L.FeatureGroup();
	mymap.addLayer(drawnItems);
	var drawControl = new L.Control.Draw({
		draw: {
             polyline: false,
             marker: false,
             circle: false,
         },
		edit: {
			featureGroup: drawnItems
		}
	});
	mymap.addControl(drawControl);
	
	// Load any saved or global geofences from DB
	$.ajax({
		url: "ajax/get_geofence_from_db.php",
		success: function(data) {
			if (data.length > 0) {
				var geo = data.split("~~~");
				var geodet;
				for (var x = 0; x<geo.length; x++) {
					geodet = geo[x].split("???");
					var shape = eval(geodet[3]).setStyle({"weight": 2}).bindPopup('<input type=text class="gname" id="gname_' + geodet[0] + '" dbid="' + geodet[0] + '" value="' + geodet[1] + '">').addTo(drawnItems).bindTooltip(geodet[1]);
//					drawnItems.addLayer(shape)
					shape.dbid = geodet[0];
				}
				// Zoom to all areas
				mymap.fitBounds(drawnItems.getBounds());
			} else {
				mymap.locate({setView: true, watch: true});
			}
		}
	});
	
	// Shape edited
	mymap.on('draw:edited', function (e) {
 		var layers = e.layers;
		var radius;
 		layers.eachLayer(function (layer) {	
	 		// Parse edited layers and create string for DB
	 		if (layer instanceof L.Circle) {
	    		latLngs = layer.getLatLng();
	    		strlatLngs = 'L.circle([' + latLngs.lat + ',' + latLngs.lng + '], ' + layer.getRadius() + ')';
	   			radius = layer.getRadius();
	 		} else {
	   			latLngs = layer.getLatLngs();
	   			strlatLngs = 'L.polygon([';
				$.each(latLngs, function (index, value) {
					$.each(value, function () {
						strlatLngs += '[' + this.lat + ',' + this.lng + '], ';
					})
				});
				strlatLngs += '])';
				radius = 0;
	   		}
	  		// Save to db edited layer
	  		$.ajax({
				url: "ajax/update_geofence_to_db.php",
				method: "POST",
				data: {
					shape:  strlatLngs,
					dbid: layer.dbid,
					radius: radius,
					geometry: JSON.stringify(layer.toGeoJSON().geometry),
				},
				success: function (data) {
					if ($.isNumeric(data)) {
						alert('GEOFENCE SAVED! ACTIONS CAN BE SETUP IN OPTIONS!');
					} else {
						alert('THERE HAS BEEN AN ERROR UPDATING THE GEOFENCE. PLEASE CONTACT SUPPORT.' + data)
					}
				},
				error: function () {
					alert('THERE HAS BEEN AN ERROR CONNECTING TO THE SERVER. PLEASE TRY AGAIN!');
				}
	  		})
	 	});
	});

	// Delete geofences
	mymap.on('draw:deleted', function (e) {
 		var layers = e.layers;

 		layers.eachLayer(function (layer) {
  			$.ajax({
				url: "ajax/delete_geofence_to_db.php",
				method: "POST",
				data: {
					dbid: layer.dbid,
				},
				success: function (data) {
					if ($.isNumeric(data)) {
						alert('GEOFENCE DELETED!');
					} else {
						alert('THERE HAS BEEN AN DELETING UPDATING THE GEOFENCE. PLEASE CONTACT SUPPORT.')
					}
				},
				error: function () {
					alert('THERE HAS BEEN AN ERROR CONNECTING TO THE SERVER. PLEASE TRY AGAIN!');
				}
  			})
 		});
	});
	
	// update DB on change of name
	$(document).on("change", ".gname", function () {
		newgname = $(this).val();
		dbid = $(this).attr("dbid");
		$.ajax({
			url: "ajax/update_geofence_name.php",
			global: false,
			data: {
				gname: 	newgname,
				dbid: dbid,
			},
			success: function (data) {
				if (!$.isNumeric(data)) {
					alert('ERROR UPDATING GEOFENCE NAME, PLEASE TRY AGAIN!')
				}
			},
			error: function () {
				alert('ERROR UPDATING GEOFENCE NAME, PLEASE TRY AGAIN!')
			}
		})
	});
	
	// Save drawn images
	mymap.on('draw:created', function (e) {
		var type = e.layerType, layer = e.layer;
    	drawnItems.addLayer(layer);
		// Save shape to DB
		var latLngs;
  		var strlatLngs;
  		var gtype;
  		var layerGeoJSON;
  		var radius;
  		if (type === 'circle') {
 			latLngs = layer.getLatLng();
 			strlatLngs = 'L.circle([' + latLngs.lat + ',' + latLngs.lng + '], ' + layer.getRadius() + ')';
 			gtype = 'circle';
			layerGeoJSON = layer.toGeoJSON();
			layerGeoJSON = JSON.stringify(layer.toGeoJSON());
			radius = layer.getRadius();
 		} else {
 			latLngs = layer.getLatLngs();
 			console.log(latLngs);
 			strlatLngs = 'L.polygon([';
			$.each(latLngs, function (index, value) {
				$.each(value, function () {
					strlatLngs += '[' + this.lat + ',' + this.lng + '], ';
				})
			})
			strlatLngs += '])';
			layerGeoJSON = JSON.stringify(layer.toGeoJSON());
			radius = 0;
			gtype = 'polygon';
		}
		var shapename = prompt('PLEASE NAME THIS LOCATION');

		if (shapename != null) {
			// Add name to popup
			layer.bindPopup(shapename);
			$.ajax({
				url: "ajax/save_geofence_to_db.php",
				method: "POST",
				dataType: "json",
				data: {
					shape: strlatLngs,
					shapename: shapename,
					gtype: gtype,
					radius: radius,
					geometry: JSON.stringify(layer.toGeoJSON().geometry),
				},
				success: function (data) {
					if (!$.isNumeric(data)) {
						drawnItems.removeLayer(layer);
						alert('THERE HAS BEEN AN ERROR SAVING YOUR GEOFENCE. PLEASE TRY AGAIN OR CONTACT SUPPORT.' + data);
					} else {
						layer.dbid = data;
						alert('GEOFENCE SAVED! ACTIONS CAN BE SETUP IN OPTIONS!')
					}
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(thrownError);
					drawnItems.removeLayer(layer);
					alert('THERE HAS BEEN AN ERROR CONNECTING TO THE SERVER. PLEASE TRY AGAIN!');
				}
  				})
		} else {
			drawnItems.removeLayer(layer)
		}
	});
});
</script>
</body>
</html>