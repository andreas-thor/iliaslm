<?php 

$jsonFile = '../lm.json';
$tempDir = 'tmp/' . time() . '/';

$content = json_decode (file_get_contents($jsonFile), TRUE);



foreach ($content['chapter'] as $chapter) {
	
	$directory = $tempDir . $chapter['name'];
	
	
	if (!is_dir($directory)) mkdir ($directory, 0755, true);
	
	$dom = DOMDocument::loadXML('
		<manifest identifier="olat_ims_cp_editor_v1" schemaLocation="http://www.imsglobal.org/xsd/imscp_v1p1 imscp_v1p1.xsd http://www.imsglobal.org/xsd/imsmd_v1p2 imsmd_v1p2p2.xsd">
			<metadata/>			
			<organizations>
				<organization identifier="olatcp-99854011956291" structure="hierarchical">
				</organization>
			</organizations>
			<resources>
			</resources>
		</manifest>');
	
	
	$organization = $dom->documentElement->getElementsByTagName('organization')->item(0);
	$organization->appendChild($dom->createElement('title', $chapter['title']));
	
	$resources = $dom->documentElement->getElementsByTagName('resources')->item(0);
	
	foreach ($chapter['page'] as $page) {
		
		$pageidentifier = $chapter['name'] . '_' . $page['name'];
		
		$item = $dom->createElement('item');
		$item->setAttribute('identifier', $pageidentifier);
		$item->setAttribute('identifierref', $pageidentifier .'_ref');
		$item->setAttribute('isvisible', 'true');
		$item->appendChild ($dom->createElement('title', $page['title']));
		$organization->appendChild($item);
		
		$resource = $dom->createElement('resource');
		$resource->setAttribute('identifier', $pageidentifier .'_ref');
		$resource->setAttribute('href', $pageidentifier . '.html');
		$resource->setAttribute('type', 'text/html');
		
		$file = $dom->createElement('file');
		$file->setAttribute('href', $pageidentifier . '.html');
		$resource->appendChild ($file);
		
		$resources->appendChild ($resource);
		
		
		
		$pageHTML = sprintf(file_get_contents('files/page.html'), 			
			$page['title'], 
			$page['title'],
			'https://www1.hft-leipzig.de/thor/dbs/' . $pageidentifier . '.jpg', 
			'https://www1.hft-leipzig.de/thor/dbs/' . $pageidentifier . '.mp4'
			);
		
		file_put_contents ( $directory . '/' . $pageidentifier . '.html' , $pageHTML);
		
		
	}
	
	
	
	file_put_contents ( $directory . '/imsmanifest.xml', $dom->saveXML());
	
// 	foreach (['accordion.js', 'ims_xml.xsd', 'imscp_v1p1.xsd', 'imsmd_v1p2p2.xsd', 'page.css'] as $staticfile) {
// 		copy('files/' . $staticfile , $directory . '/' . $staticfile);
// 	}
	
	recurseCopy('files', $directory);
	
	
	// Get real path for our folder
	echo $rootPath = realpath($directory);
	
	// Initialize archive object
	$zip = new ZipArchive();
	$zip->open($directory . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE );
	
	// Create recursive directory iterator
	/** @var SplFileInfo[] $files */
	$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::LEAVES_ONLY);
	
	foreach ($files as $name => $file)
	{
		// Skip directories (they would be added automatically)
		if (!$file->isDir())
		{
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


function recurseCopy($src, $dst)
{
	$dir = opendir($src);
	if (!is_dir($dst)) mkdir($dst);
	while(false !== ( $file = readdir($dir)) ) {
		if (( $file != '.' ) && ( $file != '..' )) {
			if ( is_dir($src . '/' . $file) ) {
				recurseCopy($src . '/' . $file, $dst . '/' . $file);
			} else {
				copy($src . '/' . $file,$dst . '/' . $file);
			}
		}
	}
	closedir($dir);
}

?>