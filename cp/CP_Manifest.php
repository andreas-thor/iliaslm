<?php

class CP_Manifest {
	
	
	private $dom;
	private $organization;
	private $resources;
	
	public function __construct($title) {
		
		$this->dom = DOMDocument::loadXML(file_get_contents('templates/imsmanifest.xml'));
		
		$this->organization = $this->dom->documentElement->getElementsByTagName('organization')->item(0);
		$this->organization->appendChild($this->dom->createElement('title', $title));
		
		$this->resources = $this->dom->documentElement->getElementsByTagName('resources')->item(0);
		
		
	}
	
	
	public function addPage ($pageidentifier, $pagetitle) {
		
		$item = $this->dom->createElement('item');
		$item->setAttribute('identifier', $pageidentifier);
		$item->setAttribute('identifierref', $pageidentifier . '_ref');
		$item->setAttribute('isvisible', 'true');
		$item->appendChild($this->dom->createElement('title', $pagetitle));
		$this->organization->appendChild($item);
		
		$resource = $this->dom->createElement('resource');
		$resource->setAttribute('identifier', $pageidentifier . '_ref');
		$resource->setAttribute('href', $pageidentifier . '.html');
		$resource->setAttribute('type', 'text/html');
		
		$file = $this->dom->createElement('file');
		$file->setAttribute('href', $pageidentifier . '.html');
		$resource->appendChild($file);
		
		$this->resources->appendChild($resource);
	}
	
	public function getXMLAsString (): string {
		return $this->dom->saveXML();
	}
	
}
	
?>