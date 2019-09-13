<?php 

require_once 'CP_Question.php';

class CP_QuestionMC extends CP_Question { 
	
	

	
	public function __construct (int $qid, array $question) {

		parent::__construct($qid);
		
		global $url;
		list ($questionText, $answerChoices) = explode("|", str_replace("[URL]", $url, $question["text"]), 2);

		$this->qid = $qid;
		$this->json = [
			'type' => 'mc',
			'text' => $questionText,
			'answers' => []
		];
		
		foreach (explode(";", $answerChoices) as $choiceNumber => $choiceText) {	// choices are separated by ";"
			
			$points = 0;
			$choiceText = trim($choiceText);
			if ($choiceText[0] == "~") {		// correct choice(s) is/are prefixed with ~
				$points = 1;
				$choiceText = substr($choiceText, 1);	// remove ~-prefix
			}
			
			$this->json['answers'][$choiceNumber] = [
				'text' => $choiceText,
				'value' => $points, 
			];
		}
		
	}

}

?>