<?php

class Question {

	private $id;

	private $type;

	private $text;



	public function __construct(string $pageId, $pos, $question) {
		$this->id = $pageId . "_" . $pos;
		$this->type = $question["type"];
		$this->text = $question["text"];
		printf("    Create Question %s\n", $this->id);
	}



	public function getXMLQuestion() {
		global $dom;
		
		$xmlQuestion = $dom->createElement("Question");
		$xmlQuestion->setAttribute("QRef", $this->id);
		return $xmlQuestion;
	}



	public function getXMLItem() {
		global $qti;
		
		
		// TODO: Other Item Types that "gap"
		
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
		
		foreach (explode("|", $this->text) as $key => $val) {
			
			if (($key % 2) == 0) { // regular text
				$xmlMattext = ($xmlFlow->appendChild($qti->createElement("material")))->appendChild($qti->createElement("mattext", $val));
				$xmlMattext->setAttribute("texttype", "text/plain");
			} else { // gap
				
				$xmlResponseStr = $xmlFlow->appendChild($qti->createElement("response_str"));
				$xmlResponseStr->setAttribute("ident", "gap_" . (($key - 1) / 2));
				$xmlResponseStr->setAttribute("rcardinality", "Single");
				
				$xmlRenderChoice = $xmlResponseStr->appendChild($qti->createElement("render_choice"));
				$xmlRenderChoice->setAttribute("shuffle", "No");
				
				foreach (explode(";", $val) as $ident => $text) {
					
					$points = "0";
					$text = trim($text);
					if ($text[0] == "*") {
						$points = "1";
						$text = substr($text, 1);
					}
					
					$xmlResponseLabel = $xmlRenderChoice->appendChild($qti->createElement("response_label"));
					$xmlResponseLabel->setAttribute("ident", $ident);
					($xmlResponseLabel->appendChild($qti->createElement("material")))->appendChild($qti->createElement("mattext", $text));
					
					$xmlRespCondition = $xmlResprocessing->appendChild($qti->createElement("respcondition"));
					$xmlRespCondition->setAttribute("continue", "Yes");
					$xmlVarEqual = ($xmlRespCondition->appendChild($qti->createElement("conditionvar")))->appendChild($qti->createElement("varequal", $text));
					$xmlVarEqual->setAttribute("respident", "gap_" . (($key - 1) / 2));
					
					$xmlSetVar = $xmlRespCondition->appendChild($qti->createElement("setvar", $points));
					$xmlSetVar->setAttribute("action", "Add");
				}
			}
		}
		
		return $xmlItem;
	}
}
?>