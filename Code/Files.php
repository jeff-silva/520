<?php namespace Code;

class Files
{

	public function fileList($dir=null)
	{
		$dir = $dir? $dir: ABSPATH;
		$dir = is_dir($dir)? $dir: dirname($dir);
		$dir = rtrim(realpath($dir), '/');

		$files = array();
		foreach(glob("{$dir}/*") as $file) {
			$files[] = $this->fileInfo($file);
		}

		return $files;
	}



	function formatBytes($size)
	{
		$base = log($size, 1024);
		$suffixes = array('', 'KB', 'MB', 'GB', 'TB');
		return round(pow(1024, $base - floor($base)), 0) . $suffixes[floor($base)];
	}



	function fileInfo($file)
	{
		$info = array_merge(array(
			'dirname' => null,
			'basename' => null,
			'extension' => null,
			'filename' => null,
		), pathinfo($file));

		$info['extension'] = strtolower($info['extension']);
		$info['is_dir'] = is_dir($file);

		// Extension icon
		if (in_array($info['extension'], array('jpg', 'jpeg', 'png', 'bmp', 'gif'))) {
			$info['icon'] = "fa fa-fw fa-file-video-o";
		}
		else if (in_array($info['extension'], array('mp3'))) {
			$info['icon'] = "fa fa-fw fa-file-audio-o";
		}
		else if (in_array($info['extension'], array('mp4'))) {
			$info['icon'] = "fa fa-fw fa-file-video-o";
		}
		else if (in_array($info['extension'], array('php', 'html', 'js', 'css'))) {
			$info['icon'] = "fa fa-fw fa-file-code-o";
		}
		else if (in_array($info['extension'], array('zip', 'rar', 'tar'))) {
			$info['icon'] = "fa fa-fw fa-file-archive-o";
		}
		else {
			$info['icon'] = "fa fa-fw fa-file";
		}


		$info['size_str'] = $this->formatBytes($size = filesize($file));
		$info['size'] = $size;
		$info['file'] = $file;
		return $info;
	}


	public function apiFileList()
	{
		return $this->fileList();
	}


	public function apiFileContent()
	{
		$file = isset($_GET['file'])? $_GET['file']: null;
		if ($file = realpath($file)) {
			$info = $this->fileInfo($file);
			$info['content'] = base64_encode(file_get_contents($file));
			return $info;
		}
		else {
			throw new \Exception('Arquivo não existe');
		}
	}

}