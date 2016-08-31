<?php
/**
 * author stefanvalea@gmail.com
 */

namespace Application\View\Helper;


use Entities\Entity\Section;
use Entities\Mapper\SectionMapper;
use Frontend\Infrastructure\Cache\CacheJson;
use Zend\View\Helper\AbstractHelper;

class SearchContentHelper extends AbstractHelper {

    public $allowedArray = array('artists', 'emerging artists', 'models', 'production', 'spokespeople', 'food & drink','influencers');

    /**
     * @var SectionMapper
     */
    private $sectionMapper;

    /**
     * @var CacheJson
     */
    private $cacheJson;

    /**
     * @param SectionMapper $sectionMapper
     */
    public function __construct(SectionMapper $sectionMapper, CacheJson $cacheJson) {
        $this->sectionMapper = $sectionMapper;
        $this->cacheJson = $cacheJson;
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


    public function __invoke() {

        $arrayCacheRequest=array('searchcontentarray');
        if ($this->cacheJson->isCached($arrayCacheRequest)) return $this->cacheJson->getCachedContent($arrayCacheRequest);

        $arrayMEntities = $this->sectionMapper->findSectionWithParent(0);
        $arrayContent = array();
        /** @var Section $mainS */
        foreach ($arrayMEntities as $mainS) {
            //if ($mainS->getStatus() === 0) continue;
            if (!in_array(strtolower($mainS->getName()), $this->allowedArray)) continue;


            /** @var Section $subsection */
            foreach ($mainS->getSubsections() as $subsection) {
                if ($subsection->getStatus() === 0) continue;
                if (($mainS->getSlug() == 'food-_-drink')||($mainS->getSlug() == 'influencers')) {
                    $arrayContent[]=$this->getElement($subsection);
                } else
                    foreach ($subsection->getSubsections() as $artist) {
                        /** @var Section $artist */
                        if ($artist->getStatus()===0) continue;
                        $arrayContent[]=$this->getElement($artist);
                }
            }
        }

        $this->cacheJson->cacheContent($arrayCacheRequest,$arrayContent);
        return json_encode($arrayContent);
    }
}