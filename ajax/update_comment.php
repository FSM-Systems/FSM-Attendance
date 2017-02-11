<?php
include "../connection.php";


$res  = pg_query($con, "update employee_attendance set 
	eacomment='" . $_REQUEST['comm'] . "' where
	eadate::date='" . $_REQUEST['adate'] . "'::date and eaemployee=" . $_REQUEST['empid'] . " and eaaction='" . $_REQUEST['atype'] . "'
");

if(pg_result_error($res) != "") {
	echo 	pg_result_error($res);
} else {
	echo "OK";
}
?>