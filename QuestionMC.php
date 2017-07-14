<?php

class QuestionMC extends Question {



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
		
		$blockText = explode("|", $this->text);
		$xmlMattext = ($xmlFlow->appendChild($qti->createElement("material")))->appendChild($qti->createElement("mattext", $blockText[0]));
		$xmlMattext->setAttribute("texttype", "text/plain");
		
				$xmlResponseLid = $xmlFlow->appendChild($qti->createElement("response_lid"));
				$xmlResponseLid->setAttribute("ident", "MCMR");
				$xmlResponseLid->setAttribute("rcardinality", "Multiple");
				
				$xmlRenderChoice = $xmlResponseLid->appendChild($qti->createElement("render_choice"));
				$xmlRenderChoice->setAttribute("shuffle", "No");
				
				foreach (explode(";", $blockText[1]) as $choiceNumber => $choiceText) {	// choices are separated by ";"
					
					$points = "0";
					$choiceText = trim($choiceText);
					if ($choiceText[0] == "~") {		// correct choice(s) is/are prefixed with ~
						$points = "1";
						$choiceText = substr($choiceText, 1);	// remove *-prefix
					}
					
					$xmlResponseLabel = $xmlRenderChoice->appendChild($qti->createElement("response_label"));
					$xmlResponseLabel->setAttribute("ident", $choiceNumber);
					$xmlMattext = ($xmlResponseLabel->appendChild($qti->createElement("material")))->appendChild($qti->createElement("mattext", $choiceText));
					$xmlMattext->setAttribute("texttype", "text/plain");
					
					$xmlRespCondition = $xmlResprocessing->appendChild($qti->createElement("respcondition"));
					$xmlRespCondition->setAttribute("continue", "Yes");
					$xmlVarEqual = ($xmlRespCondition->appendChild($qti->createElement("conditionvar")))->appendChild($qti->createElement("varequal", $choiceNumber));
					$xmlVarEqual->setAttribute("respident", "MCMR");
					
					$xmlSetVar = $xmlRespCondition->appendChild($qti->createElement("setvar", $points));
					$xmlSetVar->setAttribute("action", "Add");
				}
		
		return $xmlItem;
		
	}
}

