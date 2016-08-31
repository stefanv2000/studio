<?php
/**
 * author stefanvalea@gmail.com
 */

namespace Application\View\Helper;


use DirectoryIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Zend\View\Helper\AbstractHelper;



class TemplatesPacker extends AbstractHelper {
    private $cachedPath="cache/templates/";
    public function __invoke($path,$prefix) {
        if (!file_exists($path)) return;
        $filename = $prefix.md5($this->lastDate($path)).".js";
        //$this->createCachedFile($filename,$path);
        if (!file_exists($this->cachedPath.$filename)) {
            $mask = $this->cachedPath.$prefix.'*.js';
            array_map('unlink', glob($mask));
            $this->createCachedFile($filename,$path);
        }
        return "<script type=\"text/javascript\" src=\"/".$this->cachedPath.$filename."\"></script>";
        //include $this->cachedPath.$filename;
    }

    private function lastDate($path){
        $recursiveIterator =new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path),RecursiveIteratorIterator::SELF_FIRST);
        $maxTime = -1;
        /** @var \RecursiveDirectoryIterator $file */
        foreach($recursiveIterator as $file) {

            if ($file->isFile()&&($file->getExtension()==="html")){
                $modifiedTime =  $file->getMTime();
                if ($modifiedTime > $maxTime) $maxTime = $modifiedTime;
            }
        }
        return $maxTime;
    }

    private function createCachedFile($filename,$path){
        $exportFile = $this->cachedPath.$filename;
        $filehandle = fopen($exportFile,"w");
        fwrite($filehandle,"var arrayTemplates = new Array();");
        $this->scanFolder($path,$filehandle,"template-");
        fclose($filehandle);
        return;
        $recursiveIterator =new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path),RecursiveIteratorIterator::SELF_FIRST);
        /** @var \RecursiveDirectoryIterator $file */
        foreach($recursiveIterator as $name => $file) {

            if ($file->isFile()&&($file->getExtension()==="html")){
                //echo $file->getPath()."<br >\n";
                echo $name."<br >\n";

            }
        }
    }

    private function scanFolder($folderPath, $fileWriteHandle,$prefix){
        $iterator = new DirectoryIterator($folderPath);
        /** @var DirectoryIterator $file */
        foreach($iterator as $file) {

            if ($file->isFile()&&($file->getExtension()==="html")){
                $template = "\n"."arrayTemplates['".$prefix.$file->getBasename(".html")."']=function(){ return ".
                    json_encode(file_get_contents($file->getRealPath())).
                    "};";
                fwrite($fileWriteHandle,$template);
                //$file->getBasename(".html")
            }

            if ($file->isDir()&&!$file->isDot()){
                $this->scanFolder($file->getRealPath(),$fileWriteHandle,$prefix.$file->getBasename().'-');
            }
        }

    }

    private function scanFolder1($folderPath, $fileWriteHandle,$prefix){
        $iterator = new DirectoryIterator($folderPath);
        /** @var DirectoryIterator $file */
        foreach($iterator as $file) {

            if ($file->isFile()&&($file->getExtension()==="html")){
                $template = "\n<script type=\"text/x-tmpl\" id=\"".$prefix.$file->getBasename(".html")."\">\n".
                file_get_contents($file->getRealPath()).
                "\n</script>\n";
                fwrite($fileWriteHandle,$template);
                //$file->getBasename(".html")
            }

            if ($file->isDir()&&!$file->isDot()){
                $this->scanFolder($file->getRealPath(),$fileWriteHandle,$prefix.$file->getBasename().'-');
            }
        }

    }
}