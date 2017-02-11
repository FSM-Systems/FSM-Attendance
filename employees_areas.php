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
</style>
</head>
<body>
<div class="rep">
<button class="closebtn" onclick="$('#areas').fadeOut();">X</button>
<span style="text-decoration: underline;">Attendance Areas Allowed:</span><br><br>
	<?php
	$res = pg_query($con, "select gid, gname from geofences order by gname");
	$res2 = pg_query($con, "select * from employee_areas where aemp=" . $_REQUEST['emp'] . " order by aid");
	$arr = pg_fetch_all($res2);
	$checked = "";
	while($row = pg_fetch_assoc($res)) {
		if($arr) {
			foreach($arr as $a) {
				if($row['gid'] == $a['aarea']) {
					$checked = " checked ";
				}
			}
		}
		echo "<input type='checkbox' " . $checked .  " id='chk_" . $row['gid'] . "' class='chk'> " . $row['gname'] . "<br>";
		$checked = "";
	}
	?>
</div>
<script type="text/javascript" >
$(document).ready(function () {
	$(".chk").click(function () {
		var thisid = $(this).attr("id").replace("chk_","")
		$.ajax({
			url: "ajax/checkbox.php",
			data: {
				emp: <?php echo $_REQUEST['emp']; ?>,
				id: thisid,
				checked: $(this).is(":checked"), 
			},
			success: function (data) {
				if (!$.isNumeric(data)) {
					alert(data);
				}
			}		
		});
	})
})
</script>
</body>
</html>