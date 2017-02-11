<?php
require_once "../connection.php";

$res = pg_query($con, 'select gid, gname, gtype, gshape, gglobal from geofences');

$ret = '';

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	while($row = pg_fetch_assoc($res)) {
		$ret .= $row['gid'] . '???' . $row['gname'] . '???' . $row['gtype'] . '???' . $row['gshape'] . '???' . '~~~';
	}
	echo substr($ret, 0, strlen($ret) - 3);
}
?>