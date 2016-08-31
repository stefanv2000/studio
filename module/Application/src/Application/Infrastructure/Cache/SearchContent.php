<?php
/**
 * author stefanvalea@gmail.com
 */

namespace Application\Infrastructure\Cache;


use Entities\Entity\Section;
use Entities\Mapper\SectionMapper;
use Frontend\Infrastructure\Cache\CacheJson;

class SearchContent {
    public $allowedArray = array('artists', 'emerging artists', 'models', 'production', 'spokespeople', 'food & drink','food&drink','influencers');

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

    /**
     * @param $section Section
     */
    private function getElement($section) {



        $arrayElem = [
            'parent' => '',
            'slug' => '/' . $section->getSlug(),
            'name' => $section->getName(),
        ];
        if ($section->getParent() != null) {
            $arrayElem['parent'] .= ' / ' . $section->getParent()->getName();
            $arrayElem['slug'] = '/' . $section->getParent()->getSlug() . $arrayElem['slug'];


            if ($section->getParent()->getParent() != null) {
                $arrayElem['parent'] .= ' / ' . $section->getParent()->getParent()->getName();
                $arrayElem['slug'] = '/' . $section->getParent()->getParent()->getSlug() . $arrayElem['slug'];
            }
        }



        return $arrayElem;
    }


    public function createSearchContent($filename) {

        $arrayMEntities = $this->sectionMapper->findSectionWithParent(0);
        $arrayContent = array();
        /** @var Section $mainS */
        foreach ($arrayMEntities as $mainS) {
            //if ($mainS->getStatus() === 0) continue;
            if (!in_array(strtolower($mainS->getName()), $this->allowedArray)) continue;


            /** @var Section $subsection */
            foreach ($mainS->getSubsections() as $subsection) {
                if ($subsection->getStatus() === 0) continue;
                if (($mainS->getSlug() == 'food-_-drink')||($mainS->getSlug() == 'food_drink')||($mainS->getSlug() == 'influencers')) {
                    $arrayContent[]=$this->getElement($subsection);
                } else
                    foreach ($subsection->getSubsections() as $artist) {
                        /** @var Section $artist */
                        if ($artist->getStatus()===0) continue;
                        $arrayContent[]=$this->getElement($artist);
                    }
            }
        }

        $content = "var searchItems= ".json_encode($arrayContent).";";
        file_put_contents($filename,$content);

        //return json_encode($arrayContent);
    }
}