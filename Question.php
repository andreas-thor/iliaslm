<?php

abstract class Question {

	protected $id;

	protected $text;



	public function __construct(string $pageId, $pos, $question) {
		global $url;
		
		$this->id = $pageId . "_" . $pos;
		$this->text = str_replace("[URL]", $url, $question["text"]);
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