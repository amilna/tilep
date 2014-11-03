<?php

$xml = $_REQUEST['xml'];
$zoom = $_REQUEST['z'];
$xtile = $_REQUEST['x'];
$ytile = $_REQUEST['y'];
$type = $_REQUEST['type'];

include('./tilep.php');

$tilep = new tilep();
$tilep->createTile($xml,$zoom,$xtile,$ytile,$type = false);

?>
