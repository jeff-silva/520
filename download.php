<?php

set_time_limit(0);
include __DIR__ . '/classes/PclZip.php';
$filename = 'download.zip';


function dirlist($dir, $files=array()) {
	foreach(glob("{$dir}/*") as $file) {
		if (is_dir($file)) $files = dirlist($file);
		$files[] = str_replace(__DIR__.'/', '', $file);
	}
	return $files;
}



$files = dirlist(__DIR__);
foreach($files as $i=>$file) {
	if ($file=='download.zip') {
		unset($files[$i]);
	}
}

if (file_exists($filename)) unlink($filename);
$archive = new PclZip($filename);
$list = $archive->create(implode(',', $files));


header('Content-Description: File Transfer');
header("Content-Disposition: attachment; filename={$filename}");
header('Content-Type: application/octet-stream');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($filename));
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Expires: 0');
readfile($filename);
