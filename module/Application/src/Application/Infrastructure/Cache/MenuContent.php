<?php
/**
 * author stefanvalea@gmail.com
 */

namespace Application\Infrastructure\Cache;


use Entities\Entity\Section;
use Entities\Mapper\SectionMapper;

class MenuContent {
    /**
     * @var SectionMapper
     */
    private $sectionMapper;


    /**
     * @param SectionMapper $sectionMapper
     */
    public function __construct(SectionMapper $sectionMapper) {
        $this->sectionMapper = $sectionMapper;
    }


    public function create($filename) {
        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlug('second-menu');

        $array = array_merge(array(array('name'=>'home','slug' => '/')),array_map(function($section){ return array('name' => $section->getName(),'slug' => '/'.$section->getSlug());},array_values(array_filter($section->getSubsections(),function($section){ if ($section->getStatus()===0) return false;return true;}))));


        $content = "var menuItems= ".json_encode($array).";";
        file_put_contents($filename,$content);
        //return json_encode($array);
    }

}