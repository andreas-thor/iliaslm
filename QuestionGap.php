<?php

require_once 'Question.php';

class QuestionGap extends Question {

	private $text;
	
	
	public function __construct(string $id, $json) {
		
		parent::__construct($id);
		
		global $url;
		$this->text = str_replace("[URL]", $url, $json["text"]);
	}
	
	
	private function getXMLResprocessing ($gapident, $choiceText, $points) {
		
		global $dom;
		
		$xmlRespCondition = $dom->createElement("respcondition");
		$xmlRespCondition->setAttribute("continue", "Yes");
		$xmlVarEqual = ($xmlRespCondition->appendChild($dom->createElement("conditionvar")))->appendChild($dom->createElement("varequal", $choiceText));
		$xmlVarEqual->setAttribute("respident", $gapident);
		
		$xmlSetVar = $xmlRespCondition->appendChild($dom->createElement("setvar", $points));
		$xmlSetVar->setAttribute("action", "Add");
		
		return $xmlRespCondition;
		
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
		
		$xmlMattext = ($xmlFlow->appendChild($dom->createElement("material")))->appendChild($dom->createElement("mattext", " "));
		$xmlMattext->setAttribute("texttype", "text/plain");
		
		// gaps are separated by | so that text matches "<some Text>|first gap|<further text>|second gap|<more text>..."
		foreach (explode("|", $this->text) as $blockNumber => $blockText) {
			
			if (($blockNumber % 2) == 0) { // regular text
				$xmlMattext = ($xmlFlow->appendChild($dom->createElement("material")))->appendChild($dom->createElement("mattext", $blockText));
				$xmlMattext->setAttribute("texttype", "text/plain");
			
			} else { 
				
				// gaps are enumerated (starting with 0)
				$gapident = "gap_" . (($blockNumber - 1) / 2);	
				$xmlResponseStr = $xmlFlow->appendChild($dom->createElement("response_str"));
				$xmlResponseStr->setAttribute("ident", $gapident);
				$xmlResponseStr->setAttribute("rcardinality", "Single");
				
				// gap: choices are separated by ";"
				if (strpos($blockText, ";") === FALSE) {
					
					$xmlRenderFib = $xmlResponseStr->appendChild($dom->createElement("render_fib"));
					$xmlRenderFib->setAttribute("columns", "0");
					$xmlRenderFib->setAttribute("prompt", "Box");
					$xmlRenderFib->setAttribute("fibtype", "String");
					
					$xmlResprocessing->appendChild ($this->getXMLResprocessing ($gapident, $blockText, 1));
					
				} else {
				
					$xmlRenderChoice = $xmlResponseStr->appendChild($dom->createElement("render_choice"));
					$xmlRenderChoice->setAttribute("shuffle", "No");
					
					foreach (explode(";", $blockText) as $choiceNumber => $choiceText) {	// choices are separated by ";"
						
						$points = "0";
						$choiceText = trim($choiceText);
						if ($choiceText[0] == "~") {		// correct choice(s) is/are prefixed with ~
							$points = "1";
							$choiceText = substr($choiceText, 1);	// remove ~-prefix
						}
						
						$xmlResponseLabel = $xmlRenderChoice->appendChild($dom->createElement("response_label"));
						$xmlResponseLabel->setAttribute("ident", $choiceNumber);
						($xmlResponseLabel->appendChild($dom->createElement("material")))->appendChild($dom->createElement("mattext", $choiceText));
						
						$xmlResprocessing->appendChild ($this->getXMLResprocessing ($gapident, $choiceText, $points));
					}
				}
			}
		}
		
		return $xmlItem;
	}

}
?>