<?php
/*
*远程上传工厂类*
*/
class remoteUploadFactory {
	static $_self = null;
	private function __construct(){
	}

	static function getInstance($type){
		require_once  $type.'.class.php';
		if(!self::$_self[$type]){
			self::$_self[$type] = new $type;
		}
		return self::$_self[$type];
	}
}