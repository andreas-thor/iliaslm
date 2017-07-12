<?php

abstract class Question {

	protected $id;

	protected $text;



	public function __construct(string $pageId, $pos, $question) {
		$this->id = $pageId . "_" . $pos;
		$this->text = $question["text"];
		printf("    Create %s %s\n", get_class($this), $this->id);
		
	}



	public function getXMLQuestion() {
		global $dom;
		
		$xmlQuestion = $dom->createElement("Question");
		$xmlQuestion->setAttribute("QRef", $this->id);
		return $xmlQuestion;
	}


	abstract public function getXMLItem();
	
}
?>