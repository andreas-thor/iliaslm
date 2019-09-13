<?php 

require_once 'CP_QuestionMC.php';
require_once 'CP_QuestionGap.php';
require_once 'CP_Page.php';

class CP_PageSlide extends  CP_Page {

	
	
	public function __construct(string $pageidentifier, array $page) {
		
		$this->createPageHTML(file_get_contents('skeleton_only/pageSlide.html'), $pageidentifier, $page);		
		
		// add questions
		$questionData = '';
		
		if (array_key_exists('question', $page)) {
			foreach ($page['question'] as $qid => $question) {
				if ($question['type']=='mc') {
					$questionData .= (new CP_QuestionMC($qid, $question))->getJSONString();
				}
				if ($question['type']=='gap') {
					$questionData .= (new CP_QuestionGap($qid, $question))->getJSONString();
				}
			}
		}
		
		$this->pageHTML = str_replace('// ###QUESTIONDATA###', $questionData, $this->pageHTML);
		
		
	}
	
	

	
}


?>