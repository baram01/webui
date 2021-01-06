<?php
$destination = "tmp/cust_bck.tgz";
$source = "cust";
exec('tar czf '.$destination.' '.$source);
?>
