<?php 

class CP_QuestionGap {
	
	
	private $questionHTML;
	
	public function __construct (int $qid, array $question) {

		global $url;
		$qid += 220;	// for some reasons, id with low values (0, 1, etc) do not work
		
		$json = [
			'id' => $qid,
			'type' => 'assClozeTest',
			'title' => 'Titel',
			'question' => '',
			'nr_of_tries' => 3,
			'shuffle' => false,
			'feedback' => [ 'onenotcorrect' => '', 'allcorrect' => '' ],
			'mobs' => [],
			'gaps' => []
		];
		
		foreach (explode("|", str_replace("[URL]", $url, $question["text"])) as $blockNumber => $blockText) {
		
			if (($blockNumber % 2) == 0) { // regular text
				$json['question'] .= $blockText;
			} else { // gap 
				$json['question'] .= '[gap]' . str_replace(';', ',', $blockText) . '[/gap]';
				
				$gap =[
					'shuffle' => false,
					'type' => 1,	// other types are input fields (numeric:0, string:2)
					'item' => []
				];
				
				foreach (explode(";", $blockText) as $choiceNumber => $choiceText) {	// choices are separated by ";"
					
					$points = "0";
					$choiceText = trim($choiceText);
					if ($choiceText[0] == "~") {		// correct choice(s) is/are prefixed with ~
						$points = "1";
						$choiceText = substr($choiceText, 1);	// remove ~-prefix
					}
				
					$gap['item'][$choiceNumber] = [
						'points' => $points,
						'value' => $choiceText,
						'order' => $choiceNumber
					];
				}
				
				$json['gaps'][($blockNumber-1)/2] = $gap;
			}
			
		
		}
		
		// make line breaks in HTML
		$json['question'] = str_replace("\n", '<br/>', $json['question']);

		$this->questionHTML = file_get_contents('templates/questionGap.html');
		$this->questionHTML = str_replace('###QUESTIONID###', $qid, $this->questionHTML);
		$this->questionHTML = str_replace('###QUESTIONJSON###', json_encode($json), $this->questionHTML);
		
		
		
	}
	
	
	public function getHTMLAsString (): string {
		return $this->questionHTML;
		
	}
	
}

?>