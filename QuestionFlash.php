<?php

require_once 'Question.php';

class QuestionFlash extends Question {

	private $question;
	
	public function __construct(string $id, $json) {
		
		parent::__construct($id);
		
		global $url;
		$this->question = str_replace("[URL]", $url, $json["question"]);
	}

	
	
	private function getXMLQTIMetaDataField (string $label, string $entry) {
		
		global $dom;
		$result = $dom->createElement("qtimetadatafield");
		$result->appendChild($dom->createElement("fieldlabel", $label));
		$result->appendChild($dom->createElement("fieldentry", $entry));
		return $result;
	}
	
	
	public function getXMLItem() {
		
		global $dom;
		$taskId = "bibliothek:" . $this->id;
		
		$xmlItem = $dom->createElement("item");
		$xmlItem->setAttribute("ident", $this->id);
		$xmlItem->setAttribute("maxattempts", "1");
		$xmlItem->setAttribute("title", $taskId);
		
		
		$xmlItemMetadata =  $xmlItem->appendChild($dom->createElement("itemmetadata"));
		$xmlQTIMetadata =  $xmlItemMetadata->appendChild($dom->createElement("qtimetadata"));

		
		$xmlQTIMetadata->appendChild ($this->getXMLQTIMetaDataField("QUESTIONTYPE", "assFlashQuestion"));
		$xmlQTIMetadata->appendChild ($this->getXMLQTIMetaDataField("points", "1"));
		$xmlQTIMetadata->appendChild ($this->getXMLQTIMetaDataField("width", "700"));
		$xmlQTIMetadata->appendChild ($this->getXMLQTIMetaDataField("height", "500"));
		$xmlQTIMetadata->appendChild ($this->getXMLQTIMetaDataField("applet", "dmt.swf"));
		$xmlQTIMetadata->appendChild ($this->getXMLQTIMetaDataField("swf", base64_encode(file_get_contents("E:/Dev/DMT/client/flash/bin/dmt.swf"))));
		$xmlQTIMetadata->appendChild ($this->getXMLQTIMetaDataField("params", "a:1:{s:6:\"taskid\";s:" . strlen($taskId) . ":\"" . $taskId . "\";}"));
		
		
		$xmlPresentation = $xmlItem->appendChild($dom->createElement("presentation"));
		$xmlPresentation->setAttribute("label", "Titel");
		$xmlFlow = $xmlPresentation->appendChild($dom->createElement("flow"));
		$xmlMattext = ($xmlFlow->appendChild($dom->createElement("material")))->appendChild($dom->createElement("mattext", $this->question));
		$xmlMattext->setAttribute("texttype", "text/xhtml");
		
		
		
		
		
		return $xmlItem;
		
		
	}
}

