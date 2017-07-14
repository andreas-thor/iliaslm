<?php

require_once 'Page.php';
require_once 'Question.php';
require_once 'QuestionGap.php';
require_once 'QuestionMC.php';

class PageSlide extends Page {


	/*

	kein Video:
	 * 05_ER2RM_Generalisierung_Beispiel
	 * 04_ER: Beziehung Überblick
	 
	 
	Bild bei Aufgabe
	* 04_ER: Arten der Spezialisierung
	* 04_ER: Attribute
	 --> im Moment hart referenziert; nicht abhängig von $url und von Chapter / Page name

	Text zu Video:
	* Fallunterscheidung CASE / COALESCE
	 
	 */

	public function __construct(string $chapName, $page) {
		
		parent::__construct($chapName, $page);
		
		$this->mediaBaseName = $chapName . "_" . $this->name;
		
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
				switch ($question["type"]) {
					case "gap": $q = new QuestionGap($this->id, $pos, $question); break;
					case "mc" : $q = new QuestionMC ($this->id, $pos, $question); break;
				}
				
				array_push($this->questions, $q);
			}
		}
	}





	public function getXMLPageObject(string $url) {
		global $dom;
		
		$xmlPageObject = parent::getXMLPageObject($url);
		
		/* @var $q Question */
		foreach ($this->questions as $q) {
			($xmlPageObject->appendChild($dom->createElement("PageContent")))->appendChild ($q->getXMLQuestion());
		}
		
		return $xmlPageObject;
	}







}

?>