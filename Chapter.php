<?php
require_once 'LearningModule.php';
require_once 'Page.php';
require_once 'PageSlide.php';
require_once 'PageOverview.php';

class Chapter {

	private $name;

	private $title;

	private $pages;



	public function __construct($chap) {
		$this->name = $chap["name"];
		$this->title = $chap["title"];
		printf("Create Chapter %s (%s)\n", $this->name, $this->title);
		
		$this->pages = [];
		foreach ($chap["page"] as $page) {
			array_push($this->pages, ($page["name"]=="overview") ? new PageOverview($this->name, $page) : new PageSlide($this->name, $page));
		}
	}



	public function getXMLStructureObject() {
		global $dom;
		
		$xmlStructureObject = $dom->createElement("StructureObject");
		$xmlStructureObject->appendChild(LearningModule::getXMLMetadata($this->name, $this->title));
		
		/* @var $p Page */
		foreach ($this->pages as $p) {
			$xmlStructureObject->appendChild($p->getXMLPageObjectAlias());
		}
		
		return $xmlStructureObject;
	}



	public function getXMLPageObjects() {
		$xmlPageObjects = [];
		
		/* @var $p Page */
		foreach ($this->pages as $p) {
			array_push($xmlPageObjects, $p->getXMLPageObject());
		}
		
		return $xmlPageObjects;
	}


	public function getItems() {
		$items = [];
		
		/* @var $p Page */
		foreach ($this->pages as $p) {
			$items = array_merge($items, $p->getItems());
		}
		
		return $items;
	}
	
	
	
// 	public function getXMLMediaObjects(string $url) {
// 		$xmlMediaObjects = [];
//		
// 		/* @var $p Page */
// 		foreach ($this->pages as $p) {
// 			$xmlMediaObjects = array_merge($xmlMediaObjects, $p->getXMLMediaObjects($url));
// 		}
//		
// 		return $xmlMediaObjects;
// 	}

}
?>