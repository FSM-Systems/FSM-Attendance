<?php
include "../connection.php";

$res  = pg_query($con, "select * from employees where upper(ename)=upper('" . pg_escape_string($_REQUEST['empname']) . "')");

if(pg_result_error($res) != "") {
	echo 	pg_result_error($res);
} else {
	if(pg_num_rows($res) >= 1) {
		echo "EXIST";
	} else {
		echo "NOT EXIST";
	}
}
?>