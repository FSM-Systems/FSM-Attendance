<?php
require_once "../connection.php";

$res = pg_query($con, "insert into geofences (gshape,gname,gtype,ggeometry,gradius) values

('" . $_REQUEST['shape'] . "','" . $_REQUEST['shapename'] . "','" . $_REQUEST['gtype'] . "',

ST_SetSRID(ST_GeomFromGeoJSON('" . $_REQUEST['geometry'] . "'), 3857), " . number_format($_REQUEST['radius'],5,'.','') . ") returning gid");

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo pg_fetch_result($res, 0, 0);
}
?>