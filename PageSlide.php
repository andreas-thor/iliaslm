<?php

require_once 'Page.php';
require_once 'Question.php';
require_once 'QuestionGap.php';
require_once 'QuestionMC.php';
require_once 'QuestionFlash.php';

class PageSlide extends Page {


	/*

	Bild bei Aufgabe
	* 04_ER: Arten der Spezialisierung
	* 04_ER: Attribute
	 --> im Moment hart referenziert; nicht abhängig von $url und von Chapter / Page name

	 
	 */

	public function __construct(string $chapName, $page) {
		
		parent::__construct($chapName, $page);
		
		$this->mediaBaseName = $chapName . "_" . $this->page["name"];
		
		$this->media = [
			"slide" => [
				"caption" => "Skriptfolie",
				"format" => "image/jpeg",
				"namepattern" => "%s.jpg"
			],
			"video" => [
				"caption" => "Lernvideo",
				"format" => "video/mp4",
				"namepattern" => "%s.mp4"
			]
		];
		
		
		$this->questions = [];
		if (isset($page["question"])) {
			foreach ($page["question"] as $pos => $question) {
				
				$q = NULL;
				$itemId = $this->id . "_" . $pos;
				switch ($question["type"]) {
					case "gap": $q = new QuestionGap($itemId, $question); break;
					case "mc" : $q = new QuestionMC ($itemId, $question); break;
					case "SELECT": 
					case "CHECK":
					case "VIEW":
						$q = new QuestionFlash ($itemId, $question); break;
				}
				
				array_push($this->questions, $q);
			}
		}
	}





	public function getXMLPageObject() {
		global $dom;
		
		$xmlPageObject = parent::getXMLPageObject();
		
		/* @var $q Question */
		foreach ($this->questions as $q) {
			($xmlPageObject->appendChild($dom->createElement("PageContent")))->appendChild ($q->getXMLQuestion());
		}
		
		return $xmlPageObject;
	}







}

?>