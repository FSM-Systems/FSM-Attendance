<?php
include "../connection.php";

if($_REQUEST['checked'] == "true") {
	$sql = "insert into employee_areas (aarea, aemp) values (" . $_REQUEST['id'] . "," . $_REQUEST['emp'] . ") returning aid";
} else {
	$sql = "delete from employee_areas where aarea=" . $_REQUEST['id'] . " and aemp=" . $_REQUEST['emp'] . " returning aid";	
}

$res = pg_query($con, $sql);

if(pg_result_error($res) != "") {
	$ret = pg_result_error($res);
} else {
	echo pg_fetch_result($res, 0, 0);	
}
?>