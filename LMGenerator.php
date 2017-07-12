<?php 

require_once('Chapter.php');
// error_reporting(E_ERROR);



	// Learning Module = array of chapters
$dom;	// DOMDocument (XML File) for Learning Module
$qti; 	// DOMDocument (XML File) for Items
$lmid = time();		// Id for Learning Module (== current UNIX timestamp)



$json = readJSON("lm.json");

$chapters = [];	
foreach ($json['chapter'] as $chap) {
	array_push ($chapters, new Chapter($chap));
}

createDOM($chapters, $lmid, $json['title'], "https://www1.hft-leipzig.de/thor/dbs/");
createQTI($chapters);

writeZip($lmid, "ilias/Vorlage/");







function readJSON ($jsonFile) {
	printf ("Reading json file %s\n", $jsonFile);
	$content = file_get_contents($jsonFile);
	if ($content === false) {
		printf ("Could not read file %s\n", $jsonFile);
		exit;
	}
	return json_decode ($content, TRUE);
}



function createDOM (array $chapters, int $id, string $title, string $url) {
	
	global $dom;
	
	$dom = DOMDocument::loadXML('<?xml version="1.0" encoding="utf-8"?><!DOCTYPE ContentObject SYSTEM "http://www.ilias.de/download/dtd/ilias_co_3_7.dtd"><ContentObject Type="LearningModule"></ContentObject>');
	$dom->documentElement->appendChild (LearningModule::getXMLMetadata($id, $title, "created " . date("Y-m-d H:i:s")));
	
	/* @var $c Chapter */
	foreach ($chapters as $c) {
		$dom->documentElement->appendChild ($c->getXMLStructureObject());
	}
	
	/* @var $c Chapter */
	foreach ($chapters as $c) {
		foreach ($c->getXMLPageObjects() as $d) {
			$dom->documentElement->appendChild ($d);
		}
	}
	
	/* @var $c Chapter */
	foreach ($chapters as $c) {
		foreach ($c->getXMLMediaObjects($url) as $d) {
			$dom->documentElement->appendChild ($d);
		}
	}
	
	$dom->documentElement->appendChild (LearningModule::getXMLProperties());
	
}


function createQTI(array $chapters) {
	global $qti;
	
	$qti = DOMDocument::loadXML('<?xml version="1.0" encoding="utf-8"?><!DOCTYPE questestinterop SYSTEM "ims_qtiasiv1p2p1.dtd"><questestinterop></questestinterop>');
	
	/* @var $c Chapter */
	foreach ($chapters as $c) {
		foreach ($c->getXMLItems() as $d) {
			$qti->documentElement->appendChild ($d);
		}
	}
	
}


function writeZip (int $id, string $path) {
	global $dom, $qti;
	
	$xmlName = sprintf ('dbs_lm_%d', $id);
	
	$zip = new ZipArchive();
	if ($zip->open(sprintf('%s%s.zip', $path, $xmlName), ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) != TRUE) {
		print ("\n\nErr");
	}
	$zip->addFromString (sprintf ('%1$s/%1$s.xml', $xmlName), $dom->saveXML() );
	$zip->addEmptyDir ( sprintf ('%1$s/objects', $xmlName));
	if ($qti->documentElement->hasChildNodes()) {
		$zip->addFromString (sprintf ('%1$s/qti.xml', $xmlName), $qti->saveXML() );
	}
	$zip->close();
	
}

?>
