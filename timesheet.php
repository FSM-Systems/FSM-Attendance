<?php
include "connection.php";
?>
<!DOCTYPE html>
<html>
<head>
<style type="text/css">
html, body {
	text-align: center;
}

table.ts thead th {
	font-size: 12px;
	text-align: right;
	border-bottom-color: black;
	border-bottom-style: solid;
	border-bottom-width: 1px;
}

table.ts tbody tr {
	font-size: 12px;
	text-align: right;
	border-bottom-color: black;
	border-bottom-style: dashed;
	border-bottom-width: 1px;
}

table.ts tbody tr:nth-child(even) {
	background-color: lightgray;
}

table.ts tbody td {
	vertical-align: top;
}

table.ts tbody td img {
	padding-left: 4px;
	vertical-align: bottom;
	cursor: pointer;
}

.mapshow {
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
<script type="text/javascript" >
function showloc(coords) {
	latlng = coords.split("xxx");
	$("#mapshow").load("showmarker.php", {"lat": latlng[0], "lng": latlng[1]}).fadeIn();
}
</script>
</head>
<body>
<?php
if($_REQUEST['empid'] == "all") {
	$strsql = "
	select to_char(eadate, 'Day -> dd/mm/yyyy ') || '<br>' || ename as d, eadate as ddb, to_char(min(clockin), 'hh24:mi') as clockin, to_char(max(lunchbreak), 'hh24:mi') as lunchbreak, to_char(min(lunchback), 'hh24:mi') as lunchback, to_char(max(clockout), 'hh24:mi') as clockout, 
	to_char((max(lunchbreak)-min(clockin)) + (min(clockout) - max(lunchback)), 'hh24:mi') as t, min(geofclockin) as geofclockin, min(geoflunchbreak) as geoflunchbreak, min(geoflunchback) as geoflunchback, min(geofclockout) as geofclockout
	from (  
	select ename, eadate::date, 
	case when eaaction = 'clockin' then eadate else null end as clockin, case when eaaction = 'clockin' then ingeofence(ealatitude, ealongitude, eaemployee, eacomment) else null end as geofclockin,
	case when eaaction = 'lunchbreak' then eadate else null end as lunchbreak, case when eaaction = 'lunchbreak' then ingeofence(ealatitude, ealongitude, eaemployee, eacomment) else null end as geoflunchbreak,
	case when eaaction = 'lunchback' then eadate else null end as lunchback, case when eaaction = 'lunchback' then ingeofence(ealatitude, ealongitude, eaemployee, eacomment) else null end as geoflunchback,
	case when eaaction = 'clockout' then eadate else null end as clockout, case when eaaction = 'clockout' then ingeofence(ealatitude, ealongitude, eaemployee, eacomment) else null end as geofclockout
	
	from employee_attendance left join employees on eaemployee=eid where eadate::date=now()::date order by eadate
	) as attendance group by eadate, ename order by ddb";
	$name = "EMPLOYEES CLOCKED IN TODAY";
} else {
	$strsql = "
	select to_char(eadate, 'Day -> dd/mm/yyyy ') || '<br>' || ename as d, eadate as ddb, to_char(min(clockin), 'hh24:mi') as clockin, to_char(max(lunchbreak), 'hh24:mi') as lunchbreak, to_char(min(lunchback), 'hh24:mi') as lunchback, to_char(max(clockout), 'hh24:mi') as clockout, 
	to_char((max(lunchbreak)-min(clockin)) + (min(clockout) - max(lunchback)), 'hh24:mi') as t, min(geofclockin) as geofclockin, min(geoflunchbreak) as geoflunchbreak, min(geoflunchback) as geoflunchback, min(geofclockout) as geofclockout
	from (  
	select ename, eadate::date, 
	case when eaaction = 'clockin' then eadate else null end as clockin, case when eaaction = 'clockin' then ingeofence(ealatitude, ealongitude, eaemployee, eacomment) else null end as geofclockin,
	case when eaaction = 'lunchbreak' then eadate else null end as lunchbreak, case when eaaction = 'lunchbreak' then ingeofence(ealatitude, ealongitude, eaemployee, eacomment) else null end as geoflunchbreak,
	case when eaaction = 'lunchback' then eadate else null end as lunchback, case when eaaction = 'lunchback' then ingeofence(ealatitude, ealongitude, eaemployee, eacomment) else null end as geoflunchback,
	case when eaaction = 'clockout' then eadate else null end as clockout, case when eaaction = 'clockout' then ingeofence(ealatitude, ealongitude, eaemployee, eacomment) else null end as geofclockout
	
	from employee_attendance left join employees on eaemployee=eid where eid=" .  $_REQUEST['empid'] . " and eadate::date between '" . $_REQUEST['from'] . "' and '" . $_REQUEST['to'] . "' order by eadate
	) as attendance group by eadate, ename order by ddb";
	$name = pg_fetch_result(pg_query($con, "select ename from employees where eid=" . $_REQUEST['empid']), 0, 0);
}
//echo $strsql;
$res = pg_query($con, $strsql);
?>
<table class="ts" cellpadding="2" cellspacing="0" style="width: 900px;" align="center">
	<thead>
		<?php
		echo "<tr><td colspan=100 style='font-weight: bold; font-size: 16px; text-decoration: underline'>" . $name . "</td></tr>";		
		?>
		<tr>
			<th style="text-align: left;">DATE</th>
			<th>CLOCKED IN</th>		
			<th>LUNCH BREAK</th>		
			<th>LUNCH BACK</th>		
			<th>CLOCKED OUT</th>		
			<th>TOTAL HOURS</th>
		</tr>	
	</thead>
	<tbody>
	<?php
	if(pg_num_rows($res) > 0) {
		while($row = pg_fetch_assoc($res)) {
			echo "
			<tr>
				<td style='text-align: left'>" . $row['d'] . "</td>
				<td>" . $row['clockin'];
				if($row['clockin'] != "") {
					$data = explode("**", $row['geofclockin']);
					if($data[0] == "false") {
						echo "<img src='icons/cancel.png' onclick='showloc(\"" . $data[1] . "\")'>";
					} else {
						echo "<img src='icons/checked.png' onclick='showloc(\"" . $data[1] . "\")'>";
					}
					echo "<br><input type='text' class='comment' value='" . $data[2] . "' title='" . $data[2] . "' id='comm_" . $row['ddb'] . "' dbtype='clockin'>";
				}
				echo "</td>
				<td>" . $row['lunchbreak'];
				if($row['lunchbreak'] != "") {
					$data = explode("**", $row['geoflunchbreak']);
					if($data[0] == "false") {
						echo "<img src='icons/cancel.png' onclick='showloc(\"" . $data[1] . "\")'>";
					} else {
						echo "<img src='icons/checked.png' onclick='showloc(\"" . $data[1] . "\")'>";
					}
					echo "<br><input type='text' class='comment' value='" . $data[2] . "' title='" . $data[2] . "' id='comm_" . $row['ddb'] . "' dbtype='lunchbreak'>";
				}
				echo "</td>
				<td>" . $row['lunchback'];
				if($row['lunchback'] != "") {
					$data = explode("**", $row['geoflunchback']);
					if($data[0] == "false") {
						echo "<img src='icons/cancel.png' onclick='showloc(\"" . $data[1] . "\")'>";
					} else {
						echo "<img src='icons/checked.png' onclick='showloc(\"" . $data[1] . "\")'>";
					}
					echo "<br><input type='text' class='comment' value='" . $data[2] . "' title='" . $data[2] . "' id='comm_" . $row['ddb'] . "' dbtype='lunchback'>";
				}
				echo  "</td>
				<td>" . $row['clockout'];
				if($row['clockout'] != "") {
					$data = explode("**", $row['geofclockout']);
					if($data[0] == "false") {
						echo "<img src='icons/cancel.png' onclick='showloc(\"" . $data[1] . "\")'>";
					} else {
						echo "<img src='icons/checked.png' onclick='showloc(\"" . $data[1] . "\")'>";
					}
					echo "<br><input type='text' class='comment' value='" . $data[2] . "' title='" . $data[2] . "' id='comm_" . $row['ddb'] . "' dbtype='clockout'>";
				}
				echo "</td>
				<td>" . $row['t'] . "</td>";
			echo "
			</tr>
			";	
		}	
	} else {
		echo "<tr><td colspan=100 style='text-align: center'>NO EMPLOYEES CLOCKED IN AT THE MOMENT!</td></tr>"	;
	}
	?>	
	</tbody>
</table>
<script type="text/javascript" >
$(document).ready(function () {
	$(document).tooltip();
	$(".comment").change(function () {
		var atype = $(this).attr("dbtype");
		var adate = $(this).attr("id").replace("comm_",""); 
		var comm = $(this).val();
		$.ajax({
			url: 'ajax/update_comment.php',
			data: {
				empid: <?php echo $_REQUEST['empid']; ?>,
				atype: atype,
				adate: adate,
				comm: comm,
			},
			success: function (data) {
				if (data != "OK") {
					alert(data)
				}
			},
			error: function () {
				alert('ERROR! PLEASE TRY AGAIN!');
			}		
		})
	})
})
</script>
<div class="mapshow" id="mapshow">

</div>
</body>
</html>