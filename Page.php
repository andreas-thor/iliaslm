<?php

require_once 'Question.php';
require_once 'QuestionGap.php';
require_once 'QuestionMC.php';

abstract class Page {

	protected $id;

	protected $name;

	protected $title;

	protected $questions;

	protected $media;
	
	protected $mediaBaseName;



	public function __construct(string $chapName, $page) {
		$this->name = $page["name"];
		$this->title = $page["title"];
		$this->id = $chapName . "_" . $this->name;
		$this->questions = [];
		$this->media = [];
		$this->mediaBaseName = "";
		printf("  Create %s %s (%s)\n", get_class($this), $this->name, $this->title);                                                                                                                                          
	}



	public function getXMLPageObjectAlias() {
		global $dom;
		
		$xmlPageObject = $dom->createElement("PageObject");
		$xmlPageAlias = $xmlPageObject->appendChild($dom->createElement("PageAlias"));
		$xmlPageAlias->setAttribute("OriginId", $this->id);
		
		return $xmlPageObject;
	}



	public function getXMLPageObject() {
		global $dom;
		
		$xmlPageObject = $dom->createElement("PageObject");
		$xmlPageObject->appendChild(LearningModule::getXMLMetadata($this->id, $this->title));
		
		$xmlPageContent = $xmlPageObject->appendChild($dom->createElement("PageContent"));
		
		$xmlTabs = $xmlPageContent->appendChild($dom->createElement("Tabs"));
		$xmlTabs->setAttribute("Type", "VerticalAccordion");
		$xmlTabs->setAttribute("HorizontalAlign", "Center");
		$xmlTabs->setAttribute("Behavior", "FirstOpen");
		
		foreach ($this->media as $type => $typeinfo) {
			
			$xmlTab = $xmlTabs->appendChild($dom->createElement("Tab"));
			$xmlMediaObject = ($xmlTab->appendChild($dom->createElement("PageContent")))->appendChild($dom->createElement("MediaObject"));
			
			$xmlMediaAlias = $xmlMediaObject->appendChild($dom->createElement("MediaAlias"));
			$xmlMediaAlias->setAttribute("OriginId", $this->id . "_" . $type);
			$xmlMediaAliasItem = $xmlMediaObject->appendChild($dom->createElement("MediaAliasItem"));
			$xmlMediaAliasItem->setAttribute("Purpose", "Standard");
			$xmlLayout = $xmlMediaAliasItem->appendChild($dom->createElement("Layout"));
			$xmlLayout->setAttribute("HorizontalAlign", "Left");
			
			$xmlTab->appendChild($dom->createElement("TabCaption", $typeinfo["caption"]));
		}
		
		return $xmlPageObject;
	}



	public function getXMLMediaObjects(string $url) {
		global $dom;
		
		$xmlMediaObjects = [];
		
		foreach ($this->media as $type => $typeinfo) {
			
			$location = $url . sprintf ($typeinfo["namepattern"], $this->mediaBaseName);
			
			$xmlMediaObject = $dom->createElement("MediaObject");
			$xmlMediaObject->appendChild(LearningModule::getXMLMetadata($this->id . "_" . $type, $location, $typeinfo["format"]));
			
			$xmlMediaItem = $xmlMediaObject->appendChild($dom->createElement("MediaItem"));
			$xmlMediaItem->setAttribute("Purpose", "Standard");
			
			$xmlLocation = $xmlMediaItem->appendChild($dom->createElement("Location", $location));
			$xmlLocation->setAttribute("Type", "Reference");
			$xmlMediaItem->appendChild($dom->createElement("Format", $typeinfo["format"]));
			$xmlLayout = $xmlMediaItem->appendChild($dom->createElement("Layout"));
			$xmlLayout->setAttribute("Width", "640");
			$xmlLayout->setAttribute("Height", "480");
			$xmlLayout->setAttribute("HorizontalAlign", "Left");
			
			array_push($xmlMediaObjects, $xmlMediaObject);
		}
		
		return $xmlMediaObjects;
	}



	public function getXMLItems() {
		$xmlItems = [];
		
		/* @var $q Question */
		foreach ($this->questions as $q) {
			array_push($xmlItems, $q->getXMLItem());
		}
		
		return $xmlItems;
	}
}

?>