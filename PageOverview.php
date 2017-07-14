<?php

require_once 'Page.php';
require_once 'Question.php';
require_once 'QuestionGap.php';
require_once 'QuestionMC.php';

class PageOverview extends Page {


	private $outcomes;

	public function __construct(string $chapName, $page) {
		
		parent::__construct($chapName, $page);
		
		$this->mediaBaseName = $chapName;
		$this->outcomes = $page["outcome"];
		
		$this->media = [
			"bigpicture" => [
				"caption" => "Fachlandkarte",
				"format" => "image/jpeg",
				"namepattern" => "%s.jpg"
			],
			"script" => [
				"caption" => "Skriptfolien",
				"format" => "application/pdf",
				"namepattern" => "%s.pdf"
			], 
			"exercise" => [
				"caption" => "Übungsblatt",
				"format" => "application/pdf",
				"namepattern" => "%s_Ueb.pdf"
			]
		];

	}





	public function getXMLPageObject() {
		global $dom;
		
		
		$xmlPageObject = parent::getXMLPageObject();
		
		$xmlParagraph = ($xmlPageObject->appendChild($dom->createElement("PageContent")))->appendChild($dom->createElement("Paragraph", "Lernziele"));
		$xmlParagraph->setAttribute ("Language", "de");
		$xmlParagraph->setAttribute ("Characteristic", "Headline1");

		$xmlParagraph = ($xmlPageObject->appendChild($dom->createElement("PageContent")))->appendChild($dom->createElement("Paragraph", "Die Lernenden sind in der Lage ..."));
		$xmlParagraph->setAttribute ("Language", "de");
		$xmlParagraph->setAttribute ("Characteristic", "Standard");
		
		$xmlSimpleBulletList = $xmlParagraph->appendChild($dom->createElement("SimpleBulletList"));
		foreach ($this->outcomes as $outcome) {
			$xmlSimpleBulletList->appendChild($dom->createElement("SimpleListItem", $outcome));
		}
		
		return $xmlPageObject;
	}







	
	
	
	
	
	
}

?>