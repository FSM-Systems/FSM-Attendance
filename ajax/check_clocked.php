<?php
include "../connection.php";

$res  = pg_query($con, "select * from employee_attendance left join employees on eaemployee=eid where eadate::date=now()::date and euuid='" . pg_escape_string($_REQUEST['euuid']) . "' order by eaid");

$ret = "";

if(pg_result_error($res) != "") {
	$ret = pg_result_error($res);
} else {
	// If no clock in send NOCLOCK
	if(pg_num_rows($res) == 0 ) {
		$ret = "NOCLOCK";
	} else {
		while($row = pg_fetch_assoc($res)) {
			$ret .= $row['eaaction'] . "*";
		}	
	}
}
echo substr($ret,0, strlen($ret) - 1);
?>