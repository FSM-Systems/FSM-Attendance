<?php
require_once "../connection.php";

$res = pg_query($con, 'delete from geofences where gid=' . $_REQUEST['dbid'] . ' returning gid');

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo 1;
}
?>