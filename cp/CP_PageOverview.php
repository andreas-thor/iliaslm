<?php 

require_once 'CP_Page.php';

class CP_PageOverview extends CP_Page {

	
	public function __construct(string $pageidentifier, array $page) {
		$this->createPageHTML(file_get_contents('skeleton_only/pageOverview.html'), $pageidentifier, $page);
		
		$los = "";
		foreach ($page['outcome'] as $id => $outcome) {
			$los .= '<li>' . $outcome . '</li>';
		}
		
		$this->pageHTML = str_replace('###LEARNINGOUTCOMES###', $los, $this->pageHTML);
		
		
	}
	
}


?>