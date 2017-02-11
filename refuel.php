<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
include "jquery.php";
include "jquerymobile.php";
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
</style>
</head>
<body>
	<div class="divlogo">
	<img src="img/gctl.png" class="logo">&nbsp;
	<span id="today"></span>
	</div>
	<input type="file" accept="image/*" id="image">	
	
	<button id="refuel">REFUEL</button>
	
	<script type="text/javascript" >
	$(document).ready(function () {
		// Disable all buttons and set font size
		$(".std").prop("disabled", true);
		$("button").css("font-size", "12px");
		
		$("#refuel").click(function () {
			alert($("#image").files[0])
		})
		
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
 
 		if(navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function(position){
				curlat = position.coords.latitude;
				curlong = position.coords.longitude;
			}, function error(msg) {
				// Disable all buttons as we have no position!
				$(".std").prop("disabled", true);
				// Alert user
				alert("CANNOT GET POSITION. PLEASE TRY OUTSIDE!");
			},{maximumAge:600000, timeout:5000, enableHighAccuracy: true}) ;
		}
})
</script>
</body>
</html>