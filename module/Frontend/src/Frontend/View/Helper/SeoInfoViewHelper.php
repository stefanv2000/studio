<?php
/**
 * author stefanvalea@gmail.com
 */

namespace Frontend\View\Helper;


use Zend\View\Helper\AbstractHelper;

class SeoInfoViewHelper extends AbstractHelper{

    private $infoArray;

    public function __construct() {
        $this->infoArray = json_decode(file_get_contents("data/seoinfo.json"),true);
    }

    public function get($field){
        return $this->infoArray[$field];
    }

}