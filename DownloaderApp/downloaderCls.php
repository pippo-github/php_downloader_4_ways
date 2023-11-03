<?php

namespace core\downloadApp;
use \Exception as Exception;

class DownloaderPHP{
    public $source;
    public $output;
    public $in;
    public $out;
    public $dPath;
    function __construct($sFile, $oFile){
        $this->source = $sFile;
        $this->output = $oFile;
        $this->dPath  = "./img_downloded/";
    }

    public function prettyPrint($value){
        echo "<pre>";
            print_r($value);
        echo "</pre>";
    }
    public function getSourceName(){
        return $this->source;
    }

    public function getOutName(){
        if(! file_exists($this->dPath . "[1]" . $this->output) ){
            throw new Exception ("<b><i>The output file: " . $this->dPath . "[1]" . $this->output ." does not exists!</i></b>");
        }
        return $this->output;
    }

    public function downloadFopen(){
        $this->in = @fopen($this->source, "rb");
            if(!$this->in){
                throw new Exception("\n<br/><b>The file: " .  $this->source . " cannot be opened for reading! check your connections." . "</b><br />");
            }
        $this->out = @fopen($this->dPath . "[1]" . $this->output, "wb");
        if(!$this->out){
            die("Unable to create file: " . $this->dPath . "[1]" . $this->output . "\n");
        }

        while(!feof($this->in)) {
            $buffer = fread($this->in, 1024);
            $size = fwrite($this->out, $buffer);
        }

        if($this->in){
            fclose($this->in);
        }

        if($this->out){
            fclose($this->out);
        }
    } 

    public function downloadFSO(){
        $parseUrl = parse_url($this->source);

        $out = @fopen($this->dPath . "[2]" . $this->output, "wb");

        if(!$out){
            throw new Exception("Unable to create in write mode the file: " . $this->dPath . $this->output);
        }

        $scheme = $parseUrl["scheme"];
        $host   = $parseUrl["host"];
        $path   = $parseUrl["path"];

        $scheme = ($scheme === "https") ? "ssl://" : "http://";
        $port   = ($scheme === "ssl://") ? 443 : 80;

        $fd = fsockopen($scheme . $host, $port, $errore, $erroreNum, 30);
        if(!$fd){
            die("Error connecting, fsockfopen: " . error_get_last()["message"]);
        }

        $header = "GET $path HTTP/1.1\r\n";
        $header .= "Host: $host\r\n";
        $header .= "Connection: close\r\n\r\n";

        fwrite($fd, $header);

    // drop out the header additional info, remain just the file contents 
    $flag = 1;
    while($flag){
        $garbage = fgets($fd);
        if(strcmp($garbage, "\r\n") ===0 ){
            $flag = 0;
        }        
    }

        while(!feof($fd)){
            $buffer = fread($fd, 4096);
            fwrite($out, $buffer);
        }

        if($out){
            fclose($out);
        }

    }

function FSO_getRemoteSize($fullUrl, $printData = false) {

        $parsed_url = parse_url($fullUrl);
        $scheme = $parsed_url['scheme'];
        $host  = $parsed_url['host'];
        $path = $parsed_url['path'];
        $port = 443;

        if($printData){
            echo "\n<br /> scheme: $scheme <br />";
            echo "\n<br /> host:   $host <br />";
            echo "\n<br /> path:   $path <br />";
        }

        $cur_scheme = ($scheme === "https")      ? "ssl://" : "http://";
        $cur_port   = ($cur_scheme === "ssl://") ? "443"    : "80";

        $rFd = @fsockopen($cur_scheme . $host, $cur_port,  $errno,  $stderr, 30);
        if(!$rFd){
            throw new Exception("<b><br />URI error, non-existent or incorrect route, please check your internet connection.</b>");
        }
        $out = "HEAD $path HTTP/1.1\r\n";
        $out .= "Host: $host\r\n";
        $out .= "Connection: Close\r\n\r\n";
   
        fputs($rFd, $out);

        $response = '';

        while(!feof($rFd)){
            $buffer = fgets($rFd, 1024);
            $response .= $buffer; 
        }
        
        $head_parsed = explode("\n", $response);
        if($printData){
            $this->prettyPrint($head_parsed);
        }

        $match_result = array_filter($head_parsed,function($elemento_cur){
            return strstr($elemento_cur, "Content-Length");
        });

        $match_result = array_values($match_result);

        $match_result = $match_result[0];
        $retArr = preg_match("/(\w+\-\w+\:\s)(\d+)/", $match_result, $result_match);

        if($printData){
            echo "<br /><br />Fsockopen(), the remote file has size: " . $result_match[2] . "<br />\n";
        }

        fclose($rFd);
        return $result_match[2];
    }


    function getHeadWidthCoreFun($remoteAdd){
        $ret  = get_headers($remoteAdd);

        $match_result = array_filter($ret,function($elemento_cur){
            return strstr($elemento_cur, "Content-Length");
        });

        $match_result = array_values($match_result);

        $match_result = $match_result[0];
        $retArr = preg_match("/(\w+\-\w+\:\s)(\d+)/", $match_result, $result_match);

        echo "get_headers(), content-length obtained via native function, <i> php > 5.2</i>, size: <b>" . $result_match[2] . "</b>"; 
    }

    public function check_if_exists($remoteFile){
       return (@fopen($remoteFile, "r")) ? true :  false;
    }

    public function download_curl(){
        // Create a cURL handle
        $ch    = curl_init($this->source);
        $nFile = basename($this->source);

        // Check if any error occurred
        if (!curl_errno($ch)) {
            $out = @fopen($this->dPath . "[4]" . $nFile, "wb");

            if(!$out){
                throw new Exception ("Not possibile to open the file: " . $this->dPath . "[4]". $nFile . " in write mode!");
            }

            curl_setopt($ch, CURLOPT_FILE, $out);
            curl_exec ($ch);

            if(curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200) {
                echo "<br /><br />curl: The resource exists and has been downloaded. <br />";            
            }
            else{
                throw new Exception("<br />curl: The resource does not  exists.<br />");
            }
    
            curl_close($ch);
            fclose($out);
        }
        else{
            throw new Exception ("init curl: " . error_get_last()["message"]);
        }       
    }


    public function headerDwl(){
                 
            if(!$this->check_if_exists($this->source)){
                $msg  =  "<h3> Error url </h3>";    
                $msg .=  "<i>The URL does not exist!, check and try again.</i>";

                throw new Exception($msg);
            }
                header('Content-Description: File Transfer');
                header('Content-Type: image/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($this->output) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . $this->FSO_getRemoteSize($this->source));
                readfile($this->source);
    }
};