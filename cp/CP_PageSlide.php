<?php 

require_once 'CP_QuestionMC.php';
require_once 'CP_QuestionGap.php';
require_once 'CP_Page.php';

class CP_PageSlide extends  CP_Page {

	
	
	public function __construct(string $pageidentifier, array $page) {
		
		$this->createPageHTML(file_get_contents('templates/pageSlide.html'), $pageidentifier, $page);		
		
		// add questions
		$questionHTML = '';
		
		if (array_key_exists('question', $page)) {
			foreach ($page['question'] as $qid => $question) {
				if ($question['type']=='mc') {
					$questionHTML .= (new CP_QuestionMC($qid, $question))->getHTMLAsString ();
				}
				if ($question['type']=='gap') {
					$questionHTML .= (new CP_QuestionGap($qid, $question))->getHTMLAsString ();
				}
			}
		}
		
		$this->pageHTML = str_replace('###QUESTIONS###', $questionHTML, $this->pageHTML);
		
		
	}
	
	

	
}


?>