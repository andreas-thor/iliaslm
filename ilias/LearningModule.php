<?php

require_once 'ItemPool.php';
require_once 'Chapter.php';

class LearningModule {
    
    private $id;
    private $title;
	private $chapters;	// Learning Module = array of chapters
	
	
	public function __construct(int $id, string $title, array $jsonChapters) {
		
		$this->id = $id;
		$this->title = $title;
		$this->chapters = [];	// Learning Module = array of chapters
		foreach ($jsonChapters as $chap) {
			array_push ($this->chapters, new Chapter($chap));
// 			break;
		}
	}
		
	 
	
	public function writeZip (string $path) {
		
		$xmlName = sprintf ('dbs_lm_%d', $this->id);
		
		$zip = new ZipArchive();
		if ($zip->open(sprintf('%s%s.zip', $path, $xmlName), ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) != TRUE) {
			print ("\n\nErr");
		}
		
		$xmlLM = $this->getXMLLM();
		$zip->addFromString (sprintf ('%1$s/%1$s.xml', $xmlName), $xmlLM->saveXML() );
		$zip->addEmptyDir ( sprintf ('%1$s/objects', $xmlName));

		$xmlQTI = $this->getXMLQTI();
		if ($xmlQTI->documentElement->hasChildNodes()) {
			$zip->addFromString (sprintf ('%1$s/qti.xml', $xmlName), $xmlQTI->saveXML() );
		}
		$zip->close();
		
	}
	
	
	
	public function getItems() {
		$items = [];
		
		/* @var $c Chapter */
		foreach ($this->chapters as $c) {
			$items = array_merge($items, $c->getItems());
		}
		
		return $items;
	}
	
	
	
	public function getXMLQTI() {
		return (new ItemPool(0, "", $this->getItems()))->getXMLQTI();
	}
	
	
	public function getXMLLM() {
		
		global $dom;
		
		$dom = DOMDocument::loadXML('<?xml version="1.0" encoding="utf-8"?><!DOCTYPE ContentObject SYSTEM "http://www.ilias.de/download/dtd/ilias_co_3_7.dtd"><ContentObject Type="LearningModule"></ContentObject>');
		$dom->documentElement->appendChild (LearningModule::getXMLMetadata($this->id, $this->title . " created " . date("Y-m-d H:i:s"), "created " . date("Y-m-d H:i:s")));
		
		/* @var $c Chapter */
		foreach ($this->chapters as $c) {
			$dom->documentElement->appendChild ($c->getXMLStructureObject());
		}
		
		/* @var $c Chapter */
		foreach ($this->chapters as $c) {
			foreach ($c->getXMLPageObjects() as $d) {
				$dom->documentElement->appendChild ($d);
			}
		}
		
		$dom->documentElement->appendChild (LearningModule::getXMLProperties());
		
		return $dom;
		
	}
	
	
    
    public static function getXMLMetadata (string $id, string $title, string $description = "") {
        
        global $dom;
        
        $xmlMetadata = $dom->createElement("MetaData");
        
        $xmlGeneral = $dom->createElement("General");
        $xmlGeneral->setAttribute ("Structure", "Hierarchical");
        $xmlMetadata->appendChild ($xmlGeneral);
        
        $xmlIdentifier = $dom->createElement("Identifier");
        $xmlIdentifier->setAttribute ("Entry", $id);
        $xmlIdentifier->setAttribute ("Catalog", "ILIAS");
        $xmlGeneral->appendChild ($xmlIdentifier);
        
        $xmlTitle = $dom->createElement("Title", $title);
        $xmlTitle->setAttribute ("Language", "de");
        $xmlGeneral->appendChild ($xmlTitle);
        
        $xmlLanguage = $xmlGeneral->appendChild ($dom->createElement("Language"));
        $xmlLanguage->setAttribute ("Language", "de");
        
        $xmlDescription = $xmlGeneral->appendChild ($dom->createElement("Description", $description));
        $xmlDescription->setAttribute ("Language", "de");
        
        $xmlKeyword = $xmlGeneral->appendChild ($dom->createElement("Keyword"));
        $xmlKeyword->setAttribute ("Language", "de");
        
        return $xmlMetadata;
    }
    
    
    public static function getXMLProperties () {
    	
    	global $dom;
    	
    	$xmlProperties = $dom->createElement ("Properties");
    	
    	$prop = array (
    		"Layout" => "toc2win",
    		"PageHeader" => "pg_title",
    		"TOCMode" => "pages",
    		"ActiveLMMenu" => "y",
    		"ActiveNumbering" => "y",
    		"ActiveTOC" => "y",
    		"ActivePrintView" => "y",
    		"CleanFrames" => "n",
    		"PublicNotes" => "n",
    		"HistoryUserComments" => "n",
    		"Rating" => "n",
    		"RatingPages" => "n",
    		"LayoutPerPage" => "0",
    		"ProgressIcons" => "0",
    		"StoreTries" => "0",
    		"RestrictForwardNavigation" => "0",
    		"DisableDefaultFeedback" => "0"
    	);
    	
    	foreach ($prop as $name => $value) {
    		$xmlProperty = $dom->createElement ("Property");
    		$xmlProperty->setAttribute ("Name", $name);
    		$xmlProperty->setAttribute ("Value", $value);
    		$xmlProperties->appendChild ($xmlProperty);
    	}
    	
    	
    	return $xmlProperties;
    }
}


?>