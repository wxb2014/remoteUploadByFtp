<?php
//************************************************************
require_once 'config.php';
require_once 'UploadFile.class.php';
$config = ['maxSize'=> '1*1024*1024','allowExts' => '','allowTypes' => '','savePath' => './pic/','saveRule' => 'uniqid'];
$upload = new UploadFile($config);
$upload->thumb = true;
$upload->imageClassPath = 'Image2.php';
$upload->thumbMaxWidth  = '100';
$upload->thumbMaxHeight = '100';
var_dump($upload->upload());
var_dump($upload->getUploadFileInfo());