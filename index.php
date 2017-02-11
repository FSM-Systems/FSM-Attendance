<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
include "jquery.php";
include "jquerymobile.php";
include "leaflet.php";
?>

<style type="text/css">
body, html {
	padding: 10px;
	margin: 0 auto;
	text-align: center;
}

.logo {
	height: 50px;
	vertical-align: middle;
}

.divlogo {
	height: 50px;
	line-height: 50px;
	vertical-align: middle;
}

#mapid {
	height: 200px;
}
</style>
</head>
<body>
	<div class="divlogo">
	<img src="img/gctl.png" class="logo"><br>
	</div>
	<span id="today"></span>
	<button id="clockin" class="ui-btn std action" disabled="true">CLOCK IN</button>
	<button id="lunchbreak" class="ui-btn std action" disabled="true">CLOCK LUNCH BREAK</button>
	<button id="lunchback" class="ui-btn std action" disabled="true">CLOCK LUNCH BACK</button>
	<button id="clockout" class="ui-btn std action" disabled="true">CLOCK OUT</button>
	<button id="reloadpage" class="ui-btn std">RELOAD</button>
	<button id="register" class="ui-btn" style="display: none;">REGISTER</button><br>
	
	<div id="mapid"></div>
	<button id="myid" class="ui-btn std">MY ID</button>
	
	<script type="text/javascript" >
	$(document).ready(function () {
		
		// Disable all buttons and set font size
		$(".std").prop("disabled", true);
		$("button").css("font-size", "12px");
		
		// Show current date
		var date = new Date();
		var n = date.toDateString();
		$("#today").text(n);
		
		// Spinner settings
		var $this = $( this ),
		theme = $this.jqmData( "theme" ) || $.mobile.loader.prototype.options.theme,
		msgText = $this.jqmData( "msgtext" ) || $.mobile.loader.prototype.options.text,
		textVisible = $this.jqmData( "textvisible" ) || $.mobile.loader.prototype.options.textVisible,
		textonly = !!$this.jqmData( "textonly" );
		html = $this.jqmData( "html" ) || "";
      
      var map = L.map("mapid");
      	var osmUrl='https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
		var osm = new L.TileLayer(osmUrl);
		map.addLayer(osm);
      
		if(navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function(position){
				curlat = position.coords.latitude;
				curlong = position.coords.longitude;
				
				map.locate({setView: true, maxZoom: 16});
				function onLocationFound(e) {
					var radius = e.accuracy / 2;
					L.marker([curlat, curlong]).addTo(map)
						.bindPopup("You are within " + radius.toFixed(2) + " meters from this point").openPopup();
					L.circle([curlat, curlong], radius).addTo(map);
					
					// After position found load buttons and check id					
					// Check if UUID exists on page load. If not then  show register button
					$.ajax({
						type: 'POST',
						url: 'ajax/check_uuid.php',
						data: {
							empid: localStorage.getItem("uuid"),				
						},
						success: function (data) {
							if (data == "NOT EXIST") {
								$("#register").show();
								$(".std").prop("disabled", true);
							} else {
								$("#register").hide();
								$(".std").prop("disabled", false);
								// Enable only usable buttons
								// Check if user already clocked in and bank buttons accordingly
								$.ajax({
									type: 'POST',
									url: 'ajax/check_clocked.php',
									data: {
										euuid: localStorage.getItem("uuid"),	
									},	
									success: function (data) {
										if (data != "NOCLOCK") {
											var actions = data.split("*");
											for (var v = 0; v < actions.length; v++) {
												$("#" + actions[v]).prop("disabled", true);
											}
										}
									},
									error: function () {
										alert('ERROR RETRIEVING DATA. PLEASE CALL THE OFFICE!');
									}
								})
							}
						},
						error: function () {
							alert('CANNOT RETREIVE UUID INFO');
						}
					});
				}
				map.on('locationfound', onLocationFound);
				// Load any saved or global geofences from DB
				$.ajax({
					url: "ajax/get_geofence_from_db.php",
					success: function(data) {
						if (data.length > 0) {
							var geo = data.split("~~~");
							var geodet;
							for (var x = 0; x<geo.length; x++) {
								geodet = geo[x].split("???");
								var shape = eval(geodet[3]).setStyle({"weight": 2}).addTo(map).bindTooltip(geodet[1]);
								shape.dbid = geodet[0];
							}
						}
					}
				});
			}, function error(msg) {
					// Disable all buttons as we have no position!
					$(".std").prop("disabled", true);
					// Alert user
					alert("CANNOT GET POSITION. PLEASE TRY OUTSIDE!");
				},{maximumAge:600000, timeout:5000, enableHighAccuracy: true}) ;
		}
		
		// Attendance functions -- new geolocation as we could be fooled........... ;-))
		$(".action").click(function () {
			$.mobile.loading( "show", {
				text: msgText,
				textVisible: textVisible,
				theme: theme,
				textonly: textonly,
				html: html
    		});

			var btn = $(this);
			var action = $(this).attr("id");
			var actiontxt = $(this).text();
			switch(action) {
				// When clicking these 3 buttons we cannot click them if the previous button has not been clicked (means disabled..)
				case "clockout":
				case "lunchbreak":
				case "lunchback":
					if (btn.prev("button").prop("disabled") != true) {
						$.mobile.loading( "hide" );
						alert('YOU CANNOT COMPLETE THIS ACTION!');
						return;
					}
					break;
			}
			if (confirm(actiontxt + '?')) {
				if(navigator.geolocation) {
					navigator.geolocation.getCurrentPosition( function(position) {
						$.ajax({
							type: 'POST',
							url: 'ajax/clockit.php',
							data: {
								euuid: localStorage.getItem("uuid"),	
								lat: position.coords.latitude,
								lng: position.coords.longitude,
								action: action,
							},
							success: function (data) {
								if (data == "OK") {
									$.mobile.loading( "hide" );
									btn.prop("disabled", true); // Disable button so no more clocking action
									alert('ACTION COMPLETED!');
								} else {
									$.mobile.loading( "hide" );
									alert(data)
								};
							}		
						});
					}, function error(msg) {
						$.mobile.loading( "hide" );
						alert(msg);
					},
					{maximumAge:600000, timeout:5000, enableHighAccuracy: true}); 
				} else {
					alert("Sorry, your browser does not support HTML5 geolocation.");
					$.mobile.loading( "hide" );
	        	}	
			} else {
				$.mobile.loading( "hide" );
			}
		})
		
		$("#register").click(function () {
			$.mobile.loading( "show", {
				text: msgText,
				textVisible: textVisible,
				theme: theme,
				textonly: textonly,
				html: html
    		});
			// Register new user. If already there then update UUID
			var empname  = prompt('PLEASE INPUT NAME');
			if (empname != '') {
				// Step 1 check if already exists!
				$.ajax({
					type: 'POST',
					url: 'ajax/check_name.php',
					data: {
						empname: empname,				
					},
					success: function (data) {
						// Check if name exists! If yes ask to update otherwise insert new record.
						if (data != "EXIST") {
							// New user
							$.ajax({
								type: 'POST',
								url: 'ajax/register.php',
								data: {
									empname: empname,
								},
								success: function (data) {
									alert(data)
									var resp = data.split("±");
									if (resp[0] != "OK") {
										alert('THERE HAS BEEN AN ERROR REGISTERING THE NAME. PLEASE TRY AGAIN!');
									} else {
										localStorage.setItem("uuid", resp[1]);
										$("#register").hide();
										$(".std").prop("disabled", false);
									}
								},
								error: function () {
									alert('THERE HAS BEEN AN ERROR REGISTERING THE EMPOLYEE');
								}
							});
						}
						$.mobile.loading( "hide" );
					},
					error: function () {
						alert('THERE HAS BEEN AN ERROR RETREIVING DATA!')
						$.mobile.loading( "hide" );
					}
				})
			} else {
				alert('NO NAME SPECIFIED!');
		}
	});
	
	$("#myid").click(function () {
		alert(localStorage.getItem("uuid"));
	});
	
	$("#reloadpage").click(function () {
		location.reload();
	})
})
</script>
</body>
</html>