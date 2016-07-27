<?php
class Mime {
	
	private $mime_types = array (
		'html'=>'text/html',
		'htm'=>'text/html',
		'shtml'=>'text/html',
		'css'=>'text/css',
		'xml'=>'text/xml',
		'gif'=>'image/gif',
		'jpeg'=>'image/jpeg',
		'jpg'=>'image/jpeg',
		'js'=>'application/x-javascript',
		'atom'=>'application/atom+xml',
		'rss'=>'application/rss+xml',
			
		'mml'=>'text/mathml',
		'txt'=>'text/plain',
		'jad'=>'text/vnd.sun.j2me.app-descriptor',
		'wml'=>'text/vnd.wap.wml',
		'htc'=>'text/x-component',
			
		'png'=>'image/png',
		'tif'=>'image/tiff',
		'tiff'=>'image/tiff',
		'wbmp'=>'image/vnd.wap.wbmp',
		'ico'=>'image/x-icon',
		'jng'=>'image/x-jng',
		'bmp'=>'image/x-ms-bmp',
		'svg'=>'image/svg+xml',
		'svgz'=>'image/svg+xml',
		'webp'=>'image/webp',
			
			
		'jar'=>'application/java-archive',
		'war'=>'application/java-archive',
		'hqx'=>'application/mac-binhex40',
		'doc'=>'application/msword',
		'pdf'=>'application/pdf',
		'ps'=>'application/postscript',
		'eps'=>'application/postscript',
		'ai'=>'application/postscript',
		'rtf'=>'application/rtf',
		'xls'=>'application/vnd.ms-excel',
		'ppt'=>'application/vnd.ms-powerpoint',
		'wmlc'=>'application/vnd.wap.wmlc',
		'kml'=>'application/vnd.google-earth.kml+xml',
		'kmz'=>'application/vnd.google-earth.kmz',
		'7z'=>'application/x-7z-compressed',
		'cco'=>'application/x-cocoa',
		'jardiff'=>'application/x-java-archive-diff',
		'jnlp'=>'application/x-java-jnlp-file',
		'run'=>'application/x-makeself',
		'pl'=>'application/x-perl',
		'pm'=>'application/x-perl',
		'prc'=>'application/x-pilot',
		'pdb'=>'application/x-pilot',
		'rar'=>'application/x-rar-compressed',
		'rpm'=>'application/x-redhat-package-manager',
		'sea'=>'application/x-sea',
		'swf'=>'application/x-shockwave-flash',
		'sit'=>'application/x-stuffit',
		'tcl'=>'application/x-tcl',
		'tk'=>'application/x-tcl',
		'der'=>'application/x-x509-ca-cert',
		'pem'=>'application/x-x509-ca-cert',
		'crt'=>'application/x-x509-ca-cert',
		'xpi'=>'application/x-xpinstall',
		'xhtml'=>'application/xhtml+xml',
		'zip'=>'application/zip',
		
		'bin'=>'application/octet-stream',
		'exe'=>'application/octet-stream',
		'dll'=>'application/octet-stream',
		'deb'=>'application/octet-stream',
		'dmg'=>'application/octet-stream',
		'eot'=>'application/octet-stream',
		'iso'=>'application/octet-stream',
		'img'=>'application/octet-stream',
		'msi'=>'application/octet-stream',
		'msp'=>'application/octet-stream',
		'msm'=>'application/octet-stream',
		
		'mid'=>'audio/midi',
		'midi'=>'audio/midi',
		'kar'=>'audio/midi',
		'mp3'=>'audio/mpeg',
		'ogg'=>'audio/ogg',
		'm4a'=>'audio/x-m4a',
		'ra'=>'audio/x-realaudio',
		
		'3gpp'=>'video/3gpp',
		'3gp'=>'video/3gpp',
		'mp4'=>'video/mp4',
		'mpeg'=>'video/mpeg',
		'mpg'=>'video/mpeg',
		'mov'=>'video/quicktime',
		'webm'=>'video/webm',
		'flv'=>'video/x-flv',
		'm4v'=>'video/x-m4v',
		'mng'=>'video/x-mng',
		'asx'=>'video/x-ms-asf',
		'asf'=>'video/x-ms-asf',
		'wmv'=>'video/x-ms-wmv',
		'avi'=>'video/x-msvideo',
        'mkv'=>'video/mkv'

	);
	
	private function getDetect() {
		if (class_exists('finfo')) {
			return 'finfo';
		} else if (function_exists('mime_content_type')) {
			return 'mime_content_type';
		} else if ( function_exists('exec')) {
			$result = exec('file -ib '.escapeshellarg(__FILE__));
			if ( 0 === strpos($result, 'text/x-php') OR 0 === strpos($result, 'text/x-c++')) {
				return 'linux';
			}
			$result = exec('file -Ib '.escapeshellarg(__FILE__));
			if ( 0 === strpos($result, 'text/x-php') OR 0 === strpos($result, 'text/x-c++')) {
				return 'bsd';
			}
		}
		return 'internal';
	}
	
	public function getType($path) {
		$mime = $this->mime_types;
		$fmime = $this->getDetect();
		switch($fmime) {
			case 'finfo':
				$finfo = finfo_open(FILEINFO_MIME);
				if ($finfo)
					$type = @finfo_file($finfo, $path);
				break;
			case 'mime_content_type':
				$type = mime_content_type($path);
				break;
			case 'linux':
				$type = exec('file -ib '.escapeshellarg($path));
				break;
			case 'bsd':
				$type = exec('file -Ib '.escapeshellarg($path));
				break;
			default:
				$pinfo = pathinfo($path);
				$ext = isset($pinfo['extension']) ? strtolower($pinfo['extension']) : '';
				$type = isset($mime[$ext]) ? $mime[$ext] : 'unkown';
				break;
		}
		$type = explode(';', $type);
		 
		//需要加上这段，因为如果使用mime_content_type函数来获取一个不存在的$path时会返回'application/octet-stream'
		if ($fmime != 'internal' AND $type[0] == 'application/octet-stream') {
			$pinfo = pathinfo($path);
			$ext = isset($pinfo['extension']) ? strtolower($pinfo['extension']) : '';
			if (!empty($ext) AND !empty($mime[$ext])) {
				$type[0] = $mime[$ext];
			}
		}
		 
		return $type[0];
	}
	
}

?>