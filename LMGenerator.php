<?php
require_once ('LearningModule.php');

// error_reporting(E_ALL);

$url = "https://www1.hft-leipzig.de/thor/dbs/"; // global URL that has all the media


$jsonLM = readJSON("lm.json");
$lm = new LearningModule(time(), $jsonLM['title'], $jsonLM['chapter']);
$lm->writeZip("ilias/Vorlage/");
$ip = new ItemPool(time(), "Items von " . $jsonLM['title'], $lm->getItems());
$ip->writeZip("ilias/Vorlage/");

/*


$ipFlash = new ItemPool(time(), "Flash Items", []);

foreach ([
	"bibliothek" => "03_SQL",
	"auto_VP" => "05_ER2RM",
	"auto_HP" => "05_ER2RM",
	"auto_VR" => "05_ER2RM",
	"flug_2NF" => "06_NORM",
	"flug_3NF" => "06_NORM",
	"ausleihe" => "08_DK",
	"ausleihenutzung" => "10_DBPROG",
	"geometrie" => "10_DBPROG",
	"geraet" => "11_DBANB"

] as $repo => $description) {
	
	$jsonFlash = readJSON("E:/Dev/DMT/WebContent/WEB-INF/repo/" . $repo . ".json");
	foreach ($jsonFlash as $itemId => $jsonItem) {
		if (substr($itemId, 0, 1) == "_")
			continue;
		
		// if ($itemId != "1") continue;
		
		$jsonItem["description"] = $description;
		$ipFlash->addItem(new QuestionFlash($repo . ":" . $itemId, $jsonItem));
	}
}
$ipFlash->writeZip("ilias/Vorlage/");

*/

function readJSON($jsonFile) {
	printf("Reading json file %s\n", $jsonFile);
	$content = file_get_contents($jsonFile);
// 	$content = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($content));
	
	if ($content === false) {
		printf("Could not read file %s\n", $jsonFile);
		exit();
	}
	
	$res = json_decode($content, TRUE);
	// json_last_error_msg();
	

	
	return $res;
}

?>
