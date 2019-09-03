<?php 



class CP_Page {

	
	protected $pageHTML;
	
	protected function createPageHTML (string $template, string $pageidentifier, array $page) {
		
		global $url;
		
		$this->pageHTML = $template; 
		$this->pageHTML = str_replace('###TITLE###', $page['title'], $this->pageHTML);
		$this->pageHTML = str_replace('###PAGEID###', $pageidentifier, $this->pageHTML);
		$this->pageHTML = str_replace('###URL###', $url, $this->pageHTML);
	}
	
	
	public function getHTMLAsString (): string {
		return $this->pageHTML;
	}
	
}


?>