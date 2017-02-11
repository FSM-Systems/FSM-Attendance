<?php
include "connection.php";
?>
<!DOCTYPE html>
<html>
<head>
<style type="text/css">
.wp {
	height: 90%;
	font-size: 12px;
	padding-top: 14px;
	text-align: center;
}

.rep {
	padding-top: 10px;
	font-size: 12px;
}

/* CSS to print only timesheet area*/
@media print {
  body * {
    visibility: hidden;
  }
  .wp, .wp * {
    visibility: visible;
  }
  .wp {
    position: absolute;
    left: 0;
    top: 0;
  }
}
</style>
<?php
include "jquery.php";
include "jqueryui.php";
?>
</head>
<body>
<div class="rep">
	Select employee:
	<select id="emp">
	<?php
	$res = pg_query($con, "select eid, ename from employees order by ename");
	while($row = pg_fetch_assoc($res)) {
		echo "<option value=" . $row['eid'] . ">" . $row['ename'] . "</option>";	
	}
	?>
	</select>
	From Date: <input type="text" name="from" id="from" class="dp">
	To Date: <input type="text" name="to" id="to" class="dp">
	<button id="attendance">Display</button><button id="print" disabled="true">PRINT</button><br><br>
	<script type="text/javascript" >
		$(document).ready(function () {
			$(".dp").datepicker({dateFormat: "yy-mm-dd"})
			// On load show all employees that have clocked in today.
			$("#wp").load("timesheet.php", {"empid": "all"});
		});
		
		$("#attendance").click(function () {
			$("#wp").load("timesheet.php", {"empid": $("#emp").val(), "from": $("#from").val(), "to": $("#to").val() });
			$("#print").attr("disabled", false);
		});
		
		// On change of employee disable print so we do not make a mistake
		$("#emp").change(function () {
			$("#print").attr("disabled", true);
		})
		
		$("#print").click(function () {
			window.print();
		})
	</script>
	<div class="wp" id="wp">
	
	</div>
</div>
</body>
</html>