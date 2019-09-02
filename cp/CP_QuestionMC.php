<?php 

class CP_QuestionMC {
	
	
	private $questionHTML;
	
	public function __construct (int $qid, array $question) {

		global $url;
		list ($questionText, $answerChoices) = explode("|", str_replace("[URL]", $url, $question["text"]), 2);
		
		$json = [
			'id' => $qid,
			'type' => 'assMultipleChoice',
			'title' => 'Titel',
			'question' => $questionText,
			'nr_of_tries' => 3,
			'shuffle' => false,
			'feedback' => [ 'onenotcorrect' => '', 'allcorrect' => '' ],
			'mobs' => [], 
			'answers' => []
		];
		
		foreach (explode(";", $answerChoices) as $choiceNumber => $choiceText) {	// choices are separated by ";"
			
			$points = "0";
			$choiceText = trim($choiceText);
			if ($choiceText[0] == "~") {		// correct choice(s) is/are prefixed with ~
				$points = "1";
				$choiceText = substr($choiceText, 1);	// remove ~-prefix
			}
			
			$json['answers'][$choiceNumber] = [
				'answertext' => $choiceText,
				'points_checked' => $points, 
				'points_unchecked' => 0,
				'order' => $choiceNumber, 
				'image' => '',
				'feedback' => ''
			];
		}
		
		
		$this->questionHTML = file_get_contents('templates/questionMC.html');
		$this->questionHTML = str_replace('###QUESTIONID###', $qid, $this->questionHTML);
		$this->questionHTML = str_replace('###QUESTIONJSON###', json_encode($json), $this->questionHTML);
		
		
		
		
	}
	
	
	public function getHTMLAsString (): string {
		return $this->questionHTML;
	}
	
}

?>