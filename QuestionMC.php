<?php

class QuestionMC extends Question {


	private $text;
	
	
	public function __construct(string $id, $json) {
		
		parent::__construct($id);
		
		global $url;
		$this->text = str_replace("[URL]", $url, $json["text"]);
	}
	
	

	public function getXMLItem() {
		
		global $dom;
		
		
		$xmlItem = $dom->createElement("item");
		$xmlItem->setAttribute("ident", $this->id);
		$xmlItem->setAttribute("maxattempts", "3");
		$xmlItem->setAttribute("title", "Titel");
		$xmlPresentation = $xmlItem->appendChild($dom->createElement("presentation"));
		$xmlPresentation->setAttribute("label", "Titel");
		$xmlFlow = $xmlPresentation->appendChild($dom->createElement("flow"));
		
		$xmlResprocessing = $xmlItem->appendChild($dom->createElement("resprocessing"));
		($xmlResprocessing->appendChild($dom->createElement("outcomes")))->appendChild($dom->createElement("decvar"));
		
		$blockText = explode("|", $this->text);
		$xmlMattext = ($xmlFlow->appendChild($dom->createElement("material")))->appendChild($dom->createElement("mattext", $blockText[0]));
		$xmlMattext->setAttribute("texttype", "text/plain");
		
				$xmlResponseLid = $xmlFlow->appendChild($dom->createElement("response_lid"));
				$xmlResponseLid->setAttribute("ident", "MCMR");
				$xmlResponseLid->setAttribute("rcardinality", "Multiple");
				
				$xmlRenderChoice = $xmlResponseLid->appendChild($dom->createElement("render_choice"));
				$xmlRenderChoice->setAttribute("shuffle", "No");
				
				foreach (explode(";", $blockText[1]) as $choiceNumber => $choiceText) {	// choices are separated by ";"
					
					$points = "-1";
					$choiceText = trim($choiceText);
					if ($choiceText[0] == "~") {		// correct choice(s) is/are prefixed with ~
						$points = "1";
						$choiceText = substr($choiceText, 1);	// remove *-prefix
					}
					
					$xmlResponseLabel = $xmlRenderChoice->appendChild($dom->createElement("response_label"));
					$xmlResponseLabel->setAttribute("ident", $choiceNumber);
					$xmlMattext = ($xmlResponseLabel->appendChild($dom->createElement("material")))->appendChild($dom->createElement("mattext", $choiceText));
					$xmlMattext->setAttribute("texttype", "text/plain");
					
					$xmlRespCondition = $xmlResprocessing->appendChild($dom->createElement("respcondition"));
					$xmlRespCondition->setAttribute("continue", "Yes");
					$xmlVarEqual = ($xmlRespCondition->appendChild($dom->createElement("conditionvar")))->appendChild($dom->createElement("varequal", $choiceNumber));
					$xmlVarEqual->setAttribute("respident", "MCMR");
					
					$xmlSetVar = $xmlRespCondition->appendChild($dom->createElement("setvar", $points));
					$xmlSetVar->setAttribute("action", "Add");
				}
		
		return $xmlItem;
		
	}
}

