<?php
namespace core\DownloadApp;
use \Exception as Exception;

include_once("./DownloaderApp/downloaderCls.php");
ini_set('memory_limit', '10240');

// $URI = "https://www.jerrycala.com/images/jerry/slide/Professione-Enterteiner.jpg";
$URI = "https://www.jerrycala.com/images/jerry/slide/50-anni-di-libidine.jpg";
$bName = basename($URI);
echo "<h1>";
echo "PHP 4 ways to download a file in OOP" . PHP_EOL;
echo "</h1>";

$dwl = new DownloaderPHP($URI, $bName);
echo "URI: " . $dwl->getSourceName() . "<br />";
try{
    $dwl->downloadFopen();
    echo "File name to download: " . $dwl->getOutName();
    echo "<div class='imgDiv'>";
        echo "<img width=100% height=100% src='./img_downloded/[1]" . $dwl->getOutName()  . "'/>";
    echo "</div>";

    $printData = false;
    $dwl->FSO_getRemoteSize($URI, $printData);
    $dwl->getHeadWidthCoreFun($URI);
    $dwl->downloadFSO();

    $dwl->download_curl();
}
catch(Exception $e){
    echo $e->getMessage();
}

try{
    echo "<br />";
    echo "<br />";
    if(!$dwl->check_if_exists($URI)){
        throw new Exception("the resource does not exists!, the link &lt;a&gt; to download the file, is Not Available!");
    }
    echo "<a target='_blank' href='./download_header/headerDwl.php'> download by header function </a>";
}
catch(Exception $e){
    echo $e->getMessage();
}