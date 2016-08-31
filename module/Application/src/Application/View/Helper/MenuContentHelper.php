<?php
/**
 * author stefanvalea@gmail.com
 */

namespace Application\View\Helper;


use Entities\Entity\Section;
use Entities\Mapper\SectionMapper;
use Frontend\Infrastructure\Cache\CacheJson;
use Zend\View\Helper\AbstractHelper;

class MenuContentHelper extends AbstractHelper{

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
    public function __construct(SectionMapper $sectionMapper,CacheJson $cacheJson) {
        $this->sectionMapper = $sectionMapper;
        $this->cacheJson = $cacheJson;
    }


    public function __invoke() {
        $arrayCacheRequest=array('menucontent');
        if ($this->cacheJson->isCached($arrayCacheRequest)) return $this->cacheJson->getCachedContent($arrayCacheRequest);
        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlug('second-menu');

        $array = array_merge(array(array('name'=>'HOME','slug' => '/')),array_map(function($section){ return array('name' => $section->getName(),'slug' => '/'.$section->getSlug());},array_values(array_filter($section->getSubsections(),function($section){ if ($section->getStatus()===0) return false;return true;}))));
        $this->cacheJson->cacheContent($arrayCacheRequest,$array);
        return json_encode($array);
    }
}