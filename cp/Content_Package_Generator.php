<?php

require_once 'CP_Manifest.php';
require_once 'CP_Page.php';

$jsonFile = '../lm.json';
$tempDir = 'tmp/' . time() . '/';

$content = json_decode(file_get_contents($jsonFile), TRUE);

foreach ($content['chapter'] as $chapter) {
	
	$directory = $tempDir . $chapter['name'];
	
	if (! is_dir($directory))
		mkdir($directory, 0755, true);
	
	
	$manifest = new CP_Manifest($chapter['title']);
	
	foreach ($chapter['page'] as $page) {
		
		$pageidentifier = $chapter['name'] . '_' . $page['name'];
		
		$manifest->addPage($pageidentifier, $page['title']);
		
// 		$pageHTML = sprintf(file_get_contents('files/page.html'), $page['title'], $page['title'], 'https://www1.hft-leipzig.de/thor/dbs/' . $pageidentifier . '.jpg', 'https://www1.hft-leipzig.de/thor/dbs/' . $pageidentifier . '.mp4');
		
		$pageObj = new CP_Page($pageidentifier, $page);
		

		
		
		file_put_contents($directory . '/' . $pageidentifier . '.html', $pageObj->getHTMLAsString());
	}
	
	file_put_contents($directory . '/imsmanifest.xml', $manifest->getXMLAsString());
	


	// copy static files (css, js, etc.) and zip
	recurseCopy('static_files', $directory);
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