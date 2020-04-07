<?php 



class CP_Page {

	
	protected $pageHTML;
	
	protected function createPageHTML (string $template, string $pageidentifier, array $page, array $url) {
		
		$this->pageHTML = $template; 
		$this->pageHTML = str_replace('###TITLE###', $page['title'], $this->pageHTML);
		$this->pageHTML = str_replace('###PAGEID###', $pageidentifier, $this->pageHTML);
		
		$this->pageHTML = str_replace('###SHOW_VIDEO###', isset($page['videoid']) ? 'block' : 'none', $this->pageHTML);
		$this->pageHTML = str_replace('###URL_VIDEO###', sprintf ($url['video'], $page['videoid']), $this->pageHTML);
		$this->pageHTML = str_replace('###URL_PDF###', sprintf ($url['pdf'], $pageidentifier), $this->pageHTML);
		
	}
	
	
	public function getHTMLAsString (): string {
		return $this->pageHTML;
	}
	
}


?>