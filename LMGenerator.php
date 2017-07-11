<?php 

// error_reporting(E_ERROR);


$jsonFile = "lm.json";
$lmid = 234;
$url = "http://www1.hft-leipzig.de/thor/dbs/";

$media = [
    "image" => [
        "caption" => "Skriptfolie", 
        "format" => "image/jpeg", 
        "ext" => "jpg"
        
    ], 
    "video" => [
        "caption" => "Lernvideo", 
        "format" => "video/mp4",
        "ext" => "mp4"
    ]
];



printf ("Reading json file %s\n", $jsonFile);
$content = file_get_contents($jsonFile); 
if ($content === false) {
    printf ("Could not read file %s\n", $jsonFile);
    exit;
}
$json = json_decode ($content, TRUE);




$dom = DOMDocument::loadXML('<?xml version="1.0" encoding="utf-8"?><!DOCTYPE ContentObject SYSTEM "http://www.ilias.de/download/dtd/ilias_co_3_7.dtd"><ContentObject Type="LearningModule"></ContentObject>');
$qti = DOMDocument::loadXML('<?xml version="1.0" encoding="utf-8"?><!DOCTYPE questestinterop SYSTEM "ims_qtiasiv1p2p1.dtd"><questestinterop></questestinterop>');

$dom->documentElement->appendChild (createMetadata($lmid, $json['title'], "created " . date("Y-m-d H:i:s")));



foreach ($json['chapter'] as $chap) {
    $dom->documentElement->appendChild (createStructureObject ($chap));
}

foreach ($json['chapter'] as $chap) {
    foreach ($chap['page'] as $page) {
        $dom->documentElement->appendChild (createPageObject ($chap, $page));
        
        if (isset($page["question"])) {
            foreach ($page["question"] as $nr => $question) {
                $qti->documentElement->appendChild (createItem ($chap["name"] . "_" . $page["name"] . "_" . $nr, $question));
            }
        }
        
    }
}

foreach ($json['chapter'] as $chap) {
    foreach ($chap['page'] as $page) {
        foreach (createMediaObjects ($chap, $page) as $mo) {
            $dom->documentElement->appendChild ($mo);
        }
    }
}


$dom->documentElement->appendChild (createProperties());



$xmlName = sprintf ('dbs_lm_%d', $lmid); 

$zip = new ZipArchive();
if ($zip->open(sprintf('ilias/Vorlage/%s.zip', $xmlName), ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) != TRUE) {
    print ("\n\nErr");
}
$zip->addFromString (sprintf ('%1$s/%1$s.xml', $xmlName), $dom->saveXML() );
$zip->addEmptyDir ( sprintf ('%1$s/objects', $xmlName));
if ($qti->documentElement->hasChildNodes()) {
    $zip->addFromString (sprintf ('%1$s/qti.xml', $xmlName), $qti->saveXML() );
}
$zip->close();




function createMetadata (string $id, string $title, string $description = "") {

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



function createStructureObject ($chap) {
    global $dom;
    
    printf ("Create Chapter %s\n", $chap["name"]);
    
    $xmlStructureObject = $dom->createElement ("StructureObject");
    $xmlStructureObject->appendChild (createMetadata ($chap["name"], $chap["title"]));
    
    
    foreach ($chap['page'] as $page) {
        
        printf ("Create Page %s\n", $page["name"]);
        
        $xmlPageAlias = $dom->createElement ("PageAlias");
        $xmlPageAlias->setAttribute ("OriginId", $chap["name"] . "_" . $page["name"]);
        $xmlPageObject = $dom->createElement ("PageObject");
        $xmlPageObject->appendChild ($xmlPageAlias);
        $xmlStructureObject->appendChild ($xmlPageObject);
    }
    
    return $xmlStructureObject;
}


function createPageObject ($chap, $page) {
    
    global $dom, $media;
    
    $xmlPageObject = $dom->createElement ("PageObject");
    $xmlPageObject->appendChild (createMetadata ($chap["name"] . "_" . $page["name"], $page["title"]));
    
    $xmlPageContent = $xmlPageObject->appendChild ($dom->createElement ("PageContent"));
    
    $xmlTabs = $xmlPageContent->appendChild ($dom->createElement ("Tabs"));
    $xmlTabs->setAttribute ("Type", "VerticalAccordion");
    $xmlTabs->setAttribute ("HorizontalAlign", "Center");
    $xmlTabs->setAttribute ("Behavior", "FirstOpen");
    
    foreach ($media as $type => $typeinfo) {
        
        $xmlTab = $xmlTabs->appendChild ($dom->createElement ("Tab"));
        $xmlMediaObject = ($xmlTab->appendChild ($dom->createElement ("PageContent")))->appendChild ($dom->createElement ("MediaObject"));
        
        $xmlMediaAlias = $xmlMediaObject->appendChild ($dom->createElement ("MediaAlias"));
        $xmlMediaAlias->setAttribute ("OriginId", $chap["name"] . "_" . $page["name"] . "_" . $type);
        $xmlMediaAliasItem = $xmlMediaObject->appendChild ($dom->createElement ("MediaAliasItem"));
        $xmlMediaAliasItem->setAttribute ("Purpose", "Standard");
        $xmlLayout = $xmlMediaAliasItem->appendChild ($dom->createElement ("Layout"));
        $xmlLayout->setAttribute ("HorizontalAlign", "Left");
        
        $xmlTab->appendChild ($dom->createElement ("TabCaption", $typeinfo["caption"]));
    }
    
    
    if (isset($page["question"])) {
        foreach ($page["question"] as $nr => $question) {
            $xmlPageContent = $xmlPageObject->appendChild ($dom->createElement ("PageContent"));
            $xmlQuestion = $xmlPageContent->appendChild ($dom->createElement ("Question"));
            $xmlQuestion->setAttribute ("QRef", $chap["name"] . "_" . $page["name"] . "_" . $nr);
        }
    }
    
    
    return $xmlPageObject;
}



function createMediaObjects ($chap, $page) {
  
    global $dom, $media, $url;
    
    $xmlMediaObjects = [];
    
    foreach ($media as $type => $typeinfo) {
        $xmlMediaObject = $dom->createElement ("MediaObject");
        $xmlMediaObject->appendChild (createMetadata ($chap["name"] . "_" . $page["name"] . "_" . $type, $url . $chap["name"] . "_" . $page["name"] . "." . $typeinfo["ext"], $typeinfo["format"]));
        
        $xmlMediaItem = $xmlMediaObject->appendChild ($dom->createElement ("MediaItem"));
        $xmlMediaItem->setAttribute ("Purpose", "Standard");
    
        $xmlLocation = $xmlMediaItem->appendChild ($dom->createElement ("Location", $url . $chap["name"] . "_" . $page["name"] . "." . $typeinfo["ext"]));
        $xmlLocation->setAttribute ("Type", "Reference");
        $xmlMediaItem->appendChild ($dom->createElement ("Format", $typeinfo["format"]));
        $xmlLayout = $xmlMediaItem->appendChild ($dom->createElement ("Layout"));
        $xmlLayout->setAttribute ("Width", "640");
        $xmlLayout->setAttribute ("Height", "480");
        $xmlLayout->setAttribute ("HorizontalAlign", "Left");
        
        array_push ($xmlMediaObjects, $xmlMediaObject);
    
    }
    
    return $xmlMediaObjects;
    
}



function createItem ($id, $question) {

    global $qti;
    
    $xmlItem = $qti->createElement ("item");
    $xmlItem->setAttribute ("ident", $id);
    $xmlItem->setAttribute ("maxattempts", "3");
    $xmlItem->setAttribute ("title", "Titel");
    $xmlPresentation = $xmlItem->appendChild ($qti->createElement ("presentation"));
    $xmlPresentation->setAttribute ("label", "Titel");
    $xmlFlow = $xmlPresentation->appendChild ($qti->createElement ("flow"));
    
    $xmlResprocessing = $xmlItem->appendChild ($qti->createElement ("resprocessing"));
    ($xmlResprocessing->appendChild ($qti->createElement ("outcomes")))->appendChild ($qti->createElement ("decvar"));

    $xmlMattext = ($xmlFlow->appendChild ($qti->createElement ("material")))->appendChild ($qti->createElement ("mattext", " "));
    $xmlMattext->setAttribute ("texttype", "text/plain");
    
    foreach (explode ("|", $question["text"]) as $key => $val) {
        
        if (($key%2)==0) {  // regular text
            $xmlMattext = ($xmlFlow->appendChild ($qti->createElement ("material")))->appendChild ($qti->createElement ("mattext", $val));
            $xmlMattext->setAttribute ("texttype", "text/plain");
        } else {    // gap
            
            $xmlResponseStr = $xmlFlow->appendChild ($qti->createElement ("response_str"));
            $xmlResponseStr->setAttribute ("ident", "gap_" . (($key-1)/2));
            $xmlResponseStr->setAttribute ("rcardinality", "Single");
            
            $xmlRenderChoice = $xmlResponseStr->appendChild ($qti->createElement ("render_choice"));
            $xmlRenderChoice->setAttribute ("shuffle", "No");
            
            foreach (explode (";", $val) as $ident => $text) {
                
                $points = "0";
                $text = trim ($text);
                if ($text[0]=="*") {
                    $points = "1";
                    $text = substr ($text, 1);
                }
                
                $xmlResponseLabel = $xmlRenderChoice->appendChild ($qti->createElement ("response_label"));
                $xmlResponseLabel->setAttribute ("ident", $ident);
                ($xmlResponseLabel->appendChild ($qti->createElement ("material")))->appendChild ($qti->createElement ("mattext", $text));
                
                $xmlRespCondition = $xmlResprocessing->appendChild ($qti->createElement ("respcondition"));
                $xmlRespCondition->setAttribute ("continue", "Yes");
                $xmlVarEqual = ($xmlRespCondition->appendChild ($qti->createElement ("conditionvar")))->appendChild ($qti->createElement ("varequal", $text));
                $xmlVarEqual->setAttribute ("respident", "gap_" . (($key-1)/2));
                
                $xmlSetVar = $xmlRespCondition->appendChild ($qti->createElement ("setvar", $points));
                $xmlSetVar->setAttribute ("action", "Add");
            }
            
            
        }
        
        
        
    }
    
    return $xmlItem;
    
}



function createProperties () {
    
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

?>