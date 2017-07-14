<?php

require_once 'Question.php';

class QuestionGap extends Question {




	public function getXMLItem() {
		global $qti;
		
		
		$xmlItem = $qti->createElement("item");
		$xmlItem->setAttribute("ident", $this->id);
		$xmlItem->setAttribute("maxattempts", "3");
		$xmlItem->setAttribute("title", "Titel");
		$xmlPresentation = $xmlItem->appendChild($qti->createElement("presentation"));
		$xmlPresentation->setAttribute("label", "Titel");
		$xmlFlow = $xmlPresentation->appendChild($qti->createElement("flow"));
		
		$xmlResprocessing = $xmlItem->appendChild($qti->createElement("resprocessing"));
		($xmlResprocessing->appendChild($qti->createElement("outcomes")))->appendChild($qti->createElement("decvar"));
		
		$xmlMattext = ($xmlFlow->appendChild($qti->createElement("material")))->appendChild($qti->createElement("mattext", " "));
		$xmlMattext->setAttribute("texttype", "text/plain");
		
		// gaps are separated by | so that text mataches "<some Text>|first gap|<further text>|second gap|<more text>..."
		foreach (explode("|", $this->text) as $blockNumber => $blockText) {
			
			if (($blockNumber % 2) == 0) { // regular text
				$xmlMattext = ($xmlFlow->appendChild($qti->createElement("material")))->appendChild($qti->createElement("mattext", $blockText));
				$xmlMattext->setAttribute("texttype", "text/plain");
			
			} else { // gap: choices are separated by ";"
				
				$gapident = "gap_" . (($blockNumber - 1) / 2);	// gaps are enumerated (starting with 0)
				$xmlResponseStr = $xmlFlow->appendChild($qti->createElement("response_str"));
				$xmlResponseStr->setAttribute("ident", $gapident);
				$xmlResponseStr->setAttribute("rcardinality", "Single");
				
				$xmlRenderChoice = $xmlResponseStr->appendChild($qti->createElement("render_choice"));
				$xmlRenderChoice->setAttribute("shuffle", "No");
				
				foreach (explode(";", $blockText) as $choiceNumber => $choiceText) {	// choices are separated by ";"
					
					$points = "0";
					$choiceText = trim($choiceText);
					if ($choiceText[0] == "~") {		// correct choice(s) is/are prefixed with ~
						$points = "1";
						$choiceText = substr($choiceText, 1);	// remove *-prefix
					}
					
					$xmlResponseLabel = $xmlRenderChoice->appendChild($qti->createElement("response_label"));
					$xmlResponseLabel->setAttribute("ident", $choiceNumber);
					($xmlResponseLabel->appendChild($qti->createElement("material")))->appendChild($qti->createElement("mattext", $choiceText));
					
					$xmlRespCondition = $xmlResprocessing->appendChild($qti->createElement("respcondition"));
					$xmlRespCondition->setAttribute("continue", "Yes");
					$xmlVarEqual = ($xmlRespCondition->appendChild($qti->createElement("conditionvar")))->appendChild($qti->createElement("varequal", $choiceText));
					$xmlVarEqual->setAttribute("respident", $gapident);
					
					$xmlSetVar = $xmlRespCondition->appendChild($qti->createElement("setvar", $points));
					$xmlSetVar->setAttribute("action", "Add");
				}
			}
		}
		
		return $xmlItem;
	}

}
?>