<?php
/**
 * author stefanvalea@gmail.com
 */

namespace Application\View\Helper;


use Application\Infrastructure\Utils\Display;
use Entities\Entity\Section;
use Entities\Mapper\SectionMapper;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;

class MainMenuHelper extends AbstractHelper{

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


    public function __invoke() {

        /** @var Section $section */
        $section = $this->sectionMapper->findSectionBySlug('second-menu');

        $arrayMenu = array_merge(array(array('name'=>Display::formatString('HOME'),'slug' => '/')),array_map(function($section){ return array('name' => Display::formatString($section->getName()),'slug' => '/'.$section->getSlug());},array_values(array_filter($section->getSubsections(),function($section){ if ($section->getStatus()===0) return false;return true;}))));
        return $this->getView()->render('application/helper/menu.phtml',array('content' => $arrayMenu));
    }
}