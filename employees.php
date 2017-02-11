<!DOCTYPE html>
<html>
<head>
<?php
include "connection.php";
include "jquery.php";
include "jqueryui.php";
?>
<style type="text/css">
.rep {
	padding-top: 10px;
	font-size: 12px;
}

.areas {
	position: fixed;
	top: 15%;
	left: 20%;
	border-style: solid;
	border-color: black;
	border-width: 1px;
	border-radius: 4px;
	background-color: white;
	width: 400px;
	height: 400px;
	display: none;
	padding: 2px;
}
</style>
</head>
<body>
<div class="rep">
<span style="text-decoration: underline;">Employees registered on the Attendance System:</span><br><br>
<table align="center" class="emp">
	<thead>
		<tr>
			<th>Employee ID</th>
			<th style="text-align: left;">Employee Name</th>
			<th>Clock Areas</th>
		</tr>	
	</thead>
	<tbody>
	<?php
	$res = pg_query($con, "select * from employees order by ename");
	while($row = pg_fetch_assoc($res)) {
		echo "<tr>
			<td>" . $row['eid'] . "</td>
			<td style='text-align: left'>" . $row['ename'] . "</td>	
			<td><button id='emp_" . $row['eid'] . "' class='btnareas'>AREAS</button></td>
		</tr>";	
	}
	?>
	</tbody>
</table>
<script type="text/javascript" >
$(document).ready(function () {
	$(".btnareas").click(function () {
		$("#areas").load("employees_areas.php", { "emp": $(this).attr("id").replace("emp_","") } ).fadeIn();
	})
})
</script>
<div class="areas" id="areas">

</div>
</div>
</body>
</html>