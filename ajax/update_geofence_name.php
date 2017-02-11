<?php
require_once "../connection.php";

$res = pg_query($con, 'update geofences set gname=\'' . $_REQUEST['gname'] . '\' where gid=' . $_REQUEST['dbid'] . ' returning gid');

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo pg_fetch_result($res, 0, 0);
}
?>