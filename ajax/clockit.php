<?php
include "../connection.php";

$emp = pg_fetch_result(pg_query($con, "select eid from employees where euuid='" . $_REQUEST['euuid'] . "'"), 0, 0);

$res  = pg_query($con, "insert into employee_attendance (eaemployee, eadate, ealatitude, ealongitude, eaaction) values (" . $emp . ",now(), '" . $_REQUEST['lat'] . "','" . $_REQUEST['lng'] . "','" . $_REQUEST['action'] . "')");

if(pg_result_error($res) != "") {
	echo 	pg_result_error($res);
} else {
	echo "OK";
}
?>