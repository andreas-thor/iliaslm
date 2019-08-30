<?php 

$s = file_get_contents("../ilias/flash/HFTLSQL.swf");
// print ($s);
// $s = "url(https://www1.hft-leipzig.de/thor/HFTLSQL.swf)";
$t = base64_encode($s);

print $t;

// print substr($t, 0, 100);
?>