<?php 

require_once 'CP_Question.php';

class CP_QuestionGap extends CP_Question{
	
	
	
	public function __construct (int $qid, array $question) {

		parent::__construct($qid);
		
		$this->json = [
			'type' => 'gap',
			'blocks' => []
		];
		
		foreach (explode("|", $question["text"]) as $blockNumber => $blockText) {
		
			if (($blockNumber % 2) == 0) { // regular text
				$this->json['blocks'][$blockNumber] = [
					'type' => 'text',
					'text' => str_replace("\n", '<br/>', $blockText)
				];
			
			} else { // list of choices 
				
				$choices = [];
				// choices are separated by " ;"
				// must have a leading white space --> allows HTML entities such as &gt; to appear in the text
				foreach (explode(" ;", $blockText) as $choiceNumber => $choiceText) {	
					
					$points = "0";
					$choiceText = trim($choiceText);
					if ($choiceText[0] == "~") {		// correct choice(s) is/are prefixed with ~
						$points = "1";
						$choiceText = substr($choiceText, 1);	// remove ~-prefix
					}
				
					$choices[$choiceNumber] = [
						'value' => $points,
						'text' => $choiceText
					];
				}
				
				$this->json['blocks'][$blockNumber] = [
					'type' => 'gap',
					'choices' => $choices
				];
				
				
			}
		}
	}
	
	

}

?>