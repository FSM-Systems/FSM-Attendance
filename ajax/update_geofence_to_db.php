<?php
require_once "../connection.php";

$res = pg_query($con, "update geofences set
gshape='" . $_REQUEST['shape'] . "',
ggeometry=ST_SetSRID(ST_GeomFromGeoJSON('" . $_REQUEST["geometry"] . "'), 3857),
gradius=" . number_format($_REQUEST["radius"],5,".","") . "
where gid=" . $_REQUEST["dbid"] . " returning gid");

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo 1;
}
?>