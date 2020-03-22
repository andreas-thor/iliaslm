<?php

abstract class Question {

	protected $id;

	public function __construct(string $id) {
		
		$this->id = $id;
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