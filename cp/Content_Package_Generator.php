<?php

require_once 'CP_Manifest.php';
require_once 'CP_PageSlide.php';
require_once 'CP_PageOverview.php';


$url = "https://www1.hft-leipzig.de/thor/dbs/"; // global URL that has all the media



// load data from json file
$content = json_decode(file_get_contents('../lm.json'), TRUE);


$tmpDir = 'tmp/' . time();

foreach ($content['chapter'] as $chapter) {
	
	// set directory for chapter
	$directory =  $tmpDir . '/' . $chapter['name'];
	if (! is_dir($directory)) {
		mkdir($directory, 0755, true);
	}

	
	// create manifest and pages
	$manifest = new CP_Manifest($chapter['title']);
	foreach ($chapter['page'] as $page) {
		
		if ($page['name'] == 'overview') {
			$pageidentifier = $chapter['name'];
			$pageObj = new CP_PageOverview ($pageidentifier, $page);
		} else {
			$pageidentifier = $chapter['name'] . '_' . $page['name'];
			$pageObj = new CP_PageSlide($pageidentifier, $page);
		}
		
		
		$manifest->addPage($pageidentifier, $page['title']);
		file_put_contents($directory . '/' . $pageidentifier . '.html', $pageObj->getHTMLAsString());
		
		
	}
	file_put_contents($directory . '/imsmanifest.xml', $manifest->getXMLAsString());
	

	// copy static files (css, js, etc.) and zip
	recurseCopy('skeleton_only', $directory);
	zipDirectory($directory, $directory . '.zip');
}



function zipDirectory($directory, $zipName) {
	
	// Get real path for our folder
	$rootPath = realpath($directory);
	
	// Initialize archive object
	$zip = new ZipArchive();
	$zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE);
	
	// Create recursive directory iterator
	/** @var SplFileInfo[] $files */
	$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::LEAVES_ONLY);
	
	foreach ($files as $name => $file) {
		// Skip directories (they would be added automatically)
		if (! $file->isDir()) {
			// Get real and relative path for current file
			$filePath = $file->getRealPath();
			$relativePath = substr($filePath, strlen($rootPath) + 1);
			
			// Add current file to archive
			$zip->addFile($filePath, $relativePath);
		}
	}
	
	// Zip archive will be created only after closing object
	$zip->close();
}



function recurseCopy($src, $dst) {
	$dir = opendir($src);
	if (! is_dir($dst))
		mkdir($dst);
	while (false !== ($file = readdir($dir))) {
		if (($file != '.') && ($file != '..')) {
			if (is_dir($src . '/' . $file)) {
				recurseCopy($src . '/' . $file, $dst . '/' . $file);
			} else {
				copy($src . '/' . $file, $dst . '/' . $file);
			}
		}
	}
	closedir($dir);
}

?>