<?php

require_once 'Question.php';
require_once 'QuestionGap.php';
require_once 'QuestionMC.php';

abstract class Page {

	protected $id;

	protected $page;	// JSON object

	protected $questions;

	protected $media;
	
	protected $mediaBaseName;



	public function __construct(string $chapName, $page) {
		$this->page = $page;
		$this->id = $chapName . "_" . $this->page["name"];
		$this->questions = [];
		$this->media = [];
		$this->mediaBaseName = "";
		printf("  Create %s %s (%s)\n", get_class($this), $this->page["name"], $this->page["title"]);                                                                                                                                          
	}



	public function getXMLPageObjectAlias() {
		global $dom;
		
		$xmlPageObject = $dom->createElement("PageObject");
		$xmlPageAlias = $xmlPageObject->appendChild($dom->createElement("PageAlias"));
		$xmlPageAlias->setAttribute("OriginId", $this->id);
		
		return $xmlPageObject;
	}



	public function getXMLPageObject() {
		global $dom, $url;
		
		$xmlPageObject = $dom->createElement("PageObject");
		$xmlPageObject->appendChild(LearningModule::getXMLMetadata($this->id, $this->page["title"]));
		
		$xmlPageContent = $xmlPageObject->appendChild($dom->createElement("PageContent"));
		
		$xmlTabs = $xmlPageContent->appendChild($dom->createElement("Tabs"));
		$xmlTabs->setAttribute("Type", "VerticalAccordion");
		$xmlTabs->setAttribute("HorizontalAlign", "Center");
		$xmlTabs->setAttribute("Behavior", "FirstOpen");
		
		foreach ($this->media as $type => $typeinfo) {
			
			if ((isset ($this->page[$type])) && ($this->page[$type] == FALSE)) continue;	// "false" flag --> do not add
			
			$xmlTab = $xmlTabs->appendChild($dom->createElement("Tab"));
			$xmlPageContent = $xmlTab->appendChild($dom->createElement("PageContent"));

			// TODO: Request bei onplay, onseeking, onpause
			/* include media via HTML5 => not getXMLMediaObject necessary*/
			$html = "";
			$location = $url . sprintf ($typeinfo["namepattern"], $this->mediaBaseName);
			switch ($typeinfo["format"]) {
				case "image/jpeg":		$html = sprintf ('<img src="%s" width="640" height="480" style="border:1px solid black"/>', $location); break;
				case "video/mp4":		$html = sprintf ('<video onplay="alert(window.location.href); console.log(document.cookie);" controls width="640" height="480" style="border:1px solid black"><source src="%s" type="%s"/></video>', $location, $typeinfo["format"]); break;
				case "application/pdf":	$html = sprintf ('<object width="640" height="480" data="%1$s" type="%2$s"><a href="%1$s">Download</a></object>', $location, $typeinfo["format"]); break;
				
			}

			$xmlParagraph = $xmlPageContent->appendChild ($dom->createElement ("Paragraph", $html));
			$xmlParagraph->setAttribute("Characteristic", "Standard");
			$xmlParagraph->setAttribute("Language", "de");
			
			// additional information for media
			if ((isset ($this->page[$type])) && (is_string($this->page[$type]))) {
				$xmlPageContent = $xmlTab->appendChild($dom->createElement("PageContent"));
				$xmlParagraph = $xmlPageContent->appendChild ($dom->createElement ("Paragraph", $this->page[$type]));
				$xmlParagraph->setAttribute("Characteristic", "Standard");
				$xmlParagraph->setAttribute("Language", "de");
			}
			
			
			/* include media via MediaObject => Reference with generated ID; Media must be list in getXMLMediaObjects */ 
			// WE DO NOT INCLUDE VIA MEDIAOBJECT ANYMORE
// 			$xmlMediaObject = $xmlPageContent->appendChild($dom->createElement("MediaObject"));
// 			$xmlMediaAlias = $xmlMediaObject->appendChild($dom->createElement("MediaAlias"));
// 			$xmlMediaAlias->setAttribute("OriginId", $this->id . "_" . $type);
// 			$xmlMediaAliasItem = $xmlMediaObject->appendChild($dom->createElement("MediaAliasItem"));
// 			$xmlMediaAliasItem->setAttribute("Purpose", "Standard");
// 			$xmlLayout = $xmlMediaAliasItem->appendChild($dom->createElement("Layout"));
// 			$xmlLayout->setAttribute("HorizontalAlign", "Left");
			
			$xmlTab->appendChild($dom->createElement("TabCaption", $typeinfo["caption"]));
		}
		
		return $xmlPageObject;
	}



	public function getItems() {
		$items = [];
		
		/* @var $q Question */
		foreach ($this->questions as $q) {
			array_push($items, $q);
		}
		
		return $items;
	}
	
	
	// WE DO NOT INCLUDE VIA MEDIAOBJECT ANYMORE
// 	public function getXMLMediaObjects(string $url) {
// 		global $dom;
//		
// 		$xmlMediaObjects = [];
//		
// 		foreach ($this->media as $type => $typeinfo) {
//			
// 			$location = $url . sprintf ($typeinfo["namepattern"], $this->mediaBaseName);
//			
// 			$xmlMediaObject = $dom->createElement("MediaObject");
// 			$xmlMediaObject->appendChild(LearningModule::getXMLMetadata($this->id . "_" . $type, $location, $typeinfo["format"]));
//			
// 			$xmlMediaItem = $xmlMediaObject->appendChild($dom->createElement("MediaItem"));
// 			$xmlMediaItem->setAttribute("Purpose", "Standard");
//			
// 			$xmlLocation = $xmlMediaItem->appendChild($dom->createElement("Location", $location));
// 			$xmlLocation->setAttribute("Type", "Reference");
// 			$xmlMediaItem->appendChild($dom->createElement("Format", $typeinfo["format"]));
// 			$xmlLayout = $xmlMediaItem->appendChild($dom->createElement("Layout"));
// 			$xmlLayout->setAttribute("Width", "640");
// 			$xmlLayout->setAttribute("Height", "480");
// 			$xmlLayout->setAttribute("HorizontalAlign", "Left");
//			
// 			array_push($xmlMediaObjects, $xmlMediaObject);
// 		}
//		
// 		return $xmlMediaObjects;
// 	}




}

?>