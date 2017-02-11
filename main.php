<!DOCTYPE html>
<html>
<head>
<link href="https://fonts.googleapis.com/css?family=Roboto:100,200,400" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>
<?php
include "jquery.php";
include "jqueryui.php";
include "leaflet.php";
include "leaflet.draw.php";
include "bing.php";
?>
<style type="text/css">
body, html {
	height: 100%;
	margin: 0;
	padding: 0px;
	font-family: 'Roboto', sans-serif;
	font-weight: 200;
}

button:hover {
	cursor: pointer;
}

.title {
	font-size: 20px;
	//text-decoration: underline;
}

.ui-datepicker {
	font-size: 12px ! important;
}

.header {
	//border-bottom-color: black;
	//border-bottom-style: solid;
	//border-bottom-width: 1px;
	//background-color: lightgray;
	height: 30px;
	line-height: 30px;
	vertical-align: middle;
	text-align: center;
	width: 100%;
}


input[type="text"] {
	border-radius: 4px;
	border-color: lightgray;
	border-width: 1px;
	border-style: solid;
	cursor: pointer;
	text-align: center;
}

.workspace {
	width: 100%;
	height: calc(100% - 30px);
	text-align: center;
	
}

.menuicon {
	display: table-cell;
	text-align: center;
	padding: 15px;
}

.menuicon:hover {
	opacity: 0.5;
	cursor: pointer;	
}

.menu {
	position: relative;
	width: 500px;
	height: 130px;
	text-align: center;
	top: 50%;
  left: 50%;
  /* bring your own prefixes */
  transform: translate(-50%, -50%);
}

.logo {
	position: fixed;
	left: 0px;
	height: 50px;
	opacity: 0.6;
	z-index: 1000;
	padding-left: 50px;
	padding-top: 10px;
}

.logo:hover {
	cursor: pointer;
	opacity: 1;
}

.center {
	text-align: center;
}

.closebtn {
	position: absolute;
	height: 20px;
	width: 20px;
	line-height: 20px;
	vertical-align: middle;
	text-align: center;
	top: -10px;
	right: -10px;
	background-color: black;
	border-color: black;
	border-width: 1px;
	border-style: solid;
	border-radius: 4px;
	z-index: 1000;
	color: white;
	font-weight: bolder;
	padding: 0px;
}

.closebtn:hover {
	cursor: pointer;
	color: red;
}
</style>
</head>
<body>
<div class="header">
<img src="img/gctl.png" class="logo">
<span class="title">Attendance and Timesheets</span>
</div>
<br>
<div class="workspace" id="workspace">
	<div class="menu">
		<div class="menuicon">
			<img src="icons/newspaper.png" id="timesheets"><br>
			<span class="menutext">TIME SHEETS</span>
		</div>
		<div class="menuicon">
			<img src="icons/geolocalization.png" id="timingareas"><br>
			<span class="menutext">ATTENDANCE AREAS</span>
		</div>
		<div class="menuicon">
			<img src="icons/employees.png" id="employees"><br>
			<span class="menutext">EMPLOYEES</span>
		</div>
	</div>
</div>
<script type="text/javascript" >
$(document).ready(function () {
	// NProgress Bar
	NProgress.configure({ showSpinner: false });
	
	$(document).ajaxStart(function () {
		NProgress.start();
	})
	
	$(document).ajaxStop(function () {
		NProgress.done();
	});

	$(".logo").click(function () {
		window.location = "main.php";
	});
	
	$("#timesheets").click(function () {
		$("#workspace").load("reports.php")
	});
	
	$("#timingareas").click(function () {
		$("#workspace").load("drawareas.php")
	});
	
	$("#employees").click(function () {
		$("#workspace").load("employees.php")
	});
});
</script>
</body>
</html>