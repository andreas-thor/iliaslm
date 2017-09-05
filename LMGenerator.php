<?php 

require_once('LearningModule.php');

// error_reporting(E_ERROR);



$url = "https://www1.hft-leipzig.de/thor/dbs/";		// global URL that has all the media

/*
$jsonLM = readJSON("lm.json");

$lm = new LearningModule(time(), $jsonLM['title'], $jsonLM['chapter']);
$lm->writeZip("ilias/Vorlage/");


$ip = new ItemPool(time(), "Items von " . $jsonLM['title'], $lm->getItems());
$ip->writeZip("ilias/Vorlage/");
*/


$ipFlash = new ItemPool(time(), "Flash Items", []);
$jsonFlash = readJSON("E:/Dev/DMT/WebContent/WEB-INF/repo/bibliothek.json");
foreach ($jsonFlash as $itemId => $jsonItem) {
	if (substr($itemId, 0, 1) == "_") continue;
	
	if ($itemId != "1") continue;
	
	$ipFlash->addItem(new QuestionFlash($itemId, $jsonItem));
}
$ipFlash->writeZip("ilias/Vorlage/");



function readJSON ($jsonFile) {
	printf ("Reading json file %s\n", $jsonFile);
	$content = file_get_contents($jsonFile);
	if ($content === false) {
		printf ("Could not read file %s\n", $jsonFile);
		exit;
	}
	return json_decode ($content, TRUE);
}



?>
