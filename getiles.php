<?php

if (!isset($argv[1]))
{
	echo "masukan xml! php getiles.php [xml] [bbox] [minzoom] [maxzoom]";
	die();
}

if (!isset($argv[2]))
{
	echo "masukan bbox! php getiles.php [xml] [bbox] [minzoom] [maxzoom]";
	die();
}

if (!isset($argv[3]))
{
	echo "masukan minzoom! php getiles.php [xml] [bbox] [minzoom] [maxzoom]";
	die();
}

if (!isset($argv[4]))
{
	echo "masukan maxzoom! php getiles.php [xml] [bbox] [minzoom] [maxzoom]";
	die();
}

$xml = $argv[1];
$bbox = $argv[2];
$minzoom = $argv[3];
$maxzoom = $argv[4];

include('./tilep.php');

$tilep = new tilep();
$tilep->xmlDir = "./data/";
$tilep->dumpTiles($xml,$bbox,$minzoom,$maxzoom);
?>
