<?php
/**
 * author stefanvalea@gmail.com
 */

namespace Application\Infrastructure\Utils;


class Display {

    public static function formatString($string){
        return ucwords(strtolower($string));
    }
}