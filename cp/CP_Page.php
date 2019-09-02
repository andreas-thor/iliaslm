<?php 

class CP_Page {

	
	private $pageHTML;
	
	public function __construct(string $pageidentifier, array $page) {
		
		
		$this->pageHTML = file_get_contents('templates/page.html');
		$this->pageHTML = str_replace('###TITLE###', $page['title'], $this->pageHTML);
		$this->pageHTML = str_replace('###PAGEID###', $pageidentifier, $this->pageHTML);
		
	}
	
	
	public function getHTMLAsString (): string {
		return $this->pageHTML;
	}
	
}


?>