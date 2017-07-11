<?php


class LearningModule {
    
    
    
    public static function getXMLMetadata (string $id, string $title, string $description = "") {
        
        global $dom;
        
        $xmlMetadata = $dom->createElement("MetaData");
        
        $xmlGeneral = $dom->createElement("General");
        $xmlGeneral->setAttribute ("Structure", "Hierarchical");
        $xmlMetadata->appendChild ($xmlGeneral);
        
        $xmlIdentifier = $dom->createElement("Identifier");
        $xmlIdentifier->setAttribute ("Entry", $id);
        $xmlIdentifier->setAttribute ("Catalog", "ILIAS");
        $xmlGeneral->appendChild ($xmlIdentifier);
        
        $xmlTitle = $dom->createElement("Title", $title);
        $xmlTitle->setAttribute ("Language", "de");
        $xmlGeneral->appendChild ($xmlTitle);
        
        $xmlLanguage = $xmlGeneral->appendChild ($dom->createElement("Language"));
        $xmlLanguage->setAttribute ("Language", "de");
        
        $xmlDescription = $xmlGeneral->appendChild ($dom->createElement("Description", $description));
        $xmlDescription->setAttribute ("Language", "de");
        
        $xmlKeyword = $xmlGeneral->appendChild ($dom->createElement("Keyword"));
        $xmlKeyword->setAttribute ("Language", "de");
        
        return $xmlMetadata;
    }
    
    
    public static function getXMLProperties () {
    	
    	global $dom;
    	
    	$xmlProperties = $dom->createElement ("Properties");
    	
    	$prop = array (
    		"Layout" => "toc2win",
    		"PageHeader" => "pg_title",
    		"TOCMode" => "pages",
    		"ActiveLMMenu" => "y",
    		"ActiveNumbering" => "n",
    		"ActiveTOC" => "y",
    		"ActivePrintView" => "y",
    		"CleanFrames" => "n",
    		"PublicNotes" => "n",
    		"HistoryUserComments" => "n",
    		"Rating" => "n",
    		"RatingPages" => "n",
    		"LayoutPerPage" => "0",
    		"ProgressIcons" => "0",
    		"StoreTries" => "0",
    		"RestrictForwardNavigation" => "0",
    		"DisableDefaultFeedback" => "0"
    	);
    	
    	foreach ($prop as $name => $value) {
    		$xmlProperty = $dom->createElement ("Property");
    		$xmlProperty->setAttribute ("Name", $name);
    		$xmlProperty->setAttribute ("Value", $value);
    		$xmlProperties->appendChild ($xmlProperty);
    	}
    	
    	
    	return $xmlProperties;
    }
}


?>