<?php
include "../connection.php";

$uuid = uniqid();

$res  = pg_query($con, "insert into employees (ename, euuid) values ('" . pg_escape_string($_REQUEST['empname']) . "','" . $uuid . "') returning eid");

if(pg_result_error($res) != "") {
	echo 	pg_result_error($res);
} else {
	echo "OK±" . $uuid;
}
?>