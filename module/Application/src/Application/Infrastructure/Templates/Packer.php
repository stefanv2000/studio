<?php
/**
 * author stefanvalea@gmail.com
 */

namespace Application\Infrastructure\Templates;


use DirectoryIterator;

class Packer {

    private $cachedPath="cache/templates/";



    public function createCachedFile($filename,$path){
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
}