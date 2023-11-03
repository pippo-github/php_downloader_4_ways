<?php
namespace core\downloadApp;
use \Exception as Exception;

include_once("../namespaceDir/downloaderCls.php");

$inputSource  = "https://www.jerrycala.com/images/jerry/slide/50-anni-di-libidine.jpg";
$outputSource = basename($inputSource);
$dwl = new DownloaderPHP($inputSource, "[3]" . $outputSource);
try{
    $dwl->headerDwl();
}
catch(Exception $e){
    echo $e->getMessage();
}
