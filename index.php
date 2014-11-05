<?php

$xml = $_REQUEST['xml'];
$zoom = $_REQUEST['z'];
$xtile = $_REQUEST['x'];
$ytile = $_REQUEST['y'];
$type = $_REQUEST['type'];
$clear = $_REQUEST['clear'];

include('./tilep.php');

$tilep = new tilep();
$tilep->xmlDir = "./data/";
$tilep->createTile($xml,$zoom,$xtile,$ytile,$type,$clear);

?>
