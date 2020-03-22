<?php


class ItemPool {
	
	private $id;
	private $title;
	private $items;
	
	public function __construct(int $id, string $title, array $items) {
		
		$this->id = $id;
		$this->title = $title;
		$this->items = $items;
	}

	public function addItem ($item) {
		array_push($this->items, $item);
	}
	
	
	public function getXMLQTI() {
		
		global $dom;
		
		$dom = DOMDocument::loadXML('<?xml version="1.0" encoding="utf-8"?><!DOCTYPE questestinterop SYSTEM "ims_qtiasiv1p2p1.dtd"><questestinterop></questestinterop>');
		
		foreach ($this->items as $i) {
			$dom->documentElement->appendChild ($i->getXMLItem());
		}
		
		return $dom;
	}
	

	public function getXMLQPL() {
		
		global $dom;
		
		$dom = DOMDocument::loadXML('<?xml version="1.0" encoding="utf-8"?><!DOCTYPE ContentObject SYSTEM "http://www.ilias.de/download/dtd/ilias_co_3_7.dtd"><ContentObject Type="Questionpool_Test"></ContentObject>');
		$dom->documentElement->appendChild (LearningModule::getXMLMetadata($this->id, $this->title . " created " . date("Y-m-d H:i:s"), "created " . date("Y-m-d H:i:s")));
		
		foreach ($this->items as $i) {
			$xmlPageObject = $dom->documentElement->appendChild ($dom->createElement("PageObject"));
			$xmlPageContent = $xmlPageObject->appendChild($dom->createElement("PageContent"));
			$xmlPageContent->appendChild ($i->getXMLQuestion());
		}
		
		return $dom;

		
	}
	
	
	public function writeZip (string $path) {
		
		$qplName = sprintf ('dbs_qpl_%d', $this->id);
		$qtiName = sprintf ('dbs_qti_%d', $this->id);
		
		$zip = new ZipArchive();
		if ($zip->open(sprintf('%s%s.zip', $path, $qplName), ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) != TRUE) {
			print ("\n\nErr");
		}
		
		$xmlQPL = $this->getXMLQPL();
		$zip->addFromString (sprintf ('%1$s/%1$s.xml', $qplName), $xmlQPL->saveXML() );
		$zip->addEmptyDir ( sprintf ('%1$s/objects', $qplName));
		
		$xmlQTI = $this->getXMLQTI();
		if ($xmlQTI->documentElement->hasChildNodes()) {
			$zip->addFromString (sprintf ('%1$s/%2$s.xml', $qplName, $qtiName), $xmlQTI->saveXML() );
		}
		$zip->close();
		
	}
	
}

?>