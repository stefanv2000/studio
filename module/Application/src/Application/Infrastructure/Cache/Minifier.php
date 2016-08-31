<?php
/**
 * author stefanvalea@gmail.com
 */

namespace Application\Infrastructure\Cache;


class Minifier {

    public static function minifyJS($arrayListFiles,$filename){
        $minifiedJS = "";
        for ($i=0;$i<count($arrayListFiles);$i++){
            //$minifiedJS.=\JSMinPlus::minify(file_get_contents($arrayListFiles[$i])).";\n";
            $minifiedJS.=file_get_contents($arrayListFiles[$i]).";\n";
            //echo $arrayListFiles[$i]."<br>";flush();
        }

        file_put_contents($filename,$minifiedJS);
    }


    public static function minifyCSS($arrayListFiles,$filename){
        $minifiedCSS = "";
        for ($i=0;$i<count($arrayListFiles);$i++){
            $minifiedCSS.=\CssMin::minify(file_get_contents($arrayListFiles[$i]));
            //$minifiedCSS.=file_get_contents($arrayListFiles[$i])."\n";
            //echo $arrayListFiles[$i]."<br>";flush();
        }

        file_put_contents($filename,$minifiedCSS);
    }
}