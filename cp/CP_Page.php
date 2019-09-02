<?php 

require_once 'CP_QuestionMC.php';
require_once 'CP_QuestionGap.php';

class CP_Page {

	
	private $pageHTML;
	
	public function __construct(string $pageidentifier, array $page) {
		
		global $url;
		
		$this->pageHTML = file_get_contents('templates/page.html');
		$this->pageHTML = str_replace('###TITLE###', $page['title'], $this->pageHTML);
		$this->pageHTML = str_replace('###PAGEID###', $pageidentifier, $this->pageHTML);
		$this->pageHTML = str_replace('###URL###', $url, $this->pageHTML);
		
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
	
	
	public function getHTMLAsString (): string {
		return $this->pageHTML;
	}
	
}


?>